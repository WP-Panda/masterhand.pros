(function () {

	"use strict"

	var html = jQuery('html'),
		body = jQuery('body'),
		origin = decodeURIComponent(location.search.match(/origin=(.+)&/)[1]);

	jQuery('.mailster-form-wrap')
		.on('click tap touchstart', function (event) {
			event.stopPropagation();
		});

	body
		.on('click tap touchstart', function (event) {
			event.stopPropagation();
			html.addClass('unload');
			setTimeout(function () {
				window.parent.postMessage('mailster|c', origin)
			}, 150);
		});

	jQuery(window).on('load', function () {
		html.addClass('loaded');
		jQuery('.mailster-wrapper').eq(0).find('input').focus().select();
	});

	jQuery(document).keydown(function (e) {
		if (e.keyCode == 27) {
			setTimeout(function () {
				window.parent.postMessage('mailster|c', origin)
			}, 150);
			return false;
		}
	});

})();