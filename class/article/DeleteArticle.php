<?php
namespace org\opencomb\opencms\article;

use org\opencomb\platform\ext\Extension;

use org\jecat\framework\mvc\model\db\Article;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class DeleteArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'删除文章',
			'view:article'=>array(
				'template'=>'DeleteArticle.html',
				'class'=>'view'
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
				)
			)
		);
	}
	
	public function process()
	{
		
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->modelArticle->cid,'您没有这个分类的管理权限,无法继续浏览');
		
		//要删除哪些项?把这些项数组一起删除,如果只有一项,也把也要保证它是数组
		if ($this->params->has ( "aid" ))
		{
			$arrToDelete = is_array ( $this->params->get ( "aid" ) ) ? $this->params->get ( "aid" ) : ( array ) $this->params->get ( "aid" );
			$this->modelArticle->prototype ()->criteria ()->where ()->in ( "aid", $arrToDelete );
			$this->modelArticle->load ();
			
			if ($this->modelArticle->delete ())
			{
				$this->messageQueue ()->create ( Message::success, "删除文章成功" );
			}
			else
			{
				$this->messageQueue ()->create ( Message::error, "删除文章失败" );
			}
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
	}
	
	/**
	 * 批量附件删除
	 * 
	 * @param array $arrFilePaths 文件的相对路径数组,
	 */
	static public function deleteAttachments(array $arrFilePaths){
		$aStoreFolder = Extension::flyweight('opencms')->FilesFolder();
		var_dump($aStoreFolder->path());
	}
}