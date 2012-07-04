<?php
namespace org\opencomb\opencms\index;

use org\opencomb\coresystem\mvc\controller\Controller;
use org\opencomb\opencms\article\TopList;
// use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\system\Application;

class Index extends Controller
{
	protected $arrConfig = array(
		'title'=>'首页',
	) ;

	public function process()
	{
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		$arrTopLists = $aSetting->item('/index/toplist','toplist',array()) ;
		
		if(count($arrTopLists) > 0){
			foreach($arrTopLists as $nCid => $arrTopList){
				if(isset($arrTopList['index_new'])){
					$aToplist = new TopList(array_merge($arrTopList,array('cid'=>$nCid , 'orderby'=>'createTime')));
					$this->add($aToplist , 'topList_new_'.$nCid );
				}
				if(isset($arrTopList['index_hot'])){
					$aToplist = new TopList(array_merge($arrTopList,array('cid'=>$nCid , 'orderby'=>'views')));
					$this->add($aToplist , 'topList_hot_'.$nCid );
				}
			}
		}else{
			//TODO  输出提示文字,让管理员设置首页
		}
	}
}
