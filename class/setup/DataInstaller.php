<?php
namespace org\opencomb\opencms\setup;

use org\jecat\framework\db\DB ;
use org\jecat\framework\message\Message;
use org\jecat\framework\message\MessageQueue;
use org\opencomb\platform\ext\Extension;
use org\opencomb\platform\ext\ExtensionMetainfo ;
use org\opencomb\platform\ext\IExtensionDataInstaller ;
use org\jecat\framework\fs\Folder;

// 这个 DataInstaller 程序是由扩展 development-toolkit 的 create data installer 模块自动生成
// 扩展 development-toolkit 版本：0.2.0.0
// create data installer 模块版本：1.0.8.0

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
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=utf8" );
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8" );
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
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("97","","3","Ubuntu桌面指南12.04版发布","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	Ubuntu 桌面指南是继 Ubuntu 桌面培训[1]发布之后的又一部官方中文文档，旨在为家庭和办公室等桌面用户的日常应用提供详尽、实用的帮助和指引，是最主要的系统附带文档之一。<br style=\"margin: 0px; padding: 0px; \" />
	中文版许可证为 Creative Commons 3.0 Attribution No-Commercial Share-Alike[2]</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	项目主页及 HTML 在线浏览：&nbsp;<a href=\"http://people.ubuntu.com/~happyaron/ubuntu-docs/\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://people.ubuntu.com/~happyaron/ubuntu-docs/</a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	本文档的英文原文由 Ubuntu 文档小组维护，中文译文由 Aron Xu、Eleanor Chen、Carlos Gong 和<br style=\"margin: 0px; padding: 0px; \" />
	YunQiang Su 提供。中文版后续的维护工作由 Ubuntu 简体中文小组进行。在此特别感谢 Canonical<span style=\"margin: 0px; padding: 0px; font-size: x-small; \">&nbsp;</span>有限公司为翻译工作提供的支持。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	中文版文档将在 12.04 LTS 下次中文语言包更新时推送到各位用户的系统之中。但由于系统更新的限制，文档的堪误、修订都将优先体现在上述地址。如有疑问，请前往 Ubuntu 中文论坛上的置顶贴[3]。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	附：<br style=\"margin: 0px; padding: 0px; \" />
	[1]<a href=\"http://people.ubuntu.com/~happyaron/udc-cn/\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://people.ubuntu.com/~happyaron/udc-cn/</a><br style=\"margin: 0px; padding: 0px; \" />
	[2]<a href=\"http://people.ubuntu.com/~happyaron/ubuntu-docs/legal.html\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://people.ubuntu.com/~happyaron/ubuntu-docs/legal.html</a><br style=\"margin: 0px; padding: 0px; \" />
	[3]<a href=\"http://forum.ubuntu.org.cn/viewtopic.php?f=120&amp;t=373407\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://forum.ubuntu.org.cn/viewtopic.php?f=120&amp;t=373407</a><br style=\"margin: 0px; padding: 0px; \" />
	[4]<a href=\"http://forum.ubuntu.org.cn/viewtopic.php?f=1&amp;t=267605\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://forum.ubuntu.org.cn/viewtopic.php?f=1&amp;t=267605</a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76);\">
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76);\">
	[attachment 1]</p>
