<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\Model;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class EditCategory extends ControlPanel
{
	protected $arrConfig = array(
			'title'=>'编辑分类',
			'view'=>array(
				'template'=>'CategoryEdit.html',
			),
	) ;	
	
	
	public function process()
	{
		//权限验证
		$this->requirePurview('purview:admin_category','opencms',$this->params->get('cid'),'您没有这个分类的管理权限,无法继续浏览');
		
		//创建一个新MODEL
		$categoryModel = Model::Create('opencms:category');
		
		//载入这个MODEL的所有信息
		$categoryModel->load();
		
		//针对这个MODEL的信息进行树结构整理
		Category::buildTree($categoryModel);
		
		//拿到一个下拉菜单控件，并增加选项
		$aCatSelectWidget = $this->view->widget("category_parent");
		$aCatSelectWidget->addOption("顶级分类",null,true);
		foreach($categoryModel as $aCat)
		{
		    if($this->params->get("cid") == $aCat['cid']){
		        //在选单中排除自己,以防把自己变成自己的子分类
		        continue;
		    }
		    $aCatSelectWidget->addOption(str_repeat("--", Category::depth($categoryModel)).$aCat['title'],$aCat['cid'].":".$aCat['rgt'],false);
		}
		
		//还原数据
		if($this->params->has("cid")){
		    
		    foreach ($categoryModel as $o)
		    {
		        if($o['cid'] == $this->params->get("cid"))
		        {
			   $categoryModel_now = $categoryModel->alone();	//当前需要修改的那条内容
		        }
		    }
		    $aParentCategory = $this->parentCategory($categoryModel_now , $categoryModel);
		}else{
		    $this->messageQueue ()->create ( Message::error, "未指定栏目" );
		    return;
		}
		
		$this->setTitle($categoryModel_now->title . " - " . $this->title());
		
		$this->view->variables()->set('sPageTitle','编辑栏目') ;
		
		$this->view()->setModel($categoryModel_now);

		//数据交换
		$this->view->update();
		
		//还原父分类选单的值,如果有父分类
		if($aParentCategory){
		    $aCatSelectWidget->setValue($aParentCategory->cid.":".$aParentCategory->rgt) ;
		}
		
        //根据访问参数a=，自动加载其他action
		$this->doActions();
	}
	
	public function form()
	{
		$categoryModel = Model::Create('opencms:category');
		
		//准备分类信息
		$categoryModel->load();
		
		Category::buildTree($categoryModel);
		
		foreach ($categoryModel as $o)
		{
		    if($o['cid'] == $this->params['cid'])
		    {
		        $categoryModel_now = $categoryModel->alone();
		    }
		}
		
		//得到父分类的改变,如果改变了,就改变分类的排序

		$arrNewParent = explode(":",$this->params->get("category_parent")); //数组第一个元素是cid,第2个是rgt
		$aParentCategory = $this->parentCategory($categoryModel_now , $categoryModel);
		
		if((!$aParentCategory and $arrNewParent[0] != 0)

				|| ($aParentCategory and $aParentCategory->cid != $arrNewParent[0])){

			$aCategory = new Category($categoryModel_now);
			
			$aCategory->insertCategoryToPoint($arrNewParent[0]==0 ? Category::end : $arrNewParent[1]);

		}
		// if ($categoryModel->update(
		//         array(
		//                 'title'=>$this->params['category_title'],
		//                 'description'=>$this->params['category_dec'],
  //               ),
		//         "cid = '{$this->params['cid']}'" 
  //       ))

		// {
		// 	$this->messageQueue ()->create ( Message::success, "栏目保存成功" );
		//     $this->location('?c=org.opencomb.opencms.category.CategoryManage');
		// }
		// else
		// {
		// 	$this->messageQueue ()->create ( Message::error, "栏目保存失败" );
		// }


		$categoryModel->update(
		        array(
		                'title'=>$this->params['category_title'],
		                'description'=>$this->params['category_dec'],
                ),
		        "cid = '{$this->params['cid']}'" 
        );
		$this->messageQueue ()->create ( Message::success, "栏目保存成功" );
	    $this->location('?c=org.opencomb.opencms.category.CategoryManage');
	}
	
	/**
	 * 查找直接父分类
	 * 原理:A分类的父分类(包括父分类的分类等等)的左脚位置都比A分类的左脚小,右脚都比A分类的右脚大,在这些分类中,左脚最大的就是A分类最直接的父分类
	 * @param Model $aCategory 子分类(查询的起点)
	 * @param Model $aCategoryTree 分类集(查询集合)
	 * @return Model 父分类,如果自身是顶级分类(没有父分类),就返回null
	 */
	public function parentCategory(Model $aCategory, Model $aCategoryTree){
	    
		$aParent = null; //直接父分类
		foreach($aCategoryTree as $aCat){

			if($aCategory->data('lft') > $aCat['lft'] && $aCategory['rgt'] < $aCat['rgt']){
				if($aParent==null){
					$aParent = $aCategoryTree->alone();
				}else if($aParent['lft'] < $aCat['lft']){
					$aParent = $aCategoryTree->alone();
				}
			}
		}
		return $aParent;
	}
}

