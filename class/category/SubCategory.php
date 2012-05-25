<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\ModelListIterator;
use org\jecat\framework\message\Message;
use org\jecat\framework\mvc\model\db\Category;
use org\opencomb\coresystem\mvc\controller\Controller;

class SubCategory extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'子分类列表',
			'view'=>array(
				'template'=>'opencms:SubCategory.html',
				'class'=>'view',
				'model'=>'categoryTree',
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'limit'=>-1,
					'table'=>'opencms:category',
				)
			)
		);
	}
	
	public function process()
	{
		//准备分类信息
		if(!$this->categoryTree->load(array($this->params->get("cid")),array('cid'))){
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
		Category::buildTree($aCategoryTree);
		$this->view->variables ()->set ( 'aCatIter', $aCategoryTree );
	}
}
