<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="row-data">
		<form role="form" action="<?= $this->url('menu')?>" method="post" enctype="application/x-www-form-urlencoded">
			<?php Form::printSelect('pid', _t('Parent page'), 'treeselect', isset($this->data['pid']) ? $this->data['pid'] : 0, $this->items, 'pid') ?>
			<?php Form::printField('link', _t('Page URL'), 'text', isset($this->data['link']) ? $this->data['link'] : '') ?>
			<?php Form::printField('newwindow', _t('Open url in new window'), 'checkbox', isset($this->data['newwindow']) && $this->data['newwindow'] ? true : false) ?>

			<h2><?= _t('Language dependent data') ?></h2>
			<div>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist" id="tabdata">
					<?php foreach (Config::$aLanguages as $key => $value): ?>
						<li role="presentation"><a href="#<?= $key ?>" aria-controls="<?= $key ?>" role="tab" data-toggle="tab"><?= $value ?></a></li>
					<?php endforeach; ?>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<?php foreach (Config::$aLanguages as $key => $value): ?>
						<div role="tabpanel" class="tab-pane" id="<?= $key ?>">
							<div class="langfields">
							<?php Form::printField('title[' . $key . ']', _t('Page title'), 'text', isset($this->data[$key]['title']) ? $this->data[$key]['title'] : '') ?>
							<input type="hidden" name="lang_id[<?= $key ?>]" value="<?= isset($this->data[$key]['id']) ? $this->data[$key]['id'] : '0' ?>">
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php $this->startJs() ?>
			<script>
				$('#tabdata a').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				});

				$('#tabdata a:eq(0)').click();
			</script>
			<?php $this->stopJs() ?>
			<input type="hidden" name="id" value="<?= isset($this->data['id']) ? $this->data['id'] : 0 ?>">
			<input type="hidden" name="menuid" value="<?= $this->menuid ?>">
			<input type="hidden" name="act" value="save">

			<div class="buttonsBox">
				<button type="submit" class="btn btn-success"><?= _t('Save')?></button>
				<a href="<?= $this->url('menu?act=items&menuid='.$this->menuid)?>" class="btn btn-default"><?= _t('Cancel')?></a>
			</div>
		</form>
	</div>
</div>
<?php include(TMPLS . 'footer.php') ?>