<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\ModelListIterator;
use org\jecat\framework\message\Message;
use org\jecat\framework\mvc\model\Category;
use org\opencomb\coresystem\mvc\controller\Controller;

class SubCategory extends Controller
{
	protected $arrConfig = array(
			'title'=>'子分类列表',
			'view'=>array(
				'template'=>'opencms:SubCategory.html',
				'class'=>'view',
				'model'=>'categoryTree',
			),
	) ;	
	
	public function process()
	{
	    
	    $categoryModel = Model::Create('opencms:category');
	    
	    $articlesModel = Model::Create('opencms:article') -> hasOne('opencms:category','cid','cid');
	    
		//准备分类信息
		if(!$categoryModel->load($this->params->get("cid"),'cid')){
			$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
		}
		$aParentCat = Category::getParents($this->categoryTree->child(0));
		if($nParentCount = $aParentCat->childrenCount()){
			$aParentCat = $aParentCat->child($nParentCount-1);
			$aCategoryTree = Category::getChildren($aParentCat);
		}else{
			$this->categoryTree->load();
			$aCategoryTree = $this->categoryTree;
		}
		//Category::buildTree($aCategoryTree);
		$this->view->variables ()->set ( 'aCatIter', $aCategoryTree );
	}
}
