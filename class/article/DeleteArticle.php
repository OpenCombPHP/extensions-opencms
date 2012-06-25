<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\mvc\model\Model;

use org\opencomb\platform\ext\Extension;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class DeleteArticle extends ControlPanel
{
	protected $arrConfig = array(
			'title'=>'删除文章',
			'view'=>array(
				'template'=>'DeleteArticle.html',
				'class'=>'view'
			),
	) ;	
	
	public function process()
	{
	    
	    $articlesModel = Model::Create('opencms:article') -> hasMany('opencms:attachment','aid','aid');
	    
	    
		//权限
		$this->requirePurview('purview:admin_category','opencms',$articlesModel->cid,'您没有这个分类的管理权限,无法继续浏览');
		
		
		
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
				$sSql.= $sValue;
			}
			$sSql.=  " )";
			
			$articlesModel->where($sSql);
			$articlesModel->load ();
			
			//删除附件
			$arrFilePaths = array();
			foreach($articlesModel['attachment'] as $aAttaModel)
			{
				$arrFilePaths[] = $aAttaModel['storepath'];
			}
			
			if ($articlesModel->delete ($sSql))
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
