mailster = (function (mailster, $, window, document) {

	"use strict";

	var nav = $('.mainnav'),
		deliverynav = $('#deliverynav'),
		tabs = $('.tab'),
		deliverytabs = $('#tab-delivery').find('.subtab'),
		wpnonce = $('#mailster_nonce').val(),
		reservedtags = $('#reserved-tags').data('tags');

	$('form#mailster-settings-form')
		.on('submit.lock', function () {
			return false
		});
	if ($('#settingsloaded').length) {
		$('.submit-form').prop('disabled', false);
		$('form#mailster-settings-form').off('submit.lock');
	} else {
		mailster.log('error loading settings page');
	}

	deliverynav.on('click', 'a.nav-tab', function () {
		deliverynav.find('a').removeClass('nav-tab-active');
		deliverytabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href');
		$('#deliverymethod').val(hash.substr(1));
		$('#subtab-' + hash.substr(1)).show();
		$('input.mailster_sendtest').val(mailster.l10n.settings.save_to_test).prop('disabled', true);
		return false;
	});

	nav.on('click', 'a', function () {
		nav.find('li').removeClass('active');
		tabs.hide();
		var hash = $(this).parent().addClass('active').find('a').attr('href');
		$('#tab-' + hash.substr(1)).show();
		location.hash = hash;
		$('form#mailster-settings-form').attr('action', 'options.php' + hash);
		if ('#authentication' == hash) {
			load_spf_data();
			load_dkim_data();
		}
		return false;
	});

	$('.click-to-select').on('click', function (event) {
		if (document.selection) {
			var range = document.body.createTextRange();
			range.moveToElementText(this);
			range.select();
		} else if (window.getSelection) {
			var range = document.createRange();
			range.selectNode(this);
			window.getSelection().addRange(range);
		}

	});

	$('#mailster-settings-form')
		.on('click', 'a[href^="#"]', function () {
			nav.find('a[href="' + $(this).attr('href') + '"]').trigger('click');
		});

	(location.hash && nav.find('a[href="' + location.hash + '"]').length) ?
	nav.find('a[href="' + location.hash + '"]').trigger('click'): nav.find('a').eq(0).trigger('click');

	$('.system_mail').on('change', function () {
		$('[name="mailster_options[system_mail_template]"]').prop('disabled', $(this).val() == 0);
		$('[name="mailster_options[respect_content_type]"]').prop('disabled', $(this).val() == 0);
	});

	$('#load_location_db').on('click', function () {
		var $this = $(this),
			loader = $('.geo-ajax-loading').css({
				'visibility': 'visible'
			});

		$('button').prop('disabled', true);

		mailster.util.ajax('load_geo_data', function (response) {

			$('button').prop('disabled', false);
			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false).html(response.buttontext);
			if (response.update) $('#location_last_update').html(response.update);
			var msg = $('<div class="' + ((!response.success) ? 'error' : 'updated') + '"><p>' + response.msg + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});

		}, function (jqXHR, textStatus, errorThrown) {

			$('button').prop('disabled', false);
			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false);
			var msg = $('<div class="error"><p>' + textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});

		});

		return false;
	});

	$('.webversion-bar-checkbox').on('change', function () {
		($(this).is(':checked')) ?
		$('#webversion-bar-options').slideDown(200): $('#webversion-bar-options').slideUp(200);
	});

	$('#social-services')
		.on('change', '.social-service-dropdown', function () {
			var _this = $(this),
				_selected = _this.find(":selected"),
				_url = _selected.data('url');

			_this.next('.social-service-url-field').html('<label><span class="description">' + _url.replace('%s', '<input type="text" name="mailster_options[services][' + _this.val() + ']" value="" class="regular-text">') + '</span></label>').find('input').focus();

		})
		.on('click', '.social-service-remove', function () {
			$(this).parent().remove();
			return false;
		});

	$('.social-service-add').on('click', function () {
		$('#social-services').find('li').eq(0).clone().appendTo('#social-services').find('select').val(0).parent().find('.social-service-url-field').empty();
	});

	$('.has-archive-check').on('change', function () {
		($(this).is(':checked')) ?
		$('.archive-slug').slideDown(200): $('.archive-slug').slideUp(200);
	});

	$('.edit-slug').on('click', function () {
		$(this).parent().parent().find('span').hide().filter('.edit-slug-area').show().find('input').focus().select();
	});

	$('.users-register').on('change', function () {
		($(this).is(':checked')) ?
		$('#' + $(this).data('section')).slideDown(200): $('#' + $(this).data('section')).slideUp(200);
	});
	$('#mailster_spf').on('change', function () {
		($(this).is(':checked')) ?
		$('.spf-info').slideDown(200): $('.spf-info').slideUp(200);
	});
	$('#mailster_dkim').on('change', function () {
		($(this).is(':checked')) ?
		$('.dkim-info').slideDown(200): $('.dkim-info').slideUp(200);
	});
	$('.dkim-enter-keys').on('click', function () {
		$('.dkim-keys').slideDown();
		return false;
	});


	$('#bounce_active').on('change', function () {
		($(this).is(':checked')) ?
		$('#bounce-options').slideDown(200): $('#bounce-options').slideUp(200);
	});


	$('#mailster_generate_dkim_keys').on('click', function () {
		return ($('#dkim_keys_active').length && confirm(mailster.l10n.settings.create_new_keys));
		return false;
	});

	$('input.smtp.secure').on('change', function () {
		$('#mailster_smtp_port').val($(this).data('port'));
	});

	$('#capabilities-table')
		.on('mouseenter', 'label', function () {
			$('#current-cap').stop().html($(this).attr('title')).css('opacity', 1).show();
		})
		.on('mouseleave', 'tbody', function () {
			$('#current-cap').fadeOut();
		})
		.on('change', 'input.selectall', function () {
			var $this = $(this);
			$('input.cap-check-' + $this.val()).prop('checked', $this.prop('checked'));
		});

	$('.mailster_sendtest').on('click', function () {
		var $this = $(this),
			cont = $this.closest('.mailster-testmail'),
			loader = cont.find('.test-ajax-loading').css({
				'visibility': 'visible'
			}),
			to = cont.find('input.mailster-testmail-email').val(),
			formdata = $('form#mailster-settings-form').serialize();

		$this.prop('disabled', true);

		mailster.util.ajax('send_test', {
			test: true,
			formdata: formdata,
			basic: true,
			to: to

		}, function (response) {

			if (response.log)
				response.success ? mailster.log(response.log) : mailster.log(response.log, 'error');

			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false);
			var msg = $('<div class="' + ((!response.success) ? 'error' : 'updated') + '"><p>' + response.msg + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});

		}, function (jqXHR, textStatus, errorThrown) {

			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false);
			var msg = $('<div class="error"><p>' + textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});

		});
	});

	$('.mailster_bouncetest').on('click', function () {
		var $this = $(this),
			loader = $('.bounce-ajax-loading').css({
				'visibility': 'visible'
			}),
			status = $('.bouncetest_status').empty().show(),
			formdata = $('form#mailster-settings-form').serialize();

		$this.prop('disabled', true);

		mailster.util.ajax('bounce_test', {
			formdata: formdata
		}, function (response) {

			bounce_test_check(response.identifier, 1, formdata, function () {
				$this.prop('disabled', false);
			});

		}, function (jqXHR, textStatus, errorThrown) {

			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false);
			status.html(textStatus + ' ' + jqXHR.status + ': ' + errorThrown);

		});
	});

	$('input.cron_radio').on('change', function () {
		$('.cron_opts').hide();
		$('.' + $(this).val()).show();
	});

	$('.switch-cron-url').on('click', function () {
		$('.cron_opts').toggleClass('alternative-cron');
		return false;
	});

	$('#mailster_add_tag').on('click', function () {
		var el = $('<div class="tag"><code>{<input type="text" class="tag-key">}</code> &#10152; <input type="text" class="regular-text tag-value"> <a class="tag-remove">&#10005;</a></div>').insertBefore($(this));
		el.find('.tag-key').focus();
	});

	$('.tags')
		.on('change', '.tag-key', function () {
			var _this = $(this),
				_base = _this.parent().parent(),
				val = sanitize(_this.val());

			if (!val) _this.parent().parent().remove();

			_this.val(val);
			_base.find('.tag-value').attr('name', 'mailster_options[custom_tags][' + val + ']');

		})
		.on('click', '.tag-remove', function () {
			$(this).parent().remove();
			return false;
		});

	$('#mailster_add_field').on('click', function () {

		var el = $('<div class="customfield"><a class="customfield-move-up" title="' + mailster.l10n.settings.move_up + '">&#9650;</a><a class="customfield-move-down" title="' + mailster.l10n.settings.move_down + '">&#9660;</a><div><span class="label">' + mailster.l10n.settings.fieldname + ':</span><label><input type="text" class="regular-text customfield-name"></label></div><div><span class="label">' + mailster.l10n.settings.tag + ':</span><span><code>{</code><input type="text" class="customfield-key code"><code>}</code></span></div><div><span class="label">' + mailster.l10n.settings.type + ':</span><select class="customfield-type"><option value="textfield">' + mailster.l10n.settings.textfield + '</option><option value="textarea">' + mailster.l10n.settings.textarea + '</option><option value="dropdown">' + mailster.l10n.settings.dropdown + '</option><option value="radio">' + mailster.l10n.settings.radio + '</option><option value="checkbox">' + mailster.l10n.settings.checkbox + '</option><option value="date">' + mailster.l10n.settings.datefield + '</option></select></div><ul class="customfield-additional customfield-dropdown customfield-radio"><li><ul class="customfield-values"><li><span>&nbsp;</span> <span class="customfield-value-box"><input type="text" class="regular-text customfield-value" value=""> <label><input type="radio" value="" title="' + mailster.l10n.settings.default_selected + '" class="customfield-default" disabled> ' + mailster.l10n.settings.default_field + '</label></span></li></ul><span>&nbsp;</span> <a class="customfield-value-add">' + mailster.l10n.settings.add_field + '</a></li></ul><div class="customfield-additional customfield-checkbox"><span>&nbsp;</span><label><input type="checkbox" value="1" title="' + mailster.l10n.settings.default_field + '" class="customfield-default" disabled> ' + mailster.l10n.settings.default_checked + '</label></div><a class="customfield-remove">remove field</a><br></div>').appendTo($('.customfields').eq(0));
		el.find('.customfield-name').focus();
	});

	$('#sync_list_check').on('change', function () {
		$('#sync_list').slideToggle(200);
		$('.sync-button').prop('disabled', true);
	});

	$('#sync_list')
		.on('click', '#add_sync_item', function () {
			var items = $('.mailster_syncitem');

			items.eq(0).clone().insertAfter(items.last()).removeAttr('title').find('select').each(function () {
				$(this).attr('name', $(this).attr('name').replace('[synclist][0]', '[synclist][' + items.length + ']'));
			});

			$('.sync-button').prop('disabled', true);

		})
		.on('click', '.remove-sync-item', function () {
			$(this).parent().remove();
			$('.sync-button').prop('disabled', true);
		})
		.on('change', 'select', function () {
			$('.sync-button').prop('disabled', true);
		})
		.on('click', '#sync_subscribers_wp', function (event) {
			if (event && event.originalEvent && !confirm(mailster.l10n.settings.sync_subscriber)) return false;

			var _this = $(this),
				loader = $('.sync-ajax-loading').css({
					'visibility': 'visible'
				});

			$('.sync-button').prop('disabled', true);

			mailster.util.ajax('sync_all_subscriber', {
				offset: _this.data('offset')
			}, function (response) {

				$('.sync-button').prop('disabled', false);
				if (response.success && response.count) {
					_this.data('offset', response.offset).trigger('click');
				} else {
					loader.css({
						'visibility': 'hidden'
					});
					_this.data('offset', 0);
				}

			}, function (jqXHR, textStatus, errorThrown) {

				loader.css({
					'visibility': 'hidden'
				});
				$('.sync-button').prop('disabled', false);

			});
			return false;
		})
		.on('click', '#sync_wp_subscribers', function (event) {
			if (event && event.originalEvent && !confirm(mailster.l10n.settings.sync_wp_user)) return false;

			var _this = $(this),
				loader = $('.sync-ajax-loading').css({
					'visibility': 'visible'
				});

			$('.sync-button').prop('disabled', true);

			mailster.util.ajax('sync_all_wp_user', {
				offset: _this.data('offset')
			}, function (response) {

				$('.sync-button').prop('disabled', false);
				if (response.success && response.count) {
					_this.data('offset', response.offset).trigger('click');
				} else {
					loader.css({
						'visibility': 'hidden'
					});
					_this.data('offset', 0);
				}

			}, function (jqXHR, textStatus, errorThrown) {

				loader.css({
					'visibility': 'hidden'
				});
				$('.sync-button').prop('disabled', false);

			});
			return false;
		})


	$('.customfields')
		.on('change', '.customfield-name', function () {
			var _this = $(this),
				_tagfield = _this.parent().parent().parent().find('.customfield-key');

			if (!_tagfield.val()) _tagfield.val(_this.val()).trigger('change');
		})
		.on('change', '.customfield-key', function () {
			var _this = $(this),
				_base = _this.parent().parent().parent(),
				val = sanitize(_this.val());

			if (!val) _base.remove();

			_this.val(val);
			_base.find('.customfield-name').attr('name', 'mailster_options[custom_field][' + val + '][name]');
			_base.find('.customfield-type').attr('name', 'mailster_options[custom_field][' + val + '][type]');
			_base.find('.customfield-value').attr('name', 'mailster_options[custom_field][' + val + '][values][]');
			_base.find('.customfield-default').attr('name', 'mailster_options[custom_field][' + val + '][default]');

		})
		.on('click', '.customfield-remove', function () {
			$(this).parent().remove();
		})
		.on('click', '.customfield-move-up', function () {

			var _this = $(this).parent();
			_this.insertBefore(_this.prev());

		})
		.on('click', '.customfield-move-down', function () {

			var _this = $(this).parent();
			_this.insertAfter(_this.next());

		})
		.on('change', '.customfield-type', function () {

			var type = $(this).val();
			$(this).parent().parent().find('.customfield-additional').slideUp(200).find('input').prop('disabled', true);

			if (type != 'textfield') {
				$(this).parent().parent().find('.customfield-' + type).stop().slideDown(200).find('input').prop('disabled', false);
			}
		})
		.on('change', '.customfield-value', function () {

			$(this).next().find('input').val($(this).val());
		})
		.on('click', 'a.customfield-value-remove', function () {
			$(this).parent().parent().remove();
		})
		.on('click', 'a.customfield-value-add', function () {
			var field = $(this).parent().find('li').eq(0).clone();
			field.appendTo($(this).parent().find('ul')).find('input').val('').focus().select();
		});

	$('#mailster_import_data').on('click', function () {
		if (!confirm(mailster.l10n.settings.import_data)) return false;
	});
	$('#mailster_reset_data').on('click', function () {
		if (!confirm(mailster.l10n.settings.reset_data)) return false;
	});

	function bounce_test_check(identifier, count, formdata, callback) {
		var $this = $(this),
			loader = $('.bounce-ajax-loading').css({
				'visibility': 'visible'
			}),
			status = $('.bouncetest_status');

		mailster.util.ajax('bounce_test_check', {
			identifier: identifier,
			passes: count,
			formdata: formdata
		}, function (response) {

			status.html(response.msg);

			if (response.complete) {
				loader.css({
					'visibility': 'hidden'
				});
				callback && callback();
			} else {
				setTimeout(function () {
					bounce_test_check(identifier, ++count, formdata, callback);
				}, 1000);
			}

		}, function (jqXHR, textStatus, errorThrown) {

			loader.css({
				'visibility': 'hidden'
			});
			$this.prop('disabled', false);
			status.html(textStatus + ' ' + jqXHR.status + ': ' + errorThrown);

		});


	}

	function load_spf_data() {
		var loader = $('.spf-result').css({
			'visibility': 'visible'
		});

		mailster.util.ajax('spf_check', function (response) {

			if (response.success) {
				loader
					.html(response.message)
					.addClass(response.found ? 'verified' : 'not-verified')
					.removeClass('spinner');
			} else {}

		}, function (jqXHR, textStatus, errorThrown) {

			loader.html('error');

		});
	}

	function load_dkim_data() {
		var loader = $('.dkim-result').css({
			'visibility': 'visible'
		});

		mailster.util.ajax('dkim_check', function (response) {

			if (response.success) {
				loader
					.html(response.message)
					.addClass(response.found ? 'verified' : 'not-verified')
					.removeClass('spinner');
			} else {}

		}, function (jqXHR, textStatus, errorThrown) {

			loader.html('error');

		});
	}

	function sanitize(string) {
		var tag = mailster.util.trim(string).toLowerCase().replace(/ /g, '-').replace(/[^a-z0-9_-]*/g, '').replace(/^[_]*/, '').replace(/[_]*$/, '');
		if ($.inArray(tag, reservedtags) != -1) {
			alert(mailster.util.sprintf(mailster.l10n.settings.reserved_tag, '"' + tag + '"'));
			tag += '-a';
		}
		return tag;
	}

	return mailster;

}(mailster || {}, jQuery, window, document));