<?php
namespace org\opencomb\opencms\article ;

use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
				
				'view:article' => array(
					'template'=>'ArticleForm.html' ,
					'class' => 'form' ,
					
					'widgets' => array(
						
						'article-title' => array( 'config'=>'widget/article-title' ) ,
							
					) ,
					
				)
		) ;
	}
}

?>