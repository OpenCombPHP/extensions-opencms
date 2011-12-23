<?php
namespace org\opencomb\opencms\article;


use org\opencomb\opencms\frame\CmsFrontController;

use org\jecat\framework\db\DB;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;

class ArticleList extends CmsFrontController
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleList.html',
				'class'=>'view',
				'model'=>'articles',
				'widget:paginator' => array(
					'class' => 'paginator' ,
				) ,
			),
			'model:category'=>array(
				'orm'=>array(
					'columns' => array('lft','rgt') ,
					'table'=>'category',
				)
			),
			'model:articles'=>array(
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'hasOne:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'columns' => array('title') ,
						'table'=>'category',
					) ,
				)
			)
		);
	}
	
	public function process()
	{
		if($this->params->has("cid")){
			//准备分类信息
			if(!$this->modelCategory->load(array($this->params->get("cid")),array('cid'))){
				$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
			}
			
			$aWhere = clone $this->modelArticles->prototype()->criteria()->where();
			
			$aWhere->ge("category.lft",$this->modelCategory->data('lft'));
			$aWhere->le("category.lft",$this->modelCategory->data('rgt'));
			$aWhere->ge("category.rgt",$this->modelCategory->data('lft'));
			$aWhere->le("category.rgt",$this->modelCategory->data('rgt'));
			
			if($this->params->has('order') and $this->params->get('order') == "asc"){
				$this->modelArticles->prototype()->criteria()->addOrderBy('createTime',false);
			}else{
				$this->modelArticles->prototype()->criteria()->addOrderBy('createTime',true);
			}
			
			//页面显示结果数,默认20
			if($this->params->has("limit")){
				$this->modelArticles->prototype()->criteria()->setLimit($this->params->get("limit"));
			}else{
				$this->modelArticles->prototype()->criteria()->setLimit(20);
			}
			
			$this->modelArticles->load($aWhere);
			
			//把cid传给frame
			$this->frame()->params()->set('cid',$this->params->get("cid"));
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
		}
	}
}

?>