<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategoryManage extends ControlPanel
{
	protected $arrConfig = array(
	        
	        'title'=>'分类管理',
	        'view'=>array(
	                'template'=>'CategoryManage.html',
	                'class'=>'view',
	                'widgets'=>array(
	                        array(
	                                'config'=>'widget/category_cat'
	                        ),
	                ),
	                'model'=>'categoryTree',
	        ),
	        'perms' => array(
	        // 权限类型的许可
	                'perm.purview'=>array(
	                        'name' => 'purview:admin_category',
	                ) ,
	        ) ,
	) ;	
	
	public function process()
	{
		$this->checkPermissions('您没有这个分类的管理权限,无法继续浏览',array()) ;
		
		$categoryModel = Model::Create('opencms:category');
		
		//准备分类信息
		$categoryModel->load();
		
		$aCatSelectWidget = $this->view->widget("category_cat");
		
		Category::buildTree($categoryModel);
		
		foreach($categoryModel as $aCat)
		{
			$aCatSelectWidget->addOption($aCat['title'],$aCat['cid'],false);
		}
		
		$this->view()->setModel($categoryModel);
	}
}
