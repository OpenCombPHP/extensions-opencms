<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class TopList extends Controller
{
	protected $arrConfig = array(
			'view'=>array(
				'template'=>'opencms:TopList.html',
				'cssClass'=>'jc-view-decorater-oc',
			),
	) ;	
	
	
	public function process()
	{
	    $categoryModel = Model::create('opencms:category');
	    
	    //遍历范围,仅第一层
	    if($this->params->has('subCat') and $this->params->get('subCat') == 1)
	    {
	        $articleModel = Model::create('opencms:article')
	        ->limit(20);
	    }else{  //遍历范围,所有层
	        $articleModel = Model::create('opencms:article')
		    ->hasOne('opencms:category','cid','cid')
	        ->limit(20);
	    }
	    
	    //排序,默认按照时间反序排列
	    if($this->params->has('order') and $this->params->get('order') == "asc"){
	        $this->setTitle("最热文章");
	        $articleModel->order('createTime',false);
	    }else{
	        $this->setTitle("最新文章");
	        $articleModel->order('createTime');
	        
	    }
	    
	    //排序
	    if($this->params->has("limit")){
	        $articleModel->limit($this->params->get("limit"));
	    }
	    
		if(!$this->params->has("cid")){
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
			return;
		}
		
		//准备分类信息
		if(!$categoryModel->load($this->params->get("cid"),'cid')){
			$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
		}
		$this->view->variables()->set('sCategoryTitle',$categoryModel->data('title')) ;
		$this->view->variables()->set('nCid',$this->params->get("cid")) ;
		
		//遍历范围,仅第一层
		if($this->params->has('subCat') and $this->params->get('subCat') == 1)
		{
			$articleModel->load($this->params->get('cid'),'cid') ;
		}
		
		//遍历范围,所有层
		else
		{
		    $articleModel->where("category.lft>='{$categoryModel->data('lft')}'");
		    $articleModel->where("category.lft<='{$categoryModel->data('rgt')}'");
		    $articleModel->where("category.rgt>='{$categoryModel->data('lft')}'");
		    $articleModel->where("category.rgt<='{$categoryModel->data('rgt')}'");
			$articleModel->load();
		}
		
		$arrTimes = array();
		foreach( $articleModel as $aChild){
			$arrTimes[] = $aChild['createTime'];
		}
		$this->view->variables()->set('arrTimes',$arrTimes) ;
	}
}
