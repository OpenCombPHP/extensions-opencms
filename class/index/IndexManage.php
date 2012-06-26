<?php
namespace org\opencomb\opencms\index;

use org\jecat\framework\setting\Setting;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\mvc\model\Model;
use org\opencomb\coresystem\auth\PurviewQuery;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\system\Application;

class IndexManage extends ControlPanel
{
	protected $arrConfig = array(
        'title'=>'首页管理',
        'view'=>array(
                'template' => 'IndexManage.html',
        ),
        'perms' => array(
            'perm.purview'=>array(
                    'name' => 'purview:admin_category',
                    'target'=>PurviewQuery::all
            ) ,
        ) ,
	) ;	
	
	
	public function process()
	{
		$this->checkPermissions('您没有这个功能的权限,无法继续浏览',array()) ;
		
		$aModel = Model::Create('opencms:category')
		->load() ;
		
		//准备分类信息
		Category::buildTree($aModel);
		
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		
		$arrTopLists = $aSetting->item('/index/toplist','toplist',array()) ;
		$this->view->variables()->set('arrTopLists',$arrTopLists) ;
		$this->view()->setModel($aModel);
		
		$this->doActions();
	}
	
	public function form()
	{
	    
	    $aModel = Model::Create('opencms:category')
	    ->limit(20)
	    ->load() ;
	    
	    
		$arrTopLists = array();
		
		foreach( $this->params->get('cat') as $sCid => $arrTopList){
		    
			if(isset($arrTopList['index_new']) || isset($arrTopList['index_hot'])){
				$arrTopLists[ (int)$sCid ] = $arrTopList;
			}
		}
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		
		$aSetting->setItem('/index/toplist','toplist',$arrTopLists) ;
			
		$this->view->variables()->set('arrTopLists',$arrTopLists) ;
		
		$this->view()->setModel($aModel);
		$this->messageQueue ()->create(Message::success,"首页设置保存成功");
	}
}
