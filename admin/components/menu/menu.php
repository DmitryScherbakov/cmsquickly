<?php

class Menu extends AdminComponent
{

	function actionAdd()
	{
		$this->myHtml->page_title = 'Add menu item';
		$this->myHtml->meta_title = 'Add new item';
		$this->myHtml->items = $this->getItems($_GET['menuid']);
		$this->myHtml->data = [];
		$this->myHtml->menuid = $_GET['menuid'];
		$this->myHtml->render('edit');
	}

	function actionEdit()
	{
		$this->myHtml->page_title = 'Edit content page';
		$this->myHtml->meta_title = 'Edit page';
		$this->myHtml->items = $this->getItems($_GET['menuid']);
		$this->myHtml->data = Db::getRow('SELECT * FROM #__menu_items WHERE id=:id', [':id' => intval($_GET['id'])]);
		$this->myHtml->menuid = $_GET['menuid'];
		if ($this->myHtml->data) {
			foreach (Config::$aLanguages as $key => $value) {
				$this->myHtml->data[$key] = Db::getRow('SELECT * FROM #__menu_items_lang WHERE fid=:id AND lang=:lang', [':id' => intval($_GET['id']), ':lang' => $key]);
			}
		}
		$this->myHtml->render('edit');
	}

	function actionSave()
	{
		$id = intval($_POST['id']);
		$menuid = $_POST['menuid'];
		//prepare main part
		$data = Form::getPost(['pid' => 'int', 'menuid' => 'string', 'link' => 'string', 'newwindow' => 'checkbox']);
		//this is update
		if ($id) {
			$oldItem = Db::getRow('SELECT * FROM #__minu_items WHERE id=:id', [':id' => $id]);
			Db::update('menu_items', $data, 'id=:id', [':id' => $id]);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost(['title' => 'string'], $lang);
				if (intval($_POST['lang_id'][$lang])) {
					Db::update('menu_items_lang', $dataLang, 'id=:id', [':id' => intval($_POST['lang_id'][$lang])]);
				} else {
					$dataLang['fid'] = $id;
					$dataLang['lang'] = $lang;
					Db::insert('menu_items_lang', $dataLang);
				}
			}
		} else { //this is a new record
			$data['ordering'] = Db::getNextTreeOrdering('menu_items', $data['pid'], " AND `menuid`='$menuid'");
			$id = Db::insert('menu_items', $data);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost(['title' => 'string'], $lang);
				$dataLang['fid'] = $id;
				$dataLang['lang'] = $lang;
				Db::insert('menu_items_lang', $dataLang);
			}
		}
		$this->myHtml->redirect('menu?act=items&menuid=' . $menuid);
	}

	function actionItems()
	{
		if (isset($_GET['menuid'])) {
			$menuid = $_GET['menuid'];
			$this->myHtml->menuid = $menuid;
			$this->myHtml->page_title = 'Menu ID: ' . $menuid;
			$this->myHtml->meta_title = 'Menu ' . $menuid;
			$this->myHtml->data = Db::getAsTree('menu_items', Config::$adminLanguage, 'm.id AS id, l.title AS title, m.link AS link, m.active AS active', " AND m.`menuid`='$menuid'");
			$this->myHtml->fieldsToPrint = ['title', 'link'];
			$this->myHtml->render('items');
		} else {
			$this->myHtml->redirect('menu');
		}
	}

	function actionSaveMenu()
	{
		$sql = "REPLACE INTO #__menu SET menuid = :menuid";
		Db::query($sql, [':menuid' => $_GET['menuid']]);
		$this->myHtml->addAlert('success', _t('Menu added'));
		$this->myHtml->redirect('menu');
	}

	function actionDeleteMenu()
	{
		Db::query("DELETE FROM #__menu WHERE menuid = :menuid", [':menuid' => $_GET['menuid']]);
		$sth = Db::prepare('SELECT id FROM #__menu_items WHERE menuid=:menuid');
		$sth->execute([':menuid' => $_GET['menuid']]);
		$rows = $sth->fetchAll(PDO::FETCH_COLUMN);
		Db::query("DELETE FROM #__menu_items WHERE menuid = :menuid", [':menuid' => $_GET['menuid']]);
		$this->myHtml->addAlert('success', _t('Menu deleted'));
		$this->myHtml->redirect('menu');
	}

	function actionDefault()
	{
		$this->myHtml->page_title = 'Menu';
		$this->myHtml->meta_title = 'Menu';
		$sql = "SELECT * FROM #__menu";
		$this->myHtml->data = Db::getAll($sql);
		$this->myHtml->fieldsToPrint = ['title', 'link'];
		$this->myHtml->render('index');
	}

	function getItems($menuid)
	{
		$default = [['id' => 0, 'title' => _t('Top level')]];
		$links = Db::getAsTree('menu_items', $this->myHtml->lang, 'm.id AS id, l.title AS title, m.link', " AND m.`menuid`='$menuid'");

		return array_merge($default, $links);
	}

	function actionActive($redirectUrl = '')
	{
		$this->dbTable = 'menu_items';
		parent::actionActive('menu/?act=items&menuid=' . $_GET['menuid']);
	}

	function actionMoveUp($redirectUrl = '')
	{
		$this->dbTable = 'menu_items';
		parent::actionMoveUp('menu/?act=items&menuid=' . $_GET['menuid']);
	}

	function actionMoveDown($redirectUrl = '')
	{
		$this->dbTable = 'menu_items';
		parent::actionMoveDown('menu/?act=items&menuid=' . $_GET['menuid']);
	}

	function actionDelete($redirectUrl = '')
	{
		$this->dbTable = 'menu_items';
		parent::actionDelete('menu/?act=items&menuid=' . $_GET['menuid']);
	}
}
