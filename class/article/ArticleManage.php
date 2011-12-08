<?php
namespace org\opencomb\opencms\article;

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
			),
			'model:articles'=>array(
				'config'=>'model/articles',
				'list'=>true,
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
		
		//降序
// 		$this->modelArticles->prototype()->

		//读取
		$this->modelArticles->load ();
// 		$this->modelArticle->printStruct();
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
	}
}

?>