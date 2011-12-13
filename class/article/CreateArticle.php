<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\model\db\Category;

use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:article'=>array(
				'template'=>'ArticleForm.html',
				'class'=>'form',
				'model'=>'article',
				'widgets'=>array(
					array(
						'config'=>'widget/article_title'
					),
					array(
						'config'=>'widget/article_cat'
					),
					array(
						'config'=>'widget/article_content'
					)
				)
			),
			'model:article'=>array(
				'config'=>'model/article'
			),
			'model:categoryTree'=>array(
				'config'=>'model/categoryTree'
			)
		);
	}
	
	public function process()
	{
		//为分类select添加option
		$aCatSelectWidget = $this->viewArticle->widget("article_cat");
		
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$aCatIter = Category::loadTotalCategory($this->modelCategoryTree->prototype()) ;
		
		Category::buildTree($aCatIter);
		
		foreach($aCatIter as $aCat)
		{
			$aCatSelectWidget->addOption(str_repeat("&nbsp;&nbsp;", $aCat->depth()).$aCat->title,$aCat->cid,false);
		}
		
		//如果是提交请求...
		if ($this->viewArticle->isSubmit ( $this->params )) //前面定义了名为article的视图,之后就可以用$this->viewArticle来取得这个视图.控制器把视图当作自己的成员来管理,通过"viewArticle","viewarticle","article"这3种成员变量名都可以访问到这个view,推荐第一种
		{
			do
			{
				//加载所有控件的值
				$this->viewArticle->loadWidgets ( $this->params );
				//校验所有控件的值
				if (! $this->viewArticle->verifyWidgets ())
				{
					break;
				}
// 				var_dump($this->viewArticle->widget("article_cat")->value());exit;
				//检查
				// 				if(!$this->params->has("article_title") || strlen($this->params->get("article_title")) == 0){
				//					//把错误信息发送到这个控制器的消息队列中
				// 					$this->messageQueue()->create( Message::error, "文章标题不能为空" );
				// 				}
				// 				if(!$this->params->has("article_cat") || strlen($this->viewArticle->widget("article_cat")->value()) == 0){
				// 					$this->messageQueue()->create( Message::error, "文章分类不能为空" );
				// 				}
				// 				if(!$this->params->has("article_content") || strlen($this->article_content->value()) == 0){
				// 					$this->messageQueue()->create( Message::error, "文章内容不能为空" );
				// 				}
				
// 				$this->viewArticle->variables()->set('aArtIter',$this->modelArticles->childIterator()) ;
				
				
				//记录创建时间
				$this->modelArticle->setData('post.createTime',time());
				
				$this->viewArticle->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				
				if ($this->modelArticle->save ())
				{
// 					DB::singleton()->executeLog();
					$this->viewArticle->hideForm ();
					$this->messageQueue ()->create ( Message::success, "文章保存成功" );
				}
				else
				{
					$this->messageQueue ()->create ( Message::error, "文章保存失败" );
				}
			} while ( 0 );
		}
	
		// 		if ($this->viewAddPhoto->isSubmit ( $this->aParams )) {
	// 			do {
	// 				$this->viewAddPhoto->loadWidgets ( $this->aParams );
	// 				if (! $this->viewAddPhoto->verifyWidgets ()) {
	// 					$this->photoupdate->setValue(null);
	// 					break ;
	// 				}
	

	// 				//是否有目标相册的所有权
	// 				$aSelectAlbumModel = $this->modelAlbum->findChildBy($this->aParams->get('photoalbum'));
	// 				if( $this->nUid != $aSelectAlbumModel['uid'] )
	// 				{
	// 					$this->permissionDenied('没有权限',array()) ;
	// 				}
	

	// 				$this->viewAddPhoto->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
	// 				try{
	

	// 					//如果是在新建照片,就带上一个创建时间
	// 					if(!$this->aParams->has('pid')){
	// 						$this->modelPhoto->createTime = time() ;
	// 					}
	

	// 					//如果已经登录,就把当前的uid录入到uid字段,但事实上,编辑表单是需要权限的,所以在权限做好以后应该省略判断
	// 					if( IdManager::singleton()->currentId() && $uidFromSession = IdManager::singleton()->currentId()->userId() ){
	// 						$this->modelPhoto->uid = $uidFromSession;
	// 					}
	

	// 					//记录文件大小
	// 					if($this->aParams->has('photoupdate')){
	// 						if(($aFile = $this->viewAddPhoto->widget('photoupdate')->value()) != null){
	// 							$this->modelPhoto->bytes = $aFile->length();
	// 						}else{
	// 							$this->messageQueue()->create( Message::error, "照片提交失败" );
	// 							return ;
	// 						}
	// 					}
	

	// 					if($this->modelPhoto->save()){
	

	// 						//更新相册总大小
	// 						$aSelectAlbumModel->bytes = $aSelectAlbumModel->bytes + $this->modelPhoto->bytes;
	// 						if(!$aSelectAlbumModel->save()){
	// 							throw new Exception('更新相册总大小失败!');
	// 						}
	

	// 						$this->viewAddPhoto->hideForm();
	// 						$this->messageQueue()->create( Message::success, "照片提交完成" );
	// 					}else{
	// 						$this->messageQueue()->create( Message::error, "照片提交失败" );
	// 					}
	// 				}catch (Exception $e){
	// 					$this->messageQueue()->create( Message::error, "照片提交失败" );
	// 				}
	

	// 			} while ( 0 );
	// 		}
	// 		else {
	// 		}
	}
}

?>