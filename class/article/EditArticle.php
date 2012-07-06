<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\lang\Exception;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\fs\archive\DateAchiveStrategy;
use org\jecat\framework\fs\Folder;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class EditArticle extends ControlPanel
{
	protected $arrConfig = array(
	        
	        'title'=>'编辑文章',
	        'view'=>array(
				    'template'=>'opencms:ArticleForm.html',
	        ),
	) ;	
	
	public function process()
	{
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->view->widget('article_cat')->value(),'您没有这个分类的管理权限,无法继续浏览');
		
		
		$categoryModel = Model::Create('opencms:category');
		$articlesModel = Model::Create('opencms:article') -> hasMany('opencms:attachment','aid','aid');
		
		//为分类select添加option
		$aCatSelectWidget = $this->view->widget("article_cat");
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$categoryModel->load();
		
		Category::buildTree($categoryModel);
		
		foreach($categoryModel as $aCat)
		{
			$aCatSelectWidget->addOption($aCat['title'],$aCat['cid'],false);
		}
		
		//还原文章数据
		if($this->params->has("aid")){
			$articlesModel->load($this->params->get("aid"),"aid");
			
		    $this->view()->setModel($articlesModel);
			$this->view()->update();
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		$this->setTitle($articlesModel->title . " - " . $this->title());
		
		$this->view->variables()->set('page_h1',"编辑文章") ;
		$this->view->variables()->set('save_button',"保存修改") ;
		
		$this->doActions();
	}
	
	public function form()
	{
		//加载所有控件的值
		if (! $this->view->loadWidgets ( $this->params ))
		{
			return;
		}
		
		$articlesModel = $this->view()->model();
		
		/*已经存在的附件的处理*/
	
		if(!$this->params->has('article_exist_list') OR $this->params->get('article_exist_list') == null)
		{
			$arrExistFileList = array();
		}else{
			$arrExistFileList = $this->params->get('article_exist_list');
		}
	
		if(!$this->params->has('article_exist_file_delete') OR $this->params->get('article_exist_file_delete') == null)
		{
			$arrExistFileDelete = array();
		}else{
			$arrExistFileDelete = $this->params->get('article_exist_file_delete');
		}
	
		
		$arrFilesToDelete = array();
		foreach( $articlesModel['attachment'] as $aAttaModel)
		{
			//是否删除已有附件
			if( in_array( (string)$aAttaModel['index'] , $arrExistFileDelete ) )
			{
				$arrFilesToDelete[] = $aAttaModel['storepath'];
				$articlesModel->delete("fid = '{$aAttaModel['fid']}'",null,null,'attachment');
			}else{
				//是否显示在附件列表中
				if(in_array( (string)$aAttaModel['index'] , $arrExistFileList ))
				{
					$articlesModel->update(array('displayInList'=>'1') , "fid = '{$aAttaModel['fid']}'" , 'attachment');
				}else{
					$articlesModel->update(array('displayInList'=>'0') , "fid = '{$aAttaModel['fid']}'" , 'attachment');
				}
			}
		}
	
		/* end 已经存在的附件的处理*/
	
		/* 新附件的处理*/
		
		if($this->params->has('article_files'))
		{
			$arrArticleFiles = $this->params->get('article_files');
			$arrArticleFilesList = $this->params->get('article_list');
			if(!$arrArticleFilesList)
			{
				$arrArticleFilesList = array();
			}
			$aStoreFolder = Extension::flyweight('opencms')->FilesFolder();
			$aAchiveStrategy = DateAchiveStrategy::flyweight ( Array (true, true, true ) );
				
			
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
				$sSavedFile = $aStoreFolder->path() ;
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
	
				$arrIndexs = explode(',', $this->params->get('article_files_index'));
	
				$newAttachment = array(
		                'orginname'=>$sFileName,
		                'storepath'=>$sSavedFileRelativePath,
		                'size'=>$sFileSize,
		                'type'=>$sFileType,
		                'index'=>$arrIndexs[$nKey],
		                'aid'=>$articlesModel['aid'],
				);
				
				if(!in_array((string)( $arrIndexs[$nKey]), $arrArticleFilesList))
				{
					$newAttachment['displayInList'] = 0;
				}
		        
		        Model::create("opencms:attachment")->insert($newAttachment);
			}
		}
		/* end 新附件的处理*/
		$this->view()->setModel($articlesModel);
		$this->view()->fetch();
		
		$articlesModel->update (null,"aid='{$articlesModel['aid']}'");
		//删除用户要删除的已存在附件
		DeleteArticle::deleteAttachments($arrFilesToDelete);
		$this->messageQueue ()->create ( Message::success, "文章保存成功" );
		$this->location('?c=org.opencomb.opencms.article.ArticleManage');
	}
	
	public function getAttachmentUrl($aAttaModel)
	{
		return ArticleContent::getHttpUrl($aAttaModel['storepath']);
	}
	
	public function getAttachmentSize($aAttaModel)
	{
		return (string)($aAttaModel['size']/1000) . 'KB';
	}
	
	public function getIsDisplayInList($aAttaModel)
	{
		return $aAttaModel['displayInList']==1? 'checked':'';
	}
}
