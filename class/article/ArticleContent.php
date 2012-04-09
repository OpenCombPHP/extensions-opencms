<?php
namespace org\opencomb\opencms\article;

use org\opencomb\coresystem\mvc\controller\Controller;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\message\Message;

class ArticleContent extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:article'=>array(
				'template'=>'ArticleContent.html',
				'class'=>'view',
				'model'=>'article',
			),
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article',
					'hasMany:attachments' => array (
						'fromkeys' => array ( 'aid' ),
						'tokeys' => array ( 'aid' ),
						'table' => 'attachment',
					)
				)
			),
		);
	}
	
	public function process()
	{
		if($this->params->has("aid"))
		{
			if(!$this->modelArticle->load(array($this->params->get("aid")),array('aid')))
			{
				$this->messageQueue ()->create ( Message::error, "错误的文章编号" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		//浏览次数
		$this->modelArticle->setData( "views",(int)$this->modelArticle->data("views") + 1 );
		$this->modelArticle->save();
		
		$this->viewArticle->variables()->set('article',$this->modelArticle) ;
		
		$this->setTitle($this->modelArticle->title);
		
		//把cid传给frame
		$this->frame()->params()->set('cid',$this->modelArticle->cid);
	}
	
	public function defaultFrameConfig()
	{
		return array('class'=>'org\\opencomb\\opencms\\frame\\ArticleFrontFrame') ;
	}
}