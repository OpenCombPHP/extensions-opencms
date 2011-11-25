<?php
return array(
	'table'=>'category',
	'name'=>'category',
	'hasMany:article'=>array(
		'fromkeys'=>'cid',
		'tokeys'=>'cid',
		'config'=>'model/orm/article'
	)
);