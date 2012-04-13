<?php
namespace org\opencomb\opencms\frame ;

use org\jecat\framework\mvc\model\db\Category;

use org\opencomb\coresystem\mvc\controller\FrontFrame;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\mvc\view\View;

class ArticleFrontFrame extends FrontFrame
{
	public function createBeanConfig()
	{
		$arrParentBean = parent::createBeanConfig();
		$arrBean =  array(
			'frameview:CmsFrameView' => array(
				'template' => 'CmsFrame.html' ,
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
		$this->category->load($this->params->get('cid'),'cid');
		
		$this->categoryList->loadSql('lft < @1 and rgt > @2' , $this->category->data('lft') ,$this->category->data('rgt') );
		
		$arrBreadcrumbNavigation = array();
		foreach($this->categoryList->childIterator() as $aCat){
			$arrBreadcrumbNavigation[$aCat->title] = "?c=org.opencomb.opencms.article.ArticleList&cid=".$aCat->cid;
		}
		
		$this->frameView->viewCmsFrameView->variables()->set('arrBreadcrumbNavigation',$arrBreadcrumbNavigation) ;
	}
}
?>