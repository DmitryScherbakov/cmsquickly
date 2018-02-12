
function initAjaxFileUpload($, window, document, undefined)
{
	;
	(function ($, window, document, undefined)
	{
		// feature detection for drag&drop upload

		var isAdvancedUpload = function ()
		{
			var div = document.createElement('div');
			return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
		}();
		window.URL = window.URL || window.webkitURL;


		// applying the effect for every form

		$('.box').each(function ()
		{
			var $form = $(this),
					$uploadList = $form.find('#images_to_upload'),
					$input = $form.find('input[type="file"]'),
					$errorMsg = $form.find('.box__error span'),
					$restart = $form.find('.box__restart'),
					droppedFiles = [],
					showFiles = function (files)
					{
						droppedFiles = files;
						for (var i = 0; i < files.length; i++) {
							var img = document.createElement('img');
							img.onload = function () {
								window.URL.revokeObjectURL(this.src);
							};
							img.height = 100;
							img.src = window.URL.createObjectURL(files[i]);
							var $span = $('<span class="imageToLoad" data-name="' + files[i].name + '"></span>');
							$uploadList.append($span.append(img));
						}
						$form.trigger('submit');
					};

			// letting the server side to know we are going to make an Ajax request
			$form.append('<input type="hidden" name="ajax" value="1" />');

			// automatically submit the form on file select
			$input.on('change', function (e)
			{
				showFiles(e.target.files);
			});


			// drag&drop files if the feature is available
			if (isAdvancedUpload)
			{
				$form.addClass('has-advanced-upload') // letting the CSS part to know drag&drop is supported by the browser
						.on('drag dragstart dragend dragover dragenter dragleave drop', function (e)
						{
							// preventing the unwanted behaviours
							e.preventDefault();
							e.stopPropagation();
						})
						.on('dragover dragenter', function () //
						{
							$form.addClass('is-dragover');
						})
						.on('dragleave dragend drop', function ()
						{
							$form.removeClass('is-dragover');
						})
						.on('drop', function (e)
						{
							showFiles(e.originalEvent.dataTransfer.files);
						});
				$form.find('#uploadPhotos').hide();
			}


			// if the form was submitted

			$form.on('submit', function (e)
			{
				// preventing the duplicate submissions if the current one is in progress
				if ($form.hasClass('is-uploading'))
					return false;

				$form.addClass('is-uploading').removeClass('is-error');

				if (isAdvancedUpload) // ajax file upload for modern browsers
				{
					e.preventDefault();

					// gathering the form data
					var ajaxData = new FormData($form.get(0));
					if (droppedFiles)
					{
						$.each(droppedFiles, function (i, file)
						{
							ajaxData.append($input.attr('name'), file);
						});
					}

					// ajax request
					$.ajax({
						url: $form.attr('action'),
						type: $form.attr('method'),
						data: ajaxData,
						dataType: 'json',
						cache: false,
						contentType: false,
						processData: false
					}).always(function () {
						$form.removeClass('is-uploading');
					}).fail(function () {
						showAlert('error', 'Error. Please, contact the webmaster!');
					}).done(function (data) {
						if (data.success) {
							$uploadList.children('span').each(function () {
								if ($(this).data('name') === data.name) {
									$(this).remove();
								}
								$('#photosTbl').append(data.html);
							});
						} else {
							showAlert('error', data.html);
						}
					});
				} else {
					var iframeName = 'uploadiframe' + new Date().getTime(),
							$iframe = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

					$('body').append($iframe);
					$form.attr('target', iframeName);

					$iframe.one('load', function ()
					{
						var data = $.parseJSON($iframe.contents().find('body').text());
						$form.removeClass('is-uploading').addClass(data.success == true ? 'is-success' : 'is-error').removeAttr('target');
						if (!data.success)
							$errorMsg.text(data.error);
						$iframe.remove();
					});
				}
			});


			// restart the form if has a state of error/success

			$restart.on('click', function (e)
			{
				e.preventDefault();
				$form.removeClass('is-error is-success');
				$input.trigger('click');
			});

			// Firefox focus bug fix for file input
			$input
					.on('focus', function () {
						$input.addClass('has-focus');
					})
					.on('blur', function () {
						$input.removeClass('has-focus');
					});
		});

	})(jQuery, window, document);
}

