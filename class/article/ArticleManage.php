<?php
namespace org\opencomb\opencms\article;

use jc\mvc\model\db\Category;

use jc\mvc\view\DataExchanger;
use jc\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ArticleManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleManage.html',
				'class'=>'view',
				'model'=>'article'
			),
			'model:article'=>array(
				'class'=>'jc\\mvc\\model\\db\\ModelList',
				'config'=>'model/article',
			),
			'model:categoryTree'=>array(
				'config'=>'model/category'
			)
		);
	}
	
	public function process()
	{
		//准备分类信息
		$aCatIter = Category::loadTotalCategory ( $this->modelCategoryTree->prototype () );
		Category::buildTree ( $aCatIter );
		$this->viewArticle->variables ()->set ( 'aCatIter', $aCatIter );
		
		$this->modelArticle->load ();
// 		$this->modelArticle->printStruct();
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticle->childIterator()) ;
	}
}

?>