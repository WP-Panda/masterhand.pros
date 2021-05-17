mailster = (function (mailster, $, window, document) {

	"use strict";

	$('.register_form_wrap')
		.on('focus', 'input', function () {
			$(this).attr('data-placeholder', $(this).attr('placeholder'));
			$(this).attr('placeholder', '');
		})
		.on('blur', 'input', function () {
			$(this).attr('placeholder', $(this).attr('data-placeholder'));
		})
		.on('click', '.envato-signup', function () {

			popup(this.href, 600, 900, 'mailster_envato_signup');
			return false;
		})
		.on('click', '.howto-purchasecode', function () {

			mailster.dialog.open('registration-dialog');
			return false;
		})
		.on('change', '.tos', function () {
			$(this).val(Math.round(new Date().getTime() / 1000));
		})
		.on('submit', '.register_form', function () {

			var form = $(this),
				wrap = form.parent(),
				purchasecode = wrap.find('input.register-form-purchasecode').val(),
				slug = wrap.find('input.register-form-slug').val(),
				error;

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			mailster.util.ajax('register', {
				purchasecode: purchasecode,
				slug: slug
			}, function (response) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				if (response.success) {
					wrap.addClass('step-2').removeClass('step-1')
				} else {
					error = response.error;
					error += ' <a href="https://evp.to/error-' + response.code + '" target="_blank" rel="noopener">' + mailster.l10n.register.help + '</a>';
					form.addClass('has-error').find('.error-msg').html(error);
				}

			}, function (jqXHR, textStatus, errorThrown) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				alert(mailster.l10n.register.error + "\n\n" + errorThrown);
			});

			return false;

		})
		.on('submit', '.register_form_2', function () {

			var form = $(this),
				wrap = form.parent(),
				purchasecode = wrap.find('input.register-form-purchasecode').val(),
				slug = wrap.find('input.register-form-slug').val(),
				error;

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			mailster.util.ajax('register', {
				purchasecode: purchasecode,
				slug: slug,
				data: form.serialize()
			}, function (response) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				if (response.success) {
					wrap.addClass('step-3').removeClass('step-2')
					mailster.$.document.trigger('verified.' + slug, [response.purchasecode, response.username, response.email]);
				} else {
					if (response.code == 406 || response.code == 679 || response.code == 680) {
						form = wrap.find('.register_form');
						form.parent().removeClass('step-2').addClass('step-1');
					}
					error = response.error;
					error += ' (<a href="https://evp.to/error-' + response.code + '" target="_blank" rel="noopener">' + mailster.l10n.register.help + '</a>)';
					form.addClass('has-error').find('.error-msg').html(error);
				}
			}, function (jqXHR, textStatus, errorThrown) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				alert(mailster.l10n.register.error + "\n\n" + errorThrown);
			});

			return false;

		})
		.removeClass('loading');


	function popup(url, width, height, windowname) {

		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
			dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
			windowWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
			windowHeight = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,
			left = ((windowWidth / 2) - (width / 2)) + dualScreenLeft,
			top = ((windowHeight / 2) - (height / 2)) + dualScreenTop,
			newWindow = window.open(url, windowname, 'scrollbars=auto,resizable=1,menubar=0,toolbar=0,location=0,directories=0,status=0, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

		if (window.focus)
			newWindow.focus();

	}

	window.verifyMailster = function (slug, purchasecode, username, email) {

		var wrap = $('.register_form_wrap-' + slug);
		wrap.find('input.register-form-purchasecode').val(purchasecode);
		wrap.find('input.username').val(username);
		wrap.find('input.email').val(email);

		if (purchasecode) {
			wrap.find('.register_form').parent().removeClass('step-1').addClass('step-2');
		} else {
			wrap.find('.register_form').submit();
		}

	}

	mailster.$.document
		.on('verified.mailster', function (event, purchasecode, username, email) {

			! function (f, b, e, v, n, t, s) {
				if (f.fbq) {
					return;
				}
				n = f.fbq = function () {
					n.callMethod ?
						n.callMethod.apply(n, arguments) : n.queue.push(arguments)
				};
				if (!f._fbq) {
					f._fbq = n;
				}
				n.push = n;
				n.loaded = !0;
				n.version = '2.0';
				n.queue = [];
				t = b.createElement(e);
				t.async = !0;
				t.src = v;
				s = b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t, s)
			}(window, document, 'script',
				'https://connect.facebook.net/en_US/fbevents.js');

			fbq('init', '2235134113384930');
			fbq('track', 'CompleteRegistration');

		});


	return mailster;

}(mailster || {}, jQuery, window, document));