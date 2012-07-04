<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\mvc\model\Category;

use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\message\Message;

class ArticleList extends Controller
{
	protected $arrConfig = array(
	        
			'title'=>'文章列表',

			'view'=>array(
				'template'=>'ArticleList.html',
				'widget:paginator' => array(
					'class' => 'paginator' ,
				) ,
			),
	) ;	
	
	public function createBeanConfig()
	{
		//显示下级分类的链接
		$arrBean['frame']['controller:subCat'] =  array(
				'class' => 'org\\opencomb\\opencms\\category\\SubCategory' ,
				'params' => array('cid'=>$this->params->get("cid"))
		);
		
		return $arrBean;
	}
	
	public function process()
	{
		if($this->params->has("cid")){

		    $categoryModel = Model::Create('opencms:category');
		    $articlesModel = Model::Create('opencms:article') -> hasOne('opencms:category','cid','cid');
		    
		    //页面显示结果数,默认20
		    if($this->params->get("limit"))
		    {
		        $articlesModel->limit($this->params->get("limit"));
		    }
		    
		    if($this->params->get('order') == 'asc')
		    {
		        $articlesModel->order('createTime',false);
		    }else{
		        $articlesModel->order('createTime',true);
		    }
		    
			//准备分类信息
			if(!$categoryModel->load($this->params->get("cid"),'cid')){
				$this->messageQueue ()->create ( Message::error, "无效的分类编号" );
			}
			
			$this->setTitle($categoryModel->data('title') . " - " . $this->title());

			
			$articlesModel->where("`article`.`cid` ='{$this->params->get("cid")}'");
			$articlesModel->load();
			
			
			//DB::singleton()->executeLog() ;
			$this->view()->setModel($articlesModel);
			
			//把cid传给frame
			$this->params()->set('cid',$this->params->get("cid"));
			
			//面包屑
			//$this->params()->set('aBreadcrumbNavigation' , Category::getParents($this->category)) ;
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
		}
	}
}
