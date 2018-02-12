<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<form action="<?= ROOTURL ?>menu" method="get" class="form-inline" style="text-align: center">
		<div class="form-group">
			<label for="menuid"><?= _t('menu ID') ?></label>
			<input type="text" class="form-control" id="menuid" name="menuid" value="<?= isset($_GET['menuid']) ? trim($_GET['menuid']) : ''?>">
			<input type="hidden" value="save_menu" name="act">
		</div>
		<button type="submit" class="btn btn-default"><?= _t('Save menu') ?></button>
	</form>
</div>
<div class="row">
	<table class="tbl_data">
		<tr>
			<th><?= _t('menu ID' . $key) ?></th>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php foreach ($this->data as $row): ?>
		<tr>
			<td>
				<a href="<?= ROOTURL . 'menu/?act=items&amp;menuid=' . $row['menuid'] ?>"><?= $row['menuid'] ?></a>
			</td>
			<td style="width:200px;">
				<a href="?menuid=<?= $row['menuid'] ?>" class="btn btn-default" aria-label="Edit">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
				</a>

				<a href="?act=delete_menu&amp;menuid=<?= $row['menuid'] ?>" class="btn btn-default" aria-label="Remove">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</a>

				<a href="<?= ROOTURL . 'menu/?act=items&amp;menuid=' . $row['menuid'] ?>" class="btn btn-default" aria-label="Items">
				<span class="glyphicon glyphicon-list" aria-hidden="true"></span>
				</a>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
</div>

<?php include(TMPLS . 'footer.php') ?>