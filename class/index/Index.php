<?php
namespace org\opencomb\opencms\index;

use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\system\Application;
use org\opencomb\opencms\article\TopList;


class Index extends Controller
{
	public function createBeanConfig()
	{
		$arrBean = array(
				'view:index'=>array(
					'template'=>'Index.html',
					'class'=>'view',
				),
				
				'controllers' => array() ,
		);
		
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		$arrTopLists = $aSetting->item('/index/toplist','toplist',array()) ;
		
		foreach($arrTopLists as $nCid => $arrTopList){
			$arrBean['controllers']['topList'.$nCid] = array(
						'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
						'params' => array_merge($arrTopList,array('cid'=>$nCid)),
					);
		}
		
		return $arrBean;
	}

	public function process()
	{
	}
}