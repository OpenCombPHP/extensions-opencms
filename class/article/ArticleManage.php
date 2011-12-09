<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\db\Category;

use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ArticleManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleManage.html',
				'class'=>'view',
				'model'=>'articles',
				'widget:paginator' => array(
					'class' => 'paginator' ,
					'count' => 10,
				) ,
			),
			'model:articles'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'name'=>'article',
					'belongsTo:post'=>array(
						'fromkeys'=>'pid',
						'tokeys'=>'pid',
						'config'=>'basepost:model/orm/post'
					),
					'belongsTo:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'table'=>'category',
						'name'=>'category',
					)
				)
			),
			'model:categoryTree'=>array(
				'config'=>'model/categoryTree'
			)
		);
	}
	
	public function process()
	{
		//准备分类信息
		$aCatIter = Category::loadTotalCategory ( $this->modelCategoryTree->prototype () );
		Category::buildTree ( $aCatIter );
		$this->viewArticle->variables ()->set ( 'aCatIter', $aCatIter );
		
		//TODO 排序
		
		//TODO groupby
		
		//限制
// 		$this->modelArticles->prototype()->criteria()->setLimit();
		$this->modelArticles->load ();
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
	}
}

?>