<?php
namespace org\opencomb\opencms\setup;

use org\jecat\framework\db\DB ;
use org\jecat\framework\message\Message;
use org\jecat\framework\message\MessageQueue;
use org\opencomb\platform\ext\Extension;
use org\opencomb\platform\ext\ExtensionMetainfo ;
use org\opencomb\platform\ext\IExtensionDataInstaller ;
use org\jecat\framework\fs\Folder;

class DataInstaller implements IExtensionDataInstaller
{
	public function install(MessageQueue $aMessageQueue,ExtensionMetainfo $aMetainfo)
	{
		$aExtension = new Extension($aMetainfo);
		
		// 1 . create data table
		$aDB = DB::singleton();
		
		$aDB->execute( "CREATE TABLE IF NOT EXISTS `".$aDB->transTableName("opencms_article")."` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `from` varchar(60) NOT NULL DEFAULT '' COMMENT '来源',
  `cid` int(8) NOT NULL,
  `title` varchar(120) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `createTime` int(10) NOT NULL,
  `author` int(10) NOT NULL,
  `views` int(8) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `recommend` int(2) NOT NULL COMMENT '推荐星级, 0-10',
  `title_bold` tinyint(1) NOT NULL DEFAULT '0',
  `title_italic` tinyint(1) NOT NULL DEFAULT '0',
  `title_strikethrough` tinyint(1) NOT NULL DEFAULT '0',
  `title_color` varchar(20) NOT NULL DEFAULT '#09C',
  `url` varchar(255) NOT NULL COMMENT '外站链接',
  PRIMARY KEY (`aid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： `%s` 成功',$aDB->transTableName('opencms_article') );
		
		
		$aDB->execute( "CREATE TABLE IF NOT EXISTS `".$aDB->transTableName("opencms_attachment")."` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `orginname` varchar(255) DEFAULT NULL,
  `storepath` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL COMMENT '单位字节',
  `type` varchar(30) DEFAULT NULL COMMENT '文件类型',
  `index` int(11) DEFAULT NULL COMMENT '次序',
  `displayInList` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示在文章尾部的附件列表中',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： `%s` 成功',$aDB->transTableName('opencms_attachment') );
		
		
		$aDB->execute( "CREATE TABLE IF NOT EXISTS `".$aDB->transTableName("opencms_category")."` (
  `cid` int(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lft` int(9) NOT NULL,
  `rgt` int(9) NOT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `lft-rgt` (`lft`,`rgt`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8" );
		$aMessageQueue->create(Message::success,'新建数据表： `%s` 成功',$aDB->transTableName('opencms_category') );
		
		
		
		// 2. insert table data
		$nDataRows = 0 ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("116","","3","5555555555555","","<p>
	555555555555555[attachment 1][attachment 1][attachment 1][attachment 1][attachment 2]</p>
","0","0","8","0","0","1","1","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("115","","3","444444444455555555","","<p>
	444444444444444</p>
","0","0","8","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("122","","5","1111111111111111","","<p>
	111111111111111111111111111111</p>
","1341201555","0","8","0","0","0","0","","") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_article"),$nDataRows));
			
		$nDataRows = 0 ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("5","99",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("6","99",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("7","99",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("3","94","11","111","111","11","11","111") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("4","99",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("8","99",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("9","102",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("10","102",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("11","102",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("12","102",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("13","102",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("14","105",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("15","105",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("16","105",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("17","105",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("18","105",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("19","107",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("20","107",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("21","107",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("22","107",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("23","107",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("24","108",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("25","108",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("26","108",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("27","108",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("28","108",NULL,NULL,NULL,NULL,NULL,"1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("29","109","QQ截图20120503114222.jpg","/12/6/19/hasha743fe739f16f447ff8e7c9e34ceafa7.QQ截图20120503114222.jpg","133718","image/jpeg","1","0") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("30",NULL,"头像（小）.jpg","/12/6/20/hash9b1f9b2fd34cfbb0c27541d6d88dbc55.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("31",NULL,"头像（小）.jpg","/12/6/20/hash98b214e9dbb2f519d20e57498f5b1967.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("32",NULL,"头像（小）.jpg","/12/6/20/hashbe16dc69bcf943df48622cbeb7bafce2.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("33",NULL,"头像（小）.jpg","/12/6/20/hash6b7fb2f9c89e25081eb283482f85cc43.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("34",NULL,"头像（小）.jpg","/12/6/20/hash0c6be278e76eb91e1be955832aeabc7d.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("35",NULL,"头像（小）.jpg","/12/6/20/hash7d2c6cb06985cf1a9d8f975238b90a05.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("36",NULL,"头像（小）.jpg","/12/6/20/hash9599ec0b083eea6fadea5a3def480807.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("37",NULL,"头像（小）.jpg","/12/6/20/hashd5d8a4ac326709da12232fad281ebb29.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("109",NULL,"头像（小）.jpg","/12/6/20/hash262f5445abf33786a4057fb72ef1694b.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("110","109","头像（小）.jpg","/12/6/20/hash840fde736a88e2686e494185de066e76.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("115","122","头像（小）.jpg","/12/7/2/hashea169b32822d3b3d5e1697385a045bab.头像（小）.jpg","44715","image/jpeg","2","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("116","122","头像（小）.jpg","/12/7/2/hashb7131062195d710735c04662c0596e6d.头像（小）.jpg","44715","image/jpeg","3","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("117","116","头像（小）.jpg","/12/7/2/hashb0b5e9aadf90361315294e8d00c58b96.头像（小）.jpg","44715","image/jpeg","1","1") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("118","116","复件 头像（小）.jpg","/12/7/2/hash897e5cbb5bc1a89b91e773349695ce4e.复件 头像（小）.jpg","44715","image/jpeg","2","1") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_attachment"),$nDataRows));
			
		$nDataRows = 0 ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("29","其它","其它","133","134") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("2","设计作品","","143","144") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("3","公司新闻","公司新闻,行业动态,促销活动","123","142") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("5","国内","","126","135") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("6","国际","","136","139") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("7","体育","","127","128") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("8","军事","","124","125") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("28","首页","首页","121","122") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_category"),$nDataRows));
			
		
		
		// 3. settings
		
		$aSetting = $aExtension->setting() ;
			
				
		$aSetting->setItem('/index/toplist/','toplist',array (
  28 => 
  array (
    'index_new' => '1',
    'index_hot' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  3 => 
  array (
    'index_new' => '1',
    'index_hot' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  5 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  29 => 
  array (
    'index_hot' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
));
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/index/toplist/");
			
				
		$aSetting->setItem('/menu/mainmenu/','mainmenu',array (
  'item:2' => 
  array (
    'title' => '设计作品',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=2',
    'query' => 
    array (
      0 => 'cid=2',
      1 => 'cid=2',
    ),
  ),
  'item:3' => 
  array (
    'title' => '公司新闻',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=3',
    'query' => 
    array (
      0 => 'cid=3',
      1 => 'cid=3',
    ),
  ),
  'item:6' => 
  array (
    'title' => '国际',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=6',
    'query' => 
    array (
      0 => 'cid=6',
      1 => 'cid=6',
    ),
  ),
  'item:5' => 
  array (
    'title' => '国内',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=5',
    'query' => 
    array (
      0 => 'cid=5',
      1 => 'cid=5',
    ),
  ),
  'item:7' => 
  array (
    'title' => '体育',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=7',
    'query' => 
    array (
      0 => 'cid=7',
      1 => 'cid=7',
    ),
  ),
  'item:8' => 
  array (
    'title' => '军事',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=8',
    'query' => 
    array (
      0 => 'cid=8',
      1 => 'cid=8',
    ),
  ),
));
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/menu/mainmenu/");
			
		
		
		// 4. files
		
		$sFromPath = '/home/gaojun/www/oc.loader/extensions/opencms/0.2/data/public';
		$sDestPath = $aExtension ->filesFolder()->path();
		Folder::RecursiveCopy( $sFromPath , $sDestPath );
		$aMessageQueue->create(Message::success,'复制文件夹： `%s` to `%s`',array($sFromPath,$sDestPath));
		
	}
}
