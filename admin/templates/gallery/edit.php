<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="row-data">
		<form role="form" action="<?= COMPURL ?>" method="post" enctype="multipart/form-data">
			<?php Form::printSelect('pid', _t('Parent gallery'), 'treeselect', isset($this->data['pid']) ? $this->data['pid'] : 0, $this->galleries, 'pid') ?>
			<?php Form::printField('link', _t('Page URL'), 'text', isset($this->data['link']) ? $this->data['link'] : '', '', '', ['prefix' => DOMAIN . '/']) ?>
			<?php Form::printField('image', _t('Gallery image'), 'file') ?>
			<?php if($this->data['image']) :?>
			<a href="/images/gallery/<?= $this->data['image'] ?>" target="_blank"><img src="/images/gallery/thumb_<?= $this->data['image'] ?>"></a>
			<?php endif; ?>

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
							<?php Form::printField('meta_title[' . $key . ']', _t('Page title for SEO'), 'text', isset($this->data[$key]['meta_title']) ? $this->data[$key]['meta_title'] : '') ?>
							<?php Form::printField('meta_descr[' . $key . ']', _t('Page meta description'), 'text', isset($this->data[$key]['meta_descr']) ? $this->data[$key]['meta_descr'] : '') ?>
							<?php Form::printField('text[' . $key . ']', 'Page text', 'htmleditor', isset($this->data[$key]['text']) ? $this->data[$key]['text'] : '') ?>
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
			<input type="hidden" name="act" value="save">

			<div class="buttonsBox">
				<button type="submit" class="btn btn-success"><?= _t('Save')?></button>
				<a href="<?= $this->url('content')?>" class="btn btn-default"><?= _t('Cancel')?></a>
			</div>
		</form>
	</div>
</div>
<?php include(TMPLS . 'footer.php') ?>