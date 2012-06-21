<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Category;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\Model;

use org\opencomb\platform\ext\Extension;
use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\message\Message;

class ArticleContent extends Controller
{
	protected $arrConfig = array(
	        'title'=> '文章内容',
	        'view'=>array(
	                'template'=>'ArticleContent.html',
	        ),
	       'frame' => array('config'=>'opencms:article-frame') ,
	) ;	
	
	public function process()
	{
	    $articleModel = Model::create('opencms:article')
	    ->hasMany('opencms:attachment','aid','aid')
	    ->order('attachment.index');
	    
		if($this->params->has("aid"))
		{
			if(!$articleModel->load($this->params->get("aid"),"aid"))
			{
				$this->messageQueue ()->create ( Message::error, "错误的文章编号" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		
		//浏览次数
		$articleModel->update(array(
		        'views'=>(int)$articleModel->data('views') + 1
        ));
		
		$this->setTitle($articleModel->data('title'));
		
		//把cid传给frame
		$this->params()->set('cid',$articleModel->data('cid'));
		
		$this->view()->setModel($articleModel);
		
		
		$categoryModel = Model::create('opencms:category');
		if( $categoryModel->load( $articleModel->data('cid') , 'cid')->rowNum() == 0 ){
			$this->messageQueue ()->create ( Message::error, "错误的栏目编号" );
			return;
		}
		
		$aParentsModelList = Category::getParents($categoryModel);
		$arrModels = array();
		foreach($aParentsModelList as $aModel)
		{
		    $arrModels[] = $aParentsModelList->alone();
		}
		$arrModels[] = $aParentsModelList->alone();
		
		//面包屑
		$this->params()->set('aBreadcrumbNavigation' , $arrModels) ;
		
		
	}
	
	static public function getHttpUrl($sFilePath)
	{
		return Extension::flyweight('opencms')->FilesFolder()->httpUrl() . $sFilePath;
	}
	
	static public function getContentWithAttachmentUrl( $sContent , $aAttachmentModel )
	{
		foreach($aAttachmentModel as $aModel)
		{
			$sReplace = '';
			//如果是图片就直接显示图片
			if(strpos( $aModel['type'] , 'image' ) !== false)
			{
				$sReplace = '<img src="' . self::getHttpUrl($aModel['storepath']) . '"/>';
			}else{//不是图片就显示超链接
				$sReplace = '<a href="' . self::getHttpUrl($aModel['storepath']) . '">' . $aModel['orginname'] . '</a>';
			}
			$sContent = str_replace("[attachment {$aModel['index']}]", $sReplace, $sContent);
		}
		return $sContent;
	}
}
