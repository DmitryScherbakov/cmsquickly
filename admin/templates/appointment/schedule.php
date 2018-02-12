
<table class="schedule_table">
	<thead>
		<tr>
			<th>Открытие записи</th>
			<th>Начальная дата</th>
			<th>Конечная дата</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->schedule as $row) : ?>
			<tr id="schedule<?= $row['id'] ?>">
				<td><?= preg_replace('~(\d+)\-(\d+)\-(\d+) (\d+)\:(\d+)\:(.*)~', '$3.$2.$1 $4:$5', $row['open']) ?></td>
				<td><?= preg_replace('~(\d+)\-(\d+)\-(\d+)~', '$3.$2.$1', $row['start']) ?></td>
				<td><?= preg_replace('~(\d+)\-(\d+)\-(\d+)~', '$3.$2.$1', $row['end']) ?></td>
				<td>
					<span class="btn btn-default editSchedule"  data-end="<?=$row['end']?>" data-open="<?=$row['open']?>" data-start="<?=$row['start']?>" rel="<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span>
					<span class="btn btn-default removeSchedule" rel="<?= $row['id'] ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>