<?php
namespace org\opencomb\opencms\article;

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
		
		//还原附件信息
		$aAttaModelList = $this->modelArticle->child('attachments');
		if($aAttaModelList->childrenCount() > 0)
		{
			$sAttaListHtml = '';
			$sAttaMaxIndex = '';
			foreach($aAttaModelList as $aAttaModel)
			{
				$sAttaUrl = ArticleContent::getHttpUrl($aAttaModel['storepath']);
				$sAttaSize = (string)($aAttaModel['size']/1000) . 'KB';
				$sAttaDisplayInList = $aAttaModel['displayInList']==1? 'checked':'';
				$sAttaListHtml.="
					<div class='article_exist_file'>
						<a href='{$sAttaUrl}'>{$aAttaModel['orginname']}</a>
						{$sAttaSize}
						<a href='#' class='article_exist_files_into_content' index='{$aAttaModel['index']}' title='将附件插入到文档中,如果是图片就当作插图显示,如果是文件就插入链接'>插入到文章</a>
						<label><input name='article_exist_list[]' class='article_exist_list' type='checkbox' value='{$aAttaModel['index']}' {$sAttaDisplayInList}/>显示在附件列表</label>
						<label><input name='article_exist_file_delete[]' class='article_exist_files_delete' type='checkbox' value='{$aAttaModel['index']}'/>删除此附件</label>
					</div>
				";
				$sAttaMaxIndex = $aAttaModel['index']; 
			}
			//调整附件计数
			$sAttaMaxIndex = (int)$sAttaMaxIndex + 1;
			$sAttaListHtml.="
				<script>
					file_num = {$sAttaMaxIndex};
				</script>
			";
			
			$this->viewArticle->variables()->set('sAttaListHtml',$sAttaListHtml) ;
		}
		
		
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
				
				//权限
				$this->requirePurview('purview:admin_category','opencms',$this->viewArticle->widget('article_cat')->value(),'您没有这个分类的管理权限,无法继续浏览');
				
				/*已经存在的附件的处理*/
				$this->modelArticle->child('attachments')->printStruct();
				
				var_dump($this->params->get('article_exist_list'));
				
				var_dump($this->params->get('article_exist_file_delete'));
				
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
				foreach( $aAttaModelList->childIterator() as $aAttaModel)
				{
					//是否删除已有附件
					if( in_array( (string)$aAttaModel['index'] , $arrExistFileDelete ) )
					{
						$aAttaModelList->removeChild($aAttaModel);
						$arrFilesToDelete[] = $aAttaModel['storepath'];
////////
// $aAttaModelList->save();
////////
					}else{
						//是否显示在附件列表中
						if(in_array( (string)$aAttaModel['index'] , $arrExistFileList ))
						{
							$aAttaModel->setData('displayInList' , 1);
						}else{
							$aAttaModel->setData('displayInList' , 0);
						}
/////////
// 	$aAttaModel->save();
///////////
					}
				}
				
				/* end 已经存在的附件的处理*/
				
				$this->viewArticle->exchangeData ( DataExchanger::WIDGET_TO_MODEL );
				$this->modelArticle->printStruct();
				
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
}