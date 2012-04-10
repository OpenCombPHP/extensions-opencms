<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\fs\File;

use org\jecat\framework\fs\archive\DateAchiveStrategy;
use org\jecat\framework\fs\Folder;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\lang\Exception;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateArticle extends ControlPanel
{
	/**
	 * @example /校验器/字符长度校验器(Length):Bean格式演示[1]
	 * @forwiki /校验器/字符长度校验器(Length)
	 * @forwiki /MVC模式/视图/表单控件/文件上传(File)
	 *
	 * 字符长度校验器的bean配置数组的写法
	 */
	public function createBeanConfig()
	{
		return array(
			'title'=>'新建文章',
			'view:article'=>array(
				'template'=>'ArticleForm.html',
				'class'=>'form',
				'model'=>'article',
				'widgets'=>array(
					array(
						'id'=>'article_title',
						'class'=>'text',
						'title'=>'文章标题',
						'exchange'=>'title',
						'verifier:notempty'=>array(),
						'verifier:length'=>array(
								'min'=>2,
								'max'=>255)
					),
					array(
						'config'=>'widget/article_cat'
					),
					array(
						'config'=>'widget/article_content'
					),
						/*
					array(
						'id'=>'article_img',    //文件控件bean设置的例子
						'class'=>'file',
						'folder'=>Extension::flyweight('opencms')->filesFolder()->path(),  //取得扩展专用的文件保存路径,作为文件上传控件初始化的参数之一,这样控件就会知道应该把文件放在服务器的哪个文件夹下
						'title'=>'文章图片',
					)*/
				),
			),
			'model:article'=>array(
				'class'=>'model',
				'orm'=>array(
					'table'=>'article',
					'hasMany:attachments' => array (
						'fromkeys' => array ( 'aid',),
						'tokeys' => array ( 'aid', ),
						'table' => 'attachment',
					)
				),
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				),
			),
		);
	}
	
	public function process()
	{
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->viewArticle->widget('article_cat')->value(),'您没有这个分类的管理权限,无法继续浏览');
		
		//为分类select添加option
		$aCatSelectWidget = $this->viewArticle->widget("article_cat");
		
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$this->modelCategoryTree->prototype()->criteria()->setLimit(-1);
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		
		foreach($this->modelCategoryTree->childIterator() as $aCat)
		{
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid,false);
		}
		
		$this->viewArticle->variables()->set('page_h1',"新建文章") ;
		$this->viewArticle->variables()->set('save_button',"发布文章") ;
		
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
				
				//记录创建时间
				$this->modelArticle->setData('createTime',time());
				
				$this->viewArticle->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				
				/*           处理附件             */
				if($this->params->has('article_files'))
				{
					$arrArticleFiles = $this->params->get('article_files');
					$arrArticleFilesList = $this->params->get('article_list');
					$aStoreFolder = Extension::flyweight('opencms')->FilesFolder();
					$aAchiveStrategy = DateAchiveStrategy::flyweight ( Array (true, true, true ) );
					
					$aAttachmentsModel = $this->modelArticle->child('attachments');
					
					foreach($arrArticleFiles['name'] as $nKey=>$sFileName)
					{
						$sFileTempName = $arrArticleFiles['tmp_name'][$nKey];
						$sFileType = $arrArticleFiles['type'][$nKey];
						$sFileSize = $arrArticleFiles['size'][$nKey];
						//文件是否上传成功
						if( empty($sFileTempName) || empty($sFileType) || empty($sFileSize) )
						{
							continue;
						}
							
						//移动文件
						if (empty ( $aStoreFolder ))
						{
							throw new Exception ( "非法的路径属性,无法依赖此路径属性创建对应的文件夹对象" );
						}
							
						if (! $aStoreFolder->exists ())
						{
							$aStoreFolder = $aStoreFolder->create ();
						}
							
						// 保存文件
						$sSavedFile = $aAchiveStrategy->makeFilePath ( array(), $aStoreFolder );
						// 创建保存目录
						$aFolderOfSavedFile = new Folder( $sSavedFile ) ;
						if( ! $aFolderOfSavedFile->exists() ){
							if (! $aFolderOfSavedFile->create() )
							{
								throw new Exception ( __CLASS__ . "的" . __METHOD__ . "在创建路径\"%s\"时出错", array ($aFolderOfSavedFile->path () ) );
							}
						}
						$sSavedFile = $sSavedFile . $aAchiveStrategy->makeFilename ( array('tmp_name'=> $sFileTempName, 'name'=> $sFileName) ) ;
						
						//转换成相对路径
						if( strpos($sSavedFile , $aStoreFolder->path()) === 0 ){
							$sSavedFileRelativePath = substr($sSavedFile,strlen($aStoreFolder->path()));	
						}
						
						if(!move_uploaded_file($sFileTempName,$sSavedFile))
						{
							throw new Exception ( "上传文件失败,move_uploaded_file , 临时路径:" . $sFileTempName . ", 目的路径:" .$sSavedFile );
						}
						
						$aNewFileModel = $aAttachmentsModel->createChild();
						$aNewFileModel->setData('orginname' , $sFileName);
						$aNewFileModel->setData('storepath' , $sSavedFileRelativePath); //httpURL()
						$aNewFileModel->setData('size' , $sFileSize );
						$aNewFileModel->setData('type' , $sFileType );
						$aNewFileModel->setData('index' , $nKey+1 );
						if(!in_array((string)($nKey+1), $arrArticleFilesList))
						{
							$aNewFileModel->setData('displayInList' , 0);
						}
					}
				}
				/*           end 处理附件             */
				
				if ($this->modelArticle->save ())
				{
					$this->viewArticle->hideForm ();
					$this->messageQueue ()->create ( Message::success, "文章保存成功" );
				}
				else
				{
					$this->messageQueue ()->create ( Message::error, "文章保存失败" );
				}
			} while ( 0 );
		}
	}
}