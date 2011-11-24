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
			'view:category'=>array(
				'template'=>'CategoryForm.html',
				'class'=>'form',
				'model'=>'categoryTree',
				'widgets'=>array(
					array(
						'config'=>'widget/category_title'
					),
					array(
						'config'=>'widget/category_dec'
					)
				)
			),
			'model:categoryTree'=>array(
				'config'=>'model/category'
			)
		);
	}
	
	public function process()
	{
		//为分类select添加option
		$fff = Category::loadTotalCategory ( $this->modelCategoryTree->prototype (), true, false, $this->modelCategoryTree );
		$aCatSelectWidget = $this->viewCategory->widget ( "article_cat" );
		$aCatSelectWidget->addOption ( "文章分类...", null, true );
		foreach ( $fff as $aCat )
		{
			$aCatSelectWidget->addOption ( $aCat->title, $aCat->cid, false );
		}
		
		//如果是提交请求...
		if ($this->viewCategory->isSubmit ( $this->params )) //前面定义了名为article的视图,之后就可以用$this->viewCategory来取得这个视图.控制器把视图当作自己的成员来管理,通过"viewCategory","viewCategory","article"这3种成员变量名都可以访问到这个view,推荐第一种
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
				if ($this->modelArticle->save ())
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