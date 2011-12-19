<?php
return array(
	array(
		'title'=>'cms管理',
		'link'=>'?c=org.opencomb.opencms.index.IndexManage',
		'menu'=>array(
			'direction'=>'v',
			'items'=>array(
				array(
					'title'=>'文章管理',
					'link'=>'?c=org.opencomb.opencms.article.ArticleManage',
					'query'=>array(
						'c=org.opencomb.opencms.article.ArticleManage' ,
						'c=org.opencomb.opencms.article.CreateArticle' ,
						'c=org.opencomb.opencms.article.EditArticle' ,
					)
				),
				array(
					'title'=>'栏目管理',
					'link'=>'?c=org.opencomb.opencms.category.CategoryManage',
					'query'=> array(
						'c=org.opencomb.opencms.category.CategoryManage' ,
						'c=org.opencomb.opencms.category.CreateCategory' ,
					)
				),
				array(
					'title'=>'首页设置',
					'link'=>'?c=org.opencomb.opencms.index.IndexManage',
					'query'=>'c=org.opencomb.opencms.index.IndexManage'
				),
				array(
					'title'=>'菜单设置',
					'link'=>'?c=org.opencomb.opencms.menu.MenuManage',
					'query'=>'c=org.opencomb.opencms.menu.MenuManage'
				),
			)
		)
	)
);