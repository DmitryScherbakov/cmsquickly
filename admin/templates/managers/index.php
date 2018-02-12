<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row btn-row">
	<a href="<?= ROOTURL ?>managers/?act=add" class="btn btn-primary"><?= _t('Add new manager') ?></a>
</div>
<div class="row">
	<table class="tbl_data">
		<tr>
			<th><?= _t('Name') ?></th>
			<th><?= _t('Email') ?></th>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php foreach ($this->data as $row): ?>
			<tr>
				<td class="data_name"><?=$row['name']?></td>
				<td class="data_details"><?=$row['email']?></td>
				<td class="data_actions">
					<a href="?act=edit&amp;id=<?=$row['id']?>" class="btn btn-default" aria-label="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
					<a href="?act=delete&amp;id=<?=$row['id']?>" class="btn btn-default" aria-label="Remove"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tr>
	</table>
</div>

<?php include(TMPLS . 'footer.php') ?>