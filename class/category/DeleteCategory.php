<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class DeleteCategory extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:category'=>array(
				'template'=>'DeleteCategory.html',
				'class'=>'view'
			),
			'model:categoryTree'=>array(
				'config'=>'model/categoryTree'
			)
		);
	}
	
	public function process()
	{
		//要删除哪些项?把这些项数组一起删除,如果只有一项,也把也要保证它是数组
		if ($this->params->has ( "cid" ))
		{
			$arrToDelete = is_array ( $this->params->get ( "cid" ) ) ? $this->params->get ( "cid" ) : ( array ) $this->params->get ( "cid" );
			$this->modelCategoryTree->prototype ()->criteria ()->where ()->in ( "cid", $arrToDelete );
			if ($this->modelCategoryTree->load ())
			{
				$aCategory = new Category($this->modelCategoryTree);
				$aCategory->delete();
				$this->messageQueue ()->create ( Message::success, "删除栏目成功" );
			}
			else
			{
				$this->messageQueue ()->create ( Message::error, "删除栏目失败" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定栏目" );
		}
	}
}

?>