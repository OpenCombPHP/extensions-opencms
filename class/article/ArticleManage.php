<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ArticleManage extends ControlPanel
{
    protected $arrConfig = array(
			'title'=>'文章管理',
			'view'=>array(
				'template'=>'ArticleManage.html',
				'widget:paginator' => array(  //分页器bean
					'class' => 'paginator' ,
					'count' => 10, //每页10项
					'nums' => 5   //显示5个页码
				) ,
			),
			'perms' => array(
				// 权限类型的许可
				'perm.purview'=>array(
					'name' => 'purview:admin_category',
				) ,
			) ,
    ) ;
	
	public function process()
	{
		$this->checkPermissions('您没有这个分类的管理权限,无法继续浏览',array()) ;

		
		$categoryModel = Model::Create('opencms:category');
		
		$articlesModel = Model::Create('opencms:article')
		    ->belongsTo('opencms:category','cid','cid');
		
		if($this->params->get('cid'))
		{
		    $articlesModel->where("category.cid='{$this->params->get('cid')}'");
		}
		
		
		//准备分类信息
		$categoryModel->load();
		
		
		Category::buildTree($categoryModel);
		$this->view->variables ()->set ( 'aCatIter', $categoryModel );
		
		//搜索文章用的title模糊检索
		if($this->params->get('title'))
		{
		    $articlesModel->where("`title` like '%'. $this->params->get('title').'%'");
		}
		$articlesModel->load ();
		
// 		DB::singleton()->executeLog();
		
		$this->view->variables()->set('aArtIter',$articlesModel) ;
	}
}
