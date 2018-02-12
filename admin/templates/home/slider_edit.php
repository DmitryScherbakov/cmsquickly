<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="row-data">
		<form class="form-horizontal" role="form" action="<?= COMPURL ?>" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="image" class="col-sm-2 control-label"><?= _t('Image') ?></label>
				<div class="col-sm-10">
					<input type="file" id="image" name="image">
					<?php if ($this->data['image']) : ?>
						<a href="/images/slider/<?= $this->data['image'] ?>" target="_blank"><img src="/images/slider/thumb_<?= $this->data['image'] ?>"></a>
					<?php endif; ?>
				</div>
			</div>
			<?php foreach (Config::$aLanguages as $key => $value): ?>
				<div class="form-group">
					<label for="title" class="col-sm-2 control-label"><?= _t('Title') . ' (' . $value . ')' ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="title_<?= $key ?>" name="titles[<?= $key ?>]" value="<?= isset($this->data['titles'][$key]) ? $this->data['titles'][$key] : '' ?>">
					</div>
				</div>
			<?php endforeach; ?>
			<input type="hidden" name="id" value="<?= isset($this->data['id']) ? $this->data['id'] : '' ?>">
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" name="act" value="save" class="btn btn-primary"><?= _t('Save') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php include(TMPLS . 'footer.php') ?>