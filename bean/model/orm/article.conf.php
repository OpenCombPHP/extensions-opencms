<?php
return array(
	'table'=>'article',
	'name'=>'article',
	'belongsTo:post'=>array(
		'fromkeys'=>'pid',
		'tokeys'=>'pid',
		'config'=>'basepost:model/orm/post'
	)
);