<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<style type="text/css" media="all">
#dataTbl {border-collapse: collapse}
#dataTbl td, #dataTbl th {border: 1px solid #ccc; padding: 10px;}
body {font-size: 10pt}
@media print {
	#printBtn {display: none}
}

		</style>
	</head>
	<bod>
		<p><button id="printBtn" onclick="window.print()">Распечатать</button></p>
		<h3>Расписание на <?= $this->dayOfWeek[date('w', $this->date)] . ' ' . date('d.m.Y', $this->date)?></h3>
		<h4><?= $this->spec ?></h4>
		<table id="dataTbl">
			<thead>
				<tr>
					<th>Время</th>
					<th>ФИО</th>
					<th>Дата рождения, (возраст)</th>
					<th>Телефон</th>
					<th>Прием</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->data as $row): ?>
				<tr>
					<td><?= $row['visittime'] ?></td>
					<?php if(isset($this->visitors[$row['id']]) && $visitor = $this->visitors[$row['id']]) : ?>
					<td><?= $visitor['last_name'], ' ', $visitor['first_name'], ' ', $visitor['patronic_name'] ?></td>
					<td><?= preg_filter('/(\d{4})\-(\d{2})\-(\d{2})/', '$3.$2.$1', $visitor['birthday']), ', ', findAge($visitor['birthday'], $this->date) ?></td>
					<td><?= $visitor['phone'] ?></td>
					<td><?= $visitor['returning'] == 1 ? 'Первичный' : 'Повторный' ?></td>
					<?php else : echo str_repeat('<td></td>', 4)?>
					<?php endif;?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</bod>
</html>
<?php
function findAge($birthDate, $visitDate) {
	$diff = intval(floatval(date('Y.m', $visitDate)) - floatval(preg_filter('/(\d{4})\-(\d{2})\-(\d{2})/', '$1.$2', $birthDate)));
	return '(' . $diff . ')';
}