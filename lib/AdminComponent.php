<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminComponent
 *
 * @author User
 */
class AdminComponent extends Component
{

	function initHtml()
	{
		return new AdminHtml($this->myPage);
	}

	function actionActive($redirectUrl = '')
	{
		$sql = 'UPDATE `#__' . $this->dbTable . '` SET `active`=`active` XOR 1 WHERE id=:id LIMIT 1';
		Db::query($sql, [':id' => intval($_GET['id'])]);
		$this->myHtml->redirect($redirectUrl ? $redirectUrl : $this->component);
	}

	function actionMoveUp($redirectUrl = '')
	{
		$id = intval($_GET['id']);
		$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE id=:id LIMIT 1';
		$row = Db::getRow($sql, [':id' => $id]);
		if (array_key_exists('pid', $row)) { //this is a tree
			$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE pid=:pid AND ordering < :ordering ORDER BY ordering DESC LIMIT 1';
			$upper = Db::getRow($sql, [':pid' => $row['pid'], ':ordering' => $row['ordering']]);
		} else { //this is a list
			$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE ordering < :ordering ORDER BY ordering DESC LIMIT 1';
			$upper = Db::getRow($sql, [':ordering' => $row['ordering']]);
		}
		if ($upper) {
			$sql = 'UPDATE `#__' . $this->dbTable . '` SET `ordering`=:ordering WHERE id=:id LIMIT 1';
			Db::batchQuery($sql, [
				[':ordering' => $upper['ordering'], ':id' => $row['id']],
				[':ordering' => $row['ordering'], ':id' => $upper['id']]
			]);
		}
		$this->myHtml->redirect($redirectUrl ? $redirectUrl : $this->component);
	}

	function actionMoveDown($redirectUrl = '')
	{
		$id = intval($_GET['id']);
		$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE id=:id LIMIT 1';
		$row = Db::getRow($sql, [':id' => $id]);
		if (array_key_exists('pid', $row)) { //this is a tree
			$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE pid=:pid AND ordering > :ordering ORDER BY ordering ASC LIMIT 1';
			$upper = Db::getRow($sql, [':pid' => $row['pid'], ':ordering' => $row['ordering']]);
		} else { //this is a list
			$sql = 'SELECT * FROM `#__' . $this->dbTable . '` WHERE ordering > :ordering ORDER BY ordering ASC LIMIT 1';
			$upper = Db::getRow($sql, [':ordering' => $row['ordering']]);
		}
		if ($upper) {
			$sql = 'UPDATE `#__' . $this->dbTable . '` SET `ordering`=:ordering WHERE id=:id LIMIT 1';
			Db::batchQuery($sql, [
				[':ordering' => $upper['ordering'], ':id' => $row['id']],
				[':ordering' => $row['ordering'], ':id' => $upper['id']]
			]);
		}
		$this->myHtml->redirect($redirectUrl ? $redirectUrl : $this->component);
	}

	function actionDelete($redirectUrl = '')
	{
		$id = intval($_GET['id']);
		if ($id) {
			$this->deleteItem($id);
			$this->myHtml->addAlert('success', _t('Element deleted'));
		}
		$this->myHtml->redirect($redirectUrl ? $redirectUrl : $this->component);
	}

	protected function deleteItem($id)
	{
		$row = Db::getRow("SELECT * FROM #__{$this->dbTable} WHERE id=:id", [':id' => $id]);
		if ($row) {
			if (array_key_exists('pid', $row)) {
				$sql = "UPDATE #__{$this->dbTable} SET `ordering`=`ordering`-1 WHERE `pid`=:pid AND `ordering` > :ordering";
				Db::query($sql, [':pid' => $row['pid'], ':ordering' => $row['ordering']]);

				$children = Db::getAll("SELECT * FROM #__{$this->dbTable} WHERE pid=:id", [':id' => $row['id']]);
				if ($children) {
					for ($i = 0, $len = sizeof($children); $i < $len; $i++) {
						$this->deleteItem($children[$i]['id']);
					}
				}
			} else {
				$sql = "UPDATE #__{$this->dbTable} SET `ordering`=`ordering`-1 WHERE `ordering` > :ordering";
				Db::query($sql, [':ordering' => $row['ordering']]);
			}
			$deleteImages = !empty($row['image']) && defined('IMGDIR');
			if($deleteImages && file_exists(IMGDIR . $row['image'])) {
				unlink(IMGDIR . $row['image']);
			}
			if($deleteImages && file_exists(IMGDIR . 'thumb_' . $row['image'])) {
				unlink(IMGDIR . 'thumb_' . $row['image']);
			}
			Db::query("DELETE FROM #__{$this->dbTable}_lang WHERE fid=:id ", [':id' => $row['id']]);
			Db::query("DELETE FROM #__{$this->dbTable} WHERE id=:id ", [':id' => $row['id']]);
		}
	}
}