function showAlert(type, message) {
	if (type === 'error') {
		type = 'danger';
	}
	var box = '<div class="row"><div class="alert alert-' + type + ' alert-dismissible" role="alert">'
			+ '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
			+ message
			+ "</div></div>";
	$('#mainbox').prepend(box);
}

function loadSpecData() {
	var specId = window.selectedSpecId;
	// ajax request
	$.ajax({
		url: '/admin/appointment/',
		type: 'get',
		data: {'id_spec': specId, 'act': 'get'},
		dataType: 'json',
		cache: false,
	}).always(function () {
		//
	}).fail(function () {
		showAlert('error', 'Error. Please, contact the webmaster!');
	}).done(function (resp) {
		//проверяем что не выбрали за это время другого специалиста
		if (resp.id_spec == window.selectedSpecId) {
			window.visitors = [];
			if (resp.success) {
				var lastAddedDate = '', $mainBlock = $('#addedDatesBlock'), dayOfWeek = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
				$mainBlock.children().remove();
				window.visitors = resp.visitors;
				var i = 0, len = resp.data.length, currentDayOfWeek = 1, $box = null, $week = null, goon = i < len;
				while (goon) {

					//find week number, if not in page create week row
					//find monday
					//construct the week days if week row is empty
					//insert date data in apropriate day

					if (currentDayOfWeek === 1) {
						var prevDate = moment(resp.data[i].visitdate);
						$week = $('<div class="appointmentWeek" id="week' + prevDate.format("w") + '"></div>');
						$week.appendTo($mainBlock);
					} else if (currentDayOfWeek > 6) {
						currentDayOfWeek = 1;
						var prevDate = moment(resp.data[i - 1].visitdate);
						$week.append($('<div class="copyWeek"><button class="btn btn-default copyWeekBtn" rel="week' + prevDate.format("w") + '">Скопировать расписание<br> за недели</button></div>'));
						console.log(prevDate.format("DD.MM.YY"));
						if (i === len) {
							goon = false;
						}
						continue;
					}

					var date = moment(resp.data[i].visitdate), weekDay = date.format("e");

					if (weekDay > currentDayOfWeek) {
						console.log(weekDay, currentDayOfWeek);
						for (var dd = weekDay - 1; dd > 0; dd--) {
							var minusTime = date.valueOf() - (24 * 3600 * 1000 * dd), minusMoment = moment(minusTime), boxId = 'addedDate' + minusMoment.format("YYMMDD");

							if ($('#' + boxId).length === 0) {
								$box = $('<div class="addedDateBox"/>').attr('id', boxId);
								$box.append($('<div class="addedDateTitle"/>').text(dayOfWeek[dd] + ', ' + minusMoment.format("DD.MM")));
								$box.append($('<div class="addedDateData"/>'));
								$box.prependTo($week);
							}
						}
						currentDayOfWeek = weekDay;
					}

					var visitDate = resp.data[i].visitdate.split(/[^\d]/),
							boxId = 'addedDate' + (visitDate[0] - 2000) + visitDate[1] + visitDate[2];

					$box = $('#' + boxId);
					if (boxId !== lastAddedDate) {
						lastAddedDate = boxId;
						if ($box.size() === 0) {
							$box = $('<div class="addedDateBox"/>').attr('id', boxId);
							$box.append($('<div class="addedDateTitle"/>').text(dayOfWeek[weekDay] + ', ' + visitDate[2] + '.' + visitDate[1]));
							$box.append($('<div class="addedDateData"/>'));
							$box.appendTo($week);
						}
					}

					var $block = $('<div class="btn btn-default btn-block addedDateItem" id="i' + resp.data[i].id + '">' + visitDate[3] + ':' + visitDate[4] + '</div>').data('date', resp.data[i].visitdate).data('spec', resp.data[i].id_spec).data('occupied', resp.data[i].occupied);
					if (resp.data[i].occupied == 1) {
						$block.addClass('btn-success');
					}
					$block.appendTo($box.find('.addedDateData'));

					if ((i === len - 1 || parseInt(moment(resp.data[i + 1].visitdate).format("e")) !== parseInt(moment(resp.data[i].visitdate).format("e")) + 1) && currentDayOfWeek < 6) {
						var bb = 1;
						for (var dd = currentDayOfWeek + 1; dd < 7; dd++) {
							var newTimestamp = (bb * 24 * 3600) + parseInt(resp.data[i].timestamp), lastDate = moment(newTimestamp * 1000), boxId = 'addedDate' + lastDate.format("YYMMDD");
							$box = $('<div class="addedDateBox"/>').attr('id', boxId);
							$box.append($('<div class="addedDateTitle"/>').text(dayOfWeek[dd] + ', ' + lastDate.format("DD.MM")));
							$box.append($('<div class="addedDateData"/>'));
							$box.appendTo($week);
							currentDayOfWeek++;
							bb++;
						}
					}
					currentDayOfWeek++;
					i++;
				}
			} else {
				showAlert('error', resp.html);
			}
		} else {
			console.log('error: no selected specialist ID');
		}
	});
}

