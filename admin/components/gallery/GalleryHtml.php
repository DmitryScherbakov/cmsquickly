<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GalleryPage
 *
 * @author User
 */
class GalleryHtml extends AdminHtml
{
	public function printActions($id, $active, $extraParams = '')
	{
		//edit
		echo '<a href="?act=photos&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Edit">'
		. '<span class="glyphicon glyphicon glyphicon-picture" aria-hidden="true"></span>'
		. '</a>'
		. '<a href="?act=edit&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Edit">'
		. '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>'
		. '</a>'
		//delete
		. '<a href="?act=delete&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Remove">'
		. '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>'
		. '</a>'
		//move down
		. '<a href="?act=move_down&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Move Down">'
		. '<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>'
		. '</a>'
		//move up
		. '<a href="?act=move_up&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Move Up">'
		. '<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>'
		. '</a>'
		//activate / deactivate
		. '<a href="?act=active&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Visibility">'
		. '<span class="glyphicon glyphicon-eye-' . ($active ? 'open' : 'close') . '" aria-hidden="true"></span>'
		. '</a>';
	}

	public function getPhotoActions($id, $active, $extraParams = '')
	{
		//edit
		return '<a href="?act=edit_photo&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Edit">'
		. '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>'
		. '</a>'
		//delete
		. '<a href="?act=delete_photo&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Remove">'
		. '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>'
		. '</a>'
		//move down
		. '<a href="?act=move_down_photo&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Move Down">'
		. '<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>'
		. '</a>'
		//move up
		. '<a href="?act=move_up_photo&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Move Up">'
		. '<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>'
		. '</a>'
		//activate / deactivate
		. '<a href="?act=active_photo&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Visibility">'
		. '<span class="glyphicon glyphicon-eye-' . ($active ? 'open' : 'close') . '" aria-hidden="true"></span>'
		. '</a>';
	}

	public function printPhotoActions($id, $active, $extraParams = '')
	{
		echo $this->getPhotoActions($id, $active, $extraParams);
	}
}
