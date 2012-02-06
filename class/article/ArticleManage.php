<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

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
		return array(
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
	}
	
	public function process()
	{
		//准备分类信息
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		$this->viewArticle->variables ()->set ( 'aCatIter', $this->modelCategoryTree );
		
		$this->modelArticles->load ();
		$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
	}
}

?>