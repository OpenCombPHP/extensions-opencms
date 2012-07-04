<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategoryManage extends ControlPanel
{
	protected $arrConfig = array
	(
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
	                'perm.purview'=>array(
	                        'name' => 'purview:admin_category',
	                ) ,
	        ) ,
	) ;	
	
	public function process()
	{
		$this->checkPermissions('您没有这个分类的管理权限,无法继续浏览',array()) ;
		
		/**
		 * 创建Model
		 * @var unknown_type
		 */
		$categoryModel = Model::Create('opencms:category');
		
		/**
		 * 载入Model信息
		 */
		$categoryModel->load();
		
		/**
		 * 创建html控件对象
		 * @var unknown_type
		 */
		$aCatSelectWidget = $this->view->widget("category_cat");
		
		/**
		 * 对分类信息增加等级关系
		 */
		Category::buildTree($categoryModel);
		
		/**
		 * 初始化控件内容
		 */
		foreach($categoryModel as $aCat)
		{
			$aCatSelectWidget->addOption($aCat['title'],$aCat['cid'],false);
		}
		
		/**
		 * 视图绑定Model
		 */
		$this->view()->setModel($categoryModel);
	}
}
