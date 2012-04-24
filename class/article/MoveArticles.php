<?php
namespace org\opencomb\opencms\article;

use org\jecat\framework\db\DB;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class MoveArticles extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'转移文章',
			'view'=>array(
				'template'=>'MoveArticles.html',
				'class'=>'view',
				'model'=>'articles',
			),
			'model:articles'=>array(
				'class'=>'model',
				'list'=>true,
				'orm'=>array(
					'table'=>'article',
				)
			)
		);
	}
	
	public function process()
	{

		//权限
		$this->requirePurview('purview:admin_category','opencms',$this->params->get('from'),'您没有这个分类的管理权限,无法继续浏览');
		$this->requirePurview('purview:admin_category','opencms',$this->params->get('to'),'您没有这个分类的管理权限,无法继续浏览');
		
		if(!$this->params->has('from') || !$this->params->has('to')){
			$this->messageQueue ()->create ( Message::error, "提供的参数不完整" );
			return;
		}
			
		$arrFromCategorys = explode('_', $this->params->get('from'));
		$nToCategory = (int)$this->params->get('to');
		
		if(DB::singleton()->execute("UPDATE `opencms_article` SET  `cid` = '{$nToCategory}' WHERE `cid` in (" . implode(',', $arrFromCategorys) . ");")){
			$this->messageQueue ()->create ( Message::success, "成功转移了文章" );
		}else{
			$this->messageQueue ()->create ( Message::error, "没有转移任何文章,可能是因为没有找到文章或者目标栏目不存在" );
			return;
		}
	}
}
