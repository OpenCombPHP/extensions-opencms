<?php
namespace org\opencomb\opencms\article;


use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;

class ArticleList extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'文章列表',
			'view'=>array(
				'template'=>'ArticleList.html',
				'class'=>'view',
				'model'=>'articles',
				'widget:paginator' => array(
					'class' => 'paginator' ,
				) ,
			),
			'model:category'=>array(
				'orm'=>array(
					'columns' => array('title','lft','rgt') ,
					'table'=>'category',
				)
			),
			'model:articles'=>array(
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'limit'=>20,
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
			if(!$this->category->load(array($this->params->get("cid")),array('cid'))){
				$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
			}
			
			$this->setTitle($this->category->title . " - " . $this->title());
			
			$aWhere = clone $this->articles->prototype()->criteria()->where();
			
			$aWhere->ge("category.lft",$this->category->data('lft'));
			$aWhere->le("category.lft",$this->category->data('rgt'));
			$aWhere->ge("category.rgt",$this->category->data('lft'));
			$aWhere->le("category.rgt",$this->category->data('rgt'));
			
			if($this->params->has('order') and $this->params->get('order') == "asc"){
				$this->articles->prototype()->criteria()->addOrderBy('createTime',false);
			}else{
				$this->articles->prototype()->criteria()->addOrderBy('createTime',true);
			}
			
			//页面显示结果数,默认20
			if($this->params->has("limit")){
				$this->articles->prototype()->criteria()->setLimit($this->params->get("limit"));
			}
			
			$this->articles->load($aWhere);
			
			//把cid传给frame
			$this->params()->set('cid',$this->params->get("cid"));
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
		}
		
	}
	
	public function defaultFrameConfig()
	{
		return array('class'=>'org\\opencomb\\opencms\\frame\\ArticleFrontFrame') ;
	}
}