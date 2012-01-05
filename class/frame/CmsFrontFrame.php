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
				'modle' => 'categoryList'
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
			'model:categoryList' =>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			),
			'model:category' =>array(
				'class'=>'model',
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
		$this->modelCategory->load($this->params->get('cid'),'cid');
		
		$aWhere = clone $this->modelCategoryList->prototype()->criteria()->where();
		
		$aWhere->le("lft",$this->modelCategory->data('lft'));
		$aWhere->ge("rgt",$this->modelCategory->data('rgt'));
		
		$this->modelCategoryList->load($aWhere);
		
		$arrBreadcrumbNavigation = array();
		foreach($this->modelCategoryList->childIterator() as $aCat){
			$arrBreadcrumbNavigation[$aCat->title] = "?c=org.opencomb.opencms.article.ArticleList&cid=".$aCat->cid;
		}
		
		$this->frameView->viewCmsFrameView->variables()->set('arrBreadcrumbNavigation',$arrBreadcrumbNavigation) ;
	}
}
?>