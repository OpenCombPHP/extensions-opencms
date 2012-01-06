<?php
namespace org\opencomb\opencms\index;

use org\opencomb\opencms\OpenCMS;

use org\opencomb\platform\system\PlatformFactory;
use org\opencomb\platform\Platform;
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
				'title'=>'首页管理',
			'view:index'=>array(
				'template' => 'IndexManage.html',
				'class' => 'form',
				'model' => 'categoryTree',
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			),
		);
	}
	
	public function process()
	{
		//准备分类信息
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
		$this->modelCategoryTree->load();
		Category::buildTree($this->modelCategoryTree);
		
		if( $this->viewIndex->isSubmit($this->params) )
		{
			$arrTopLists = array();
			foreach( $this->params->get('cat') as $sCid => $arrTopList){
				if(isset($arrTopList['index_new']) || isset($arrTopList['index_hot'])){
					$arrTopLists[ (int)$sCid ] = $arrTopList;
				}
			}
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$aSetting->setItem('/index/toplist','toplist',$arrTopLists) ;
			
			$this->viewIndex->variables()->set('arrTopLists',$arrTopLists) ;
			
			$this->messageQueue ()->create(Message::success,"首页设置保存成功");
		}else{
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$arrTopLists = $aSetting->item('/index/toplist','toplist',array()) ;
			
			$this->viewIndex->variables()->set('arrTopLists',$arrTopLists) ;
		}
	}
	

}