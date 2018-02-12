<?php

class treeMenu
{

	private $myPage;

	function __construct($linkToPage, $params)
	{
		$this->myPage = $linkToPage;
		$menuId = $params['menuid'];
		$items = Db::getAsTree('menu_items', $this->myPage->lang, 'm.id AS id, l.title AS title, m.link', " AND m.`menuid`='$menuId' AND m.`active`=1");
		$this->printMenu($menuId, $items);
	}

	function printMenu($menuId, $items, $isSubmenu = false)
	{
		echo $isSubmenu ? '<ul class="submenu">' : '<ul id="' . $menuId . '">';
		for ($i = 0, $len = sizeof($items); $i < $len; $i++) {
			echo "\n", '<li><a href="' . $this->myPage->url($items[$i]['link']) . '">' . $items[$i]['title'] . '</a>';
			if (!empty($items[$i]['children'])) {
				$this->printMenu($menuId, $items[$i]['children'], true);
			}
		}
		echo "\n</ul>\n";
	}

}
