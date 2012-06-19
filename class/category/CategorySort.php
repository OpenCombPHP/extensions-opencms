<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\Model;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategorySort extends ControlPanel
{
	protected $arrConfig = array(
			'title'=>'分类排序',
			'view'=>array(
				'template'=>'CategorySort.html',
				'class'=>'view',
			),
	) ;	
	
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
		
		//权限
		$this->requirePurview('purview:admin_category','opencms',$nCid,'您没有这个分类的管理权限,无法继续浏览');
		
		
		$categoryModel = Model::Create('opencms:category');
		
		//准备分类信息
		$categoryModel->load();
		
		Category::buildTree($categoryModel);
		
		foreach ($categoryModel as $o)
		{
		    if($o['cid'] == $nCid)
		    {
		        $aHandleCategoryModel = $categoryModel->alone();
		    }
		}
		
		if(!$aHandleCategoryModel){
			$this->messageQueue ()->create ( Message::error, "没有找到对应的栏目,栏目排序失败" );
			return;
		}
		
		
		$aParentCategory = $this->parentCategory($aHandleCategoryModel , $categoryModel);
		
		
		$aChildren = array();
		if($aParentCategory){
			$aBrotherCategorys = $this->childrenCategory($aParentCategory , $categoryModel);
			
		}else{
			$aBrotherCategorys = $categoryModel;
		}
		foreach( $aBrotherCategorys as $aCat){
		    
			if(Category::depth($aHandleCategoryModel) == Category::depth($aBrotherCategorys)){
				$aChildren[] = $aBrotherCategorys->alone();
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
	 * @param Model $aCategory 子分类(查询的起点)
	 * @param Model $aCategoryTree 分类集(查询集合)
	 * @return Model 父分类,如果自身是顶级分类(没有父分类),就返回null
	 */
	public function parentCategory(Model $aCategory, Model $aCategoryTree){
	    
		$aParent = null; //直接父分类
		foreach($aCategoryTree as $aCat){
		    
			if($aCategory->data('lft') > $aCat['lft'] && $aCategory['rgt'] < $aCat['rgt']){
				if($aParent==null){
					$aParent = $aCategoryTree->alone();
				}else if($aParent['lft'] < $aCat['lft']){
					$aParent = $aCategoryTree->alone();
				}
			}
		}
		return $aParent;
	}
	
	/**
	 * 查找直接子分类
	 */
	public function childrenCategory(Model $aCategory, Model $aCategoryTree){
	    
		$aChildren = Model::create($aCategory->prototype()); 
		
		foreach($aCategoryTree as $aCat){
			if($aCategory->data('lft') < $aCat['lft'] && $aCategory['rgt'] > $aCat['rgt']){
				$aChildren->addRow($aCat);
			}
		}
		
		return $aChildren;
	}
}