","1339037459","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("2","","6","日本海啸“鬼船”","","<p>
	&nbsp;</p>
<div>
	　　4月5日，美国海岸防卫队将去年日本海啸后漂流到阿拉斯加的一艘日本渔船击沉，结束它在太平洋上长达一年多的孤独旅程。美国海岸警卫队解释说，此举主要是为了避免这艘无人控制的渔船危害其他船只的航行安全。</div>
<div>
	　　这艘被形容为&ldquo;鬼船&rdquo;的日本渔船，原本停泊在日本青森县海港，在去年3月的大地震后被海啸卷走，在海上漂流长达一年之久，它竟然横渡太平洋，漂流到北美洲附近的海域。上个月加拿大方面发现该船的踪迹。</div>
","1333701817","0","74","0","0","0","0","#09C","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("94","","3","Fedora17发布","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	Fedora 17 正式发布，主要新特性如下：</p>
<ol style=\"margin: 0px; padding: 10px 0px 10px 25px; list-style-position: inside; list-style-image: initial; color: rgb(76, 76, 76); font-family: Arial, sans-serif; font-size: 14px; \">
	<li style=\"margin: 0px; padding: 2px 0px; \">
		搭载了最新的 GNOME 3.4 和 KDE 4.8 桌面环境。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		集成了 OpenStack, Eucalyptus 和 Open Nebula</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		GIMP 2.8 。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		更多的虚拟化技术改进。</li>
</ol>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	更多详情见<a href=\"http://docs.fedoraproject.org/en-US/Fedora/17/html/Release_Notes/index.html\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \" target=\"_blank\">官方通告</a>。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	# ISO 下载：&nbsp;<a href=\"http://fedoraproject.org/zh_CN/get-fedora-options\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://fedoraproject.org/zh_CN/get-fedora-options</a></p>
","1337846195","0","74","0","0","0","0","#09C","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("92","","3","Linux Mint 13 Maya发布","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	开发代号为 Maya ，基于&nbsp;Ubuntu 12.04 的&nbsp;Linux Mint 13 长期支持版正式发布，它提供了两个版本:</p>
<ol style=\"margin: 0px; padding: 10px 0px 10px 25px; list-style-position: inside; list-style-image: initial; color: rgb(76, 76, 76); font-family: Arial, sans-serif; font-size: 14px; \">
	<li style=\"margin: 0px; padding: 2px 0px; \">
		Linux Mint 13 MATE edition: 搭载了 MATE 用户界面（GNOME 2.3.x 的分支）。<br style=\"margin: 0px; padding: 0px; \" />
		<a href=\"http://wowubuntu.com/wp-content/uploads/2012/05/linux-mint-13-mate-1.png\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \"><img alt=\"\" class=\"alignnone  wp-image-6326\" height=\"214\" src=\"http://wowubuntu.com/wp-content/uploads/2012/05/linux-mint-13-mate-1-1024x595.png\" style=\"margin: 0px; padding: 0px; border: none; text-align: center; max-width: 100%; \" title=\"linux-mint-13-mate (1)\" width=\"368\" /></a></li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		Linux Mint 13 Cinnamon edition: 搭载了&nbsp;Cinnamon 用户界面 （GNOME Shell 的分支）。<br style=\"margin: 0px; padding: 0px; \" />
		<a href=\"http://wowubuntu.com/wp-content/uploads/2012/05/linux-mint-13-cinnamon.png\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \"><img alt=\"\" class=\"alignnone  wp-image-6327\" height=\"224\" src=\"http://wowubuntu.com/wp-content/uploads/2012/05/linux-mint-13-cinnamon-1024x622.png\" style=\"margin: 0px; padding: 0px; border: none; text-align: center; max-width: 100%; \" title=\"linux-mint-13-cinnamon\" width=\"368\" /></a></li>
</ol>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	更多详情见<a href=\"http://www.linuxmint.com/rel_maya_whatsnew.php\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \" target=\"_blank\">官方通告</a>。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	下载：<a href=\"http://www.linuxmint.com/download.php\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \" target=\"_blank\">http://www.linuxmint.com/download.php</a></p>
","1337846166","0","74","0","0","0","0","#09C","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("93","","3","GIMP 2.8 发布","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	著名的开源图像处理软件 GIMP 发布 2.8 正式版，主要新特性如下：</p>
<ol style=\"margin: 0px; padding: 10px 0px 10px 25px; list-style-position: inside; list-style-image: initial; color: rgb(76, 76, 76); font-family: Arial, sans-serif; font-size: 14px; \">
	<li style=\"margin: 0px; padding: 2px 0px; \">
		新增单窗口模式。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		可创建图层组。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		笔刷和工具预设值改善。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		输出颜色值到&nbsp;CSS, PHP, Java, Python 或纯文本。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		核心整合&nbsp;GEGL &nbsp;库。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		输出到 PDF 文件。</li>
	<li style=\"margin: 0px; padding: 2px 0px; \">
		其它等等。</li>
</ol>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	源代码下载：&nbsp;<a href=\"ftp://ftp.gimp.org/pub/gimp/v2.8/\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">ftp://ftp.gimp.org/pub/gimp/v2.8/</a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	官方网站：&nbsp;<a href=\"http://www.gimp.org/\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://www.gimp.org/</a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	#&nbsp;Ubuntu 12.04 或 11.10 安装：（注：以下 PPA 还只有 GIMP 2.8 RC1 ，不过估计最终版会尽快释出）</p>
<blockquote style=\"margin: 10px 0px; padding: 10px 20px; font-family: Georgia, Arial, Times, serif; font-size: 14px; line-height: 19px; background-color: rgb(244, 244, 244); color: rgb(102, 102, 102); \">
	<p style=\"margin: 5px 0px; padding: 5px 0px; \">
		sudo add-apt-repository ppa:otto-kesselgulasch/gimp<br style=\"margin: 0px; padding: 0px; \" />
		sudo apt-get update<br style=\"margin: 0px; padding: 0px; \" />
		sudo apt-get install gimp</p>
</blockquote>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	--消息<a href=\"http://www.webupd8.org/2012/05/gimp-28-stable-finally-available-for.html\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \" target=\"_blank\">来源</a></p>
","1337846181","0","74","0","0","0","0","#09C","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("95","","3","Ubuntu 官方中文名发布","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	Ubuntu 于昨天正式发布，在下载页上我们也看到了以下这样一个 Logo ，是的，很显然 Ubuntu 中文名被肯诺公司官方定名为 &ldquo;友帮拓&rdquo; 了。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	<a href=\"http://wowubuntu.com/wp-content/uploads/2012/04/download-logo-chinese.png\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \"><img alt=\"\" class=\"alignnone size-full wp-image-6263\" height=\"35\" src=\"http://wowubuntu.com/wp-content/uploads/2012/04/download-logo-chinese.png\" style=\"margin: 0px; padding: 0px; border: none; text-align: center; max-width: 100%; \" title=\"download-logo-chinese\" width=\"268\" /></a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	目前这个 Logo 还在，看这里：&nbsp;<a href=\"http://www.ubuntu.com/download\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">http://www.ubuntu.com/download</a>&nbsp;。</p>
","1338877562","0","74","0","0","0","0","#be2d2d","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("96","","3","Secure Shell","","<p>
	&nbsp;</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	Secure Shell 是专门为 Chrome OS 打造的原生 SSH 客户端程序，以插件的形式存在， 同时也可以在 Chrome 浏览器中安装。也就是说，现在你可以直接在 Chrome 浏览器中通过 SSH 远程操作你的服务器了。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	<a href=\"http://wowubuntu.com/wp-content/uploads/2012/05/secureshell2.png\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \"><img alt=\"\" class=\"alignnone size-full wp-image-6284\" height=\"349\" src=\"http://wowubuntu.com/wp-content/uploads/2012/05/secureshell2.png\" style=\"margin: 0px; padding: 0px; border: none; text-align: center; max-width: 100%; \" title=\"secureshell2\" width=\"384\" /></a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	Secure&nbsp;Shell 又名 hterm (HTML&nbsp;Terminal) ，是一个完全用&nbsp;JavaScript 写成、并与&nbsp;xterm 兼容的终端模似器。它提供与 Putty ( Win 平台）及&nbsp;ssh 命令行 (MacOSX 及 Linux ) 相类似功能。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	更多详情见<a href=\"http://git.chromium.org/gitweb/?p=chromiumos/platform/assets.git;a=blob;f=chromeapps/hterm/doc/faq.txt\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \" target=\"_blank\">官方的 FAQ 页面</a>。</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	# 安装：</p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	<a href=\"https://chrome.google.com/webstore/detail/pnhechapfaindjhompbnflcldabbghjo\" style=\"margin: 0px; padding: 0px; text-decoration: none; color: rgb(204, 102, 0); border: 0px; \">https://chrome.google.com/webstore/detail/pnhechapfaindjhompbnflcldabbghjo</a></p>
<p style=\"margin: 5px 0px; padding: 5px 0px; font-family: Arial; font-size: 14px; line-height: 19px; color: rgb(76, 76, 76); \">
	试用了一下，目前&nbsp;Secure Shell 的功能还比较弱，很多功能都不支持，比如不支持 Key 认证，不支持端口转发等等。</p>
","1338877575","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("98","","2","那些不死的英雄","","<p>
	&nbsp;</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	有些游戏能够更新换代的出下去，游戏中的各类灵魂角色功不可没。如果哪个脑子有坑的游戏剧情编剧敢把人物&ldquo;写死&rdquo;，那么有可能就会重蹈福尔摩斯作者被砸玻璃的覆辙。下面我们将会简评一下与死亡擦肩而过的游戏人物，让我们来看看他们究竟经历了哪些旅程。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	<strong>内森&middot;德雷克（《神秘海域》系列）</strong></p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	如果要说有哪个人出现在错误的时间和错误的地点，那就是内森了。这位寻宝猎人总是把自己置身于最危险、最恐怖的地方，但他总能奇迹一般的脱险。说实话我对此感到十分佩服，我很想知道为什么他总能在最危险的关头脱身。就连从飞机上掉落下来，与变异士兵交战和被数量军用卡车追赶也无法将他置于死地。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	他最接近死亡的一次是在《神秘海域2：纵横四海》中，他有一次悬在悬崖边上，差点摔落。只不过他最终还是逃生了，这人太恐怖了！</p>
","1339037745","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("99","","6","凶手上网搜杀人方法留下大量证据","","<p>
	&nbsp;</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	社交媒体活动对于现代人来说，几乎是很自然很本能的事了。但是在试图使用它协助犯罪行为前，最好先学学怎么掩盖自己犯罪的痕迹。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	这对来自佛罗里达州的情侣在自己所发送的短信息、Facebook上的记录和使用Google搜索引擎的记录统统指向他们19岁的室友的死亡，现已被当地警方逮捕。</p>
<p class=\"f_center\" style=\"font-size: 14px; line-height: 23px; text-align: center; color: rgb(0, 0, 0); font-family: 宋体, serif; \">
	<img alt=\"智商低真可怕！凶手上网搜杀人方法留下大量证据\" src=\"http://img4.cache.netease.com/game/2012/6/6/2012060616313529161.jpg\" style=\"vertical-align: top; border: 1px solid rgb(204, 204, 204); \" /></p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	受害者名为Juliana Mensch，在上个月24日在佛州洛德代尔堡市的家中被人勒死，受害人与情侣凶手一直合租房屋，而两人之前曾经和受害者有过经济上的纠纷。命案发生几天后，这名32岁的名为James Ayers的男子就被警方拘捕，在被捕前他曾经将自己的罪行坦露给了一个朋友，因为那几天他的女朋友，22岁的Nicole Okrzesik将整个杀人案都怪在James身上。然而，一项警方对于Okrzesik的社交网络历史记录的调查却充分揭示出了这对情侣在杀人前和杀人后的想法。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik曾经使用她的电脑搜索了多次与命案相关的内容，比如&ldquo;使用化学制剂让人失去知觉&rdquo;，&ldquo;致人昏迷&rdquo;，&ldquo;在对方死在睡觉时的方法&rdquo;，&ldquo;如何让一个人窒息&rdquo;和&ldquo;怎么给人下毒&rdquo;等。在Orkzesik搜索这些关键词仅仅几分钟过后，室友Mensch就被害了。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	第二天，Orkzesik和Ayers互发了&ldquo;几百条短信&rdquo;短信内容显示Ayers可能对杀人的行为感到十分后悔：</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：我现在就打911，记住你现在开的是可是她的车。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik：别说了！</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：操，老子才不管这么多，我直接叫警察算了。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik：我们明明可以把嫌疑清掉，忘掉这个屎摊了，你他妈为什么一定毁掉我们两个人的生活呢？</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：不要忘了当时是你抓住她的，想通了快点给我打电话！</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	实际上直到5月27号案情都还不是很明朗。但是根据两名罪犯在Facebook上聊天的文字记录，Okrzesik和Ayers都还在就如何处理受害者的尸体这个问题进行争辩。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	当地媒体The New Times公布了两人的聊天内容：</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik：Danielle 说了什么？</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：就说 Jules（被害人）失踪了</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik：你怎么说的？</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：当时是凌晨3点所以我没回，但是我们必须想个办法，如果她（尸体）味道越来越大，被人发现我们就完了。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik：我们就不能随便找个地方把她扔了然后当作什么都没发生过吗？</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Ayers：拜托你快点过来吧，我需要你，这件事也有你的份。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	&nbsp;</p>
<div class=\"gg200x300\" style=\"padding: 0px; margin: 0px 19px 0px 0px; float: left; color: rgb(0, 0, 0); font-family: 宋体, serif; font-size: 14px; line-height: 23px; text-align: justify; \">
	&nbsp;</div>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	Okrzesik当天没有出现，而第二天就跑到当地一个康复门诊去报到了。Ayers后来向警方自首，现在因一级谋杀被控告，此案将在6月25日开庭审理。Orkzesik在5月10日被正式拘捕，由于证据充分，她马上就被定罪了。她的案子将会与6月7日被递交给陪审团。</p>
<p style=\"font-size: 14px; line-height: 23px; text-indent: 2em; color: rgb(0, 0, 0); font-family: 宋体, serif; text-align: justify; \">
	这绝对已经不是第一次罪犯们在社交网络和Google上留下犯罪证据了。早在2005年，就有一名苹果公司的Mac专家因 Google&ldquo;如何用折颈摔（neck snap break）杀掉我老婆&rdquo;而留下了证据。还有不少类似的案件，他们并不是说明社交网络对他们多有帮助，而是人们已经太过于依赖社交网络了。（来源：煎蛋）</p>
","1339037867","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("100","","2","美国总统们语文水平","","<p>
	&nbsp;</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	2012美国大选奥巴马小布什罗姆尼肯尼迪初中时候，因为学校有第二外语的教学要求，我曾经同时学习过英语和德语两种语言。当时英语底子还不牢固，又要灌输许多新的德语单词和语法规则，一下子还真不太容易招架得住。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	其中令我喜忧参半的一点，是两种语言之间的单词拼写大同小异却似是而非，要拼对单词还真不是一件容易的事。比如英语里面的苹果apple到了德语成了Apfel，父亲从英语的father变成了Vater。不过真正令人头疼的，还是国家名、人名这一类的专有名词，你看，瑞士，英语里面叫Switzerland，德语里面成了die Schweiz，从读音到拼写都相差那么一点点，害得我有次英语课拼写单词时反倒拼出了&ldquo;Schweitzerland&rdquo;这么个&ldquo;怪胎单词&rdquo;。的确，要背熟单词的拼法，可真得下点功夫。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	可别以为只有像我当时那样的语言初学者才会犯拼错单词这样的错误。看似一国之主、高高在上的美国总统及候选人们，栽在拼写、语法错误上的跟头可不是一次两次了。这些错误，有些贻笑大方，有些隐蔽难查，有些无心插柳，有些有意为之，或成为政治对手竞相攻击的把柄，或成为茶余饭后消遣时光的谈资，细细品来，颇为有趣。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	罗姆尼：&ldquo;美国&rdquo;国名都会拼写错误 奥巴马：演讲大师也有语法纰漏</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	本周美国总统选战中一条有趣的新闻，便是共和党候选人罗姆尼阵营推出的iPhone助选软件的启动屏幕上，把&ldquo;美国&rdquo;(America)这个单词给拼错成了&ldquo;Amercia&rdquo;。这个疏忽一下激起了全美媒体的冷嘲热讽，对共和党阵营的工作态度与绩效提出质疑，同时也借此事件反思美国人日益下降的语言文字基本功。</p>
<center style=\"color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		<img alt=\"\" height=\"628\" src=\"http://www.zhengtan.me/wp-content/uploads/2012/06/mitt-romney-america-spelling-amercia-app-im-with-stupid.jpg\" style=\"border: 1px solid rgb(204, 204, 204); max-width: 600px; padding: 7px; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial; \" width=\"480\" /></p>
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		罗姆尼阵营推出的iPhone助选软件把&ldquo;美国&rdquo;(America)错拼成了&ldquo;Amercia&rdquo;。</p>
</center>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	其实罗姆尼阵营所犯下的这个拼写错误，应该算是粗心大意&mdash;&mdash;&ldquo;America&rdquo;应该怎么拼，相信杨百翰大学英语系科班出身的罗姆尼不会不知道，只是在设计与勘误等具体工作流程上存在疏忽，让他这位&ldquo;总boss&rdquo;成为众矢之的背了黑锅。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	相比之下，美国现任总统奥巴马在&ldquo;我爱祖国语言美&rdquo;上的表现就出彩得多，不仅出版的自传文字平实广受好评，而且在演讲的口语表达中也思路清晰、用词精准，成为许多人眼中当之无愧的&ldquo;演讲大师&rdquo;。不过，这位&ldquo;吃文字饭&rdquo;的律政翘楚，有时也会不小心犯下一些许多英语初学者常犯的语法错误。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	在上任后举行的第一次新闻发布会中，奥巴马一开场便说&ldquo;The 240,000 jobs lost in October marks the 10th consecutive month that our economy has shed jobs.&rdquo;在这句话中，奥巴马一不小心便犯了主谓不一致的错误，&ldquo;240,000 jobs&rdquo;(24万个工作岗位)作为复数名词，后面应该紧接复数形式的动词&ldquo;mark&rdquo;，而奥巴马却用了单数形式的&ldquo;marks&rdquo;。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	看似简单的不定冠词使用规则，也让奥巴马&ldquo;躺着中枪&rdquo;了一回。奥巴马在回忆他舅舅激战奥斯维辛解放集中营时，曾说道&ldquo;I had a uncle who was part of the first American troops to go into Auschwitz and liberate the concentration camps.&rdquo;想必许多英语初学者都能指出，在uncle(舅舅)这个以元音发音为开头的单词前，习惯上都应使用&ldquo;an&rdquo;作为不定冠词。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	同许多美国年轻人一样，奥巴马总统在讲话时也经常在人称代词的使用上犯错。比如，他经常使用主格的&ldquo;I&rdquo;代替宾格的&ldquo;me&rdquo;，说出&ldquo;a very personal decision for Michelle and I&rdquo;、&ldquo;the main disagreement with John and I&rdquo;、&ldquo;graciously invited Michelle and I&rdquo;之类带有语法错误的短语(这些例子中的&ldquo;I&rdquo;都应该用&ldquo;me&rdquo;代替)。又比如，他似乎也搞不清反身代词&ldquo;myself&rdquo;的用法，在上任前同小布什总统见面交谈后，说出了&ldquo;a substantive conversation between myself and the president&rdquo;这样的话。在这个例子中，首先，根据英语的习惯，应将&ldquo;我&rdquo;放在最后;其次，此处不应使用反身代词&ldquo;myself&rdquo;，而应用宾格的&ldquo;me&rdquo;。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	你看，严格说来，在短短半句话里面，美国总统奥巴马都能犯下两个语法错误!《非你莫属》的嘉宾红人文颐女士如果得知，也可以宽心了。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	小布什：英语语病集大成者</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	要说起美国总统犯下的语法错误，奥巴马同他的前任小布什相比，那真是小巫见大巫了。作为&ldquo;英语语病集大成者&rdquo;，小布什那些&ldquo;一鸣惊人&rdquo;的言论，恐怕真要砸了他母校耶鲁大学与哈佛商学院的金字招牌。小布什对英语语言文字的&ldquo;创造性破坏&rdquo;，让他获得了一大批&ldquo;粉丝&rdquo;，将其语录整理为&ldquo;布什主义&rdquo;(Bushism，也包括他在公共场合演讲所犯下的非语法性常识错误)以便&ldquo;流芳百世&rdquo;。</p>
<center style=\"color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		<img alt=\"\" height=\"408\" src=\"http://www.zhengtan.me/wp-content/uploads/2012/06/bushisms.jpg\" style=\"border: 1px solid rgb(204, 204, 204); max-width: 600px; padding: 7px; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial; \" width=\"466\" /></p>
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		小布什堪称英语语病集大成者，自成一派人称&ldquo;布什主义&rdquo;。</p>
</center>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	同奥巴马一样，小布什也经常在人称代词的使用上备受煎熬，不过奥巴马的那些错误大多是因为受到了美国流行文化用语不规范的影响，尚情有可原;而小布什的一些语病，则来得颇有些让人摸不着头脑了。比如2001年上任后不久在某次谈及教育的讲话中，小布什说：&ldquo;You teach a child to read, and he or her will be able to pass a literacy test.&rdquo;在英语中为了照顾男女性别的平等，常会使用&ldquo;he or she&rdquo;、&ldquo;him or her&rdquo;(他或她)这样的固定用法，而小布什突然冒出&ldquo;he or her&rdquo;这样&ldquo;混搭&rdquo;风格的短语，真是创意无限。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	除了语法之外，小布什在英语遣词造句方面的造诣，也鲜有人敌。在1999年他对《经济学人》杂志曾说&ldquo;Keep good relations with the Grecians.&rdquo;(&ldquo;同希腊人保持良好关系。&rdquo;)可惜的是，希腊人民恐怕是很难领到这个情了，因为&ldquo;希腊人&rdquo;在英语中应该被称作&ldquo;Greeks&rdquo;而非&ldquo;Grecians&rdquo;。在2001年的一次讲话中，小布什说：&ldquo;I am mindful not only of preserving executive powers for myself, but for predecessors as well.&rdquo;这里其实小布什想要说的是&ldquo;successor&rdquo;(继任者)这个单词，却不知为何脑经短路般地冒出了它的反义词&ldquo;predecessor&rdquo;(前辈)。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	而小布什犯下的最为有名的语言错误，恐怕要算他2000年在阿肯色州说出的&ldquo;惊世骇俗&rdquo;的这句话&mdash;&mdash;&ldquo;They misunderestimated me.&rdquo;(&ldquo;他们错误地低估了我。&rdquo;)其中的&ldquo;misunderestimate&rdquo;一词史无前例地融合了&ldquo;misunderstand&rdquo;(误解)和&ldquo;underestimate&rdquo;(低估)这两个英语单词，虽然是生造词且语义累赘，但简单易懂、朗朗上口，反倒成为了&ldquo;布什主义&rdquo;最富标志意义的代表性作品。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	据说在美国，一些中小学教师将&ldquo;布什主义&rdquo;的语法错误细心整理，并教授给班上的学生们，告诉他们要&ldquo;引以为戒&rdquo;。如此说来，小布什对于提高美国下一代人口的语言文字修养，说不定还起到了一些促进作用。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	肯尼迪：&ldquo;我是柏林甜甜圈&rdquo;?</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	相比在自己的母语中犯各类尴尬的语言错误，美国总统如果在说外语的时候稍有疏漏，是不是就不会被人这样&ldquo;咬文嚼字&rdquo;了呢?诚然，在说外语的时候犯一些错误，不仅情有可原，说不定还给大众留下自信、有胆识的正面印象。不过美国前总统约翰&middot;肯尼迪在&ldquo;秀&rdquo;德语时所犯下的一个&ldquo;错误&rdquo;，一直让人争议不断、记忆犹新。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	1963年6月26日，美国前总统肯尼迪造访柏林。当时冷战已经开始，柏林墙逐渐将柏林分为东西两部分，德国弥漫着紧张的气氛。为了鼓舞德国人民、传达美国的外交政策，肯尼迪在舍嫩贝格市政厅对着45万名德国民众，发表了他最为著名的公共演讲之一。在演讲中，肯尼迪动情地说：</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	Two thousand years ago the proudest boast was &ldquo;civis Romanus sum.&rdquo; Today, in the world of freedom, the proudest boast is &ldquo;Ich bin ein Berliner!&rdquo;&hellip; All free men, wherever they may live, are citizens of Berlin, and, therefore, as a free man, I take pride in the words &ldquo;Ich bin ein Berliner!&rdquo;</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	(两千年前最自豪的句子是&ldquo;civis Romanus sum&rdquo;(&ldquo;我是罗马公民&rdquo;)。今天，在自由世界，最自豪的句子是&ldquo;Ich bin ein Berliner&rdquo;(&ldquo;我是柏林人&rdquo;)&hellip;&hellip;所有自由人，无论生活在哪里，都是柏林的公民。因此，身为自由人，我以&ldquo;Ich bin ein Berliner&rdquo;(&ldquo;我是柏林人&rdquo;)感到自豪!)肯尼迪的这段演讲在当时受到了普遍的好评，然而却有&ldquo;专业人士&rdquo;站出来说，在德语中提及一个人的职业或家乡时，通常不会使用不定冠词&ldquo;ein&rdquo;，因此&ldquo;Ich bin ein Berliner&rdquo;的正确说法应该是&ldquo;Ich bin Berliner&rdquo;;而&ldquo;Ich bin ein Berliner&rdquo;则会让人产生另一种联想&mdash;&mdash;&ldquo;Berliner&rdquo;除了表示&ldquo;柏林人&rdquo;外，还可以表示一种广受喜爱的食品&ldquo;柏林甜甜圈&rdquo;。因此，肯尼迪总统说出的&ldquo;Ich bin ein Berliner&rdquo;，实际上会让听众理解成&ldquo;我是柏林甜甜圈&rdquo;。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	这样一种有趣的说法立刻受到了媒体的广泛报道，包括美国有线电视新闻网、《时代》周刊、《纽约时报》、英国广播公司、《卫报》等主流媒体都曾引用过这个&ldquo;笑话&rdquo;，把它看成是严肃冷战中一段有意思的小插曲。</p>
<center style=\"color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		<img alt=\"\" height=\"275\" src=\"http://www.zhengtan.me/wp-content/uploads/2012/06/jfk.jpg\" style=\"border: 1px solid rgb(204, 204, 204); max-width: 600px; padding: 7px; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial; \" width=\"456\" /></p>
	<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; \">
		1963年肯尼迪总统在柏林演讲时说&ldquo;Ich bin ein Berliner&rdquo;。</p>
</center>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	然而真相是，肯尼迪总统其实并没有犯错。肯尼迪是美国人，并不是真正的柏林人，他在演讲中的指代更多也只是象征意味，因此在&ldquo;Berliner&rdquo;之前加上不定冠词并不算错。而将&ldquo;Berliner&rdquo;指代&ldquo;柏林甜甜圈&rdquo;，其实是柏林以外的德国人才有的习惯&mdash;&mdash;这种全名叫做&ldquo;Berliner Pfannkuchen&rdquo;(德国煎饼)的食品，柏林以外的人们喜欢称其为&ldquo;Berliner&rdquo;(柏林)，而柏林人则把它叫做&ldquo;Pfannkuchen&rdquo;(煎饼)。当年肯尼迪演讲时，在场的观众并没有对他的德语产生任何的误解，以德语为母语的听众也能够根据上下文的意思理解在这句话中的&ldquo;Berliner&rdquo;是指人而非甜甜圈。硬要认为肯尼迪曾经说过&ldquo;我是柏林甜甜圈&rdquo;，恐怕只是对德语一知半解的人们一厢情愿罢了。</p>
<p style=\"margin: 0px; padding: 0px 0px 1.2em; text-indent: 32px; color: rgb(85, 85, 85); font-family: \'Microsoft Yahei\', \'Helvetica Neue\', \'Luxi Sans\', \'DejaVu Sans\', Tahoma, \'Hiragino Sans GB\', STHeiti; font-size: 16px; line-height: 27px; background-color: rgb(247, 247, 247); \">
	以此类推，下次你见到一个老外高兴地对你说&ldquo;I am a Hamburger&rdquo;时可别上去大咬一口呀!人家可不是自称&ldquo;汉堡包&rdquo;，而是说自己的家乡是德国的汉堡。</p>
","1339037918","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("101","","1","广州200人围捕飞车劫匪","","<p>
	5分钟左右就抓到了一个，确实很快。（来广州）第一天便遇上了不顺心的事，但对广州的印象不会大打折扣。我以为丢了钱包报警，警察也不会怎么在意，但他们很快就帮我找回来了。　　&mdash;&mdash;&mdash;被抢事主陈女士</p>
<div class=\"hzhwzl\">
	&nbsp;</div>
<p>
	　　前日20：53，事主陈女士被飞车贼抢走钱包</p>
<p>
	　　前日20：58，便衣驾车追赶并撞倒劫匪摩托，当场抓住1人；另一劫匪跳桥逃入附近小区</p>
<p>
	　　前日23：00左右，两百人参与搜捕，最终在小区一车 底 下 揪 出 第 二名劫匪</p>
<p>
	　　她来广州的第一天，在街上被摩托车飞抢。街坊们扶起受伤的她，帮忙报警。约5分钟后，夺路狂奔的飞抢摩托车，在洛溪新城南浦桥被警方撞倒，一名嫌犯被抓，一名嫌犯跳桥钻入附近小区。警方、治安队员、小区保安200余人，搜索1个多小时后，将躲在车</p>
","1341554990","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("102","","1","四川男子不满情人劈腿捅其70刀 一审被判死刑","","<p class=\"first\">
	现年40岁的四川男子程某平，面对已婚情人提出的分手要求仍不愿放手，几度要求复合都遭拒绝，愤而70余刀将情人捅 死。随后为免罪行败露，程某平又40余刀杀害与情人合租的有孕室友。昨日上午，身背3条人命的程某平，被广州中院一审以故意杀人罪判处死刑，赔偿受害人家 属费用共计40余万元。程某平当庭表示不服，要求上诉。被害人家属则表示接受判决结果。</p>
<p>
	据悉，本案被告人程某平，现年40岁，四川人。据程某平供述，2010年5月，他在供职的白云区某货运公司认识了年方30岁的李某水。&ldquo;当时我知道她已经结婚了，还有个孩子，但她和丈夫感情不好，没住在一起，已经商议好要离婚了。&rdquo;不久后，程某平和李某水建立情人关系。</p>
<p>
	随后，在两人交往的1年多时间里，程某平声称对李某水付出太多，&ldquo;金钱上我就为她花费了10多万元&rdquo;帮她买衣服，买化妆品和首饰，&ldquo;不断满足她的要求&rdquo;，还借了5万元给李某水的姐姐。</p>
<p>
	2010年10月，因为&ldquo;手紧&rdquo;，程某平还挪用了公司货款4万余元，带着李某水一起逃到岳阳开起了一家服装店。不久后，在李某水的提议下，两人又在海南呆了一段日子，但最终李某水还是决定回到广州。</p>
<p>
	&ldquo;回广州后不久，她就要和我分手。&rdquo;程某平怀疑李某水是不是背叛了自己，&ldquo;她上班发廊的老板娘告诉我，她又有其他男人了。&rdquo;当晚，程某平来到李某水的出租屋楼下，&ldquo;亲眼看见她上了一个50多岁老头的商务车&rdquo;。</p>
","1341555030","0","74","0","0","0","0","#d71d1d","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("103","","1","河南巩义城管强制瓜农买高价帐篷 不买不能卖瓜","","<p>
	　　昨日，在巩义卖瓜的中牟瓜农老李向本报反映称，巩义市为了方便市民购瓜并对进城瓜农进行统一管理，城市管理行政执法局设立了很多瓜果临时便民 点。&ldquo;开始我们可感动，终于能光明正大地进城了，后来才发现我们自带的遮阳伞不让用，必须花300块买个带广告的帐篷。结果昨天下午一场大雨，执法局说帐 篷不合格，又让我们花200块买了把带广告的遮阳伞！&rdquo;</p>
<p>
	　　<strong>买完帐篷买遮阳伞 瓜还没卖先出五百</strong></p>
<p>
	　　昨日上午11时，记者赶到位于巩义市杜甫路与桐本路附近的巩义市汽车站，看到杜甫路南侧路边的树荫下停着十多辆瓜车，瓜车上全都撑着一把橘黄色遮阳伞（如图）。</p>
<p>
	　　据瓜农老李介绍，6月下旬，执法局的工作人员称瓜农自带的遮阳伞不合格，不能使用，需要统一购置同一规格的帐篷。&ldquo;工作人员称所有的瓜农都得买，一顶帐篷要300块，不买就不能在便民点继续卖瓜了。&rdquo;</p>
<p>
	　　附近的瓜农告诉记者，4日下午巩义下起了大雨，大家纷纷撑起帐篷避雨，可这时执法局工作人员又说瓜农的帐篷搭得不合格，需要再花200元钱买把遮阳伞。</p>
<p>
	　　&ldquo;俺家的帐篷当即就被收走了！&rdquo;老李指了指头顶那把橘黄色遮阳伞说。记者留意到，伞上有&ldquo;巩义市便民服务点&rdquo;的字样。</p>
<p>
	　　&ldquo;俺卖瓜8毛钱一斤，花500块钱买一顶帐篷和一把伞，相当于625斤的瓜白卖了！对俺来说，这可真是&lsquo;天价&rsquo;呀！&rdquo;瓜农老邓叹了口气说，&ldquo;瓜农们伤不起啊！&rdquo;</p>
<p>
	　　记者在老李出示的一张6月24日开具的收据上看到&ldquo;今收到汽车站对面帐篷（2）人民币陆佰元整（老李和弟弟一人买了一顶帐篷）&rdquo;的字样，所盖的章为某通信公司财务专用章。</p>
<p>
	　　&ldquo;执法局的人说这伞和帐篷是瓜农必须买的，可这钱到底是啥性质却说不清！按说买东西应该给开发票的吧，但人家不给我们开发票，你说要是收的押金或是保证金，那在这收据上就得写明白啊？&rdquo;老李提出了自己的疑问。</p>
<p>
	　　&ldquo;瓜果便民点哪有强制瓜农消费的道理？&rdquo;&ldquo;这么做太不合适了，那可都是瓜农们的血汗钱呀！&rdquo;前来买瓜的市民也纷纷表达了自己的观点。</p>
<p>
	　　上午11时25分，老李领着记者来到北山口路与杜甫路交叉口南侧路西的一个小院，他说这儿就是与执法局&ldquo;合作&rdquo;的通信公司的仓库，瓜农们购置帐篷和伞都在这里。&ldquo;不拿执法局开的条，不管出多少钱，人家都不会卖给你！&rdquo;记者在外敲了半天铁门，始终无人来开。</p>
<p>
	　<strong>　交的钱是啥性质 工作人员避而不谈</strong></p>
<p>
	　　老李和众多瓜农交的钱到底是啥性质？昨日下午，记者致电巩义汽车站第一瓜果临时便民点处公示的一个执法局座机电话，一名值班的男工作人员称其不清楚此事，并要记者打电话到市容中队了解情况。</p>
<p>
	　　记者以瓜农的身份进行咨询，市容中队一名男值班人员称，4日下午之所以要收瓜农的帐篷，是因为&ldquo;上面突击检查了，你们的帐篷不合格！你们没收到 发的粉色小条吗？拿着那条再去买个太阳伞就行了&rdquo;。记者问被收走的帐篷是否还能拿回时，该工作人员称&ldquo;你得先去服务中心接受处理再说&rdquo;。记者问等瓜农回家 时，上交的钱能否悉数退还时，该工作人员说：&ldquo;你能保证帐篷和伞完好无损吗？&rdquo;记者再追问瓜农所交的究竟是货款还是押金时，该工作人员避而不谈，并不耐烦 地说：&ldquo;撑帐篷和打伞都是有标准的，有疑问的话就来服务中心面谈吧！&rdquo;然后挂断了电话。线索提供李先生新闻热线0371-96211 □记者张瞧文闫化庄图</p>
","1341555064","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("104","","4","国际液态奶三聚氰胺有新规 比国内标准严格近17倍","","<p>
	　　昨天，曾让国人闻之色变的&ldquo;三聚氰胺&rdquo;再度引发热议。有媒体援引世界卫生组织消息称，联合国负责制定食品安全标准的国际食品法典委员会为牛奶中 三聚氰胺含量设定新标准，今后每千克液态牛奶中三聚氰胺含量不得超过0.15毫克。而该新标比现时我国相关标准严格近17倍。对此，有乳业专家认为，我国 企业若要执行国际新标准难度较大，其对企业检测仪器设备投入，对奶牛牧场饲料、奶源控制以及环境控制均有很大压力。</p>
<p>
	　　<strong>世卫 三聚氰胺含量降至0.15毫克</strong></p>
<p>
	　　前天，世界卫生组织对外公告称，联合国负责制定食品安全标准的国际食品法典委员会为牛奶中三聚氰胺含量设定了新标准。</p>
<p>
	　　据新标准规定，每千克液态牛奶中三聚氰胺含量不得超过0.15毫克。该组织机构曾规定，每千克用于制造奶粉的牛奶中三聚氰胺含量最多不得超过1毫克，其他食品中，三聚氰胺含量不得超过每千克2.5毫克。</p>
<p>
	　　世卫组织专家解释，三聚氰胺含量标准指食品中三聚氰胺自然的、不可避免的含量，而非人为添加的含量。&ldquo;制定三聚氰胺含量上限标准，有助于各国区别食品中无法避免且对健康无碍的三聚氰胺含量与蓄意添加三聚氰胺的行为。&rdquo;</p>
","1341555102","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("105","","8","菲律宾抗议设三沙市 称从未请美方派机巡视南海","","<p>
	　　本报讯 菲律宾外交部4日传召中国驻菲律宾大使马克卿，向中方提交照会，抗议中国近日设立三沙市。<br />
	<br />
	　　菲律宾外交部发言人5日表示，三沙市的设立有违南海各方行为准则的精神，并称包括南沙群岛大部分、黄岩岛及周边海域，是菲律宾领土主权的一部分。发言人又指，中方的举动违背了2002年签订的互不侵犯协定。(宗禾)<br />
	<br />
	<strong>　　菲称未请美方派机巡视南海</strong><br />
	<br />
	　　本报讯《菲律宾每日问询者报》5日报道，菲总统阿基诺三世5日呼吁中国政府发表有关黄岩岛问题的声明前，&ldquo;注意自己的措辞&rdquo;，考虑清楚&ldquo;事实真相&rdquo;；他表示，菲从未向中国发出过任何&ldquo;带有挑衅性的言论&rdquo;。<br />
	<br />
	　　阿基诺表示:&ldquo;我不知道所说的菲律宾官员们发出的挑衅性言论到底是什么。但另外一方(中国方面)说出了很多事情。&rdquo;<br />
	<br />
	　　阿基诺还称，菲内阁将讨论是否重新向黄岩岛派遣船只，不过这主要取决于天气状况。<br />
	<br />
	　　阿基诺还澄清说，菲方并未请求美国侦察机巡视南海。他只是提了一下如有必要，可能要求美国派飞机帮助菲律宾。<br />
	<br />
	　　菲总统府发言人陈显达也用中文警告中国在发表南海有关声明时&ldquo;小心一点。&rdquo;(宗禾)</p>
","1341555159","0","74","0","0","0","0","","") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_article") . '` (`aid`,`from`,`cid`,`title`,`summary`,`text`,`createTime`,`author`,`views`,`recommend`,`title_bold`,`title_italic`,`title_strikethrough`,`title_color`,`url`) VALUES ("106","","7","孔卡问题成发布会焦点 里皮发怒：我只关心比赛","","<p>
	　　有关孔卡<a href=\"http://weibo.com/u/2639934773?zw=sports\" target=\"_blank\">(微博)</a>想离开恒大<a href=\"http://weibo.com/u/2041593835?zw=sports\" target=\"_blank\">(微博)</a>的 肥皂剧依然在继续，并且有了最新的进展。昨天，阿根廷人已经向俱乐部正式提出了希望离开广州的想法，对于这一突如其来的变故，恒大董事长刘永灼用&ldquo;震惊&rdquo; 来加以形容，同时表示会全力挽留前者留在队中。但孔卡本人似乎已经铁了心想要回到自己熟悉的巴西联赛踢球，他甚至在接受阿根廷国家通讯社采访时提出了这样 一个设想，那就是自掏300万欧元来买断自己的合同，获得自由身。</p>
<p>
	　<strong>　300万欧元解决不了问题</strong></p>
<p>
	　　关于孔卡的回归，巴西弗洛米嫩塞俱乐部表现出了兴趣，但目前横亘在他们面前的是巨额转会费。当初恒大以1000万美元的转会费将孔卡从他们手中 买走，现在想要再买回去，他们必须得拿出一张巨额支票才有可能，但凭借这家俱乐部的财力，这件事情难度很大。正是因为这一点，孔卡不得不自己动起了小脑 筋，在接受阿根廷国家通讯社采访时，孔卡表示，自己愿意掏出300万欧元来买断合同，从而在获得自由身之后加盟弗洛米嫩塞，但实际上，这完全是他的一厢情 愿。如果想要买断合同，他将要付出的远远不止300万欧元。</p>
<p>
	　　根据FIFA转会条例韦伯斯特条款规定，年龄大于28岁的球员可以在合同履行两年后买断合同离队，而买断费用就是他剩余合同的年薪总额。从根本 上来说，这一条款并不适用于孔卡，首先他目前在恒大只效力了一年，还没有达到可以买断合同的条件，其次，即便恒大同意让孔卡买断合同，后者也必须要支付剩 余两年半合同的年薪总额，按照孔卡年薪700万美元来计算，这将是一个巨额天文数字！即便他等到明年夏天符合买断合同条件后作出这一决定，也将要付出整整 1000万美元才能买回自由身。因此，有关300万欧元就能买断合同的说法完全是子虚乌有。</p>
<p>
	　　现在，孔卡想要回家的路只有一条，那就是找一个付得起千万级美金转会费的买家，否则就别再闹腾了。</p>
<p>
	　<strong>　里皮发怒不愿谈孔卡</strong></p>
<p>
	　　昨天上午，恒大队一行已经抵达上海金山，备战本周末与申鑫的中超<a href=\"http://weibo.com/zhongchaobaodao?zw=sports\" target=\"_blank\">(微博)</a>联 赛。在昨天召开的赛前新闻发布会上，孔卡无疑成为了大家的焦点。发布会刚刚开始，就有记者提问：&ldquo;请问里皮先生，今天俱乐部官方已经证实孔卡提出转会申 请，你对此事怎么看？&nbsp;&rdquo;而里皮的答案也在预料之中，马上予以了否认和回避：&ldquo;我还没有听说这件事，我的心思都放在了明天和上海申鑫<a href=\"http://weibo.com/u/2050026257?zw=sports\" target=\"_blank\">(微博)</a>的比赛中。孔卡已经随队来到上海，马上就要参加赛前训练了。&nbsp;&rdquo;</p>
<p>
	　　无奈之下，记者只好追问道：&ldquo;俱乐部已经官方证实了，刘总在接受采访中确认过，作为主教练也可以做出一些回应吧？&rdquo;此时里皮的脸色已经有些不 悦，&ldquo;我只关心明天的比赛，不谈比赛之外的事。有什么等比赛结束后再谈。&nbsp;&rdquo;当记者第三次追问道&ldquo;如果孔卡真的离开，会对你的技战术设计影响严重么&rdquo;这样 的问题后，翻译已经不敢再将这个问题传达给银狐了，因为后者已经脸色铁青。</p>
<p>
	　　在巴西媒体的描述下，孔卡似乎在恒大一天也呆不下去了，但在昨天的赛前训练中，这位阿根廷球星显得心情不错。在整个训练过程中，他都是有说有笑 地在和队友、助教们交流，还时不时会做一些搞怪动作，并发出爽朗的笑声。而在训练结束之后，当记者向孔卡求证有关希望离开的说法时，后者表示，现在不方便 发表任何言论，要等待转会事件尘埃落定之后才会接受采访。</p>
","1341555200","0","74","0","0","0","0","","") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_article"),$nDataRows));
			
		$nDataRows = 0 ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_attachment") . '` (`fid`,`aid`,`orginname`,`storepath`,`size`,`type`,`index`,`displayInList`) VALUES ("1","97","QQ截图20120709111121.jpg","hash17adc00833cf4c3089843900db8529d6.QQ截图20120709111121.jpg","7254","image/jpeg","1","1") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_attachment"),$nDataRows));
			
		$nDataRows = 0 ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("1","社会","我们的联系方式,业务内容,历史","3","4") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("2","设计作品","","143","144") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("3","公司新闻","公司新闻,行业动态,促销活动","123","142") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("4","经济","","7","8") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("5","国内","","126","135") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("6","国际","","136","139") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("7","体育","","127","128") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("8","军事","","124","125") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("29","其它","其它","133","134") ') ;
		$nDataRows+= $aDB->execute( 'REPLACE INTO `' . $aDB->transTableName("opencms_category") . '` (`cid`,`title`,`description`,`lft`,`rgt`) VALUES ("28","首页","首页","121","122") ') ;
		$aMessageQueue->create(Message::success,'向数据表%s插入了%d行记录。',array($aDB->transTableName("opencms_category"),$nDataRows));
			
		
		
		// 3. settings
		
		$aSetting = $aExtension->setting() ;
			
				
		$aSetting->setItem('/index/toplist/','toplist',array (
  1 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  4 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  3 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  8 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  5 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  7 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  6 => 
  array (
    'index_new' => '1',
    'limit_new' => '10',
    'limit_hot' => '10',
  ),
  2 => 
  array (
    'index_new' => '1',
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
			
				
		$aSetting->setItem('/','data-version','0.1.0');
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/");
			
		
		
		// 4. files
		
		$sFromPath = $aExtension->metainfo()->installPath()."/data/public";
		$sDestPath = $aExtension ->filesFolder()->path();
		Folder::RecursiveCopy( $sFromPath , $sDestPath );
		$aMessageQueue->create(Message::success,'复制文件夹： `%s` to `%s`',array($sFromPath,$sDestPath));
		
		$sFromPath = $aExtension->metainfo()->installPath()."/data/setup";
		$sDestPath = $aExtension->dataFolder()->path();
		Folder::RecursiveCopy( $sFromPath , $sDestPath );
		$aMessageQueue->create(Message::success,'复制文件夹： `%s` to `%s`',array($sFromPath,$sDestPath));
		
	}
}
