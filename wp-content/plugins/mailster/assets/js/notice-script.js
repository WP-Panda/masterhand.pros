mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.notices = mailster.notices || {};

	mailster.notices.$ = $('.mailster-notice');

	mailster.$.document
		.on('click', '.mailster-notice .notice-dismiss, .mailster-notice .dismiss', function (event) {

			event.preventDefault();

			var el = $(this).closest('.mailster-notice'),
				id = el.data('id'),
				type = !event.altKey ? 'notice_dismiss' : 'notice_dismiss_all';

			if (event.altKey) el = mailster.notices.$;

			if (id) {
				mailster.util.ajax(type, {
					id: id
				});
				el.fadeTo(100, 0, function () {
					el.slideUp(100, function () {
						el.remove();
					});
				})
			}
		});

	return mailster;

}(mailster || {}, jQuery, window, document));