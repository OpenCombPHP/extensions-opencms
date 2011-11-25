<?php
return array(
	'table'=>'article',
	'name'=>'article',
	'belongsTo:post'=>array(
		'fromkeys'=>'pid',
		'tokeys'=>'pid',
		'config'=>'basepost:model/orm/post'
	),
	'belongsTo:category'=>array(
		'fromkeys'=>'cid',
		'tokeys'=>'cid',
		'config'=>'model/orm/category'
	)
);