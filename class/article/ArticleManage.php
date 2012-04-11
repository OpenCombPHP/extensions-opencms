<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\auth\IdManager;

use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ArticleManage extends ControlPanel
{
	/**
	 * @example /MVC模式/视图/控件/分页器(Paginator)
	 * @forwiki /MVC模式/视图/控件/分页器(Paginator)
	 *
	 * 分页器bean配置方法
	 */
	public function createBeanConfig()
	{
		$arrBean = array(
			'title'=>'文章管理',
			'view:article'=>array(
				'template'=>'ArticleManage.html',
				'class'=>'view',
				'model'=>'articles',
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
			'model:articles'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'name'=>'article',
					'belongsTo:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'table'=>'category',
						'name'=>'category',
// 						'where'=>array("category.cid=@1",$this->params->get('cid'))
					)
				)
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'limit'=>-1,
					'table'=>'category',
					'name'=>'category',
				)
			)
		);
		
		if($this->params->get('cid'))
		{
			$arrBean['model:articles']['orm']['where'] = array("category.cid=@1",$this->params->get('cid'));
		}
		
// 		var_dump($arrBean);
		return $arrBean;
	}
	
	public function process()
	{
		$this->checkPermissions('您没有这个分类的管理权限,无法继续浏览',array()) ;
		
		//准备分类信息
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		$this->viewArticle->variables ()->set ( 'aCatIter', $this->modelCategoryTree );
		
		//搜索文章用的title模糊检索
		if($this->params->get('title'))
		{
			$this->modelArticles->loadSql("`title` like @1", '%'. $this->params->get('title').'%' );
		}else{
			$this->modelArticles->load ();
		}
		
// 		DB::singleton()->executeLog();
		
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
	}
}