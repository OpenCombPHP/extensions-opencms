<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\db\Category;

use org\opencomb\platform\ext\Extension;
use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\message\Message;

class ArticleContent extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view'=>array(
				'template'=>'ArticleContent.html',
				'class'=>'view',
				'model'=>'article',
			),
			'frame' => array('config'=>'opencms:article-frame') ,
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article',
					'hasMany:attachments' => array (
						'fromkeys' => array ( 'aid' ),
						'tokeys' => array ( 'aid' ),
						'table' => 'attachment',
						'orderby' => 'index'
					)
				)
			),
			'model:category'=>array(
					'orm'=>array(
							'columns' => array('cid','title','lft','rgt') ,
							'table'=>'opencms:category',
					)
			),
		);
	}
	
	public function process()
	{
		if($this->params->has("aid"))
		{
			if(!$this->article->load(array($this->params->get("aid")),array('aid')))
			{
				$this->messageQueue ()->create ( Message::error, "错误的文章编号" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		//浏览次数
		$this->article->setData( "views",(int)$this->article->data("views") + 1 );
		$this->article->save();
		
		$this->view->variables()->set('article',$this->article) ;
		
		$this->setTitle($this->article->title);
		
		//把cid传给frame
		$this->params()->set('cid',$this->article->cid);
		
		$this->category->load( $this->article->cid , 'cid');
		$aParentsModelList = Category::getParents($this->category);
		$arrModels = array();
		foreach($aParentsModelList as $aModel)
		{
			$arrModels[] = $aModel;
		}
		$arrModels[] = $this->category;
		
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
