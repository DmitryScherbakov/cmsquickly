<?php
return array(
	'image' => array(
		'width' => 900,
		'height' => 600,
		'thumb_width' => 270,
		'thumb_height' => 179,
	),

	'main_fields' => array(
		'pid' => 'int',
		'link' => 'string',
		'link_code' => 'md5(link)',
		'active'=>'checkbox',
		'order' => 'int',
		'variables' => 'string',
	),
	'lang_fields' => array(
		'lang' => 'string',
		'title' => 'string',
		'text' => 'html',
		'page_title' => 'string',
		'meta_kw' => 'string',
		'meta_descr' => 'string'
	),
);
?>