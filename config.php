<?php

class Config
{
	/* database section */

	public static $aDbConf = [
		'host' => 'localhost',
		'user' => 'user',
		'password' => 'password',
		'db_name' => 'dbname',
		'prefix' => 'prefix_',
		'show_sql_on_errors' => true,
	];

	/* languages section */
	public static $aLanguages = [
		'en' => 'english',
		'ru' => 'русский',
	];
	public static $defaultLanguage = 'en';
	public static $adminLanguage = 'en';

	/* cms defaults */
	public static $aPageDefaults = [
		'homeComponent' => 'home',
		'defaultComponent' => 'content',
	];

}

/* constants section */
define('DOMAIN', 'example.com');
define('DOMAIN_ROOT', 'example.com');
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('ADMINDIR', 'admin');
define('LIB', ROOT . 'lib' . DIRECTORY_SEPARATOR);
define('PLUGINDIR', ROOT . 'plugins' . DIRECTORY_SEPARATOR);
define('CACHE', ROOT . 'cache' . DIRECTORY_SEPARATOR);

