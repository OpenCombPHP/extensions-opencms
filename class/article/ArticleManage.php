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
				) ,
			),
			'model:articles'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'name'=>'article',
					'belongsTo:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'table'=>'category',
						'name'=>'category',
					)
				)
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			)
		);
	}
	
	public function process()
	{
		//准备分类信息
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		$this->viewArticle->variables ()->set ( 'aCatIter', $this->modelCategoryTree );
		
		$this->modelArticles->load ();
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
	}
}

?>