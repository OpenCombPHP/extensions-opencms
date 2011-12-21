<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\db\IModel;
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
				'template'=>'CategoryForm.html',
				'class'=>'form',
				'model'=>'category',
				'widgets'=>array(
					array(
						'config'=>'widget/category_title'
					),
					array(
						'config'=>'widget/category_dec'
					),
					array(
						'config'=>'widget/category_parent'
					),
				)
			),
			'model:categoryTree'=>array(
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
			if($this->params->get("cid") == $aCat->cid){
				//在选单中排除自己,以防把自己变成自己的子分类
				continue;
			}
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid.":".$aCat->rgt,false);
		}
		//还原数据
		if($this->params->has("cid")){
			$this->modelCategory->load(array($this->params->get("cid")),array("cid"));
			$aParentCategory = $this->parentCategory($this->modelCategory , $this->modelCategoryTree);
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定栏目" );
			return;
		}
		$this->viewCategory->variables()->set('sPageTitle','编辑栏目') ;
		
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
				//得到父分类的改变,如果改变了,就改变分类的排序
				$arrNewParent = explode(":",$this->params->get("category_parent")); //数组第一个元素是cid,第2个是rgt
				if((!$aParentCategory and $arrNewParent[0] != 0)
					|| ($aParentCategory and $aParentCategory->cid != $arrNewParent[0])){
					$aCategory = new Category($this->modelCategory);
					$aCategory->insertCategoryToPoint($arrNewParent[0]==0 ? Category::end : $arrNewParent[1]);
				}
				if ($this->modelCategory->save ())
				{
					$this->viewCategory->hideForm ();
					$this->messageQueue ()->create ( Message::success, "栏目保存成功" );
				}
				else
				{
					$this->messageQueue ()->create ( Message::error, "栏目保存失败" );
				}
// 				DB::singleton()->executeLog();
			} while ( 0 );
		}else{
			$this->viewCategory->exchangeData ( DataExchanger::MODEL_TO_WIDGET);
			//还原父分类选单的值,如果有父分类
			if($aParentCategory){
				$aCatSelectWidget->setValue($aParentCategory->cid.":".$aParentCategory->rgt) ;
			}
		}
	}
	
	/**
	 * 查找直接父分类
	 * 原理:A分类的父分类(包括父分类的分类等等)的左脚位置都比A分类的左脚小,右脚都比A分类的右脚大,在这些分类中,左脚最大的就是A分类最直接的父分类
	 * @param IModel $aCategory 子分类(查询的起点)
	 * @param IModel $aCategoryTree 分类集(查询集合)
	 * @return IModel 父分类,如果自身是顶级分类(没有父分类),就返回null
	 */
	public function parentCategory(IModel $aCategory, IModel $aCategoryTree){
		$aParent = null; //直接父分类
		foreach($aCategoryTree->childIterator() as $aCat){
			if($aCategory->lft > $aCat->lft && $aCategory->rgt < $aCat->rgt){ 
				if($aParent==null){
					$aParent = $aCat;
				}else if($aParent->lft < $aCat->lft){
					$aParent = $aCat;
				}
			}
		}
		return $aParent;
	}
}

?>