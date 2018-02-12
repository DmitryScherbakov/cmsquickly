<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row btn-row">
	<a href="<?= ROOTURL ?>specialists/?act=add" class="btn btn-primary"><?= _t('Add new specialist') ?></a>
</div>
<div class="row">
	<table class="tbl_data">
		<tr>
			<th><?= _t('Photo') ?></th>
			<th><?= _t('Name') ?></th>
			<th><?= _t('Description') ?></th>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php foreach ($this->data as $row): ?>
			<tr>
				<td class="data_image"><img src="/images/specialists/thumb_<?=$row['image']?>" alt=""></td>
				<td class="data_name"><?=$row['name']?></td>
				<td class="data_details"><?=$row['details']?></td>
				<td class="data_actions"><?= $this->printActions($row['id'], $row['active'])?></td>
			</tr>
		<?php endforeach; ?>
		</tr>
	</table>
</div>

<?php include(TMPLS . 'footer.php') ?>