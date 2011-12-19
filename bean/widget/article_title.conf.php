<?php
return array(
	'id'=>'article_title',
	'class'=>'text',
	'title'=>'文章标题',
	'exchange'=>'title',
	'verifier:notempty'=>array(),
	'verifier:length'=>array(
			'min'=>2,
			'max'=>255)
);