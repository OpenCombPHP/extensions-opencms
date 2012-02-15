<?php
namespace org\opencomb\opencms\aspect ;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\lang\aop\jointpoint\JointPointMethodDefine;

class ControlPanelFrameAspect
{
	/**
	 * @pointcut
	 */
	public function pointcutCreateBeanConfig()
	{
		return array(
			new JointPointMethodDefine('org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame','createBeanConfig') ,
		) ;
	}
	
	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	private function createBeanConfig()
	{
		// 调用原始原始函数
		$arrConfig = aop_call_origin() ;
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig(
				$arrConfig['frameview:frameView']['widget:mainMenu']['items']['CMS']
				, BeanFactory::singleton()->findConfig('widget/control-panel-frame-menu','opencms')
		) ;
		
		return $arrConfig ;
	}
}
?>