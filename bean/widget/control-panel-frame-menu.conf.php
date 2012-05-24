<?php
return array(
	'item:CMS' => array(
		'title'=>'内容',
		'link'=>'?c=org.opencomb.opencms.article.ArticleManage',
		'direction'=>'v',
		
		'menu'=> 1 ,
		'item:ArticleManage' => array(
			'title'=>'文章管理',
			'link'=>'?c=org.opencomb.opencms.article.ArticleManage',
			'query'=>array(  //query可以让以下几个页面也带有侧边菜单
				'c=org.opencomb.opencms.article.ArticleManage' ,
				'c=org.opencomb.opencms.article.CreateArticle' ,
				'c=org.opencomb.opencms.article.EditArticle' ,
			)
		),
		'item:CategoryManage' => array(
			'title'=>'栏目管理',
			'link'=>'?c=org.opencomb.opencms.category.CategoryManage',
			'query'=> array(
				'c=org.opencomb.opencms.category.CategoryManage' ,
				'c=org.opencomb.opencms.category.CreateCategory' ,
				'c=org.opencomb.opencms.category.EditCategory' ,
				'c=org.opencomb.opencms.category.DeleteCategory' ,
			)
		),
		'item:IndexManage' => array(
			'title'=>'首页设置',
			'link'=>'?c=org.opencomb.opencms.index.IndexManage',
			'query'=>'c=org.opencomb.opencms.index.IndexManage'
		),
		'item:MenuManage' => array(
			'title'=>'菜单设置',
			'link'=>'?c=org.opencomb.opencms.menu.MenuManage',
			'query'=>'c=org.opencomb.opencms.menu.MenuManage'
		),
	)
);
