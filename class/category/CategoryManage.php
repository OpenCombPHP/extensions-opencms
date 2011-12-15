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
			'view:category'=>array(
				'template'=>'CategoryManage.html',
				'class'=>'view',
				'model'=>'categoryTree',
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			)
		);
	}
	
	public function process()
	{
		// 权限认证
		$this->requirePurview(OpenCMS::PURVIEW_ADMIN, 'opencms') ;
		
		//准备分类信息
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
	}
}

?>