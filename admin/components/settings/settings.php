<?php

class Settings extends AdminComponent
{
	/*
	 * Ajax method
	 */

	function actionSitemap()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'
			. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$rows = Db::getAll("SELECT * FROM #__content WHERE active=1");
		$address = 'https://' . DOMAIN . '/';
		foreach ($rows as $row) {
			$xml .= '<url><loc>' . $address . $row['link'] . '</loc>';
			if (isset($row['lastmod']) && is_numeric($row['lastmod'])) {
				$xml .= '<lastmod>' . date('Y-m-d', $row['lastmod']) . '</lastmod>';
			}
			$xml .= '</url>';
		}
		$xml .= '</urlset>';

		$sitemap = ROOT . 'sitemap.xml';

		file_put_contents($sitemap, $xml);

		if (!isset($_GET['ajax'])) {
			$this->myHtml->redirect('settings');
		} else {
			if ($xml === file_get_contents($sitemap)) {
				echo json_encode(['ok' => 1]);
			} else if (!file_exists($sitemap) || !is_writable($sitemap)) {
				echo json_encode(['error' => _t('sitemap.xml is not writable')]);
			}
		}
	}

	function actionDefault()
	{
		$this->myHtml->page_title = 'Settings';
		$this->myHtml->meta_title = 'Settings';
		$this->myHtml->render('settings');
	}

	function getItems($menuid)
	{
		$default = [['id' => 0, 'title' => _t('Top level')]];
		$links = Db::getAsTree('menu_items', $this->myHtml->lang, 'm.id AS id, l.title AS title, m.link', " AND m.`menuid`='$menuid'");

		return array_merge($default, $links);
	}
}