$(function () {

	if ($('.specList').size() > 0) {
		$('.specItem').eq(0).addClass('active');
		window.selectedSpecId = $('.specItem').eq(0).data('specid');
		loadSpecData();

		$('.specItem').on('click', function () {
			var $this = $(this), specId = $this.data('specid');
			window.selectedSpecId = specId;
			$('.specItem').removeClass('active');
			$this.addClass('active');
			loadSpecData();
		});

		$('#visitdate').datetimepicker({
			locale: 'ru',
			daysOfWeekDisabled: [0],
			inline: true,
			sideBySide: true,
			enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
		});

		$('#addVisit').on('click', function () {
			var specId = window.selectedSpecId, date = $('#visitdate').data("DateTimePicker").date().format('YYYY-MM-DD HH:mm');
			// ajax request
			$.ajax({
				url: '/admin/appointment/',
				type: 'post',
				data: {'id_spec': specId, 'visitdate': date, 'act': 'save'},
				dataType: 'json',
				cache: false,
			}).always(function () {
				//
			}).fail(function () {
				showAlert('error', 'Error. Please, contact the webmaster!');
			}).done(function (resp) {
				if (resp.success) {
					console.log(resp);
					loadSpecData();
				} else {
					showAlert('error', resp.html);
				}
			});
		});

		$('#showVisits').on('click', function () {
			var specId = window.selectedSpecId, date = $('#visitdate').data("DateTimePicker").date().format('YYYY-MM-DD'), url = '/admin/appointment/?act=show&specId=' + specId + '&date=' + date;
			console.log('spec = ' + specId + ' on date = ' + date);
			window.open(url);
		});

		$(document).on('click', '.copyWeekBtn', function () {
			var $this = $(this), weekId = $this.attr('rel');
			console.log('week = ' + weekId);
		});

		$(document).on('click', '.addedDateItem', function () {
			var $this = $(this), id = parseInt($this.attr('id').substring(1)), visitOccupied = $this.data('occupied') == 1, $visitorBlock = $('#modalVisitorBlock');
			$('#modalId').val(id);
			$('#modalSpecId').val($this.data('spec')).trigger('change');
			$('#modalVisitDate').data("DateTimePicker").date(moment($this.data('date')));
			$('#modalVisitOccupied').prop('checked', visitOccupied);

			//если есть данные по клиенту, то выводим их соответственно
			if (visitOccupied) {
				var visitorData = window.visitors[id];
				if (visitorData) {
					$('#modalClientName').val(visitorData['name']);
					$('#modalClientBirth').val(visitorData['birthday']);
					$('#modalClientPhone').val(visitorData['phone']);
					$('#modalClientEmail').val(visitorData['email']);
					if (visitorData['returning'] == 2) {
						$('#modalClientReturn1')[0].checked = false;
						$('#modalClientReturn2')[0].checked = true;
					} else {
						$('#modalClientReturn1')[0].checked = true;
						$('#modalClientReturn2')[0].checked = false;
					}
				} else {
					$('#modalClientName').val('');
					$('#modalClientBirth').val('');
					$('#modalClientPhone').val('');
					$('#modalClientEmail').val('');
					$('#modalClientReturn1')[0].checked = true;
					$('#modalClientReturn2')[0].checked = false;
				}
				$visitorBlock.show();
			} else {
				$('#modalClientName').val('');
				$('#modalClientBirth').val('');
				$('#modalClientPhone').val('');
				$('#modalClientEmail').val('');
				$('#modalClientReturn1')[0].checked = true;
				$('#modalClientReturn2')[0].checked = false;
				$visitorBlock.hide();
			}

			$('#changeVisitModal').modal();
		});

		$('#modalVisitDate').datetimepicker({
			locale: 'ru',
			daysOfWeekDisabled: [0],
			sideBySide: true,
			enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
			format: "DD.MM.YYYY HH:mm"
		});

		$('#modalVisitOccupied').on('change', function () {
			var id = $('#modalId').val(), visitorData = window.visitors[id], $this = $(this), $visitorBlock = $('#modalVisitorBlock');
			if (!$this[0].checked && visitorData && !confirm("Если вы пометите время как незанятое, то это приведет к удалению данных записавшегося. Продолжить?")) {
				$this[0].checked = !$this[0].checked;
			}

			if ($this[0].checked) {
				$visitorBlock.show();
			} else {
				$visitorBlock.hide();
			}
		});

		$('#appModalRemove').on('click', function () {
			var id = $('#modalId').val(), visitorData = window.visitors[id], deleteApproved = false;
			if (visitorData) {
				deleteApproved = confirm("Удаление времени приведет так же удалению данных записавшегося. Продолжить?");
			} else {
				deleteApproved = true;
			}

			if (deleteApproved) {
				$.ajax({
					url: '/admin/appointment/',
					type: 'post',
					data: {'id': id, 'act': 'remove'},
					dataType: 'json',
					cache: false,
				}).always(function () {
					$('#changeVisitModal').modal('hide');
				}).fail(function (resp) {
					showAlert('error', resp.html);
				}).done(function (resp) {
					if (resp.success) {
						loadSpecData();
					} else {
						showAlert('error', resp.html);
					}
				});
			}
		});

		$('#appModalSave').on('click', function () {
			var id = $('#modalId').val(),
					date = $('#modalVisitDate').data("DateTimePicker").date().format('YYYY-MM-DD HH:mm'),
					visitor = {name: $('#modalClientName').val(), birth: $('#modalClientBirth').val(), phone: $('#modalClientPhone').val(), email: $('#modalClientEmail').val(), returning: $('input[name=first_time]:checked').val()};
			$.ajax({
				url: '/admin/appointment/',
				type: 'post',
				data: {id: id, act: 'edit', specId: parseInt($('#modalSpecId').val()), visitdate: date, occupied: $('#modalVisitOccupied')[0].checked ? 1 : 0, visitor: visitor},
				dataType: 'json',
				cache: false,
			}).always(function () {
				$('#changeVisitModal').modal('hide');
			}).fail(function (resp) {
				showAlert('error', resp.html);
			}).done(function (resp) {
				if (resp.success) {
					loadSpecData();
				} else {
					showAlert('error', resp.html);
				}
			});
		});
	}
});