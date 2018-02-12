<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Component
 *
 * @author User
 */
class Component
{

	protected $componentConfig = [];
	protected $myPage;
	protected $myHtml;
	protected $dbTable;

	function __construct($currentPage)
	{
		$this->myPage = $currentPage;
		$this->dbTable = strtolower(get_class($this));

		if (file_exists(COMPROOT . $this->component . '_config.php')) {
			$this->componentConfig = require(COMPROOT . $this->component . '_config.php');
		}

		if (method_exists($this, 'initHtml')) {
			$this->myHtml = $this->initHtml();
		} else {
			$this->myHtml = new Html($this->myPage);
		}

		//get possible action from url
		$action = $currentPage->possibleAction !== '' ? 'action' . implode(array_map('ucfirst', explode('_', $currentPage->possibleAction))) : 'actionDefault';
		$method = isset($_REQUEST['act']) ? 'action' . implode(array_map('ucfirst', explode('_', $_REQUEST['act']))) : $action;
		if (method_exists($this, $method)) {
			$this->$method();
		} else {
			$this->actionDefault();
		}
	}

	public function &__get($name)
	{
		if(isset($this->myPage->$name)) {
			return $this->myPage->$name;
		}
		return null;
	}

	protected function getItemById($id, $dbTable='') {
		if(!$dbTable) {
			$dbTable = $this->dbTable;
		}
		$item = Db::getRow("SELECT * FROM #__{$dbTable} WHERE id=:id", [':id' => $id]);
		if ($item) {
			$defaults = array_fill_keys(array_keys($this->componentConfig['db'][$dbTable.'_lang']), '');
			foreach (Config::$aLanguages as $key => $value) {
				$langData = Db::getRow("SELECT * FROM #__{$dbTable}_lang WHERE fid=:id AND lang=:lang", [':id' => $id, ':lang' => $key]);
				if($langData) {
					$item[$key] = $langData;
				} else {
					$item[$key] = $defaults;
				}
			}
		}
		return $item;
	}

}
