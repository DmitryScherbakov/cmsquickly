<?php include(TMPLS.'header.php')?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row btn-row">
	<a href="<?=ROOTURL?>menu/?act=add&amp;menuid=<?= $this->menuid ?>" class="btn btn-primary"><?= _t('Add new menu') ?></a>
</div>
<div class="row">
	<table class="tbl_data">
		<tr>
			<?php foreach ($this->fieldsToPrint as $key): ?>
			<th><?= _t('Table field ' . $key) ?></th>
			<?php endforeach; ?>
			<th><?= _t('Actions') ?></th>
		</tr>
		<?php $this->printTableTree($this->data, 1, $this->fieldsToPrint, '&amp;menuid=' . $this->menuid) ?>
	</table>
</div>

<?php include(TMPLS.'footer.php')?>