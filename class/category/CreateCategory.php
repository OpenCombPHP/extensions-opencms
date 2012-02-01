<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateCategory extends ControlPanel
{
	/**
	 * @example /mvc/视图(View)
	 * @forwiki /mvc/视图(View)
	 * @forwiki /mvc/视图/表单视图(FormView)
	 * @forwiki /mvc/视图/表单控件/下拉菜单(Select)
	 * @forwiki /mvc/视图/表单控件/表单控件(FormWidget)
	 * 
	 * 演示如何设置一个view的bean
	 */
	public function createBeanConfig()
	{
		return array(
			'title'=>'新建分类',  //视图的标题
			'view:category'=>array( //view的name属性,格式是view:(name属性)
				'template'=>'CategoryForm.html',
				'class'=>'form',    //指定初始化view对象的类,这里使用Formview类,form是Formview的别名
				'model'=>'category', //绑定model
				'widgets'=>array(
					array(
						'config'=>'widget/category_title'  //引用外部的bean配置文件,以便重复使用相同的配置
					),
					array(
						'config'=>'widget/category_dec'
					),
					array(
						'id'=>'category_parent',
						'class'=>'select',    //select widget的bean配置
						'title'=>'分类关系',
						/*
						'options'=>array(     //options 配置方法
						  	array('0','请选择..',true),
						     array('value1','text1',false),
						     array('value2','text2',false),
						     array('value3','text3',false)
						)
						*/
					)
				)
			),
			'model:categoryTree'=>array(   //model的bean,model的name是categoryTree
				'config'=>'model/categoryTree',
			),
			'model:category'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'category',
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
		
		$aCatSelectWidget = $this->viewCategory->widget("category_parent");
		$aCatSelectWidget->addOption("顶级分类",null,true);
		foreach($this->modelCategoryTree->childIterator() as $aCat)
		{
			$bSelect = $aCat->rgt == $this->params->get('target') ? true : false;
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid.":".$aCat->rgt,$bSelect);
		}
		
		$this->viewCategory->variables()->set('sPageTitle','新建栏目') ;
		
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
				if ($this->modelCategory->save ())
				{
					$target = explode(":",$this->viewCategory->widget("category_parent")->value());
					$aCategory = new Category($this->modelCategory);
					if(count($target) == 1){
						//添加顶级栏目
						$aCategory->insertCategoryToPoint();
					}else{
						//添加子栏目
						$aCategory->insertCategoryToPoint((int)$target[1]);
					}
					$this->viewCategory->hideForm ();
					$this->messageQueue ()->create ( Message::success, "栏目保存成功" );
				}
				else
				{
					$this->messageQueue ()->create ( Message::error, "栏目保存失败" );
				}
			} while ( 0 );
		}
	}
}

?>