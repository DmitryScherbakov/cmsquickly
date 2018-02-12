<?php

define('IMGDIR', ROOT . 'images' . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR);
define('PHOTODIR', ROOT . 'images' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR);
define('COMPURL', ROOTURL . 'gallery/');
require(COMPROOT . 'GalleryHtml.php');

class Gallery extends AdminComponent
{

	function actionAdd()
	{
		$this->myHtml->page_title = _t('Add gallery');
		$this->myHtml->meta_title = _t('Add new gallery');
		$this->myHtml->galleries = $this->getGalleries();
		$this->myHtml->data = [];
		$this->myHtml->render('edit');
	}

	function actionEdit()
	{
		$this->myHtml->page_title = 'Edit gallery';
		$this->myHtml->meta_title = 'Edit gallery';
		$this->myHtml->galleries = $this->getGalleries();
		$this->myHtml->data = Db::getRow('SELECT * FROM #__gallery WHERE id=:id', [':id' => intval($_GET['id'])]);
		if ($this->myHtml->data) {
			foreach (Config::$aLanguages as $key => $value) {
				$this->myHtml->data[$key] = Db::getRow('SELECT * FROM #__gallery_lang WHERE fid=:id AND lang=:lang', [':id' => intval($_GET['id']), ':lang' => $key]);
			}
		}
		$this->myHtml->render('edit');
	}

