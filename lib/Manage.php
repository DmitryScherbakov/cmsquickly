<?php

class Manage
{

	private static $_instance;
	private static $_user;
	private static $adminLogin = 'admin';
	private static $adminPass = '123123';
	private static $permissions = 0; //3 все разрешено, 2 читать и редактировать (но не удалять), 1 только читать, 0 запрещено

	const SALT = 'saltvalue123';
	const READ = 1;
	const CHANGE = 2;
	const ALL = 3;

	private function __construct()
	{
		$this->init();
	}

	public static function __callStatic($name, $args)
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self();
		}
		//return self::$_instance->$name(implode(', ', $args));
		return call_user_func_array([self::$_instance, $name], $args);
	}

	private function init()
	{
		if (isset($_SESSION['secret'])) {
			if ($_SESSION['secret'] === md5(self::$adminLogin . self::SALT . self::$adminPass . $this->getIp())) {
				self::$permissions = self::ALL;
			} else if ($manager = Db::getRow("SELECT * FROM #__managers WHERE MD5(CONCAT(`email`,'" . self::SALT . "',`password`,'" . $this->getIp() . "'))=:key LIMIT 1", [':key' => $_SESSION['secret']])) {
				$manager['rules'] = empty($manager['rules']) ? [] : json_decode($manager['rules']);
				self::$_user = $manager;
			}
		}
	}

	private function logIn($login, $password)
	{
		if ($login === self::$adminLogin && $password === self::$adminPass) {
			$_SESSION['secret'] = md5(self::$adminLogin . self::SALT . self::$adminPass . $this->getIp());
			//это админ, позволяем ему всё
			self::$permissions = self::ALL;
		} else {
			$key = md5($login . self::SALT . $password . $this->getIp());
			$manager = Db::getRow("SELECT * FROM #__managers WHERE MD5(CONCAT(`email`,'" . self::SALT . "',`password`,'" . $this->getIp() . "'))=:key LIMIT 1", [':key' => $key]);
			if ($manager) {
				$manager['rules'] = empty($manager['rules']) ? [] : json_decode($manager['rules']);
				self::$_user = $manager;
				$_SESSION['secret'] = $key;
			}
		}
	}

	private function logOut()
	{
		$_SESSION['secret'] = null;
	}

	private function getIp()
	{
		return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	}

	private function checkPermissions($compName, $type)
	{
		if(!$this->allowed($compName, $type)) {
			header('HTTP/1.1 403 Forbidden');
			exit;
		}
	}

	private function allowed($compName, $type)
	{
		if(self::$permissions === self::ALL) {
			return true;
		} else if(isset(self::$_user['rules']) && isset(self::$_user['rules'][$compName])) {
			if(max(self::$permissions, self::$_user['rules'][$compName]) === self::ALL) { //все разрешено, так что дальше проверять бессмысленно
				return true;
			} else if($type === self::CHANGE && max(self::$permissions, self::$_user['rules'][$compName]) >= self::CHANGE) {
				return true;
			} else if($type === self::READ && max(self::$permissions, self::$_user['rules'][$compName]) >= self::READ) {
				return true;
			}
			return false;
		}
		return false;
	}

}
