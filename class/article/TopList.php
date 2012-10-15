<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\db\DB;
use org\jecat\framework\mvc\model\Model;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class TopList extends Controller
{
	protected $arrConfig = array(
			'view'=>array(
				'template'=>'opencms:TopList.html',
			),
	) ;	
	
	
	public function process()
	{
	    $categoryModel = Model::create('opencms:category');
	    
	    $type = $this->params->get('type');

	    //遍历范围,仅第一层
	    $articleModel = Model::create('opencms:article')
	        ->limit($this->params->get("limit_".$type));
	    
	    //排序,默认按照时间反序排列
	    if($this->params->has('orderby')){
	        $this->setTitle("最热文章");
	        $articleModel->order($this->params->get('orderby'));
	    }else{
	        $this->setTitle("最新文章");
	        $articleModel->order('createTime');
	        
	    }
	    
	    //limit
	    if($this->params->has("limit_".$type)){
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
		if($this->params->has('subCat_'.$type) and $this->params->get('subCat_'.$type) == 1)
		{
			$aCatChildren = Category::getChildren($categoryModel);
			if($aCatChildren->rowNum() > 0){
				$arrCatChildren = array();
				foreach($aCatChildren as $aChild){
					$arrCatChildren[] = $aChild['cid'];
				}
				$arrCatChildren[] = $this->params->get("cid");
				$sWhere = "`article`.`cid` IN (" . implode(',', $arrCatChildren) . ")";
			}else{
				$sWhere = "`article`.`cid` = '" . $categoryModel['cid'] . "'";
			}

			$articleModel->where( $sWhere );
			$articleModel->load();
		}
		//遍历范围,所有层
		else
		{
			$articleModel->load($this->params->get('cid'),'cid') ;
		}
		
		$this->view()->setModel($articleModel);
		$arrTimes = array();
		foreach( $articleModel as $aChild){
			$arrTimes[] = $aChild['createTime'];
		}
		$this->view->variables()->set('arrTimes',$arrTimes) ;
	}
}
