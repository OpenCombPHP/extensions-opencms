<?php
namespace org\opencomb\opencms;

// use org\jecat\framework\auth\PurviewManager;
use org\jecat\framework\system\AccessRouter;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension ;

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
// 		$aAccessRouter->addController("org\\opencomb\\opencms\\category\\CreateCategory",'createcategory','') ;
		//设置首页控制器
		$aAccessRouter->setDefaultController("org\\opencomb\\opencms\\index\\Index") ;
		
		//菜单
		AOP::singleton()->register('org\\opencomb\\opencms\\aspect\\ControlPanelFrameAspect') ;
		AOP::singleton()->register('org\\opencomb\\opencms\\aspect\\MainMenuAspect') ;
	}
}