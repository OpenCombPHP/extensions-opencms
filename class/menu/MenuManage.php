<?php
namespace org\opencomb\opencms\menu;

use org\jecat\framework\mvc\model\Model;

use org\opencomb\coresystem\auth\PurviewQuery;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\system\Application;

class MenuManage extends ControlPanel
{
	protected $arrConfig = array(
			'title'=>'菜单管理',
			'view'=>array(
				'template' => 'MenuManage.html',
				'class' => 'view',
				'model' => 'categoryTree',
			),
			'perms' => array(
					// 权限类型的许可
					'perm.purview'=>array(
							'name' => 'purview:admin_category',
							'target'=>PurviewQuery::all
					) ,
			) ,
	) ;	
	
	
	public function process()
	{
		$this->checkPermissions('您没有这个功能的权限,无法继续浏览',array()) ;
		
		$categoryModel = Model::Create('opencms:category');
		$articlesModel = Model::Create('opencms:article') ;
		
		//准备分类信息
		$categoryModel->load();
		Category::buildTree($categoryModel);
		
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		$arrMenus = $aSetting->item('/menu/mainmenu','mainmenu',array()) ;
		
		$this->view()->setModel($categoryModel);
		$this->view->variables()->set('arrMenus',$arrMenus) ;
		
		$this->doActions();
	}
	
	public function form()
	{
	    $categoryModel = Model::Create('opencms:category');
	    //准备分类信息
	    $categoryModel->load();
	    Category::buildTree($categoryModel);
	    
		$arrMenus = array();
		if($this->params->get('cat')){
			foreach( $this->params->get('cat') as $sCid => $arrMenu){
				if(isset($arrMenu['mainmenu'])){
				    foreach ($categoryModel as $o)
				    {
				        if($o['cid'] == $sCid)
				        {
				            $aCatModel = $categoryModel->alone();
				        }
				    }
					
					$arrMenus[ 'item:'.(int)$sCid ] = array(
							'title'=>$aCatModel->data('title'),
							'link'=>'?c=org.opencomb.opencms.article.ArticleList&cid='.$sCid ,
							'query'=>array('cid='.$sCid ),
					);
					
					$aChildModelList = Category::getChildren($aCatModel);
					$arrMenus[ 'item:'.(int)$sCid ]['query'][] = 'cid='. $aCatModel['cid'];
					foreach($aChildModelList as $aModel)
					{
						$arrMenus[ 'item:'.(int)$sCid ]['query'][] = 'cid='. $aModel['cid'];
					}
				}
			}
		}
		$aSetting = Application::singleton()->extensions()->extension('opencms')->setting() ;
		
		if(!$arrMenus){
			$aSetting->deleteKey('/menu/mainmenu');
		}else{
			$aSetting->setItem('/menu/mainmenu','mainmenu',$arrMenus) ;
		}
		
		$this->view->variables()->set('arrMenus',$arrMenus) ;
		
		$this->view()->setModel($categoryModel);
		$this->messageQueue ()->create(Message::success,"菜单列表设置保存成功");
	}
}
