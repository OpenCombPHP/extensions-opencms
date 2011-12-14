<?php
namespace org\opencomb\opencms\index;

use org\opencomb\opencms\OpenCMS;

use org\opencomb\system\PlatformFactory;
use org\opencomb\Platform;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\lang\oop\ClassLoader;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\system\Application;

class IndexManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:index'=>array(
				'template'=>'IndexManage.html',
				'class'=>'form'
			),
			'model:categoryTree'=>array(
				'config'=>'model/categoryTree'
			)
		);
	}
	
	public function process()
	{
		// 权限认证
		//$this->requirePurview(OpenCMS::PURVIEW_ADMIN,'opencms') ;
				
		//准备分类信息
		$aCatIter = Category::loadTotalCategory ( $this->modelCategoryTree->prototype () );
		
		Category::buildTree ( $aCatIter );
		
		$this->viewIndex->variables()->set('aCatIter',$aCatIter) ;
		
		if( $this->viewIndex->isSubmit($this->params) )
		{
			$arrTopLists = array();
			foreach( $this->params->get('cat') as $sCid => $arrTopList){
				if(!isset($arrTopList['index']) || $arrTopList['index']!="1"){
					continue;
				}
				$arrTopLists[ (int)$sCid ] = $arrTopList;
			}
			
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$aSetting->setItem('/index/toplist','toplist',$arrTopLists) ;
			
			$this->viewIndex->variables()->set('arrTopLists',$arrTopLists) ;
			
			$this->viewIndex->createMessage(Message::success,"最新文章列表设置保存成功");
		}else{
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$arrTopLists = $aSetting->item('/index/toplist','toplist',array()) ;
			
			$this->viewIndex->variables()->set('arrTopLists',$arrTopLists) ;
		}
	}
	

}