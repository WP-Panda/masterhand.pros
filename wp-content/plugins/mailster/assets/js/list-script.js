mailster = (function (mailster, $, window, document) {

	"use strict";

	$.easyPieChart && $('.piechart').easyPieChart({
		animate: 1000,
		rotate: 180,
		barColor: mailster.colors.main,
		trackColor: mailster.colors.track_light,
		lineWidth: 9,
		size: 75,
		lineCap: 'butt',
		onStep: function (value) {
			this.$el.find('span').text(Math.round(value));
		},
		onStop: function (value) {
			this.$el.find('span').text(Math.round(value));
		}
	});

	$('.detail').on('click', function () {

		var _this = $(this).addClass('active'),
			_ul = _this.find('.click-to-edit'),
			_first = _ul.find('> li').first(),
			_last = _ul.find('> li').last();

		if (!_first.is(':hidden')) {
			_first.hide();
			_last.show().find('input').first().focus().select();
		}

	});

	return mailster;

}(mailster || {}, jQuery, window, document));