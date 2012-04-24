<?php
return array(
	'class'=>'model',
	'orm'=>array(
		'table'=>'opencms:category',
		'hasMany:article'=>array(
				'fromkeys'=>'cid',
				'tokeys'=>'cid',
				'config'=>'model/orm/article'
		)
	)
);