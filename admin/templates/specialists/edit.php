<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="row-data">
		<form role="form" action="<?= $this->url('specialists')?>" method="post" enctype="multipart/form-data">
			<?php Form::printField('name', _t('Name'), 'text', isset($this->data['name']) ? $this->data['name'] : '') ?>
			<?php Form::printField('details', _t('Details'), 'text', isset($this->data['details']) ? $this->data['details'] : '') ?>
			<?php Form::printField('agerestrict', 'Ограничение по возрасту', 'number', (isset($this->data['agerestrict']) ? $this->data['agerestrict'] : ''), '', '', ['help' => 'Не старше указаных лет. 0 - без ограничений по возрасту']) ?>
			<?php Form::printField('image', _t('Page image'), 'file') ?>
			<?php if($this->data['image']) :?>
			<a href="/images/specialists/<?= $this->data['image'] ?>" target="_blank"><img src="/images/specialists/thumb_<?= $this->data['image'] ?>"></a>
			<?php endif; ?>
			<input type="hidden" name="id" value="<?= isset($this->data['id']) ? $this->data['id'] : 0 ?>">
			<input type="hidden" name="act" value="save">

			<div class="buttonsBox">
				<button type="submit" class="btn btn-success"><?= _t('Save')?></button>
				<a href="<?= $this->url('specialists')?>" class="btn btn-default"><?= _t('Cancel')?></a>
			</div>
		</form>
	</div>
</div>
<?php include(TMPLS . 'footer.php') ?>