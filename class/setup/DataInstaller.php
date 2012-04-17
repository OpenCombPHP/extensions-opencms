<?php
namespace org\opencomb\opencms\setup;

use org\jecat\framework\db\DB ;
use org\jecat\framework\message\Message;
use org\jecat\framework\message\MessageQueue;
use org\opencomb\platform\ext\Extension;
use org\opencomb\platform\ext\ExtensionMetainfo ;
use org\opencomb\platform\ext\IExtensionDataInstaller ;

class DataInstaller implements IExtensionDataInstaller
{
	public function install(MessageQueue $aMessageQueue,ExtensionMetainfo $aMetainfo)
	{
		$aExtension = new Extension($aMetainfo);
		
		// 1 . create data table
		
		DB::singleton()->execute( "CREATE TABLE IF NOT EXISTS `opencms_article` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `from` varchar(60) NOT NULL COMMENT '来源',
  `cid` int(8) NOT NULL,
  `title` varchar(120) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `createTime` int(10) NOT NULL,
  `author` int(10) NOT NULL,
  `views` int(8) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `recommend` int(2) NOT NULL COMMENT '推荐星级, 0-10',
  PRIMARY KEY (`aid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： %s',"opencms_article");
		
		
		DB::singleton()->execute( "CREATE TABLE IF NOT EXISTS `opencms_attachment` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `orginname` varchar(255) DEFAULT NULL,
  `storepath` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL COMMENT '单位字节',
  `type` varchar(30) DEFAULT NULL COMMENT '文件类型',
  `index` int(11) DEFAULT NULL COMMENT '次序',
  `displayInList` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示在文章尾部的附件列表中',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： %s',"opencms_attachment");
		
		
		DB::singleton()->execute( "CREATE TABLE IF NOT EXISTS `opencms_category` (
  `cid` int(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lft` int(9) NOT NULL,
  `rgt` int(9) NOT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `lft-rgt` (`lft`,`rgt`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： %s',"opencms_category");
		
		
		
		// 2. insert table data
		
		
		
		
		// 3. settings
		
		
		// 4. files
		
	}
}
