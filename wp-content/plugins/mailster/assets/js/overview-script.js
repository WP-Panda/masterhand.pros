mailster = (function (mailster, $, window, document) {

	"use strict";

	var current = [],
		scrolltimeout = false,
		rows = $('tr.type-newsletter'),
		select1 = $('#bulk-action-selector-top'),
		select2 = $('#bulk-action-selector-bottom');

	$('#posts-filter').on('submit', function () {
		var s1 = select1.val(),
			s2 = select2.val(),
			v = s1 != -1 ? s1 : (s2 != -1 ? s2 : false);

		switch (v) {
		case 'finish':
			return confirm(mailster.l10n.campaigns.finish_campaigns);
			break;
		case 'start':
			return confirm(mailster.l10n.campaigns.start_campaigns);
			break;
		}

	});

	$('.column-status')
		.on('click', 'a.live-action', function () {

			if ($(this).hasClass('finish') && !confirm(mailster.l10n.campaigns.finish_campaign)) {
				return false;
			}

			var row = $(this).parent().parent().parent().addClass('loading');

			$.get($(this).attr('href'), function () {
				setTimeout(function () {
					wp.heartbeat.connectNow();
				}, 500);
			});
			return false;

		});

	mailster.$.document
		.on('scroll', function () {
			clearTimeout(scrolltimeout);
			scrolltimeout = setTimeout(function () {
				wp.heartbeat.connectNow();
			}, 400);
		})
		.on('heartbeat-send', function (e, data) {

			var ids = [],
				id;

			rows.each(function () {
				id = parseInt($(this).find('input').eq(0).val(), 10);
				current[id] = current[id] || {};
				if (mailster.util.inViewport(this))
					ids.push(id);
			});

			data['mailster'] = {
				page: 'overview',
				ids: ids
			};

		})
		.on('heartbeat-tick', function (e, data) {

			var first = false;

			if (data['mailster']) {

				if (!current) {
					current = $.extend(data['mailster'], current);
					first = true;
				}

				var change = false,
					i = 0;

				$.each(rows, function (id, row) {

					var rowdata;
					row = $(row);
					id = row.attr('id').replace('post-', '');
					row.removeClass('loading');

					if (!data['mailster'][id]) return;
					rowdata = data['mailster'][id];

					row.find('.column-status')[rowdata.cron ? 'removeClass' : 'addClass']('cron-issue');
					row.find('.campaign-status').html(rowdata.cron ? '' : rowdata.status_title);

					$.each(rowdata, function (key, value) {
						if (!first && current[id][key] == value) return;

						var statuschange = current[id] && current[id].status && (rowdata.status != current[id].status || rowdata.is_active != current[id].is_active);

						switch (key) {
						case 'status':
							if (statuschange) {
								row.removeClass('status-' + current[id].status).addClass('status-' + rowdata.status);
							}
						case 'sent':
						case 'total':
						case 'sent_formatted':
							break;
						case 'column-status':
							if (rowdata.status == 'active' && !statuschange) {
								var progress = row.find('.campaign-progress'),
									p = (rowdata.sent / rowdata.total * 100);
								progress.find('.bar').width(p + '%');
								progress.find('span').eq(1).html(rowdata.sent_formatted);
								progress.find('span').eq(2).html(rowdata.sent_formatted);
								progress.find('var').html(Math.round(p) + '%');
							}
							if (!statuschange) break;
						default:
							var el = row.find('.' + key);
							if (!el.is(':visible')) break;
							el.fadeTo(10, 0.01, function () {
								el.html(value).delay(10 * (i++)).fadeTo(200, 1);
							});

						}

						change = true;

					});


				});

				if (change) wp.heartbeat.interval('fast');
				current = $.extend(current, data['mailster']);
			}

		});

	wp.heartbeat.interval('fast');
	if (wp.heartbeat.connectNow) wp.heartbeat.connectNow();

	return mailster;

}(mailster || {}, jQuery, window, document));