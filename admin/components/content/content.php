<?php

define('IMGDIR', ROOT . 'images' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR);

class Content extends AdminComponent
{

	protected $dbTable = 'content';

	function actionAdd()
	{
		$this->myHtml->page_title = _t('Add content page');
		$this->myHtml->meta_title = _t('Add new page');
		$this->myHtml->pages = $this->getPages();
		$this->myHtml->galleries = $this->getGalleries();
		$this->myHtml->templates = $this->getTemplates();
		$this->myHtml->data = [];
		$this->myHtml->render('edit');
	}

	function actionEdit()
	{
		$this->myHtml->page_title = _t('Edit content page');
		$this->myHtml->meta_title = _t('Edit page');
		$this->myHtml->pages = $this->getPages();
		$this->myHtml->galleries = $this->getGalleries();
		$this->myHtml->templates = $this->getTemplates();
		$this->myHtml->data = Db::getRow('SELECT * FROM #__content WHERE id=:id', [':id' => intval($_GET['id'])]);
		if ($this->myHtml->data) {
			foreach (Config::$aLanguages as $key => $value) {
				$this->myHtml->data[$key] = Db::getRow('SELECT * FROM #__content_lang WHERE fid=:id AND lang=:lang', [':id' => intval($_GET['id']), ':lang' => $key]);
			}
		}
		$this->myHtml->render('edit');
	}

	function actionSave()
	{
		$id = intval($_POST['id']);
		//prepare main part
		$data = Form::getPost(['pid' => 'int', 'gal_id' => 'int', 'link' => 'string', 'template' => 'string']);
		$data['link_code'] = md5($data['link']);
		if (!empty($_FILES['image']['name'])) {
			$img = Form::uploadFile('image', IMGDIR);
			if ($img) {
				Img::crop(IMGDIR . $img, IMGDIR . 'thumb_' . $img, $this->componentConfig['image']['thumb_width'], $this->componentConfig['image']['thumb_height']);
				Img::resize(IMGDIR . $img, $this->componentConfig['image']['width'], $this->componentConfig['image']['height']);
				$data['image'] = $img;
			}
		}
		//this is update
		if ($id) {
			$oldItem = Db::getRow('SELECT * FROM #__content WHERE id=:id', [':id' => $id]);
			if (!empty($data['image']) && $oldItem['image'] != '') {
				if (file_exists(IMGDIR . $oldItem['image'])) {
					unlink(IMGDIR . $oldItem['image']);
				}
				if (file_exists(IMGDIR . 'thumb_' . $oldItem['image'])) {
					unlink(IMGDIR . 'thumb_' . $oldItem['image']);
				}
			}
			Db::update('content', $data, 'id=:id', [':id' => $id]);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost(['title' => 'string', 'meta_title' => 'string', 'meta_descr' => 'string', 'text' => 'html'], $lang);
				if (intval($_POST['lang_id'][$lang])) {
					Db::update('content_lang', $dataLang, 'id=:id', [':id' => intval($_POST['lang_id'][$lang])]);
				} else {
					$dataLang['fid'] = $id;
					$dataLang['lang'] = $lang;
					Db::insert('content_lang', $dataLang);
				}
			}
		} else { //this is a new record
			$data['ordering'] = Db::getNextTreeOrdering('content', $data['pid']);
			$id = Db::insert('content', $data);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost(['title' => 'string', 'meta_title' => 'string', 'meta_descr' => 'string', 'text' => 'html'], $lang);
				$dataLang['fid'] = $id;
				$dataLang['lang'] = $lang;
				Db::insert('content_lang', $dataLang);
			}
		}
		$this->myHtml->redirect('content');
	}

	function actionDefault()
	{
		$this->myHtml->page_title = _t('Content');
		$this->myHtml->meta_title = _t('Content');
		$this->myHtml->data = Db::getAsTree('content', Config::$adminLanguage, 'm.id AS id, l.title AS title, m.link AS link, m.active AS active');
		$this->myHtml->fieldsToPrint = ['title', 'link'];
		$this->myHtml->render('index');
	}

	function getGalleries()
	{
		//global $this->myPage;
		$default = [['id' => 0, 'title' => _t('None')]];
		$galleries = Db::getAsTree('gallery', Config::$adminLanguage, 'm.id AS id, l.title AS title');

		return array_merge($default, $galleries);
	}

	function getPages()
	{
		//global $this->myPage;
		$tree = Db::getAsTree('content', $this->myHtml->lang, 'm.id AS id, l.title AS title');
		array_unshift($tree, ['id' => 0, 'title' => _t('None')]);
		return $tree;
	}

	function getTemplates()
	{
		$templates = [];
		$path = ROOT . 'templates' . DIRECTORY_SEPARATOR;
		$files = $this->getFilesList($path);
		foreach ($files as $filename) {
			if (is_file($path . $filename)) {
				$templates[] = $filename;
			} else {
				$subfiles = $this->getFilesList($path . $filename . DIRECTORY_SEPARATOR);
				foreach ($subfiles as $subfilename) {
					if (is_file($path . $filename . DIRECTORY_SEPARATOR . $subfilename)) {
						$templates[] = $filename . DIRECTORY_SEPARATOR . $subfilename;
					}
				}
			}
		}
		return array_combine(array_values($templates), $templates);
	}

	function getFilesList($path)
	{
		$files = [];
		$dirs = [];
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				if (is_file($path . $file)) {
					$files[] = $file;
				} else if (is_dir($path . $file)) {
					$dirs[] = $file;
				}
			}
			closedir($handle);
		}
		sort($dirs);
		sort($files);
		return array_merge($dirs, $files);
	}

}
