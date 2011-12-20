<?php
namespace org\opencomb\opencms\aspect ;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\lang\aop\jointpoint\JointPointMethodDefine;

class MainMenuAspect
{
	/**
	 * @pointcut
	 */
	public function pointcutCreateBeanConfig()
	{
		return array(
			new JointPointMethodDefine('org\\opencomb\\coresystem\\mvc\\controller\\FrontFrame','createBeanConfig') ,
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
		$aSetting = \org\jecat\framework\system\Application::singleton()->extensions()->extension('opencms')->setting() ;
		$arrMenus = $aSetting->item('/menu/mainmenu','mainmenu',array()) ;
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig(
				$arrConfig['frameview:frameView']['widget:mainMenu']['items']
				,$arrMenus
		) ;
		return $arrConfig ;
	}
}
?>