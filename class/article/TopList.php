<?php
namespace org\opencomb\opencms\article;


use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class TopList extends Controller
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:article'=>array(
				'template'=>'TopList.html',
				'class'=>'view',
				'model'=>'articles',
			),
			'model:category'=>array(
				'orm'=>array(
					'columns' => array('title','lft','rgt') ,
					'table'=>'category',
				)
			),
		);
		
		//遍历范围,仅第一层
		if($this->params->has('subCat') and $this->params->get('subCat') == 1){
			$arrBean['model:articles'] = array(
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
				)
			);
		}else{  //遍历范围,所有层
			$arrBean['model:articles'] = array(
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'hasOne:category'=>array(
						'fromkeys'=>'cid',
						'tokeys'=>'cid',
						'columns' => array('title') ,
						'table'=>'category',
					) ,
				)
			); 
		}
		return $arrBean;
	}
	
	public function process()
	{
		if(!$this->params->has("cid")){
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
			return;
		}
		
		//准备分类信息
		if(!$this->modelCategory->load(array($this->params->get("cid")),array('cid'))){
			$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
		}
		$this->viewArticle->variables()->set('sCategoryTitle',$this->modelCategory->data('title')) ;
		$this->viewArticle->variables()->set('nCid',$this->params->get("cid")) ;
		
		$aWhere = clone $this->modelArticles->prototype()->criteria()->where();
		
		//遍历范围,仅第一层
		if($this->params->has('subCat') and $this->params->get('subCat') == 1){
			$aWhere->eq("cid",$this->params->get('cid'));
		}else{ //遍历范围,所有层
			$aWhere->ge("category.lft",$this->modelCategory->data('lft'));
			$aWhere->le("category.lft",$this->modelCategory->data('rgt'));
			$aWhere->ge("category.rgt",$this->modelCategory->data('lft'));
			$aWhere->le("category.rgt",$this->modelCategory->data('rgt'));
		}
		
		//排序依据(列)
		$sOrderBy = "creatTime";
		if($this->params->has('orderby')){
			$sOrderBy = $this->params->get('orderby');
		}
		
		//排序,默认按照时间反序排列
		$bOrder = true;
		$this->setTitle("最新文章");
		if($this->params->has('order') and $this->params->get('order') == "asc"){
			$bOrder = false;
			$this->setTitle("最热文章");
		}
		$this->modelArticles->prototype()->criteria()->addOrderBy($sOrderBy,$bOrder);
		
		//页面显示结果数,默认20
		if($this->params->has("limit")){
			$this->modelArticles->prototype()->criteria()->setLimit($this->params->get("limit"));
		}else{
			$this->modelArticles->prototype()->criteria()->setLimit(20);
		}
		
		$this->modelArticles->load($aWhere);
	}
}

?>