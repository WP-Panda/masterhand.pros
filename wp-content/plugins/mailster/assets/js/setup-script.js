mailster = (function (mailster, $, window, document) {

	"use strict";

	var steps = $('.mailster-setup-step'),
		currentStep, currentID,
		status = $('.status'),
		spinner = $('.spinner'),
		hash = location.hash.substr(1);

	if (hash && $('#step_' + hash).length) {
		currentStep = $('#step_' + hash);
	} else {
		currentStep = steps.eq(0);
	}

	currentID = currentStep.attr('id').replace(/^step_/, '');

	steps.hide();
	step(currentID);

	$('form.mailster-setup-step-form').on('submit', function () {
		$('.next-step:visible').hide();
		return false;
	});

	$('#mailster-setup')
		.on('click', '.validation-skip-step', function () {
			return confirm(mailster.l10n.setup.skip_validation);
		})
		.on('click', '.next-step', function () {

			if ($(this).hasClass('disabled')) return false;

			if (tinymce) tinymce.get('post_content').save();

			var form = $(this).parent().parent().find('form'),
				data = form.serialize();
			mailster.util.ajax('wizard_save', {
				id: currentID,
				data: data
			}, function (response) {

			});

		})
		.on('click', '.load-language', function () {

			status.html(mailster.l10n.setup.load_language);
			spinner.css('visibility', 'visible');
			mailster.util.ajax('load_language', function (response) {

				spinner.css('visibility', 'hidden');
				status.html(response.html);
				if (response.success) {
					location.reload();
				}

			});

			return false;


		})
		.on('click', '.quick-install', function () {

			var _this = $(this);

			install(_this.data('plugin'), _this.data('method'), _this.parent());

		})
		.on('click', '.edit-slug', function () {
			$(this).parent().parent().find('span').hide().filter('.edit-slug-area').show().find('input').focus().select();
		});

	mailster.$.document
		.on('verified.mailster', function () {
			$('.validation-next-step').removeClass('disabled');
			$('.validation-skip-step').addClass('disabled');
		});


	check_language();

	var deliverynav = $('#deliverynav'),
		deliverytabs = $('.deliverytab');

	deliverynav.on('click', 'a.nav-tab', function () {
		deliverynav.find('a').removeClass('nav-tab-active');
		deliverytabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href').substr(1);
		$('#deliverymethod').val(hash);
		$('#deliverytab-' + hash).show();

		if ($('#deliverytab-' + hash).find('.quick-install').length) {
			$('.delivery-next-step').addClass('disabled').html(sprintf(mailster.l10n.setup.enable_first, $(this).html()));
		} else {
			$('.delivery-next-step').removeClass('disabled').html(sprintf(mailster.l10n.setup.use_deliverymethod, $(this).html()));
		}
		return false;
	});

	mailster.$.window
		.on('hashchange', function () {

			var id = location.hash.substr(1) || 'start',
				current = $('.mailster-setup-steps-nav').find("a[href='#" + id + "']"),
				next, prev;

			if (current.length) {
				step(id);
				current.parent().parent().find('a').removeClass('next prev current');
				current.parent().prevAll().find('a').addClass('prev');
				current.addClass('current');
				if (tinymce && tinymce.activeEditor) tinymce.activeEditor.theme.resizeTo('100%', 200);
			}

			if ('finish' == id) {
				mailster.util.ajax('wizard_save', {
					id: id,
					data: null
				});
			}


		})

	mailster.events.push('documentReady', function () {
		mailster.$.window.trigger('hashchange');
	})

	function check_language() {

		status.html(mailster.l10n.setup.check_language);
		spinner.css('visibility', 'visible');

		mailster.util.ajax('check_language', function (response) {

			spinner.css('visibility', 'hidden');
			status.html(response.html);
			if (response.success) {}

		});
	}

	function step(id) {

		var step = $('#step_' + id);

		if (step.length) {
			currentStep.hide();
			currentStep = step;
			currentStep.show();
			currentID = id;
		}

	}

	function install(plugin, method, element, callback) {

		status.html(mailster.l10n.setup.install_addon);
		spinner.css('visibility', 'visible');

		mailster.util.ajax('quick_install', {
			plugin: plugin,
			method: method,
			step: 'install'
		}, function (response) {

			status.html(mailster.l10n.setup.activate_addon);
			mailster.util.ajax('quick_install', {
				plugin: plugin,
				method: method,
				step: 'activate'
			}, function (response) {

				status.html(mailster.l10n.setup.receiving_content);
				mailster.util.ajax('quick_install', {
					plugin: plugin,
					method: method,
					step: 'content'
				}, function (response) {

					status.html('');
					spinner.css('visibility', 'hidden');
					element.html(response.content);
					deliverynav.find('a.nav-tab-active').trigger('click');

				});

			});

		});

	}


	return mailster;

}(mailster || {}, jQuery, window, document));