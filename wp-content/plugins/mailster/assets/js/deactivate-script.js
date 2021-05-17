mailster = (function (mailster, $, window, document) {
	"use strict"

	var dialog = $('#mailster-deactivation-dialog'),
		form = $('#mailster-deactivation-survey'),
		survey_extra = $('.mailster-survey-extra'),
		textareas = form.find('textarea');

	$('tr[data-slug="mailster"]').on('click', '.deactivate > a', function () {
		mailster.dialog.open('deactivation-dialog');
		return false;
	})

	dialog
		.on('click', '.deactivate', function () {
			form.submit();
			return false;
		})
		.on('click', '.cancel', function () {
			mailster.dialog.close();
			return false;
		});

	$('.mailster-delete-data').on('change', '[name="delete_data"]', function () {
		$('.mailster-delete-data').find('input').not(this).prop('checked', $(this).prop('checked')).prop('disabled', !$(this).prop('checked'));
	})

	form
		.on('submit', function () {
			if (!$('[name="mailster_surey_reason"]:checked').length) {
				alert(mailster.l10n.deactivate.select_reason);
				return false;
			}
		})
		.on('change', '[name="mailster_surey_reason"]', function () {
			textareas.prop('disabled', true);
			survey_extra.hide();
			$(this).parent().parent().find('.mailster-survey-extra').show().find('textarea').prop('disabled', false).focus();
		});

	return mailster;

}(mailster || {}, jQuery, window, document));