<?php
namespace org\opencomb\opencms;

use jc\lang\aop\AOP;

use oc\ext\Extension;

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