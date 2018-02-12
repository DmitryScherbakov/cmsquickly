<?php

class Html
{

	private $myPage;
	private $vars = array();
	private $jsFiles = [];
	private $jsScripts = [];
	private $cssFiles = [];
	private $cssStyles = [];
	private $openGraphData = [];
	private $extraMetaTags = [];

	public function __construct($page)
	{
		$this->myPage = $page;
	}

	public function setData($data)
	{
		foreach ($data as $name => $value) {
			$this->vars[$name] = $value;
		}
	}

	public function addMetaTag($name, $content)
	{
		$this->extraMetaTags[$name] = $content;
	}

	//recomended types: title, description, image, type, url
	public function addOpenGraph($type, $content)
	{
		$this->openGraphData[$type] = $content;
	}

	public function printMetaTags()
	{
		foreach ($this->extraMetaTags as $name => $content) {
			echo '<meta name="' . $name . '" content="' . $content . '"/>', "\n";
		}
	}

	public function printOpenGraph()
	{
		if(!isset($this->openGraphData['type'])) {
			$this->openGraphData['type'] = 'website';
		}
		foreach ($this->openGraphData as $type => $content) {
			echo '<meta property="og:' . $type . '" content="' . $content . '"/>', "\n";
		}
	}

	public function url($url, $full = false)
	{
		if ($full) {
			return (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . DOMAIN . ROOTURL . $url;
		}
		return ROOTURL . $url;
	}

	public function &__get($name)
	{
		if (isset($this->myPage->$name)) {
			return $this->myPage->$name;
		}
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this->vars[$name]);
	}

	public function render($templateFile, $componentFolder = true)
	{
		$templateFile = str_replace(['.php', '\\'], ['', DIRECTORY_SEPARATOR], $templateFile);
		if ($componentFolder) {
			include( COMPTMPL . basename($templateFile) . '.php');
		} else {
			include(TMPLS . $templateFile . '.php');
		}
	}

	public function prependJsFile()
	{
		$tmp = [];
		foreach (func_get_args() as $url) {
			$tmp[] = $url;
		}
		$this->jsFiles = array_merge($tmp, $this->jsFiles);
	}

	public function addJsFile()
	{
		foreach (func_get_args() as $url) {
			$this->jsFiles[] = $url;
		}
	}

	public function addCssFile()
	{
		foreach (func_get_args() as $url) {
			$this->cssFiles[] = $url;
		}
	}

	public function startJs()
	{
		ob_start();
	}

	public function stopJs()
	{
		$this->jsScripts[] = trim(str_replace(['<script>', '</script>'], '', ob_get_clean()));
	}

	public function startCss()
	{
		ob_start();
	}

	public function stopCss()
	{
		$this->cssStyles[] = trim(str_replace(['<style>', '</style>'], '', ob_get_clean()));
	}

	public function printAllCss()
	{
		foreach ($this->cssFiles as $url) {
			echo '<link rel="stylesheet" href="' . $url . '" type="text/css">', "\n";
		}

		if (!empty($this->cssStyles)) {
			echo '<style>', "\n";
			foreach ($this->cssStyles as $row) {
				echo $row, "\n";
			}
			echo '</style>', "\n";
		}
	}

	public function printAllJs()
	{
		foreach ($this->jsFiles as $url) {
			echo '<script src="' . $url . '" type="text/javascript"></script>', "\n";
		}

		if (!empty($this->jsScripts)) {
			echo '<script>', "\n";
			foreach ($this->jsScripts as $row) {
				echo $row, "\n";
			}
			echo '</script>', "\n";
		}
	}

	public function redirect($url)
	{
		header('Location: ' . $this->url($url));
	}

	public function addAlert($type, $message)
	{
		if (!isset($_SESSION['alerts'])) {
			$_SESSION['alerts'] = [];
		}

		if (!isset($_SESSION['alerts'][$type])) {
			$_SESSION['alerts'][$type] = [];
		}

		$_SESSION['alerts'][$type][] = $message;
	}

	public function pageNotFound()
	{
		header("HTTP/1.0 404 Not Found");
		$this->render('404', false);
	}

	public function printAlerts()
	{
		if (!empty($_SESSION['alerts'])) {
			foreach ($_SESSION['alerts'] as $type => $messages) {
				echo '<div class="alert-' . $type . '">';
				foreach ($messages as $message) {
					echo $message;
				}
				echo '</div>';
			}
			$_SESSION['alerts'] = [];
		}
	}

	function loadPlugin($pluginName, $params = [])
	{
		if (file_exists(PLUGINDIR . basename($pluginName . '.php'))) {
			require (PLUGINDIR . basename($pluginName . '.php'));
			new $pluginName($this, $params);
		}
	}

}
