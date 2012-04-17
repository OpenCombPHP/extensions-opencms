<?php
namespace org\opencomb\opencms;

// use org\jecat\framework\auth\PurviewManager;
use org\jecat\framework\db\DB;

use org\jecat\framework\db\sql\compiler\NameMapper;

use org\opencomb\platform\service\Service;

use org\opencomb\platform\mvc\view\widget\Menu;
use org\opencomb\coresystem\auth\PurviewSetting;
use org\jecat\framework\system\AccessRouter;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension ;
use org\jecat\framework\bean\BeanFactory;


/**
 * 
 * @wiki /蜂巢/Opencms
 * @author anubis
 *
 * Opencms是基于蜂巢系统的cms系统.
 */
class OpenCMS extends Extension
{
	const PURVIEW_ADMIN = 'purview:admin' ;
	const PURVIEW_ADMIN_ARTICLE = 'purview:admin_article' ;
	const PURVIEW_ADMIN_CATEGORY = 'purview:admin_category' ;
	
	/**
	 * 载入扩展
	 */
	public function load()
	{
		$aAccessRouter = AccessRouter::singleton() ;
		//给控制器起别名
 		$aAccessRouter->addController("org\\opencomb\\opencms\\category\\CreateCategory",'createcategory','') ;
		//设置首页控制器
		$aAccessRouter->setDefaultController("org\\opencomb\\opencms\\index\\Index") ;
		
		// 注册菜单build事件的处理函数
		Menu::registerBuildHandle(
			'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame', 'frameView' , 'mainMenu'
			, array(__CLASS__,'buildControlPanelMenu')
		) ;
		Menu::registerBuildHandle(
			'org\\opencomb\\coresystem\\mvc\\controller\\FrontFrame', 'frameView' , 'mainMenu'
			, array(__CLASS__,'buildFrontFrameMenu')
		) ;
	}

	static public function buildControlPanelMenu(array & $arrConfig)
	{
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig(
				$arrConfig
				, BeanFactory::singleton()->findConfig('widget/control-panel-frame-menu','opencms')
		) ;
	}
	static public function buildFrontFrameMenu(array & $arrConfig)
	{
		// 调用原始原始函数
		$aSetting = \org\jecat\framework\system\Application::singleton()->extensions()->extension('opencms')->setting() ;
		$arrMenus = $aSetting->item('/menu/mainmenu','mainmenu',array()) ;
		
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig( $arrConfig, $arrMenus ) ;
	}
}