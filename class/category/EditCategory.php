<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class EditCategory extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:category'=>array(
				'template'=>'EditCategory.html',
				'class'=>'form',
				'model'=>'categoryTree',
				'widgets'=>array(
					array(
						'config'=>'widget/category_title'
					),
					array(
						'config'=>'widget/category_dec'
					),
				)
			),
			'model:categoryTree'=>array(
				'config'=>'model/category'
			)
		);
	}
	
	public function process()
	{
		//还原数据
		if($this->params->has("cid")){
			$this->modelCategoryTree->load(array($this->params->get("cid")),array("cid"));
			$this->viewCategory->exchangeData ( DataExchanger::MODEL_TO_WIDGET);
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		//如果是提交请求...
		if ($this->viewCategory->isSubmit ( $this->params ))
		{
			do
			{
				//加载所有控件的值
				$this->viewCategory->loadWidgets ( $this->params );
				//校验所有控件的值
				if (! $this->viewCategory->verifyWidgets ())
				{
					break;
				}
				$this->viewCategory->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				if ($this->modelCategoryTree->save ())
				{
					$this->viewCategory->hideForm ();
					$this->messageQueue ()->create ( Message::success, "文章保存成功" );
				}
				else
				{
					$this->messageQueue ()->create ( Message::error, "文章保存失败" );
				}
			} while ( 0 );
		}
	}
}

?>