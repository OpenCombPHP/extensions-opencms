<?php
namespace org\opencomb\opencms\category;

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
				'config'=>'model/category',
			)
		);
	}
	
	public function process()
	{
// 		$this->modelCategoryTree->printStruct();
		//准备分类信息
		$aCatIter = Category::loadTotalCategory($this->modelCategoryTree->prototype()) ;
		$this->modelCategoryTree->printStruct() ;
		
		Category::buildTree($aCatIter);
		
		$this->viewCategory->variables()->set('aCatIter',$aCatIter) ;
	}
}

?>