	function actionSave()
	{
		$id = intval($_POST['id']);
		//prepare main part
		$data = Form::getPost($this->componentConfig['db']['gallery']);
		$data['link_code'] = md5($data['link']);
		if (!empty($_FILES['image']['name'])) {
			$img = Form::uploadFile('image', IMGDIR);
			if ($img) {
				Img::resize(IMGDIR . $img, $this->componentConfig['image']['width'], $this->componentConfig['image']['height']);
				Img::crop(IMGDIR . $img, IMGDIR . 'thumb_' . $img, $this->componentConfig['image']['thumb_width'], $this->componentConfig['image']['thumb_height']);
				$data['image'] = $img;
			}
		}
		//this is update
		if ($id) {
			$oldItem = Db::getRow('SELECT * FROM #__gallery WHERE id=:id', [':id' => $id]);
			if (!empty($data['image']) && $oldItem['image'] != '') {
				unlink(IMGDIR . $oldItem['image']);
				unlink(IMGDIR . 'thumb_' . $oldItem['image']);
			}
			Db::update('gallery', $data, 'id=:id', [':id' => $id]);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost($this->componentConfig['db']['gallery_lang'], $lang);
				if (intval($_POST['lang_id'][$lang])) {
					Db::update('gallery_lang', $dataLang, 'id=:id', [':id' => intval($_POST['lang_id'][$lang])]);
				} else {
					$dataLang['fid'] = $id;
					$dataLang['lang'] = $lang;
					Db::insert('gallery_lang', $dataLang);
				}
			}
		} else { //this is a new record
			$data['ordering'] = Db::getNextTreeOrdering('gallery', $data['pid']);
			$id = Db::insert('gallery', $data);
			if ($id) {
				foreach (Config::$aLanguages as $lang => $langTitle) {
					$dataLang = Form::getPost($this->componentConfig['db']['gallery_lang'], $lang);
					$dataLang['fid'] = $id;
					$dataLang['lang'] = $lang;
					Db::insert('gallery_lang', $dataLang);
				}
			} else {

			}
		}
		$this->myHtml->redirect('gallery');
	}

	function actionEditPhoto()
	{
		$this->myHtml->page_title = 'Edit photo';
		$this->myHtml->meta_title = 'Edit photo';
		$this->myHtml->galleries = $this->getGalleries();
		$this->myHtml->data = $this->getItemById(intval($_GET['id']), 'photos');
		$this->myHtml->render('photo_edit');
	}

	function actionSavePhoto()
	{
		$id = intval($_POST['id']);
		//this is update
		if ($id) {
			$data = Form::getPost($this->componentConfig['db']['photos']);
			if (!empty($_FILES['image']['name'])) {
				$img = Form::uploadFile('image', PHOTODIR);
				if ($img) {
					Img::resize(PHOTODIR . $img, $this->componentConfig['image']['width'], $this->componentConfig['image']['height']);
					Img::crop(PHOTODIR . $img, PHOTODIR . 'thumb_' . $img, $this->componentConfig['image']['thumb_width'], $this->componentConfig['image']['thumb_height']);
					$data['image'] = $img;
				}
			}

			$oldItem = Db::getRow('SELECT * FROM #__photos WHERE id=:id', [':id' => $id]);
			if (!empty($data['image']) && $oldItem['image'] != '') {
				unlink(IMGDIR . $oldItem['image']);
				unlink(IMGDIR . 'thumb_' . $oldItem['image']);
			}
			Db::update('photos', $data, 'id=:id', [':id' => $id]);
			foreach (Config::$aLanguages as $lang => $langTitle) {
				$dataLang = Form::getPost($this->componentConfig['db']['photos_lang'], $lang);
				if (intval($_POST['lang_id'][$lang])) {
					Db::update('photos_lang', $dataLang, 'id=:id', [':id' => intval($_POST['lang_id'][$lang])]);
				} else if(isset($dataLang['title'])) {
					$dataLang['fid'] = $id;
					$dataLang['lang'] = $lang;
					Db::insert('photos_lang', $dataLang);
				}
			}
		}
		$this->myHtml->redirect('gallery?act=photos&id=' . $_POST['gal_id']);
	}

	//this is can be an AJAX call
	function actionUploadPhoto()
	{
		$galId = intval($_GET['id']);
		$response = ['success' => false, 'error' => false, 'name' => '', 'html' => ''];
		if (isset($_FILES['files']) && !empty($_FILES['files']['name']) && !is_array($_FILES['files']['name']) && $img = Form::uploadFile('files', PHOTODIR)) {
			$data = ['gal_id' => $galId, 'image' => $img];
			Img::resize(PHOTODIR . $img, $this->componentConfig['image']['width'], $this->componentConfig['image']['height']);
			Img::crop(PHOTODIR . $img, PHOTODIR . 'thumb_' . $img, $this->componentConfig['image']['thumb_width'], $this->componentConfig['image']['thumb_height']);
			$data['ordering'] = Db::getVal("SELECT MAX(ordering)+1 FROM #__photos WHERE gal_id=:galId", [':galId' => $galId]);

			$id = Db::insert('photos', $data);
			$response['success'] = true;
			$response['name'] = $_FILES['files']['name'];
			$response['html'] = '<tr><td><img src="/images/photos/thumb_' . $img . '" alt=""></td><td></td>
				<td class="data_actions">' . $this->myHtml->getPhotoActions($id, 1) . '</td></tr>';
		} else {
			$response['error'] = true;
			$response['html'] = _t('Upload failed. ') . implode('; ', Form::getErrors());
			$response['name'] = strval($_FILES['files']['name']);
		}

		if (isset($_POST['ajax'])) {
			echo json_encode($response);
		} else {
			$this->myHtml->redirect('gallery?act=photos&id=' . $galId);
		}
	}

	function actionPhotos()
	{
		$id = intval($_GET['id']);
		$gallery = Db::getRow("SELECT * FROM #__gallery_lang WHERE fid=:id AND lang=:lang", [':id' => $id, ':lang' => $this->lang]);
		$this->myHtml->page_title = _t('Gallery') . ' ' . $gallery['title'];
		$this->myHtml->meta_title = $this->myHtml->page_title;
		$sql = "SELECT m.id AS id, m.image, l.title, m.active AS active FROM #__photos AS m LEFT JOIN #__photos_lang AS l ON m.id=l.fid WHERE gal_id=:gal_id AND (l.lang=:lang OR l.lang IS NULL) ORDER BY m.ordering";
		$this->myHtml->photos = Db::getAll($sql, [':lang' => $this->lang, ':gal_id' => $id]);
		$this->myHtml->galId = $id;
		$this->myHtml->fieldsToPrint = ['title', 'image'];
		$this->myHtml->render('photos');
	}

	//
	function actionActivePhoto()
	{
		$this->dbTable = 'photos';
		parent::actionActive('gallery?act=photos&id=' . $_GET['gal_id']);
	}

	function actionMoveUpPhoto()
	{
		$this->dbTable = 'photos';
		parent::actionMoveUp('gallery?act=photos&id=' . $_GET['gal_id']);
	}

	function actionMoveDownPhoto()
	{
		$this->dbTable = 'photos';
		parent::actionMoveDown('gallery?act=photos&id=' . $_GET['gal_id']);
	}

	function actionDeletePhoto()
	{
		$this->dbTable = 'photos';
		parent::actionDelete('gallery?act=photos&id=' . $_GET['gal_id']);
	}

	function actionDefault()
	{
		$this->myHtml->page_title = 'Gallery';
		$this->myHtml->meta_title = 'Gallery';
		$this->myHtml->data = Db::getAsTree('gallery', Config::$adminLanguage, 'm.id AS id, l.title AS title, m.link AS link, m.active AS active');
		$this->myHtml->fieldsToPrint = ['title', 'link'];
		$this->myHtml->render('index');
	}

	function getGalleries()
	{
		$default = [['id' => 0, 'title' => _t('None')]];
		$tree = Db::getAsTree('gallery', $this->myHtml->lang, 'm.id AS id, l.title AS title');

		return array_merge($default, $tree);
	}

	function initHtml()
	{
		return new GalleryHtml($this->myPage);
	}

}
