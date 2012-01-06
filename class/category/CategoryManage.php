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
		//准备分类信息
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
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