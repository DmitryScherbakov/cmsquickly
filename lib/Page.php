<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page
 *
 * @author User
 */
class Page
{
	public $lang;
	public $component;
	public $rawUrl = '';
	public $compUrl = '';
	public $possibleAction = '';

	public function __construct($pageUrl)
	{
		$this->rawUrl = $pageUrl;
		$this->component = Config::$aPageDefaults['homeComponent'];
		$this->lang = Config::$defaultLanguage;
		$parts = explode('/', $pageUrl);
		if ($pageUrl && is_array($parts) && $parts[0] != '') {
			if ($parts[0] == ADMINDIR) {
				array_shift($parts);
			}
			if (in_array($parts[0], array_keys(Config::$aLanguages))) {
				$this->lang = $parts[0];
				array_shift($parts);
				$this->compUrl = implode('/', $parts);
			}
			if (!empty($parts[0])) {
				if (self::componentExists($parts[0])) {
					$this->component = $parts[0];
					array_shift($parts);
					$this->possibleAction = isset($parts[0]) ? $parts[0] : '';
					$this->compUrl = implode('/', $parts);
				} else {
					$this->component = Config::$aPageDefaults['defaultComponent'];
					$this->possibleAction = isset($parts[0]) ? $parts[0] : '';
					$this->compUrl = implode('/', $parts);
				}
			}
		}
	}

	public static function componentExists($componentName)
	{
		return file_exists(COMPS . basename($componentName));
	}
}
