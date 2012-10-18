<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Model;

use org\jecat\framework\fs\archive\DateAchiveStrategy;
use org\jecat\framework\fs\Folder;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\lang\Exception;
use org\jecat\framework\mvc\model\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class CreateArticle extends ControlPanel
{
	protected $arrConfig = array(
	        'title'=>'新建文章',
	        'view'=>array(
	                'template'=>'ArticleForm.html',
	        ),
	) ;	
	
	public function process()
	{
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->view->widget('article_cat')->value(),'您没有这个分类的管理权限,无法继续浏览');
		
		
		$categoryModel = Model::Create('opencms:category');
		
		//为分类select添加option
		$aCatSelectWidget = $this->view->widget("article_cat");
		
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$categoryModel->load();
		
		Category::buildTree($categoryModel);
		
		foreach($categoryModel as $aCat)
		{
			$aCatSelectWidget->addOption($aCat['title'],$aCat['cid'],false);
		}
		
		$this->view->variables()->set('page_h1',"新建文章") ;
		$this->view->variables()->set('save_button',"发布文章") ;
		
		$this->doActions();
	}
	
	public function form()
	{
		//加载所有控件的值
		if (! $this->view->loadWidgets ( $this->params ))
		{
			return;
		}
		
		$articlesModel = Model::Create('opencms:article')
		->hasMany('opencms:attachment','aid','aid');
		
		//记录创建时间
		$newArticles['createTime'] = time();

		/*           处理附件             */
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

				$newAttachment['attachment'][] = 
				        array(
				                'attachment.orginname'=>$sFileName,
				                'attachment.storepath'=>$sSavedFileRelativePath,
				                'attachment.size'=>$sFileSize,
				                'attachment.type'=>$sFileType,
				                'attachment.index'=>$arrIndexs[$nKey],
		                );
				
				if(!in_array((string)( $arrIndexs[$nKey]), $arrArticleFilesList))
				{
				    $newAttachment['displayInList'] = 0;
				}
		        //$articlesModel->setData('attachment', $newAttachment);
			}
			
			
		}
		/*           end 处理附件             */
		
		$newAttachment['createTime'] = time();
		$newAttachment['from '] = $this->params->get('article_title');
		$newAttachment['cid'] = $this->params->get('article_cat');
		$newAttachment['title'] = $this->params->get('article_title');
		$newAttachment['summary'] = $this->params->get('article_content');
		$newAttachment['text'] = $this->params->get('article_content');
		$newAttachment['author'] = 0;//$this->params->get('article_author');
		$newAttachment['title_bold'] = $this->params->get('article_title_bold');
		$newAttachment['title_italic'] = $this->params->get('article_title_italic');
		$newAttachment['title_strikethrough'] = $this->params->get('article_title_strikethrough');
		$newAttachment['title_color'] = $this->params->get('article_title_color');
		$newAttachment['url'] = $this->params->get('article_url');
		//$articlesModel->setData("createTime", time());
		//$this->view()->setModel($articlesModel);
		//$this->view()->fetch();
		
		if ($articlesModel->insert($newAttachment))
		{
			$this->messageQueue ()->create ( Message::success, "文章保存成功" );
			$this->location('?c=org.opencomb.opencms.article.ArticleManage');
		}
		else
		{
			$this->messageQueue ()->create ( Message::error, "文章保存失败" );
		}
	}


	public function resizeImage($sFileTempName , $maxwidth , $maxheight = null)
	{
	    $arrImgInfo = \getImageSize($sFileTempName);
		$pic_width = $arrImgInfo[0];
		$pic_height = $arrImgInfo[1];
		$filetype = $arrImgInfo['mime'];

	    if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
	    {
	        if($maxwidth && $pic_width>$maxwidth)
	        {
	            $widthratio = $maxwidth/$pic_width;
	            $resizewidth_tag = true;
	        }
	        $resizeheight_tag = false;
	        if($maxheight && $pic_height>$maxheight)
	        {
	            $heightratio = $maxheight/$pic_height;
	            $resizeheight_tag = true;
	        }
	        if($resizewidth_tag && $resizeheight_tag)
	        {
	            if($widthratio<$heightratio)
	                $ratio = $widthratio;
	            else
	                $ratio = $heightratio;
	        }
	        if($resizewidth_tag && !$resizeheight_tag)
	            $ratio = $widthratio;
	        if($resizeheight_tag && !$resizewidth_tag)
	            $ratio = $heightratio;
	        $newwidth = $pic_width * $ratio;
	        $newheight = $pic_height * $ratio;


	        $file = null;
	        if( $filetype == 'image/jpeg'){
	        	$file = imagecreatefromjpeg($sFileTempName);
	        }elseif($filetype == 'image/gif'){
	        	$file = imagecreatefromgif($sFileTempName);
	        }elseif($filetype == 'image/png'){
	        	$file = imagecreatefrompng($sileTempName);
	        }

	        // if(function_exists("imagecopyresampled"))
	        // {
	            $newim = imagecreatetruecolor($newwidth,$newheight);
	           imagecopyresampled($newim,$file,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
	        // }
	        // else
	        // {
	        //     $newim = imagecreate($newwidth,$newheight);
	        //    imagecopyresized($newim,$file,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
	        // }

	        unlink($sFileTempName);

	        if( $filetype == 'image/jpeg'){
	        	imagejpeg($newim,$sFileTempName);
	        }elseif($filetype == 'image/gif'){
	        	imagegif($newim,$sFileTempName);
	        }elseif($filetype == 'image/png'){
	        	imagepng($newim,$sFileTempName);
	        }
	        imagedestroy($newim);
	    }
	    else
	    {
	        //do nothing
	    }           
	}
	
}
