jQuery(document).ready(function ($) {

	"use strict"

	$('body')

	.on('submit.mailster', 'form.mailster-ajax-form', function (event) {

		event.preventDefault();

		var form = $(this),
			data = form.serialize(),
			info = $('<div class="mailster-form-info"></div>'),
			c;

		if ('function' === typeof window.mailster_pre_submit) {
			c = window.mailster_pre_submit.call(this, data);
			if (c === false) return false;
			if (typeof c !== 'undefined') data = c;
		}

		form.addClass('loading').find('.submit-button').prop('disabled', true);

		$.post(form.attr('action'), data, handlerResponse, 'JSON')
			.fail(function (jqXHR, textStatus, errorThrown) {
				var response;

				try {
					response = JSON.parse(jqXHR.responseText);
					if (!response.html) {
						response = {
							html: 'There was an error with the response:<br><code>[' + response.code + '] ' + response.message + '</code>',
							success: false
						}
					}
				} catch (err) {
					response = {
						html: 'There was an error while parsing the response:<br><code>' + jqXHR.responseText + '</code>',
						success: false
					}
				}
				handlerResponse(response);
				if (console) console.error(jqXHR.responseText);
			});

		function handlerResponse(response) {

			form.removeClass('loading has-errors').find('div.mailster-wrapper').removeClass('error');

			form.find('.mailster-form-info').remove();

			if ('function' === typeof window.mailster_post_submit) {
				c = window.mailster_post_submit.call(form[0], response);
				if (c === false) return false;
				if (typeof c !== 'undefined') response = c;
			}

			form.find('.submit-button').prop('disabled', false);

			if (response.html) info.html(response.html);
			if ($(document).scrollTop() < form.offset().top) {
				info.prependTo(form);
			} else {
				info.appendTo(form);
			}

			if (response.success) {

				if (!form.is('.is-profile'))
					form
					.find('.mailster-form-fields').slideUp(100)
					.find('.mailster-wrapper').find(':input').prop('disabled', true).filter('.input').val('');

				(response.redirect) ? location.href = response.redirect: info.show().addClass('success');

			} else {

				if (response.fields)
					$.each(response.fields, function (field) {
						form.addClass('has-errors').find('.mailster-' + field + '-wrapper').addClass('error');
					})
				info.show().addClass('error');
			}

		}


	});


});