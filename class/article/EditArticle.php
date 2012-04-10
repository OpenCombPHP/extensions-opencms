<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;

use org\jecat\framework\lang\Exception;

use org\opencomb\platform\ext\Extension;
use org\jecat\framework\fs\archive\DateAchiveStrategy;
use org\jecat\framework\fs\Folder;
use org\jecat\framework\mvc\model\db\Category;
use org\jecat\framework\mvc\view\DataExchanger;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class EditArticle extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'编辑文章',
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
				'class'=>'model',
				'orm'=>array(
					'table'=>'article',
					'hasMany:attachments' => array (
						'fromkeys' => array ( 'aid',),
						'tokeys' => array ( 'aid', ),
						'table' => 'attachment',
						'orderby' => 'index'
					)
				)
			),
			'model:categoryTree'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'category',
					'name'=>'category',
				)
			)
		);
	}
	
	public function process()
	{
		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->viewArticle->widget('article_cat')->value(),'您没有这个分类的管理权限,无法继续浏览');
		
		//为分类select添加option
		$aCatSelectWidget = $this->viewArticle->widget("article_cat");
		
		$aCatSelectWidget->addOption("文章分类...",null,true);
		
		$this->modelCategoryTree->load();
		
		Category::buildTree($this->modelCategoryTree);
		
		foreach($this->modelCategoryTree->childIterator() as $aCat)
		{
			$aCatSelectWidget->addOption(str_repeat("--", Category::depth($aCat)).$aCat->title,$aCat->cid,false);
		}
		
		//还原文章数据
		if($this->params->has("aid")){
			$this->modelArticle->load(array($this->params->get("aid")),array("aid"));
			$this->viewArticle->exchangeData ( DataExchanger::MODEL_TO_WIDGET);
		}else{
			$this->messageQueue ()->create ( Message::error, "未指定文章" );
		}
		
		$this->setTitle($this->modelArticle->title . " - " . $this->title());
		
		$this->viewArticle->variables()->set('page_h1',"编辑文章") ;
		$this->viewArticle->variables()->set('save_button',"保存修改") ;
		
		//如果是提交请求...
		if ($this->viewArticle->isSubmit ( $this->params ))
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
				
				$aAttaModelList = $this->modelArticle->child('attachments');
				$arrFilesToDelete = array();
				foreach( $aAttaModelList as $aAttaModel)
				{
					//是否删除已有附件
					if( in_array( (string)$aAttaModel['index'] , $arrExistFileDelete ) )
					{
						$aAttaModel->delete();
						$arrFilesToDelete[] = $aAttaModel['storepath'];
					}else{
						//是否显示在附件列表中
						if(in_array( (string)$aAttaModel['index'] , $arrExistFileList ))
						{
							$aAttaModel->setData('displayInList' , 1);
						}else{
							$aAttaModel->setData('displayInList' , 0);
						}
					}
				}
				
				/* end 已经存在的附件的处理*/
				
				/* 新附件的处理*/
				/* end 新附件的处理*/
				
				$this->viewArticle->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				$this->viewArticle->printStruct();
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
		}else{
			
		}
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