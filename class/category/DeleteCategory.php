<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateCategory extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'model:categoryTree'=>array(
				'config'=>'model/category'
			)
		);
	}
	
	public function process()
	{
		//还原数据
		if($this->params->has("cid")){
			$arrToDelete = is_array($this->params->get("cid"))? $this->params->get("cid"):(array)$this->params->get("cid");
			$this->modelCategoryTree->load($arrToDelete,array("cid"));
			$this->modelCategoryTree->delete();
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
	}
}

?>