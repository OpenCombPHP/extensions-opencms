<?php
namespace org\opencomb\opencms\menu;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\system\Application;

class MenuManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
				'title'=>'菜单管理',
			'view:menuManage'=>array(
				'template' => 'MenuManage.html',
				'class' => 'form',
				'model' => 'categoryTree',
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'limit'=>-1,
					'table'=>'category',
					'name'=>'category',
				)
			),
		);
	}
	
	public function process()
	{
		//准备分类信息
		$this->modelCategoryTree->load();
		Category::buildTree($this->modelCategoryTree);
		
		if( $this->viewMenuManage->isSubmit($this->params) )
		{
			$arrMenus = array();
			foreach( $this->params->get('cat') as $sCid => $arrMenu){
				if(isset($arrMenu['mainmenu'])){
					$arrMenus[ (int)$sCid ] = array(
							'title'=>$this->modelCategoryTree->findChildBy($sCid,"cid")->data('title'),
							'link'=>'?c=org.opencomb.opencms.article.ArticleList&cid='.$sCid ,
					);
				}
			}
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$aSetting->setItem('/menu/mainmenu','mainmenu',$arrMenus) ;
			
			$this->viewMenuManage->variables()->set('arrMenus',$arrMenus) ;
			
			$this->messageQueue ()->create(Message::success,"菜单列表设置保存成功");
		}else{
			$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
			$arrMenus = $aSetting->item('/menu/mainmenu','mainmenu',array()) ;
			
			$this->viewMenuManage->variables()->set('arrMenus',$arrMenus) ;
		}
	}
}