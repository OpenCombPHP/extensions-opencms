<?php
namespace org\opencomb\opencms\category;

use org\opencomb\opencms\OpenCMS;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CategorySort extends ControlPanel
{
	public function createBeanConfig()
	{
		return array (
				'view:category'=>array(
						'template'=>'CategorySort.html',
						'class'=>'view',
				),
				'model:categoryTree' => array (
						'class'=>'categoryTree',
						'orm'=>array(
								'table'=>'category',
								'name'=>'category',
						),
					),
			);
	}
	
	public function process()
	{
		//向哪?
		if ($this->params->has ( 'to' ))
		{
			$sTo = $this->params->get ( 'to' );
		}else{
			$this->messageQueue ()->create ( Message::error, "缺少信息,栏目排序失败" );
		}
		
		//被移动的栏目的cid
		if ($this->params->has ( 'cid' ))
		{
			$nCid = ( int ) $this->params->get ( 'cid' );
		}else{
			$this->messageQueue ()->create ( Message::error, "缺少信息,栏目排序失败" );
		}
		
		//准备分类信息
		$aCatIter = Category::loadTotalCategory ( $this->modelCategoryTree->prototype () );
		
		Category::buildTree ( $aCatIter );
		
// 		$aCatIter->next();
		var_dump($aCatIter->current());
// 		$aCatIter->next();
// 		while($aCatIter->valid()){
// 			var_dump($aCatIter->current());
// 			$aCatIter->next();
// 		}
		
		//找到被操作的栏目
		$this->modelCategoryTree->load(array($nCid),array('cid'));
	}
}

?>