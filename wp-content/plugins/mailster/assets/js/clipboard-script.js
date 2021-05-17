mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.events.push('documentReady', function () {
		var clipboard = new Clipboard('.clipboard');
		clipboard.on('success', function (e) {
			var html = $(e.trigger).html();
			$(e.trigger).html(mailster.l10n.clipboard.copied);
			setTimeout(function () {
				$(e.trigger).html(html);
				e.clearSelection();
			}, 3000);
		});

		clipboard.on('error', function (e) {});
	})

	return mailster;

}(mailster || {}, jQuery, window, document));