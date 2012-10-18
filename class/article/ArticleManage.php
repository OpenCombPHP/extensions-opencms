<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ArticleManage extends ControlPanel
{
    protected $arrConfig = array(
			'title'=>'文章管理',
			'view'=>'ArticleManage.html',
			'perms' => array(
				// 权限类型的许可
				'perm.purview'=>array(
					'name' => 'purview:admin_category',
				) ,
			) ,
    ) ;
	
	public function process()
	{
		$categoryModel = Model::Create('opencms:category');
		
		$articlesModel = Model::Create('opencms:article')
		    ->belongsTo('opencms:category','cid','cid')
		    ->order('createTime');
		
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
		    $articlesModel->where("`article`.`title` like '%". $this->params()->get('title')."%'");
		}
		$this->view()->setModel($articlesModel);
		$articlesModel->load ();
		
		$this->view->variables()->set('aArtIter',$articlesModel) ;
	}
}
