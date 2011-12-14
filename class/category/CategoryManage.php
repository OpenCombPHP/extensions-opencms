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
			),
			'model:categoryTree'=>array(
				'config'=>'model/categoryTree',
			)
		);
	}
	
	public function process()
	{
		// 权限认证
		$this->requirePurview(OpenCMS::PURVIEW_ADMIN, 'opencms') ;
		
		//准备分类信息
		$aCatIter = Category::loadTotalCategory($this->modelCategoryTree->prototype()) ;
		
		Category::buildTree($aCatIter);
		
		$this->viewCategory->variables()->set('aCatIter',$aCatIter) ;
	}
}

?>