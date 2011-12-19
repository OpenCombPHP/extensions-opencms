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
			'model:category'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'category',
				)
			),
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article'
				)
			),
		);
	}
	
	public function process()
	{
		//要删除哪些项?把这些项数组一起删除,如果只有一项,也把也要保证它是数组
		if ($this->params->has ( "cid" ))
		{
			$arrToDelete = is_array ( $this->params->get ( "cid" ) ) ? $this->params->get ( "cid" ) : ( array ) $this->params->get ( "cid" );
			$this->modelCategory->prototype ()->criteria ()->where ()->in ( "cid", $arrToDelete );
			if ($this->modelCategory->load ())
			{
				//保证正在删除的分类没有文章
				if($this->modelArticle->load (array($this->modelCategory->data('cid')),array('cid'))){
					$this->messageQueue ()->create ( Message::error, "栏目中有文章,请先转移文章再删除栏目" );
					return;
				}
				
				//保证正在删除的分类没有子分类
				if(Category::rightPoint($this->modelCategory) - Category::leftPoint($this->modelCategory) > 1){
					$this->messageQueue ()->create ( Message::error, "栏目中有子栏目,请先转移子栏目再试" );
					return;
				}
				$aCategory = new Category($this->modelCategory);
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