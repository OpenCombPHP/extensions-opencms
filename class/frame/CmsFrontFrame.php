<?php
namespace org\opencomb\opencms\frame ;

use org\opencomb\coresystem\mvc\controller\FrontFrame;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\mvc\view\View;

class CmsFrontFrame extends FrontFrame
{
	public function createBeanConfig()
	{
		$arrBean =  array(
			'frameview:CmsFrameView' => array(
				'template' => 'CmsFrame.html' ,
				'widget:mainMenu' => array( 'config'=>'coresystem:widget/front-frame-menu' ) ,
			) ,
			'controllers' => array() ,
		) ;
		
		$nCid = $this->params->get('cid');
		
		$arrBean['controllers']['topListNew'] = array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('cid'=>$nCid , 'orderby'=>'createTime'),
		);
		$arrBean['controllers']['topListHot'] = array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('cid'=>$nCid , 'orderby'=>'views'),
		);
		
// 		var_dump($arrBean);exit;
		
		return $arrBean;
	}
// 	public function buildBean(array & $arrConfig,$sNamespace='*',\org\jecat\framework\bean\BeanFactory $aBeanFactory=null)
// 	{
// 		if($sNamespace=='*')
// 		{
// 			$sNamespace = $this->application()->extensions()->extensionNameByClass( get_class($this) )?: '*' ;
// 		}
// 		return parent::buildBean($arrConfig,$sNamespace) ;
// 	}
}
?>