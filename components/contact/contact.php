<?php

class Contact extends Component
{
	function actionSend()
	{
		$resp = $this->getReCapchaResponse();
		//get variabls and send by email, ex.
		$this->actionDefault();
	}

	function actionDefault()
	{
		$this->myHtml->meta_title = _t('meta title');
		$this->myHtml->meta_descr = _t('meta description');
		$this->myHtml->addOpenGraph('title', 'open graph title');
		$this->myHtml->addOpenGraph('description', 'open graph description');
		$this->myHtml->addOpenGraph('image', $this->myHtml->url('i/biglogo.jpg', true));
		$this->myHtml->addOpenGraph('url', $this->myHtml->url('contact', TRUE));

		$this->myHtml->render('contact');
	}
	
	function getReCapchaResponse() 
	{
		//https://www.google.com/recaptcha/api/siteverify
		$fields = array(
			'secret' => 'seckretkey',
			'response' => $_POST["g-recaptcha-response"],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		);
						
		//open connection
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		//execute post
		return json_decode(curl_exec($ch), true);
	}

}
