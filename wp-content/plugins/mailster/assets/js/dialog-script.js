mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.dialog = mailster.dialog || {};

	var current,
		dialog;

	mailster.$.document
		.on('click', '.notification-dialog-dismiss', function (event) {
			event.stopPropagation();
			cancel();
		})
		.on('click', '.notification-dialog-background', function (event) {
			event.stopPropagation();
			cancel();
		})
		.on('click', '.notification-dialog-submit', function (event) {
			event.stopPropagation();
			submit();
		});

	function cancel() {
		close();
	}

	function submit() {
		close();
	}

	function close() {
		dialog.addClass('hidden');
		mailster.$.document
			.off('keyup.mailster_dialog');
		current = null;
	}

	function open(id) {
		dialog = $('.mailster-' + id);
		current = id;
		dialog.removeClass('hidden');
		mailster.$.document
			.on('keyup.mailster_dialog', function (event) {
				if (event.which == 27) {
					cancel();
				}
			});
	}

	mailster.dialog.current = current;
	mailster.dialog.close = close;
	mailster.dialog.open = open;

	return mailster;

}(mailster || {}, jQuery, window, document));