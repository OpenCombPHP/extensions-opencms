<?php
return array(
	'id'=>'article_content',
	'class'=>'richText',
	'title'=>'文章内容',
	'configuration'=>' toolbar : "Full" ',
	'exchange'=>'text',
	'verifier:notempty'=>array(),
	'verifier:length'=>array(
		'min'=>6
	)
);