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
			        'view:CategoryList'=>array(
			                'template'=>'ArticleCategoryList.html',
			        ),
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
		    $categoryModel->load($this->params->get("cid"),'cid');
		    $categoryModel2 = $categoryModel->clone();
			if( $categoryModel->rowNum() === 0 ) {
				$this->messageQueue ()->create( Message::error, "无效的分类编号" );
				return;
			}
			
			$this->setTitle($categoryModel->data('title') . " - " . $this->title());

			//$articlesModel->where("`article`.`cid` ='{$this->params->get("cid")}'");
			$articlesModel->where("category.lft>={$categoryModel->lft} and category.lft<={$categoryModel->rgt} and category.rgt>={$categoryModel->lft} and category.rgt<={$categoryModel->rgt}");
			$articlesModel->load();
			
			//DB::singleton()->executeLog() ;
			$this->view()->setModel($articlesModel);


			$aParentsModelList = Category::getParents($categoryModel2);
			$arrModels = array();
			foreach($aParentsModelList as $aModel)
			{
			    $arrModels[] = $aParentsModelList->alone();
			}
			$arrModels[] = $aParentsModelList->alone();
			//显示上级分类
			$this->params()->set('arrParentCat' , $arrModels) ;
			
			//把cid传给frame
			$this->params()->set('cid',$this->params->get("cid"));
			
			//其他分类
			$categoryModel2 = clone $categoryModel;
			$categoryModel2->where("lft>{$categoryModel->lft} and lft<{$categoryModel->rgt} and rgt>{$categoryModel->lft} and rgt<{$categoryModel->rgt}");
			$categoryModel2->load();
			$this->view()->viewByName('CategoryList')->setModel($categoryModel2);
			
			
			//面包屑
			//$this->params()->set('aBreadcrumbNavigation' , Category::getParents($this->category)) ;
			
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定分类" );
		}
	}
}
