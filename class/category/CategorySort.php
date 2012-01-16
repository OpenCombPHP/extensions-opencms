<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\IModel;

use org\opencomb\opencms\OpenCMS;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategorySort extends ControlPanel
{
	public function createBeanConfig()
	{
		return array (
				'title'=>'分类排序',
			'view:category'=>array(
				'template'=>'CategorySort.html',
				'class'=>'view',
			),
			'model:categoryTree' => array (
				'config'=>'model/categoryTree',
			),
		);
	}
	
	public function process()
	{
		//向哪?
		if ($this->params->has ( 'to' ))
		{
			$sTo = $this->params->get ( 'to' );   //up or down
		}else{
			$this->messageQueue ()->create ( Message::error, "缺少信息,栏目排序失败" );
			return;
		}
		
		//被移动的栏目的cid
		if ($this->params->has ( 'cid' ))
		{
			$nCid = ( int ) $this->params->get ( 'cid' );
		}else{
			$this->messageQueue ()->create ( Message::error, "缺少信息,栏目排序失败" );
			return;
		}
		
		//准备分类信息
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		
		$aHandleCategoryModel = $this->modelCategoryTree->findChildBy($nCid,'cid');
		if(!$aHandleCategoryModel){
			$this->messageQueue ()->create ( Message::error, "没有找到对应的栏目,栏目排序失败" );
			return;
		}
		
		$aParentCategory = $this->parentCategory($aHandleCategoryModel , $this->modelCategoryTree);
		//
		
		$aChildren = array();
		if($aParentCategory){
			$aBrotherCategorys = Category::categoryChildren($aParentCategory)->childIterator();
		}else{
			$aBrotherCategorys = $this->modelCategoryTree->childIterator();
		}
		foreach( $aBrotherCategorys as $aCat){
			if(Category::depth($aHandleCategoryModel) == Category::depth($aCat)){
				$aChildren[] = $aCat;
			}
		}
		
		$aLeftBrother = null;
		$aRightBrother = null;
		if(is_int($nKey = array_search($aHandleCategoryModel, $aChildren))){
			if(array_key_exists($nKey-1, $aChildren)){
				$aLeftBrother = $aChildren[$nKey -1];
			}
			if(array_key_exists($nKey+1, $aChildren)){
				$aRightBrother =  $aChildren[$nKey +1];
			}
		}
		
		$aHandleCategory = new Category($aHandleCategoryModel);
		
		if($sTo == "up" && $aLeftBrother){
			$aHandleCategory->insertCategoryToPoint($aLeftBrother->lft);
		}else if($sTo == "down" && $aRightBrother){
			$aHandleCategory->insertCategoryToPoint((int)$aRightBrother->rgt + 1);
		}else{
			$this->messageQueue ()->create ( Message::error, "此栏目不能再移动" );
			return;
		}
		$this->messageQueue ()->create ( Message::success, "栏目排序成功" );
	}
	
	/**
	 * 查找直接父分类
	 * 原理:A分类的父分类(包括父分类的分类等等)的左脚位置都比A分类的左脚小,右脚都比A分类的右脚大,在这些分类中,左脚最大的就是A分类最直接的父分类
	 * @param IModel $aCategory 子分类(查询的起点)
	 * @param IModel $aCategoryTree 分类集(查询集合)
	 * @return IModel 父分类,如果自身是顶级分类(没有父分类),就返回null
	 */
	public function parentCategory(IModel $aCategory, IModel $aCategoryTree){
		$aParent = null; //直接父分类
		foreach($aCategoryTree->childIterator() as $aCat){
			if($aCategory->lft > $aCat->lft && $aCategory->rgt < $aCat->rgt){
				if($aParent==null){
					$aParent = $aCat;
				}else if($aParent->lft < $aCat->lft){
					$aParent = $aCat;
				}
			}
		}
		return $aParent;
	}
}

?>