<?php
$start_time = microtime(true);
session_start();
$useCache = false;
/* url parsing */
$pos = strpos($_SERVER['REQUEST_URI'], '?');
if ($pos) {
	$sRawUrl = trim(substr($_SERVER['REQUEST_URI'], 0, $pos), '/');
} else {
	$sRawUrl = trim($_SERVER['REQUEST_URI'], '/');
}
$cachefile = 'cache/' . md5($sRawUrl) . '.htm';
if ($useCache && file_exists($cachefile)) {
	readfile($cachefile);
	flush();
	if (time() - 7200 > filemtime($cachefile)) {
		unlink($cachefile);
	}
} else {
	/* configuration */
	require_once('config.php');
	require_once('init.php');
	require_once('funcs.php');
	define('ROOTURL', '/');
	define('SCRIPTROOT', ROOT);
	define('TMPLS', SCRIPTROOT . 'templates' . DIRECTORY_SEPARATOR);
	define('COMPS', SCRIPTROOT . 'components' . DIRECTORY_SEPARATOR);

	$myPage = new Page($sRawUrl);

	define('PAGELANG', $myPage->lang);
	define('COMPROOT', COMPS . $myPage->component . DIRECTORY_SEPARATOR);
	define('COMPTMPL', TMPLS . $myPage->component . DIRECTORY_SEPARATOR);
	if ($useCache) {
		ob_start();
	}

	include(COMPROOT . $myPage->component . '.php');
	$componentClass = ucfirst($myPage->component);
	new $componentClass($myPage);

	if ($useCache) {
		file_put_contents($cachefile, ob_get_flush());
	}
}
//echo '<p>Total execution time is '.(microtime(true) - $start_time).'s</p>';