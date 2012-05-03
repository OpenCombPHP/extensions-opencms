<?php 
return array(
		
		'class'=>'org\\opencomb\\coresystem\\mvc\\controller\\FrontFrame' ,
		
		'view:BreadcrumbNavigation' => array(
				'template' => 'opencms:BreadcrumbNavigation.html' ,
		) ,
		
		// 控制器栏目内最新内容
		'controller:topListNew' => array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('orderby'=>'createTime'),
		) ,
		
		// 控制器栏目内最热内容
		'controller:topListHot' => array(
				'class' => 'org\\opencomb\\opencms\\article\\TopList' ,
				'params' => array('orderby'=>'views'),
		) ,
) ;