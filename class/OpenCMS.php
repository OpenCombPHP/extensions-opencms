<?php
namespace org\opencomb\opencms;


// use org\jecat\framework\auth\PurviewManager;
// use org\jecat\framework\system\AccessRouter;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\ext\Extension ;

class OpenCMS extends Extension
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
// 		$aAccessRouter = AccessRouter::singleton() ;
// 		$aAccessRouter->addController("org\\opencomb\\opencms\\category\\CreateCategory",'createcategory','') ;
		
// 		$aAccessRouter->setDefaultController("org\\opencomb\\opencms\\category\\CreateCategory") ;
		
// 		// 权限管理器
// 		PurviewManager::setSingleton( new DBPurviewManager('coresystem_purview') ) ;
		//菜单
		AOP::singleton()->register('org\\opencomb\\opencms\\aspect\\ControlPanelFrameAspect') ;
	}
}