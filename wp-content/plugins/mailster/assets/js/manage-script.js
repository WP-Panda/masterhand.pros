mailster = (function (mailster, $, window, document) {

	"use strict";

	var importstatus = $('.import-status'),
		exportstatus = $('.export-status'),
		progress = $('#progress'),
		progressbar = progress.find('.bar'),
		wpnonce = $('#mailster_nonce').val(),
		importdata = null,
		importerrors = 0,
		importstarttime,
		importidentifier,

		uploader_init = function () {
			var uploader = new plupload.Uploader(wpUploaderInit);

			uploader.bind('Init', function (up) {
				var uploaddiv = $('#plupload-upload-ui');

				if (up.features.dragdrop && !$(document.body).hasClass('mobile')) {
					uploaddiv.addClass('drag-drop');
					$('#drag-drop-area').bind('dragover.wp-uploader', function () { // dragenter doesn't fire right :(
						uploaddiv.addClass('drag-over');
					}).bind('dragleave.wp-uploader, drop.wp-uploader', function () {
						uploaddiv.removeClass('drag-over');
					});
				} else {
					uploaddiv.removeClass('drag-drop');
					$('#drag-drop-area').unbind('.wp-uploader');
				}

				if (up.runtime == 'html4')
					$('.upload-flash-bypass').hide();

			});

			uploader.bind('FilesAdded', function (up, files) {
				$('#media-upload-error').html('');
				$('#wordpress-users').fadeOut();

				setTimeout(function () {
					up.refresh();
					up.start();
				}, 1);

			});

			uploader.bind('BeforeUpload', function (up, file) {
				progress.removeClass('finished error hidden');
				importstatus.html('uploading');
			});

			uploader.bind('UploadFile', function (up, file) {});

			uploader.bind('UploadProgress', function (up, file) {
				importstatus.html(mailster.util.sprintf(mailster.l10n.manage.uploading, file.percent + '%'));
				progressbar.stop().animate({
					'width': file.percent + '%'
				}, 100);
			});

			uploader.bind('Error', function (up, err) {
				importstatus.html(err.message);
				progress.addClass('error');
				up.refresh();
			});

			uploader.bind('FileUploaded', function (up, file, response) {
				response = JSON.parse(response.response);
				importidentifier = response.identifier;
				if (!response.success) {
					importstatus.html(response.message);
					progress.addClass('error');
					up.refresh();
					uploader.unbind('UploadComplete');
				}
			});

			uploader.bind('UploadComplete', function (up, files) {
				importstatus.html(mailster.l10n.manage.prepare_data);
				progress.addClass('finished');
				get_import_data();
			});

			uploader.init();
		}

	typeof wpUploaderInit == 'object' && mailster.events.push('documentReady', uploader_init);

	$('.wrap')
		.on('change', '#signup', function () {
			$('#signupdate').prop('disabled', !$(this).is(':checked'));
		})
		.on('click', '.do-import', function () {

			var lists = $('#lists').serialize(),
				order = $('#subscriber-table').serialize();

			if (!/%5D=email/.test(order)) {
				alert(mailster.l10n.manage.select_emailcolumn);
				return false;
			}
			if (!$('input[name="status"]:checked').length) {
				alert(mailster.l10n.manage.select_status);
				return false;
			}

			if (!confirm(mailster.l10n.manage.confirm_import)) return false;


			var _this = $(this).prop('disabled', true),
				status = $('input[name="status"]:checked').val(),
				existing = $('input[name="existing"]:checked').val(),
				signup = $('#signup').is(':checked'),
				signupdate = $('#signupdate').val(),
				keepstatus = $('#keepstatus').is(':checked'),
				loader = $('#import-ajax-loading').css({
					'display': 'inline-block'
				}),
				identifier = $('#identifier').val(),
				performance = $('#performance').is(':checked');



			progress.removeClass('hidden');
			progressbar.stop().width(0);
			$('.step1').slideUp();
			$('.step2-body').html('<br><br>').parent().show();

			importstarttime = new Date();

			do_import(0, {
				identifier: identifier,
				order: order,
				lists: lists,
				status: status,
				keepstatus: keepstatus,
				existing: existing,
				signupdate: signup ? signupdate : null,
				performance: performance
			});

			importstatus.html(mailster.l10n.manage.prepare_import);

			window.onbeforeunload = function () {
				return mailster.l10n.manage.onbeforeunloadimport;
			};


		})
		.on('change', '.wordpress-users-toggle', function () {
			$(this).parent().parent().parent().find('li input').prop('checked', $(this).prop('checked'));
		})
		.on('click', '#addlist', function () {
			var val = $('#new_list_name').val();
			if (!val) return false;

			$('<li><label><input name="lists[]" value="' + val + '" type="checkbox" checked> ' + val + ' </label></li>').appendTo('#lists > ul');
			$('#new_list_name').val('');

		})
		.on('change', '.list-toggle', function () {
			$(this).parent().parent().parent().find('ul input').prop('checked', $(this).prop('checked'));
		})
		.on('change', 'input[name="status"]', function () {
			if ($(this).val() <= 0) {
				$('.pending-info').show();
			} else {
				$('.pending-info').hide();
			}
		});

	;

	$('#paste-import')
		.on('focus', function () {
			$(this).val('').addClass('focus');
		})
		.on('blur', function () {
			$(this).removeClass('focus');
			var value = mailster.util.trim($(this).val());

			if (value) {
				mailster.util.ajax('import_subscribers_upload_handler', {
					data: value
				}, function (response) {

					if (response.success) {
						importidentifier = response.identifier;
						$('#wordpress-users').fadeOut();
						get_import_data();
					} else {
						importstatus.html(response.message);
						progress.addClass('error');
					}
				}, function () {

					importstatus.html('Error');
				});
			}
		});
	$('#import_wordpress')
		.on('submit', function () {

			var data = $(this).serialize();
			mailster.util.ajax('import_subscribers_upload_handler', {
				wordpressusers: data
			}, function (response) {

				if (response.success) {
					importidentifier = response.identifier;
					$('#wordpress-users').fadeOut();
					get_import_data();
				} else {
					importstatus.html(response.message);
					progress.addClass('error');
				}
			}, function () {

				importstatus.html('Error');
			});

			return false;
		});

	$('select[name="outputformat"]').on('change', function () {
		$('#csv-separator')[$(this).val() == 'csv' ? 'show' : 'hide']();
	});


	mailster.events.push('documentReady', function () {
		$.fn.sortable && $(".export-order")
			.sortable({
				connectWith: '.export-order',
				_placeholder: "ui-state-highlight",
				containment: ".export-order-wrap",
				receive: function (event, ui) {
					ui.item.find('input').prop('checked', ui.item.closest('.export-order').is('.selected'));
				},
			})
			.on('change', 'input', function () {
				var _this = $(this);
				_this.parent().appendTo(_this.is(':checked') ? $('.export-order.selected') : $('.export-order.unselected'))
			});
	});

	$('.export-order-wrap')
		.on('click', '.export-order-add', function () {
			$(".export-order.unselected").find('li').appendTo('.export-order.selected').find('input').prop('checked', true);
			return false;
		})
		.on('click', '.export-order-remove', function () {
			$(".export-order.selected").find('li').appendTo('.export-order.unselected').find('input').prop('checked', false);
			return false;
		})

	$('#export-subscribers').on('submit', function () {

		var data = $(this).serialize();

		mailster.util.ajax('export_contacts', {
			data: data,
		}, function (response) {

			if (response.success) {

				window.onbeforeunload = function () {
					return mailster.l10n.manage.onbeforeunloadexport;
				};

				var limit = $('.performance').val();

				$('.step2').slideDown();
				$('.step2-body').html(mailster.util.sprintf(mailster.l10n.manage.write_file, '0.00 Kb'));
				progress.removeClass('finished error hidden');
				progressbar.stop().width(0);
				do_export(0, limit, response.count, data);

			} else {
				alert(response.msg);
			}

		}, function (jqXHR, textStatus, errorThrown) {
			alert(textStatus);
		});
		return false;
	});

	$('#delete-subscribers')
		.on('submit', function () {

			var input = prompt(mailster.l10n.manage.confirm_delete, '');

			if (!input) return false;

			if ('delete' == input.toLowerCase()) {

				var data = $(this).serialize();

				progress.removeClass('finished error hidden');

				progressbar.stop().animate({
					'width': '99%'
				}, 25000);

				mailster.util.ajax('delete_contacts', {
					data: data,
				}, function (response) {

					if (response.success) {
						progressbar.stop().animate({
							'width': '100%'
						}, 200, function () {
							$('.delete-status').html(response.msg);
							progress.addClass('finished');
						});
					} else {
						progressbar.stop();
						$('.delete-status').html(response.msg);
						progress.addClass('error');
					}

				}, function (jqXHR, textStatus, errorThrown) {

					progressbar.stop();
					$('.delete-status').html('[' + jqXHR.status + '] ' + errorThrown);
					progress.addClass('error');

				});

			}

			return false;
		});

	$('#delete-subscribers').on('change', 'input, select', update_deletion_count);

	update_deletion_count();

	function update_deletion_count() {

		setTimeout(function () {
			var data = $('#delete-subscribers').serialize();
			$('#delete-subscriber-button').prop('disabled', true);

			mailster.util.ajax('get_subscriber_count', {
				data: data
			}, function (response) {

				if (response.success) {
					$('#delete-subscriber-button').val(mailster.util.sprintf(mailster.l10n.manage.delete_n_subscribers, response.count)).prop('disabled', !response.count);
				}

			});
		}, 10);

	}

	$('input.selectall').on('change', function () {
		var _this = $(this),
			name = _this.attr('name');

		$('input[name="' + name + '"]').prop('checked', _this.prop('checked'));
	});


	function do_export(offset, limit, count, data) {

		var t = new Date().getTime(),
			percentage = (Math.min(1, (limit * offset) / count) * 100);

		exportstatus.html(mailster.util.sprintf(mailster.l10n.manage.prepare_download, count, ''));

		mailster.util.ajax('do_export', {
			limit: limit,
			offset: offset,
			data: data
		}, function (response) {

			var finished = percentage >= 100 && response.finished;

			if (response.success) {

				if (!finished) do_export(offset + 1, limit, count, data);

				progressbar.animate({
					'width': (percentage) + '%'
				}, {
					duration: 1000,
					easing: 'swing',
					queue: false,
					step: function (percentage) {
						exportstatus.html(mailster.util.sprintf(mailster.l10n.manage.prepare_download, count, Math.ceil(percentage) + '%'));
					},
					complete: function () {
						exportstatus.html(mailster.util.sprintf(mailster.l10n.manage.prepare_download, count, Math.ceil(percentage) + '%'));
						if (finished) {
							window.onbeforeunload = null;
							progress.addClass('finished');
							$('.step2-body').html(mailster.l10n.manage.export_finished);

							exportstatus.html(mailster.util.sprintf(mailster.l10n.manage.downloading, count));
							if (response.filename) setTimeout(function () {
								document.location = response.filename
							}, 1000);

						} else {
							$('.step2-body').html(mailster.util.sprintf(mailster.l10n.manage.write_file, response.total));
						}
					}
				});

			} else {

				progressbar.stop();
				progress.addClass('error');
				window.onbeforeunload = null;
				exportstatus.html(mailster.l10n.manage.error_export);
				$('.step2-body').html(response.msg);

			}

		}, function (jqXHR, textStatus, errorThrown) {


		});

	}

	function do_import(id, options) {

		var percentage = 0;

		if (!id) id = 0;

		mailster.util.ajax('do_import', {
			id: id,
			options: options
		}, function (response) {

			percentage = (Math.min(1, (response.imported + response.errors) / response.total) * 100);

			$('.step2-body').html('<p>' + get_stats(response.f_imported, response.f_errors, response.f_total, percentage, response.memoryusage) + '</p>');
			importerrors = 0;
			var finished = percentage >= 100;

			if (response.success) {

				if (!finished) do_import(id + 1, options);

				progressbar.animate({
					'width': (percentage) + '%'
				}, {
					duration: 1000,
					easing: 'swing',
					queue: false,
					step: function (percentage) {
						importstatus.html(mailster.util.sprintf(mailster.l10n.manage.import_contacts, Math.ceil(percentage) + '%'));
					},
					complete: function () {
						importstatus.html(mailster.util.sprintf(mailster.l10n.manage.import_contacts, Math.ceil(percentage) + '%'));
						if (finished) {
							window.onbeforeunload = null;
							progress.addClass('finished');
							$('.step2-body').html(response.html).slideDown();

						}
					}
				});
			} else {
				upload_error_handler(percentage, id, options);
			}
		}, function (jqXHR, textStatus, errorThrown) {

			upload_error_handler(percentage, id, options);

		});

	}

	function get_import_data() {

		progress.removeClass('finished error');

		mailster.util.ajax('get_import_data', {
			identifier: importidentifier
		}, function (response) {
			progress.addClass('hidden');

			$('.step1').slideUp();
			$('.step2-body').html(response.html).parent().show();

			$('input.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				showAnim: 'fadeIn',
				onClose: function () {}
			});

			importstatus.html('');

			importdata = response.data;
		});

	}

	function upload_error_handler(percentage, id, options) {

		importerrors++;

		if (importerrors >= 5) {

			alert(mailster.l10n.manage.error_importing);
			window.onbeforeunload = null;
			return;
		}

		var i = importerrors * 5,
			str = '',
			errorint = setInterval(function () {

				if (i <= 0) {
					clearInterval(errorint);
					progress.removeClass('paused');
					do_import(id, options);
					str = Math.round(percentage) + '%';
				} else {
					progress.addClass('paused');
					str = '<span class="error">' + mailster.util.sprintf(mailster.l10n.manage.continues_in, (i--)) + '</span>';

				}
				importstatus.html(mailster.util.sprintf(mailster.l10n.manage.import_contacts, str));


			}, 1000);
	}


	function get_stats(imported, errors, total, percentage, memoryusage) {

		var timepast = new Date().getTime() - importstarttime.getTime(),
			timeleft = Math.ceil(((100 - percentage) * (timepast / percentage)) / 60000);

		return mailster.util.sprintf(mailster.l10n.manage.current_stats, '<strong>' + imported + '</strong>', '<strong>' + total + '</strong>', '<strong>' + errors + '</strong>', '<strong>' + memoryusage + '</strong>') + '<br>' +
			mailster.util.sprintf(mailster.l10n.manage.estimate_time, timeleft);

	}

	return mailster;

}(mailster || {}, jQuery, window, document));