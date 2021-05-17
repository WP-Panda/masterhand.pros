mailster = (function (mailster, $, window, document) {

	"use strict";

	var single_test = $('#singletest').val() || null,
		start_button = $('.start-test'),
		output = $('.tests-output'),
		textoutput = $('.tests-textoutput'),
		tests = $('.tests-wrap'),
		testinfo = $('.test-info'),
		progress = $('#progress'),
		progressbar = progress.find('.bar'),
		outputnav = $('#outputnav'),
		outputtabs = $('.subtab'),
		errors, tests_run;

	start_button.on('click', function () {
		start_button.hide();
		progress.show();
		output.empty();
		textoutput.val(textoutput.data('pretext'));
		tests_run = 1;
		test(single_test);
		errors = {
			'error': 0,
			'warning': 0,
			'notice': 0,
			'success': 0,
		};
		return false;
	});

	output.on('click', 'a', function () {
		if (this.className.match(/retest/)) return true;
		if (this.href) window.open(this.href);
		return false;
	});

	tests
		.on('change', 'input', function () {
			$(this).is(':checked') ?
				tests.removeClass('no-' + $(this).data('type')) :
				tests.addClass('no-' + $(this).data('type'));
		});

	outputnav.on('click', 'a.nav-tab', function () {
		outputnav.find('a').removeClass('nav-tab-active');
		outputtabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href');
		location.hash = hash;
		$('#subtab-' + hash.substr(1)).show();
		if (hash == '#systeminfo') {
			var textarea = $('#system_info_content');
			if (mailster.util.trim(textarea.val())) return;
			textarea.val('...');
			mailster.util.ajax('get_system_info', function (response) {

				if (response.log)
					mailster.log(response.log);
				textarea.val(response.msg);
			});
		}
		return false;
	});


	if (/autostart/.test(location.search)) {
		start_button.trigger('click');
	} else {
		(location.hash && outputnav.find('a[href="' + location.hash + '"]').length) ?
		outputnav.find('a[href="' + location.hash + '"]').trigger('click'): outputnav.find('a').eq(0).trigger('click');
	}

	function test(test_id) {

		mailster.util.ajax('test', {
			'test_id': test_id,
		}, function (response) {

			errors['error'] += response.errors.error;
			errors['warning'] += response.errors.warning;
			errors['notice'] += response.errors.notice;
			errors['success'] += response.errors.success;

			$(response.message.html).appendTo(output);
			textoutput.val(textoutput.val() + response.message.text);

			if (response.nexttest && !single_test) {
				progressbar.width(((++tests_run) / response.total * 100) + '%');
				testinfo.html(mailster.util.sprintf(mailster.l10n.tests.running_test, tests_run, response.total, response.next));
			} else {
				progressbar.width('100%');
				setTimeout(function () {
					start_button.html(mailster.l10n.tests.restart_test).show();
					progress.hide();
					progressbar.width(0);
					testinfo.html(mailster.util.sprintf(mailster.l10n.tests.tests_finished, errors.error, errors.warning, errors.notice));
				}, 500);
			}

			if (response.nexttest && !single_test) {
				setTimeout(function () {
					test(response.nexttest);
				}, 100);
			} else {}

		}, function (jqXHR, textStatus, errorThrown) {});
	}

	return mailster;

}(mailster || {}, jQuery, window, document));