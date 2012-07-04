<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

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
	    
	    $type = $this->params->get('type');

	    //遍历范围,仅第一层
	    if($this->params->has('subCat_'.$type) and $this->params->get('subCat'.$type) == 1)
	    {
	        $articleModel = Model::create('opencms:article')
	        ->limit($this->params->get("limit_".$type));
	    }else{  //遍历范围,所有层
	        $articleModel = Model::create('opencms:article')
		    ->hasOne('opencms:category','cid','cid')
	        ->limit($this->params->get("limit_".$type));
	    }
	    
	    //排序,默认按照时间反序排列
	    if($this->params->has('order') and $this->params->get('order') == "asc"){
	        $this->setTitle("最热文章");
	        $articleModel->order($this->params->get('orderby'));
	    }else{
	        $this->setTitle("最新文章");
	        $articleModel->order($this->params->get('orderby'));
	        
	    }
	    
	    //排序
	    if($this->params->has("limit".$type)){
	        $articleModel->limit($this->params->get("limit_".$type));
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
			$articleModel->where("`article`.`cid` ='{$this->params->get("cid")}'");
			$articleModel->load();
		}
		
		$this->view()->setModel($articleModel);
		$arrTimes = array();
		foreach( $articleModel as $aChild){
			$arrTimes[] = $aChild['createTime'];
		}
		$this->view->variables()->set('arrTimes',$arrTimes) ;
	}
}
