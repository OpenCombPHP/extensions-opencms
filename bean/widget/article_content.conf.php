<?php
return array(
	'id'=>'article_content',
	'class'=>'text',
	'type'=>'multiple',
	'title'=>'文章内容',
	'exchange'=>'text',
	'verifier:notempty'=>array(),
	'verifier:length'=>array(
		'min'=>6
	)
);