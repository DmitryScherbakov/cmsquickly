<!DOCTYPE html>
<html lang="<?= $this->lang ?>">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		<title><?= $this->meta_title ?></title>
		<meta name="description" content="<?= $this->meta_descr ?>">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<meta name="theme-color" content="#ffffff">
		<?php $this->printMetaTags() ?>
		<?php $this->printOpenGraph() ?>
		<?php
		$this->addCssFile(
			ROOTURL . 'css/styles.css'
			);

		$this->prependJsFile('https://code.jquery.com/jquery-3.1.0.min.js');
		$this->addJsFile(ROOTURL . 'js/scripts.js');

		$this->printAllCss();
		?>
	</head>
	<body>


<div class="wrapper">
  <header class="header">
  	Header
  	<?php $this->loadPlugin('treemenu', ['menuid' => 'topmenu']); ?>
  </header>
