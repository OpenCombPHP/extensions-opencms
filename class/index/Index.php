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
		
		if(count($arrTopLists) > 0){
			foreach($arrTopLists as $nCid => $arrTopList){
				if(isset($arrTopList['index_new'])){
					$arrBean['controllers']['topList_new_'.$nCid] = array(
							'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
							'params' => array_merge($arrTopList,array('cid'=>$nCid , 'orderby'=>'createTime')),
					);
				}
				if(isset($arrTopList['index_hot'])){
					$arrBean['controllers']['topList_hot_'.$nCid] = array(
							'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
							'params' => array_merge($arrTopList,array('cid'=>$nCid , 'orderby'=>'views')),
					);
				}
			}
		}else{
			//TODO  输出提示文字,让管理员设置首页
			
		}
		
		return $arrBean;
	}

	public function process()
	{
	}
}