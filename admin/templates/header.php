<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $this->meta_title ?> :: CrusAdmin</title>
		<meta name="robots" content="noindex">

		<?php $this->addCssFile('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', '//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css', ROOTURL . 'css/styles.css', ROOTURL . 'css/custom.css') ?>

		<?php $this->addJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', '//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js', '//cdn.tinymce.com/4/tinymce.min.js', ROOTURL . 'js/scripts.min.js', ROOTURL . 'js/custom.js') ?>
		<?php $this->startJs() ?>
		<script>
			tinymce.init({
				selector: "textarea.htmleditor",
				browser_spellcheck: true,
				extended_valid_elements: 'iframe[*],div[*],a[*]',
				height: 300,
				theme: 'modern',
				inline: false,
				convert_urls: false,
				plugins: [
					'advlist autolink lists link image charmap hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen',
					'insertdatetime media nonbreaking save table directionality',
					'emoticons template paste textcolor colorpicker textpattern imagetools'
				],
				toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor emoticons',
				menubar: 'edit insert view format table tools',
				image_advtab: true,
				content_css: [
					'https://fonts.googleapis.com/css?family=Roboto:300,400,700&subset=cyrillic',
					'<?= ROOTURL . 'css/content.css' ?>'
				]
			});
		</script>
		<?php $this->stopJs() ?>


	</head>
	<body role="document">
		<!-- Fixed navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="http://crusadmin.com" target="_blank">CrusAdmin</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<?php foreach ($this->getTopMenu() as $item): ?>
							<li<?php if ($item['link'] == $this->component) echo ' class="active"' ?>>
								<a href="<?= ROOTURL . $item['link'] ?>"<?php if ($item['subitems']) echo ' class="dropdown-toggle" data-toggle="dropdown"'; ?>>
									<?= $item['title'] ?>
									<?php if ($item['subitems']) echo '<b class="caret"></b>'; ?>
								</a>
								<?php if ($item['subitems']): ?>
									<ul class="dropdown-menu">
										<?php foreach ($item['subitems'] as $row): ?>
											<li><a href="<?= ROOTURL . $item['link'] . $row['link'] ?>">Action</a></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>

		<div class="container-fluid" role="main">
			<div class="row">
				<div id="mainbox">
					<?php $this->printAlerts() ?>
