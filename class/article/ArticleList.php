<?php
namespace org\opencomb\opencms\article;

use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\message\Message;

class ArticleList extends Controller
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'title'=>'文章列表',

			'frame' => array('config'=>'opencms:article-frame') ,
				
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
					'table'=>'opencms:category',
				)
			),
			'model:articles'=>array(
				'list'=>true,
				'orm'=>array(
					'table'=>'opencms:article',
					'limit'=>20,
					'orderDesc'=>'createTime',
					'hasOne:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'columns' => array('title') ,
						'table'=>'opencms:category',
					) ,
				)
			),

			// 			'frame' => array(
			// 					'config' => '' ,
			// 			) ,
			
// 			'frame' => array(
// 					'class' => 'org\\opencomb\\coresystem\\mvc\\controller\\FrontFrame' ,
// 					'frameview:CmsFrameView' => array(
// 							'template' => 'opencms:CmsFrame.html' ,
// 							// 控制器栏目内最新内容
// 							'controller:topListNew' => array(
// 									'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
// 									'params' => array('orderby'=>'createTime'),
// 							) ,
// 							// 控制器栏目内最热内容
// 							'controller:topListHot' => array(
// 									'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
// 									'params' => array('orderby'=>'views'),
// 							) ,
// 							'model:categoryList' =>array(
// 									'class'=>'model',
// 									'list'=>true,
// 									'orm'=>array(
// 											'table'=>'category',
// 											'name'=>'category',
// 									)
// 							),
// 							'model:category' =>array(
// 									'class'=>'model',
// 									'orm'=>array(
// 											'table'=>'category',
// 											'name'=>'category',
// 									)
// 							),
// 					) ,
// 			) ,
		);
		
		//页面显示结果数,默认20
		if($this->params->get("limit")){
			$arrBean['model:articles']['orm']['limit'] = (int)$this->params->get("limit");
		}
		
		if($this->params->get('order') == 'asc')
		{
			unset($arrBean['model:articles']['orm']['orderDesc']);
			$arrBean['model:articles']['orm']['orderBy'] = 'createTime';
		}
		
		return $arrBean;
	}
	
	public function process()
	{
		if($this->params->has("cid")){
			//准备分类信息
			if(!$this->category->load(array($this->params->get("cid")),array('cid'))){
				$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
			}
			
			$this->setTitle($this->category->title . " - " . $this->title());
			
			$this->articles->loadSql(
					"category.lft>=@1 and category.lft<=@2 and category.rgt>=@3 and category.rgt<=@4"
					,$this->category->lft
					,$this->category->rgt
					,$this->category->lft
					,$this->category->rgt
			) ;
			
			//把cid传给frame
			$this->params()->set('cid',$this->params->get("cid"));
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
		}
	}
	/*
	public function defaultFrameConfig()
	{
		return array('class'=>'org\\opencomb\\opencms\\frame\\ArticleFrontFrame') ;
	}*/
}
