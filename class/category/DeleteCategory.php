<?php
namespace org\opencomb\opencms\category;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class DeleteCategory extends ControlPanel
{
	protected $arrConfig = array(
	        
	        'title'=>'删除分类',
	        'view'=>array(
	                'template'=>'DeleteCategory.html',
	                'class'=>'view'
	        ),
	) ;	
	
	public function process()
	{
	    $this->messageQueue ()->create ( Message::error, "未指定分类" );
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->params->get('cid'),'您没有这个分类的管理权限,无法继续浏览');
		
		//要删除哪些项?把这些项数组一起删除,如果只有一项,也把也要保证它是数组
		if ($this->params->get ( "cid" ))
		{
			$arrToDelete = explode(',', $this->params->get ( "cid" )); 
			if($arrToDelete === false){
				$this->messageQueue ()->create ( Message::error, "未指定栏目" );
			}
			
			foreach($arrToDelete as $nCatIdToDelete){
				$this->delCat($nCatIdToDelete);
			}
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定栏目" );
		}
		
		$this->location('?c=org.opencomb.opencms.category.CategoryManage');
	}
	
	public function delCat($nCatIdToDelete)
	{
	    
	    $categoryModel = Model::Create('opencms:category');
	    $articlesModel = Model::Create('opencms:article');

		if ($categoryModel->load( $nCatIdToDelete , 'cid') -> rowNum())
		{
			//保证正在删除的分类没有文章
			if($articlesModel->load ($categoryModel['cid'],'cid') -> rowNum()){
				$this->messageQueue ()->create ( Message::error, "栏目中有文章,请先转移文章再删除栏目" );
				return;
			}
			
			//保证正在删除的分类没有子分类
			if($categoryModel['rgt'] - $categoryModel['lft'] > 1){
				$this->messageQueue ()->create ( Message::error, "栏目中有子栏目,请先转移子栏目再试" );
				return;
			}
			$aCategory = new Category($categoryModel);
			$aCategory->delete();
			$this->messageQueue ()->create ( Message::success, "删除栏目成功" );
		}
		else
		{
			$this->messageQueue ()->create ( Message::error, "删除栏目失败" );
		}
	}
}
