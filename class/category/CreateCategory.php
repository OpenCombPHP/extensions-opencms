<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\ui\xhtml\Mark;

use org\jecat\framework\mvc\model\Model;

use org\opencomb\coresystem\auth\PurviewQuery;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateCategory extends ControlPanel
{
	protected $arrConfig = array(
	        
	        'title'=>'新建分类',  //视图的标题
	        'view'=>array( //view的name属性,格式是view:(name属性)
	                'template'=>'CategoryCreate.html',
	                'class'=>'view',    //指定初始化view对象的类,这里使用Formview类,form是Formview的别名
	        ),
	        'perms' => array(
	                // 权限类型的许可
	                'perm.purview'=>array(
	                        'name' => 'purview:admin_category',
	                        'target' => PurviewQuery::all,
	                ) ,
	        ) ,
	) ;	
	
	public function process()
	{
		$this->checkPermissions('您没有新建分类的权限,无法继续浏览',array()) ;
		
		$categoryModel = Model::Create('opencms:category');
		
		//准备分类信息
		$categoryModel->load();
		
		Category::buildTree($categoryModel);
		
		$aCatSelectWidget = $this->view->widget("category_parent");
		$aCatSelectWidget->addOption("顶级分类",null,true);
		foreach($categoryModel as $aCat)
		{
		    $bSelect = $aCat['rgt'] == $this->params->get('target') ? true : false;
		    $aCatSelectWidget->addOption(str_repeat("--", Category::depth($categoryModel)).$aCat['title'],$aCat['cid'].":".$aCat['rgt'],$bSelect);
		}
		
		$this->view->variables()->set('sPageTitle','新建栏目') ;
		
		$this->doActions();
	}
	
	public function form()
	{
		$categoryModel = Model::Create('opencms:category');
		$target = explode(":",$this->params['category_parent']);
		
		
		$categoryModel->setData('title', $this->params['category_title']);
		$categoryModel->setData('description', $this->params['category_dec']);
		$aCategory = new Category($categoryModel);
		
		if(count($target) == 1){
			//添加顶级栏目
			$aCategory->insertCategoryToPoint(null);
		}else{
			//添加子栏目
			$aCategory->insertCategoryToPoint((int)$target[1]);
		}
		$this->location('?c=org.opencomb.opencms.category.CategoryManage');
	}
}
