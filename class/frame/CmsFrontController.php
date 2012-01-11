<?php
namespace org\opencomb\opencms\frame ;

use org\opencomb\opencms\frame\CmsFrontFrame;
use org\opencomb\coresystem\mvc\controller\Controller;

class CmsFrontController extends Controller
{
    public function createFrame()
    {
    	return new CmsFrontFrame($this->params()) ;
    }
}
?>