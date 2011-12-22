<?php
namespace org\opencomb\opencms\frame ;

use org\opencomb\opencms\frame\CmsFrontFrame;
use org\opencomb\coresystem\mvc\controller\Controller;
use org\opencomb\platform\ext\ExtensionManager;
use org\jecat\framework\mvc\view\Webpage;

class CmsFrontController extends Controller
{
    public function createFrame()
    {
    	return new CmsFrontFrame($this->params()) ;
    }
}
?>