<?php
return array(
	'id'=>'category_title',
	'class'=>'text',
	'title'=>'分类标题',
	'exchange'=>'title',
	'verifier:notempty'=>array(),
	'verifier:length'=>array(
			'min'=>2,
			'max'=>255)
);