window.mailster = window.mailster || {};

// block localization
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.l10n = window.mailster_l10n;

	return mailster;

}(mailster || {}, jQuery, window, document));
// end localization

// events
mailster = (function (mailster, $, window, document) {

	"use strict";

	var triggertimeout,
		isEnabled = !$('#mailster_disabled').val(),
		events = {
			documentReady: [],
			windowLoad: [],
		},
		last;

	mailster.events = mailster.events || false;

	mailster.status = {
		documentReady: false,
		windowLoad: false,
		windowLoadPending: false,
	};

	//already events registered
	if (mailster.events) {
		for (var i in mailster.events) {
			mailster.log(i, mailster.events[i]);
			if (typeof mailster.events[i] == 'string') {
				last = mailster.events[i];
				events[last] = events[last] || [];
				continue;
			}
			events[last].push(mailster.events[i]);
		}
	}

	mailster.events = events;

	$(document).ready(documentReady);
	$(window).on("load", windowLoad);

	function documentReady(context) {
		context = typeof context === typeof undefined ? $ : context;
		events.documentReady.forEach(function (component) {
			component(context);
		});
		mailster.status.documentReady = true;
		if (mailster.status.windowLoadPending) {
			windowLoad(setContext());
		}
	}

	function windowLoad(context) {
		if (mailster.status.documentReady) {
			mailster.status.windowLoadPending = false;
			context = typeof context === "object" ? $ : context;
			events.windowLoad.forEach(function (component) {
				component(context);
			});
			mailster.status.windowLoad = true;
		} else {
			mailster.status.windowLoadPending = true;
		}
	}

	function debug(data, type) {
		if (console) {
			for (var i = 0; i < data.length; i++) {
				console[type](data[i]);
			}
		}
	}

	function setContext(contextSelector) {
		var context = $;
		if (typeof contextSelector !== typeof undefined) {
			return function (selector) {
				return $(contextSelector).find(selector);
			};
		}
		return context;
	}

	mailster.events.push = function () {

		var params = Array.prototype.slice.call(arguments),
			event = params.shift(),
			callbacks = params || null;

		mailster.events[event] = mailster.events[event] || [];

		for (var i in callbacks) {
			mailster.events[event].push(callbacks[i]);
		}

		return true;

	}

	mailster.trigger = function () {

		var params = Array.prototype.slice.call(arguments),
			triggerevent = params.shift(),
			args = params || null;

		if (mailster.events[triggerevent]) {
			for (var i = 0; i < mailster.events[triggerevent].length; i++) {
				mailster.events[triggerevent][i].apply(mailster, args);
			}
		} else {
			//events[triggerevent] = [];
		}
	}

	mailster.log = function () {

		debug(arguments, 'log');
	}

	mailster.error = function () {

		debug(arguments, 'error');
	}

	mailster.warning = function () {

		debug(arguments, 'warn');
	}


	return mailster;

}(mailster || {}, jQuery, window, document));


mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.util = mailster.util || {};

	mailster.util.requestAnimationFrame = window.requestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.msRequestAnimationFrame;

	mailster.util.ajax = function (action, data, callback, errorCallback, dataType) {

		if ('function' === typeof data) {
			if ('function' === typeof callback) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}

		dataType = dataType ? dataType : "JSON";
		$.ajax({
			type: 'POST',
			url: mailster.ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: mailster.wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				var response = mailster.util.trim(jqXHR.responseText);
				if (textStatus == 'error' && !errorThrown) return;
				mailster.log(response, 'error');
				if ('JSON' == dataType) {
					var maybe_json = response.match(/{(.*)}$/);
					if (maybe_json && callback) {
						try {
							callback.call(this, JSON.parse(maybe_json[0]));
						} catch (e) {
							mailster.log(e, 'error');
						}
						return;
					}
				}
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);
				alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\n' + mailster.l10n.common.check_console)

			},
			dataType: dataType
		});
	}

	mailster.util.rgb2hex = function (str) {
		var colors = str.match(/rgb\((\d+), ?(\d+), ?(\d+)\)/);

		function nullify(val) {
			val = parseInt(val, 10).toString(16);
			return val.length > 1 ? val : '0' + val; // 0 -> 00
		}
		return colors ? '#' + nullify(colors[1]) + nullify(colors[2]) + nullify(colors[3]) : str;

	}

	mailster.util.sanitize = function (string) {
		return mailster.util.trim(string).toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_-]*/g, '');
	}

	mailster.util.sprintf = function () {
		var a = Array.prototype.slice.call(arguments),
			str = a.shift(),
			total = a.length,
			reg;
		for (var i = 0; i < total; i++) {
			reg = new RegExp('%(' + (i + 1) + '\\$)?(s|d|f)');
			str = str.replace(reg, a[i]);
		}
		return str;
	}

	mailster.util.trim = function (string) {
		if ('string' !== typeof string) {
			return string;
		}
		return string.trim();
	}

	mailster.util.isWebkit = 'WebkitAppearance' in document.documentElement.style;
	mailster.util.isMozilla = (/firefox/i).test(navigator.userAgent);
	mailster.util.isMSIE = (/msie|trident/i).test(navigator.userAgent);
	mailster.util.isTouchDevice = 'ontouchstart' in document.documentElement;

	mailster.util.CodeMirror = null;

	mailster.events.push('documentReady', function () {
		mailster.util.CodeMirror = wp.CodeMirror || window.CodeMirror;
	});

	mailster.util.top = function () {
		return $('html,body').scrollTop() || document.scrollingElement.scrollTop;
	}

	mailster.util.scroll = function (pos, callback, speed) {
		var t;
		pos = Math.round(pos);
		if (isNaN(speed)) speed = 200;
		if (!mailster.util.isMSIE && mailster.util.top() == pos) {
			callback && callback();
			return
		}
		$('html,body').stop().animate({
			'scrollTop': pos
		}, speed, function () {
			//prevent double execution
			clearTimeout(t);
			t = setTimeout(callback, 0);
		});
	}

	mailster.util.jump = function (val, rel) {
		val = Math.round(val);
		if (rel) {
			window.scrollBy(0, val);
		} else {
			window.scrollTo(0, val);
		}
	}

	mailster.util.inViewport = function (el, offset) {
		var rect = el.getBoundingClientRect();

		if (!offset) offset = 0;

		//only need top and bottom
		return (
			rect.top + offset >= 0 &&
			rect.top - offset <= (window.innerHeight || document.documentElement.clientHeight) /*or $(window).height() */
		);
	}

	mailster.util.debounce = function (callback, delay) {

		return mailster.util.throttle(callback, delay, true);

	}

	mailster.util.throttle = function (callback, delay, debounce) {
		var timeout,
			last = 0;

		if (delay === undefined) delay = 250;

		function api() {
			var that = this,
				elapsed = +new Date() - last,
				args = arguments;

			function run() {
				last = +new Date();
				callback.apply(that, args);
			};

			function clear() {
				timeout = undefined;
			};

			if (debounce && !timeout) {
				run();
			}

			timeout && clearTimeout(timeout);

			if (debounce === undefined && elapsed > delay) {
				run();
			} else {
				timeout = setTimeout(debounce ? clear : run, debounce === undefined ? delay - elapsed : delay);
			}
		};

		return api;
	};

	return mailster;

}(mailster || {}, jQuery, window, document));



mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$ = {};
	mailster.dom = {};

	mailster.$.window = $(window);
	mailster.$.document = $(document);

	//open externals in a new tab
	mailster.$.document
		.on('click', 'a.external', function () {
			window.open(this.href);
			return false;
		})

	mailster.util.tb_position = function () {
		if (!window.TB_WIDTH || !window.TB_HEIGHT) return;
		$('#TB_window').css({
			marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px',
			marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
			width: TB_WIDTH + 'px'
		});
	}

	mailster.events.push('documentReady', function () {
		window.tb_position = mailster.util.tb_position;
		for (var i in mailster.$) {
			mailster.dom[i] = mailster.$[i][0];
		}
	});
	return mailster;

}(mailster || {}, jQuery, window, document));