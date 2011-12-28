<?php
namespace org\opencomb\opencms\frame ;

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
		);
		BeanFactory::mergeConfig( $arrParentBean ,$arrBean );
		return $arrParentBean;
	}
}
?>