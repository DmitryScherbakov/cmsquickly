<?php

class Home extends Component
{

	function actionDefault()
	{
		$this->myHtml->slider = Db::getAll("SELECT * FROM #__slider WHERE active=1 ORDER BY ordering");

		$data = Db::getContentDataByUrl('', $this->myHtml->lang);
		$this->myHtml->setData($data);
		$this->myHtml->addOpenGraph('title', _t('open graph title');
		$this->myHtml->addOpenGraph('description', _t('open graph description');
		$this->myHtml->addOpenGraph('image', $this->myHtml->url('i/biglogo.jpg', true));
		$this->myHtml->addOpenGraph('url', $this->myHtml->url('', TRUE));

		$this->myHtml->render('home');
	}

}
