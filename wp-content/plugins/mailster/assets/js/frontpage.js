jQuery(document).ready(function ($) {

	"use strict"

	var iframe = $('iframe'),
		sharebtn = $('.share').find('a'),
		sharebox = $('.sharebox'),
		share = sharebox.find('h4'),
		iOS = window.orientation !== 'undefined';


	function resize() {
		var height = window.innerHeight || $(window).height();
		iframe.attr("height", height + 300);
		if (iOS) {
			height = Math.max(iframe.contents().find("html").height(), iframe.height());
			$('body').height(height);
		}
	}

	$(window).on({
		'load.mailster resize.mailster': resize
	}).trigger('resize.mailster');

	$('#header').on('mousedown', function (event) {
		if (event.target.id == 'header') sharebox.fadeOut(600);
	});

	iframe.on('load', function () {
		iframe.contents().find('body')
			.on('click', 'a', function () {
				window.open(this.href);
				return false;
			})
			.on('mousedown', function () {
				sharebox.fadeOut(600);
			});
	});

	$('.social-services').on('click', 'a', function () {

		var href = this.href;

		if (!/^https?/.test(href)) return true;

		var dimensions = $(this).data(),
			dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
			dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
			width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
			height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,
			left = ((width / 2) - (dimensions.width / 2)) + dualScreenLeft,
			top = ((height / 2) - (dimensions.height / 2)) + dualScreenTop,
			newWindow = window.open(href, 'mailster_share', 'scrollbars=auto,resizable=1,menubar=0,toolbar=0,location=0,directories=0,status=0, width=' + dimensions.width + ', height=' + dimensions.height + ', top=' + top + ', left=' + left);

		if (window.focus)
			newWindow.focus();

		return false;
	});

	sharebtn.on('mouseenter', function () {
		sharebox.fadeIn(100);
	});

	share.on('click', function () {
		share.removeClass('active').next().slideUp(100);
		$(this).addClass('active').next().stop().slideDown(100, function () {
			$(this).find('input').eq(0).focus().select();
		});
	});

	sharebox.find('li.active').find('div').eq(0).show();

	$('#emailform').on('submit', function () {
		var _this = $(this),
			loader = $('#ajax-loading').css({
				'visibility': 'visible'
			}),
			data = _this.serialize();

		_this.find('input.button').prop('disabled', true);

		$.post(ajaxurl, {
			action: 'mailster_forward_message',
			data: data
		}, function (response) {
			loader.css({
				'visibility': 'hidden'
			});
			_this.find('.status').html(response.msg);
			if (!response.success) _this.find('input.button').prop('disabled', false);

		}, "JSON");
		return false;
	});

	$('.appsend').on('click', function () {

		var url = 'mailto:' + $('#receiver').val() + '?body=' + $('#message').val().replace(/\n/g, '%0D%0A') + '%0D%0A%0D%0A' + $('#url').val();
		window.location = url;

		return false;
	});

	if (placeholderIsSupported()) sharebox.find("[placeholder]").bind('focus.placeholder', function () {
		var el = $(this);
		if (el.val() == el.attr("placeholder")) {
			el.val("");
			el.removeClass("placeholder");
		}
	}).bind('blur.placeholder', function () {
		var el = $(this);
		if (el.val() == "" || el.val() == el.attr("placeholder")) {
			el.addClass("placeholder");
			el.val(el.attr("placeholder"));
		} else {

		}
	}).trigger('blur.placeholder');

	function placeholderIsSupported() {
		var test = document.createElement('input');
		return ('placeholder' in test);
	}

});