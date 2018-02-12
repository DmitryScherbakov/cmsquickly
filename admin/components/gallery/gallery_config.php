<?php

return array(
	'image' => [
		'width' => 900,
		'height' => 600,
		'thumb_width' => 270,
		'thumb_height' => 179,
	],
	'db' => [
		'gallery' => ['pid' => 'int', 'link' => 'string'],
		'gallery_lang' => ['title' => 'string', 'meta_title' => 'string', 'meta_descr' => 'string', 'text' => 'html'],
		'photos' => ['gal_id' => 'int'],
		'photos_lang' => ['title' => 'string'],
	],
);
