<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<form method="post" action="<?= COMPURL ?>?act=upload_photo&id=<?=$this->galId?>" enctype="multipart/form-data" novalidate class="box" accept="image/*">
		<div class="box__input">
			<svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/></svg>
			<input type="file" name="files" id="file" class="box__file" />
			<label for="file"><strong><?= _t('Choose a file')?></strong><span class="box__dragndrop"> <?= _t('or drag it here') . '. ' . _t('Max files size').' '.ini_get('upload_max_filesize')?></span>.</label>
			<div id="images_to_upload"></div>
			<button type="submit" class="box__button" id="uploadPhotos"><?= _t('Upload') ?></button>
		</div>
	</form>
</div>
<?php $this->startJs() ?>
<script>
	$(function () {
		initAjaxFileUpload(jQuery, window, document);
	});
</script>
<?php $this->stopJs() ?>
<div class="row">
	<table class="tbl_data" id="photosTbl">
		<tr>
			<th><?= _t('Photo') ?></th>
			<th><?= _t('Photo title') ?></th>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php foreach ($this->photos as $row): ?>
			<tr>
				<td><img src="/images/photos/thumb_<?= $row['image'] ?>" alt=""></td>
				<td><?= $row['title'] ?></td>
				<td class="data_actions">
					<?php $this->printPhotoActions($row['id'], $row['active'], '&gal_id='.$this->galId); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>

<?php include(TMPLS . 'footer.php') ?>