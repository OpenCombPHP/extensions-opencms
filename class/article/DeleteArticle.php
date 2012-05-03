<?php
namespace org\opencomb\opencms\article;

use org\opencomb\platform\ext\Extension;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class DeleteArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'删除文章',
			'view'=>array(
				'template'=>'DeleteArticle.html',
				'class'=>'view'
			),
			'model:article'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
					'limit'=>-1,
					'hasMany:attachments' => array (
							'fromkeys' => array ( 'aid',),
							'tokeys' => array ( 'aid', ),
							'table' => 'attachment',
					)
				)
			)
		);
	}
	
	public function process()
	{
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->article->cid,'您没有这个分类的管理权限,无法继续浏览');
		
		//要删除哪些项?把这些项数组一起删除,如果只有一项,也把也要保证它是数组
		if ($this->params->get ( "aid" ))
		{
			$arrAids = explode(',', $this->params->get ( "aid" ));
			$sSql = 'aid in ( ';
			foreach($arrAids as $nKey=>$sValue)
			{
				if($nKey)
				{
					$sSql.=',';
				}
				$sSql.= '@'.($nKey+1);
			}
			$sSql.=  " )";
			
			$this->article->loadSql ( $sSql , $arrAids);
			
			//删除附件
			$arrFilePaths = array();
			foreach($this->article->child('attachments') as $aAttaModel)
			{
				$arrFilePaths[] = $aAttaModel['storepath'];
			}
			
			if ($this->article->delete ())
			{
				$this->deleteAttachments($arrFilePaths);
				$this->messageQueue ()->create ( Message::success, "删除文章成功" );
			}
			else
			{
				$this->messageQueue ()->create ( Message::error, "删除文章失败" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		$this->location('/?c=org.opencomb.opencms.article.ArticleManage');
	}
	
	/**
	 * 批量附件删除
	 * 
	 * @param array $arrFilePaths 文件的相对路径数组,
	 * 
	 * @return boolean 如果有一个文件删除失败就返回false
	 */
	static public function deleteAttachments(array $arrFilePaths , $sExtension = 'opencms'){
		if(!$arrFilePaths)
		{
			return true;
		}
		$sStorePath = Extension::flyweight($sExtension)->FilesFolder()->path();
		$bSuccess = true;
		foreach($arrFilePaths as $sFilePath)
		{
			$bSuccess = $bSuccess && @unlink( $sStorePath . $sFilePath );
		}
		
		return $bSuccess;
	}
}
