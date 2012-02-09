<?php
namespace org\opencomb\opencms\category;

use org\opencomb\opencms\OpenCMS;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategoryManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'分类管理',
			'view:category'=>array(
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
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'limit'=>-1,
					'table'=>'category',
					'name'=>'category',
				)
			)
		);
	}
	
	public function process()
	{
		$this->checkPermissions('您没有这个分类的管理权限,无法继续浏览',array()) ;
		//准备分类信息
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		
		$aCatSelectWidget = $this->viewCategory->widget("category_cat");
		foreach($this->modelCategoryTree->childIterator() as $aCat)
		{
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid,false);
		}
	}
}
?>