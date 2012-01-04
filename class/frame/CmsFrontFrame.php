<?php
namespace org\opencomb\opencms\frame ;

use org\jecat\framework\mvc\model\db\Category;

use org\opencomb\coresystem\mvc\controller\FrontFrame;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\mvc\view\View;

class CmsFrontFrame extends FrontFrame
{
	public function createBeanConfig()
	{
		$arrParentBean = parent::createBeanConfig();
		$arrBean =  array(
			'frameview:CmsFrameView' => array(
				'template' => 'CmsFrame.html' ,
				'modle' => 'category'
			) ,
			// 控制器栏目内最新内容
			'controller:topListNew' => array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('orderby'=>'createTime'),
			) ,
			// 控制器栏目内最热内容
			'controller:topListHot' => array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('orderby'=>'views'),
			) ,
			'model:category' =>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			),
		);
		BeanFactory::mergeConfig( $arrParentBean ,$arrBean );
		return $arrParentBean;
	}
	
	public function process(){
		$this->modelCategory->load();
		Category::buildTree($this->modelCategory);
		$arrBreadcrumbNavigation = array();
		
		$aCategory = $this->modelCategory->findChildBy($this->params->get('cid'),'cid');
		
		$this->getBreadcrumbNavigation($aCategory , $this->modelCategory , &$arrBreadcrumbNavigation);
		
		for($i=0;$i<count($arrBreadcrumbNavigation);$i++){
			$arrBreadcrumbNavigation[$i] = "?c=org.opencomb.opencms.article.ArticleList&cid=".$arrBreadcrumbNavigation[$i];
		}
		
		$arrBreadcrumbNavigation[$aCategory->title] = "?c=org.opencomb.opencms.article.ArticleList&cid=".$this->params->get('cid');
		
		$this->frameView->viewCmsFrameView->variables()->set('arrBreadcrumbNavigation',$arrBreadcrumbNavigation) ;
	}
	
	private function getBreadcrumbNavigation($aCategory , $aCategoryModel , $arrParentList){
		if(!$aCategory){
			return;
		}
		$aParentCategory = $this->parentCategory($aCategory , $aCategoryModel );
		if(!$aParentCategory) return;
		$arrParentList[$aParentCategory->title] = $aParentCategory->cid;
		$this->getBreadcrumbNavigation($aParentCategory, $aCategoryModel, &$arrParentList);
	}
	
	/**
	 * 查找直接父分类
	 * 原理:A分类的父分类(包括父分类的分类等等)的左脚位置都比A分类的左脚小,右脚都比A分类的右脚大,在这些分类中,左脚最大的就是A分类最直接的父分类
	 * @param IModel $aCategory 子分类(查询的起点)
	 * @param IModel $aCategoryTree 分类集(查询集合)
	 * @return IModel 父分类,如果自身是顶级分类(没有父分类),就返回null
	 */
	public function parentCategory( $aCategory,  $aCategoryTree){
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