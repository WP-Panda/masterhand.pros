mailster = (function (mailster, $, window, document) {

	"use strict";

	if (typeof mailster_updates == 'undefined') {
		return;
	}

	var $output = $('#output'),
		$error = $('#error-list'),
		finished = [],
		current, current_i,
		skip = $('<span>&nbsp;</span><a class="skipbutton button button-small" href title="skip this step">skip</a>'),
		skipit = false,
		performance = mailster_updates_performance[0] || 1,
		keys = $.map(mailster_updates, function (element, index) {
			return index
		});

	$output.on('click', '.skipbutton', function () {
		skipit = true;
		return false;
	});

	mailster.$.document.ajaxError(function () {
		$error.append('script paused...continues in 5 seconds<br>');
		setTimeout(function () {
			$error.empty();
			run(current_i, true);
		}, 5000);
	});

	if (mailster_updates_options.autostart) {
		$('#mailster-update-process').show();
		run(0);
	} else {
		$('#mailster-update-info').show();
		$('#mailster-start-upgrade')
			.on('click', function () {
				$('#mailster-update-process').slideDown(200);
				$('#mailster-update-info').slideUp(200);
				run(0);
			});
	}

	function run(i, nooutput) {

		if (!i) {
			window.onbeforeunload = function () {
				return 'You have to finish the update before you can use Mailster!';
			};
		}

		var id = keys[i];

		current_i = i;

		if (!(current = mailster_updates[id])) {
			finish();
			return
		}

		if (!nooutput) output(id, '<strong>' + current + '</strong> ...', true);

		do_update(id, function () {
			setTimeout(function () {
				run(++i);
			}, 1000);
		}, function () {
			error();
		}, 1);

	}

	function do_update(id, onsuccess, onerror, round) {

		mailster.util.ajax('batch_update', {
			id: id,
			performance: performance
		}, function (response) {

			if (response && response.success) {

				if (response.output) textoutput(response.output);

				if (skipit) {
					output(id, ' &otimes;', false);
					skipit = false;
					onsuccess && onsuccess();
				} else if (response[id]) {
					output(id, ' &#10004;', false);
					onsuccess && onsuccess();
				} else {
					output(id, '.', false, round);
					setTimeout(function () {
						do_update(id, onsuccess, onerror, ++round)
					}, 5);
				}

			} else {
				onerror && onerror();
			}

		}, function (jqXHR, textStatus, errorThrown) {

			textoutput(jqXHR.responseText);
			alert('There was an error while doing the update! Please check the textarea on the right for more info!');
			error();

		});


	}

	function error() {

		window.onbeforeunload = null;

		output('error', 'error', true);

	}

	function finish() {

		window.onbeforeunload = null;

		output('finished', '<strong>Alright, all updates have been finished!</strong>', true, 0, true);
		output('finished_button', '<a href="admin.php?page=mailster_welcome" class="button button-primary">Ok, fine!</a>', true, 0, true);

		$('#mailster-post-upgrade').show();

	}

	function output(id, content, newline, round, nobox) {

		var el = $('#output_' + id).length ?
			$('#output_' + id) :
			$('<div id="output_' + id + '" class="' + (nobox ? '' : 'updated inline') + '" style="padding: 0.5em 6px;word-wrap: break-word;"></div>').appendTo($output);


		el.append(content);
		round > 20 ? el.append(skip.show()) : skip.hide();

	}

	function textoutput(content) {

		var curr_content = $('#textoutput').val();

		content = content + "\n\n" + curr_content;

		$('#textoutput').val(mailster.util.trim(content));

	}

	return mailster;

}(mailster || {}, jQuery, window, document));