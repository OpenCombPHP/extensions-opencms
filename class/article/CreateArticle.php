<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\db\Category;

use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleForm.html',
				'class'=>'form',
				'model'=>'article',
				'widgets'=>array(
					array(
						'config'=>'widget/article_title'
					),
					array(
						'config'=>'widget/article_cat'
					),
					array(
						'config'=>'widget/article_content'
					)
				)
			),
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article'
				)
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
		//为分类select添加option
		$aCatSelectWidget = $this->viewArticle->widget("article_cat");
		
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		
		foreach($this->modelCategoryTree->childIterator() as $aCat)
		{
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid,false);
		}
		
		$this->viewArticle->variables()->set('page_h1',"新建文章") ;
		
		//如果是提交请求...
		if ($this->viewArticle->isSubmit ( $this->params )) //前面定义了名为article的视图,之后就可以用$this->viewArticle来取得这个视图.控制器把视图当作自己的成员来管理,通过"viewArticle","viewarticle","article"这3种成员变量名都可以访问到这个view,推荐第一种
		{
			do
			{
				//加载所有控件的值
				$this->viewArticle->loadWidgets ( $this->params );
				//校验所有控件的值
				if (! $this->viewArticle->verifyWidgets ())
				{
					break;
				}
				
				//记录创建时间
				$this->modelArticle->setData('createTime',time());
				
				$this->viewArticle->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				
				if ($this->modelArticle->save ())
				{
// 					DB::singleton()->executeLog();
					$this->viewArticle->hideForm ();
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