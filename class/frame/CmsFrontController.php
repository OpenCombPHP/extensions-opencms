<?php
namespace org\opencomb\opencms\frame ;

use org\opencomb\coresystem\mvc\controller\Controller;

class CmsFrontController extends Controller
{
    public function defaultFrameConfig()
    {
    	return array('class'=>'org\\opencomb\\opencms\\frame\\CmsFrontFrame') ;
    }
}
?>