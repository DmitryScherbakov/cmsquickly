<?php

define('IMGDIR', ROOT . 'images' . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR);
define('COMPURL', ROOTURL . 'home/');

class Home extends AdminComponent
{

	function actionAdd()
	{
		$this->myHtml->page_title = _t('Add slider image');
		$this->myHtml->meta_title = _t('Add new slider image');
		$this->myHtml->data = [];
		$this->myHtml->render('slider_edit');
	}

	function actionEdit()
	{
		$this->myHtml->page_title = _t('Edit slider image');
		$this->myHtml->meta_title = _t('Edit slider image');
		$this->myHtml->data = Db::getRow('SELECT * FROM #__slider WHERE id=:id', [':id' => intval($_GET['id'])]);
		if ($this->myHtml->data) {
			$this->myHtml->data['titles'] = json_decode($this->myHtml->data['titles'], true);
		}
		$this->myHtml->render('slider_edit');
	}

	function actionSave()
	{
		$id = intval($_POST['id']);
		//prepare main part
		$data['titles'] = json_encode($_POST['titles']);
		if (!empty($_FILES['image']['name'])) {
			$img = Form::uploadFile('image', IMGDIR);
			if ($img) {
				Img::crop(IMGDIR . $img, IMGDIR . $img, $this->componentConfig['slide_width'], $this->componentConfig['slide_height']);
				Img::crop(IMGDIR . $img, IMGDIR . 'thumb_' . $img, $this->componentConfig['thumb_width'], $this->componentConfig['thumb_height']);
				$data['image'] = $img;
			}
		}
		//this is update
		if ($id) {
			$oldItem = Db::getRow('SELECT * FROM #__slider WHERE id=:id', [':id' => $id]);
			if (!empty($data['image']) && $oldItem['image'] != '' && file_exists(IMGDIR . $oldItem['image'])) {
				unlink(IMGDIR . $oldItem['image']);
				unlink(IMGDIR . 'thumb_' . $oldItem['image']);
			}
			Db::update('slider', $data, 'id=:id', [':id' => $id]);
		} else { //this is a new record
			$sql = "SELECT MAX(`ordering`) FROM #__slider";
			$ordering = intval(Db::getVal($sql));
			$data['ordering'] = $ordering ? $ordering + 1 : 1;
			$data['active'] = 1;
			$id = Db::insert('slider', $data);
		}
		$this->myHtml->redirect('home');
	}

	function actionActive($redirectUrl = '')
	{
		$this->dbTable = 'slider';
		parent::actionActive();
	}

	function actionMoveUp($redirectUrl = '')
	{
		$this->dbTable = 'slider';
		parent::actionMoveUp();
	}

	function actionMoveDown($redirectUrl = '')
	{
		$this->dbTable = 'slider';
		parent::actionMoveDown();
	}

	function actionDelete($redirectUrl = '')
	{
		$this->dbTable = 'slider';
		parent::actionDelete();
	}

	function actionDefault()
	{
		$this->myHtml->page_title = _t('Home');
		$this->myHtml->meta_title = _t('Home');
		$this->myHtml->data = Db::getAll('SELECT * FROM #__slider ORDER BY ordering');
		$this->myHtml->render('home');
	}

}
