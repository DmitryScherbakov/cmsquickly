<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row btn-row">
	<a href="<?=COMPURL?>?act=add" class="btn btn-primary"><?= _t('Add new slider image') ?></a>
</div>
<div class="row">
	<table class="tbl_data">
		<tr>
			<th><?= _t('Image') ?></th>
			<th><?= _t('Title') ?></th>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php foreach ($this->data as $row) : $row['titles'] = json_decode($row['titles'], TRUE);?>
		<tr>
			<td><img src="/images/slider/thumb_<?= $row['image']?>" alt=""></td>
			<td><?= isset($row['titles'][$this->lang]) ? $row['titles'][$this->lang] : '' ?></td>
			<td class="data_actions"><?php $this->printActions($row['id'], $row['active'])?></td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
</div>
<?php include(TMPLS . 'footer.php') ?>