<?php

class Content extends Component
{

	function actionDefault()
	{
		$data = Db::getContentDataByUrl($this->myHtml->compUrl, $this->myHtml->lang);
		if ($data) {
			$subPages = Db::getAll('SELECT * FROM #__content WHERE pid=:pid AND active=1 ORDER BY ordering', [':pid' => $data['id']]);
			if ($subPages) {
				foreach ($subPages as $idx => $row) {
					$langData = Db::getRow('SELECT * FROM #__content_lang WHERE fid=:fid AND lang=:lang LIMIT 1', [':fid' => $row['id'], ':lang' => $this->myHtml->lang]);
					if ($langData) {
						unset($langData['id'], $langData['fid'], $langData['lang']);
						$subPages[$idx] = array_merge($row, $langData);
					}
				}
			}
			$data['children'] = $subPages;
			if($data['gal_id']) {
				$sql = "SELECT m.id AS id, m.image, l.title, m.active AS active FROM #__photos AS m LEFT JOIN #__photos_lang AS l ON m.id=l.fid WHERE gal_id=:gal_id AND (l.lang=:lang OR l.lang IS NULL) AND active=1 ORDER BY m.ordering";
				$data['photos'] = Db::getAll($sql, [':gal_id' => $data['gal_id'], ':lang' => $this->lang]);
				$this->myHtml->addCssFile();
				//magnific-popup.css
			}

			$this->myHtml->setData($data);
			$this->myHtml->addOpenGraph('title', $data['meta_title']);
			$this->myHtml->addOpenGraph('description', $data['meta_descr']);
			$this->myHtml->addOpenGraph('image', $this->myHtml->url('i/biglogo.jpg', true));
			$this->myHtml->addOpenGraph('url', $this->myHtml->url($data['link'], TRUE));
			$this->myHtml->render($data['template'], false);
		} else {
			$this->myHtml->pageNotFound();
		}
	}

}
