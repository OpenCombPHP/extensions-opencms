<?php
namespace org\opencomb\opencms\category;

use jc\mvc\model\db\Category;
use jc\mvc\view\DataExchanger;
use jc\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategoryManage extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:category'=>array(
				'template'=>'CategoryManage.html',
				'class'=>'view',
				'model'=>'categoryTree'
			),
			'model:categoryTree'=>array(
				'config'=>'model/category'
			)
		);
	}
	
	public function process()
	{
		//准备分类信息
		$aCatIter = Category::loadTotalCategory($this->modelCategoryTree->prototype()) ;
		
		Category::buildTree($aCatIter);
		
		$this->viewCategory->variables()->set('aCatIter',$aCatIter) ;
		
	}
}

?>