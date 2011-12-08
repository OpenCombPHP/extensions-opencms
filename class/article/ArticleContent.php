<?php
namespace org\opencomb\opencms\article;


use org\jecat\framework\db\DB;
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
				'config'=>'model/article'
			),
// 			'model:categoryTree'=>array(
// 				'config'=>'model/category'
// 			)
		);
	}
	
	public function process()
	{
		if($this->params->has("pid")){
			if(!$this->modelArticle->load(array($this->params->get("pid")),array('pid'))){
				$this->messageQueue ()->create ( Message::error, "错误的文章编号" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		$this->viewArticle->variables()->set('article',$this->modelArticle) ;
	}
}

?>