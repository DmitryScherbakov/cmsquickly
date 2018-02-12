<?php
$aLang = [];
if(file_exists(SCRIPTROOT . 'langs/' . PAGELANG . '.php')) {
	$aLang = include(SCRIPTROOT . 'langs/' . PAGELANG . '.php');
}

function _t($text) {
	 global $aLang;
	return array_key_exists($text, $aLang) ? $aLang[$text] : $text;
}

