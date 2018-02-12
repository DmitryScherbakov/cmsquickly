<?php

class AdminHtml extends Html
{

	private $treePaddingPx = 10;
	private $topMenu;

	function getTopMenu()
	{
		if ($this->topMenu == NULL) {
			$topMenu = [];

			$d = dir(COMPS);
			while (false !== ($entry = $d->read())) {
				$paramsFile = COMPS . $entry . DIRECTORY_SEPARATOR . $entry . '_params.php';
				if ($entry != '.' && $entry != '..' && file_exists($paramsFile)) {
					$tmp = include($paramsFile);
					if ($tmp['topmenu_name']) {
						$topMenu[$entry] = [
							'link' => $entry,
							'title' => _t($tmp['topmenu_name']),
							'subitems' => !empty($tmp['topmenu_subitems']) ? $tmp['topmenu_subitems'] : [],
							'ordering' => !empty($tmp['ordering']) ? $tmp['ordering'] : 100,
						];
					}
				}
			}
			$d->close();
			usort($topMenu, function($al, $bl) {
				if ($al['ordering'] == $bl['ordering']) {
					return 0;
				}
				return ($al['ordering'] > $bl['ordering']) ? +1 : -1;
			});

			$this->topMenu = $topMenu;
		}
		return $this->topMenu;
	}

	public function printAlerts()
	{
		if (!isset($_SESSION['alerts'])) {
			return;
		}
		echo '<div class="row">', "\n";
		//types: success, info, warning, danger
		foreach ($_SESSION['alerts'] as $type => $rows) {
			while ($text = array_pop($_SESSION['alerts'][$type])) {
				echo '<div class="alert alert-' . $type . ' alert-dismissible" role="alert">'
				. '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
				. $text
				. "</div>\n";
			}
		}
		echo '</div>', "\n";
	}

	public function printTableTree($tree, $level, $fieldsToPrint, $extraParams = '', $extraActionsClass='')
	{
		for ($i = 0; $i < sizeof($tree); $i++) {
			echo "<tr>\n";
			$setPadding = true;
			foreach ($tree[$i] as $class => $value) {
				if (in_array($class, $fieldsToPrint)) {
					echo '<td class="data_' . $class . '"';
					if ($setPadding) {
						echo ' style="padding-left:' . ($level * $this->treePaddingPx) . 'px"';
						$setPadding = false;
					}
					echo '>' . $value . '</td>', "\n";
				}
			}
			echo '<td class="data_actions'.($extraActionsClass ? ' '.$extraActionsClass : '').'">';
			$this->printActions($tree[$i]['id'], $tree[$i]['active'], $extraParams);
			echo '</td>', "\n";
			echo "<tr>\n";
			if (!empty($tree[$i]['children'])) {
				$this->printTableTree($tree[$i]['children'], $level + 1, $fieldsToPrint, $extraParams);
			}
		}
	}

	public function printActions($id, $active, $extraParams = '')
	{
		//edit
		echo '<a href="?act=edit&amp;id=' . $id . $extraParams . '" class="btn btn-default" aria-label="Edit">'
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

}
