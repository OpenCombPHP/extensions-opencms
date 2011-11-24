<?php
namespace org\opencomb\opencms;

use org\jecat\framework\lang\aop\AOP;
use org\opencomb\ext\Extension ;

class OpenCMS extends Extension
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
		AOP::singleton()->register('org\\opencomb\\development\\toolkit\\aspect\\ControlPanelFrameAspect') ;
	}
}