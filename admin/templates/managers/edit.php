<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="row-data">
		<form role="form" action="<?= $this->url('managers')?>" method="post" enctype="multipart/form-data">
			<?php Form::printField('name', _t('Name'), 'text', isset($this->data['name']) ? $this->data['name'] : '') ?>
			<?php Form::printField('email', _t('Email'), 'text', isset($this->data['email']) ? $this->data['email'] : '') ?>
			<?php Form::printField('password', _t('Password'), 'text', '', '', '', ['help' => 'Задайте пароль, он перепишет предыдущий. Должен быть не менее 6 символов.']) ?>

			<strong><?= _t('Set permissions for accessing components') ?></strong>
			<table id="managers_permissions">
				<thead>
					<tr>
						<th><?= _t('Component') ?></th>
						<th><?= _t('Read') ?></th>
						<th><?= _t('Change') ?></th>
						<th><?= _t('Delete') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->components as $key=>$value): ?>
					<tr>
						<td><?= $value ?></td>
						<td><label class="switch"><input type="checkbox" name="<?=$key?>_read"<?php if(isset($this->data['rules'][$key]) && $this->data['rules'][$key] > 0) echo ' checked'?>><span class="slider round"></span></label></td>
						<td><label class="switch"><input type="checkbox" name="<?=$key?>_change"<?php if(isset($this->data['rules'][$key]) && $this->data['rules'][$key] > 1) echo ' checked'?>><span class="slider round"></span></label></td>
						<td><label class="switch"><input type="checkbox" name="<?=$key?>_all"<?php if(isset($this->data['rules'][$key]) && $this->data['rules'][$key] > 2) echo ' checked'?>><span class="slider round"></span></label></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="id" value="<?= isset($this->data['id']) ? $this->data['id'] : 0 ?>">
			<input type="hidden" name="act" value="save">

			<div class="buttonsBox">
				<button type="submit" class="btn btn-success"><?= _t('Save')?></button>
				<a href="<?= $this->url('managers')?>" class="btn btn-default"><?= _t('Cancel')?></a>
			</div>
		</form>
	</div>
</div>
<?php include(TMPLS . 'footer.php') ?>