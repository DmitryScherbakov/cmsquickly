<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1>Диапазон дат</h1>
</div>
<div class="row">
	<div class="separated_block">
		<div class="separated_block_title">Управление показом Онлайн записи</div>
		<div class="separated_block_data">
			<button id="addSchedule" class="btn btn-primary">Добавить</button> <span class="help">Все записи, что Вы добавите после этой даты, не будут видны посетителям до наступления этой даты и времени</span>
			<div id="scheduleTableBlock">
				<?php include('schedule.php') ?>
			</div>
		</div>
	</div>
</div>

<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<div class="separated_block">
		<div class="separated_block_title">Выберите специалиста</div>
		<div class="separated_block_data">
			<div class="specList">
				<?php foreach ($this->specialists as $row): ?>
					<div class="specItem" data-specid="<?= $row['id'] ?>">
						<?php if ($row['image']): ?>
							<div><img src="/images/specialists/thumb_<?= $row['image'] ?>" class="specPhoto"></div>
						<?php else : ?>
							<div><img src="/i/default.jpg" class="specPhoto"></div>
						<?php endif; ?>
						<div class="specName">
							<?= str_replace(' ', '<br>', $row['name']) ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="separated_block">
		<div class="separated_block_title">Выберите время</div>
		<div class="separated_block_data">
			<div class="form-group">
				<div class="specVisitBox">
					<div class="col-md-8">
						<div id="visitdate"></div>
					</div>
					<div class="col-md-2">
						<button id="addVisit" class="btn btn-primary">Добавить дату и <br>время записи</button>
					</div>
					<div class="col-md-2" style="margin-left:20px">
						<button id="showVisits" class="btn btn-success">Показать записи <br>на дату</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="separated_block">
		<div class="separated_block_title">Данные Онлайн записи</div>
		<div class="separated_block_data">
			<div id="addedDatesBlock"></div>
		</div>
	</div>
</div>

<div id="changeVisitModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="appModalTitle">Запись на прием</h4>
			</div>
			<div class="modal-body" id="appModalBody">
				<div class="row form-horizontal" style="padding:10px 35px">
					<div class="form-group">
						<label class="col-sm-2 control-label">Специалист:</label>
						<div class="col-sm-10">
							<select id="modalSpecId" class="form-control">
								<?php foreach ($this->specialists as $row): ?>
									<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Выбранное время:</label>
						<div class="col-sm-10">
							<div class="input-group date" id="modalVisitDate">
								<input type="text" class="form-control" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-10 checkbox">
							<label for="modalVisitOccupied">
								<input type="checkbox" id="modalVisitOccupied" style="vertical-align: middle" />
								<span style="vertical-align: middle;font-weight: bold;">Время отмечено как занятое</span>
							</label>
						</div>
					</div>
				</div>
				<div id="modalVisitorBlock">
					<div class="row form-horizontal" style="padding:10px 35px">
						<div class="form-group">
							<label class="col-sm-2 control-label">ФИО:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="modalClientName" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Дата рождения:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="modalClientBirth" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Телефон:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="modalClientPhone" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Email:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="modalClientEmail" value="">
							</div>
						</div>
						<div class="form_row">
							<input type="radio" name="first_time" value="1" id="modalClientReturn1"> <label for="modalClientReturn1">Первое посещение</label>
							<input type="radio" name="first_time" value="2" id="modalClientReturn2"> <label for="modalClientReturn2">Повторное посещение</label>
						</div>
					</div>
				</div>
				<input type="hidden" id="modalId" value="">
			</div>
			<div class="modal-footer" id="appModalFooter">
				<button type="button" id="appModalRemove" class="btn btn-danger">Удалить</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" id="appModalSave" class="btn btn-primary">Сохранить</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- модальное окно добавления и редактирования расписания -->
<div id="scheduleModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="appModalTitle">Расписание открытия записи</h4>
			</div>
			<div class="modal-body" id="appModalBody">
				<div class="row form-horizontal" style="padding:10px 35px">
					<!-- Открытие записи -->
					<div class="form-group">
						<label class="col-sm-2 control-label">Открытие записи:</label>
						<div class="col-sm-10">
							<div class="input-group date" id="modalScheduleOpen">
								<input type="text" class="form-control" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<!-- Начальная дата -->
					<div class="form-group">
						<label class="col-sm-2 control-label">Начальная дата:</label>
						<div class="col-sm-10">
							<div class="input-group date" id="modalScheduleStart">
								<input type="text" class="form-control" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<!-- Конечная дата -->
					<div class="form-group">
						<label class="col-sm-2 control-label">Конечная дата:</label>
						<div class="col-sm-10">
							<div class="input-group date" id="modalScheduleEnd">
								<input type="text" class="form-control" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="appModalFooter">
				<input type="hidden" id="appScheduleId" value="0" name="appScheduleId">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" id="appScheduleSave" class="btn btn-primary">Сохранить</button>
			</div>
		</div>
	</div>
</div>



<?php $this->startJs(); ?>
<script>
	$(function () {
		$('#modalClientPhone').mask('+7 (000) 000-00-00');
		$('#modalClientBirth').mask('00.00.0000');
	});
</script>
<?php $this->stopJs(); ?>
<?php include(TMPLS . 'footer.php') ?>