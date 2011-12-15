<?php
namespace org\opencomb\opencms;


// use org\jecat\framework\auth\PurviewManager;
// use org\jecat\framework\system\AccessRouter;
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
// 		$aAccessRouter = AccessRouter::singleton() ;
// 		$aAccessRouter->addController("org\\opencomb\\opencms\\category\\CreateCategory",'createcategory','') ;
		
// 		$aAccessRouter->setDefaultController("org\\opencomb\\opencms\\category\\CreateCategory") ;
		
		//菜单
		AOP::singleton()->register('org\\opencomb\\opencms\\aspect\\ControlPanelFrameAspect') ;
	}
}