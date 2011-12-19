<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class ArticleContent extends Controller
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleContent.html',
				'class'=>'view',
				'model'=>'article',
			),
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article'
				)
			),
		);
	}
	
	public function process()
	{
		if($this->params->has("aid")){
			if(!$this->modelArticle->load(array($this->params->get("aid")),array('aid'))){
				$this->messageQueue ()->create ( Message::error, "错误的文章编号" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		//浏览次数
		$this->modelArticle->setData("views",(int)$this->modelArticle->data("views")+1);
		$this->modelArticle->save();
		
		$this->viewArticle->variables()->set('article',$this->modelArticle) ;
	}
}
?>