<?php

require('security.php');

if (!defined('AUTHORIZED')) {
	header('HTTP/1.0 401 Unauthorized');
	die('Authorization Required.');
}

$pos = strpos($_SERVER['REQUEST_URI'], '?');
if ($pos) {
	$sRawUrl = trim(substr($_SERVER['REQUEST_URI'], 0, $pos), '/');
} else {
	$sRawUrl = trim($_SERVER['REQUEST_URI'], '/');
}
define('ROOTURL', '/' . ADMINDIR . '/');
define('SCRIPTROOT', ROOT . ADMINDIR . DIRECTORY_SEPARATOR);
define('TMPLS', SCRIPTROOT . 'templates' . DIRECTORY_SEPARATOR);
define('COMPS', SCRIPTROOT . 'components' . DIRECTORY_SEPARATOR);

$myPage = new Page($sRawUrl);
define('PAGELANG', $myPage->lang);
require SCRIPTROOT . 'funcs.php';
define('COMPROOT', COMPS . $myPage->component . DIRECTORY_SEPARATOR);
define('COMPTMPL', TMPLS . $myPage->component . DIRECTORY_SEPARATOR);


$selectedComponent = COMPROOT . $myPage->component;
if (file_exists($selectedComponent . '.php')) {

	Manage::checkPermissions($myPage->component, Manage::READ);

	include($selectedComponent . '.php');
	$componentClass = ucfirst($myPage->component);
	new $componentClass($myPage);
} else {
	header('HTTP/1.0 404 Not Found');
	die('No such component.');
}