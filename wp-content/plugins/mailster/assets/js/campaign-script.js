// block DOM
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$ = mailster.$ || {};

	mailster.$.body = $('body');
	mailster.$.wpbody = $('#wpbody');
	mailster.$.form = $('#post');
	mailster.$.title = $('#title');
	mailster.$.iframe = $('#mailster_iframe');
	mailster.$.templateWrap = $('#template-wrap');
	mailster.$.template = $('#mailster_template');
	mailster.$.datafields = $('[name^="mailster_data"]');
	mailster.$.content = $('#content');
	mailster.$.excerpt = $('#excerpt');
	mailster.$.plaintext = $('#plain-text-wrap');
	mailster.$.html = $('#html-wrap');
	mailster.$.head = $('#head');
	mailster.$.optionbar = $('#optionbar');
	mailster.$.editbar = $('#editbar');

	mailster.campaign_id = parseInt($('#post_ID').val(), 10);
	mailster.user_id = parseInt($('#user-id').val(), 10);
	mailster.enabled = false;
	mailster.editable = !$('#mailster_disabled').val();

	return mailster;

}(mailster || {}, jQuery, window, document));
// end DOM

// events
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.events.push('documentReady', function () {
		if (!mailster.editable) {
			mailster.$.iframe.on('load', function () {
				mailster.trigger('iframeLoaded');
			});
		}
		mailster.$.iframe.attr('src', mailster.$.iframe.data('src'));
	});

	mailster.events.push('editorLoaded', function () {
		mailster.$.iframe.removeClass('loading');
	});

	mailster.events.push('iframeLoaded', function () {
		mailster.$.iframe.removeClass('loading');
		mailster.$.iframecontents = mailster.$.iframe.contents();
	});

	mailster.events.push('disable', function () {
		mailster.enabled = false;
	});

	mailster.events.push('enable', function () {
		mailster.enabled = true;
	});

	mailster.events.push('redraw', function () {
		mailster.trigger('refresh');
	});

	mailster.events.push('resize', function () {
		$('#editor-height').val(mailster.editor.getHeight());
	});

	mailster.events.push('save', function () {

		if (!mailster.editor || !mailster.editor.loaded) return;

		var content = mailster.editor.getFrameContent(),
			length = mailster.optionbar.undos.length,
			lastundo = mailster.optionbar.undos[length - 1];

		if (lastundo != content) {

			mailster.$.content.val(content);
			mailster.details.$.preheader.prop('readonly', !content.match('{preheader}'));
			mailster.optionbar.undos = mailster.optionbar.undos.splice(0, mailster.optionbar.currentUndo + 1);
			mailster.optionbar.undos.push(content);

			if (length >= mailster.l10n.campaigns.undosteps) mailster.optionbar.undos.shift();
			mailster.optionbar.currentUndo = mailster.optionbar.undos.length - 1;

			if (mailster.optionbar.currentUndo) mailster.$.optionbar.find('a.undo').removeClass('disabled');
			mailster.$.optionbar.find('a.redo').addClass('disabled');

			if (wp && wp.autosave) wp.autosave.local.save();
		}

	});

	return mailster;

}(mailster || {}, jQuery, window, document));
// end events

// block general
mailster = (function (mailster, $, window, document) {

	"use strict";

	var resizeTimout;

	if (mailster.util.isMSIE) mailster.$.body.addClass('ie');
	if (mailster.util.isTouchDevice) mailster.$.body.addClass('touch');

	mailster.$.window
		.on('resize.mailster', doResize);

	mailster.$.document
		.on('change', 'input[name=screen_columns]', function () {
			mailster.$.window.trigger('resize');
		})
		.on('click', '.restore-backup', function (e, data) {
			var data = wp.autosave.local.getSavedPostData();
			mailster.editor.setContent(data.content);
			mailster.$.title.val(data.post_title);
			return false;
		})
		.on('submit', 'form#post', function () {
			if (!mailster.enabled) return false;
			mailster.trigger('save');
		})
		.on('change', '.dynamic_embed_options_taxonomy', function () {
			var $this = $(this),
				val = $this.val();
			$this.parent().find('.button').remove();
			if (val != -1) {
				if ($this.parent().find('select').length < $this.find('option').length - 1)
					$(' <a class="button button-small add_embed_options_taxonomy">' + mailster.l10n.campaigns.add + '</a>').insertAfter($this);
			} else {
				$this.parent().html('').append($this);
			}

			return false;
		})
		.on('click', '.add_embed_options_taxonomy', function () {
			var $this = $(this),
				el = $this.prev().clone();

			el.insertBefore($this).val('-1');
			$('<span> ' + mailster.l10n.campaigns.or + ' </span>').insertBefore(el);
			$this.remove();

			return false;
		});

	// overwrite autosave function since we don't need it
	!mailster.editable && mailster.events.push('documentReady', function () {
		mailster.$.window.off('beforeunload.edit-post');
	});

	function doResize() {
		clearTimeout(resizeTimout);
		resizeTimout = setTimeout(function () {
			mailster.trigger('refresh');
		}, 250);
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end general


// block utils
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.util = mailster.util || {};

	mailster.util.isTinyMCE = null;

	mailster.events.push('documentReady', function () {
		mailster.util.isTinyMCE = typeof tinymce == 'object';
	});

	mailster.util.getRealDimensions = function (el, callback) {
		el = el.eq(0);
		if (el.is('img') && el.attr('src')) {
			var image = new Image(),
				factor;
			image.onload = function () {
				factor = ((image.width / el.width()).toFixed(1) || 1);
				if (callback) callback.call(this, image.width, image.height, isFinite(factor) ? parseFloat(factor) : 1)
			}
			image.src = el.attr('src');
		};
	}

	mailster.util.tempMsg = function (message, type, el, callback) {

		var msg = $('<div class="' + (type) + '"><p>' + message + '</p></div>').hide().prependTo(el).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
			msg.remove();
			callback && callback();
		});

		return msg;
	}

	mailster.util.selectRange = function (input, startPos, endPos) {
		if (document.selection && document.selection.createRange) {
			input.focus();
			input.select();
			var range = document.selection.createRange();
			range.collapse(true);
			range.moveEnd("character", endPos);
			range.moveStart("character", startPos);
			range.select();
		} else {
			input.selectionStart = startPos;
			input.selectionEnd = endPos;
		}
		return true;
	}

	mailster.util.changeColor = function (color_from, color_to, element, original) {
		if (!color_from) color_from = color_to;
		if (!color_to) return false;
		color_from = color_from.toUpperCase();
		color_to = color_to.toUpperCase();
		if (color_from == color_to) return false;
		var raw = mailster.editor.getContent(),
			reg = new RegExp(color_from, 'gi');

		if (element)
			element.data('value', color_to);

		$('#mailster-color-' + color_from.substr(1)).attr('id', 'mailster-color-' + color_to.substr(1));

		if (reg.test(raw)) {
			mailster.editor.setContent(raw.replace(reg, color_to), 0);
		}

		if (original) {
			//mailster.editor.colors.map[original] = color_to;
		}

	}

	mailster.util.replace = function (str, match, repl) {
		if (match === repl)
			return str;
		do {
			str = str.replace(match, repl);
		} while (match && str.indexOf(match) !== -1);
		return str;
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end utils


// block thickbox
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.thickbox = mailster.thickbox || {};

	mailster.thickbox.$ = {};
	mailster.thickbox.$.preview = $('.mailster-preview-iframe');

	mailster.thickbox.$.preview
		.on('load', function () {
			var $this = $(this),
				contents = $this.contents(),
				body = contents.find('body');

			body.on('click', 'a', function () {
				var href = $(this).attr('href');
				if (href && href != '#') window.open(href);
				return false;
			});
		});

	return mailster;

}(mailster || {}, jQuery, window, document));
// end thickbox



// block modules
mailster = (function (mailster, $, window, document) {

	"use strict";

	var metabox = $('#mailster_template'),
		selector = $('#module-selector'),
		search = $('#module-search'),
		module_thumbs = selector.find('li'),
		toggle = $('a.toggle-modules');

	mailster.modules = mailster.modules || {};
	mailster.modules.showSelector = !!parseInt(window.getUserSetting('mailstershowmodules', 1), 10);
	mailster.modules.dragging = false
	mailster.modules.selected = false;

	function colorSwap(html) {
		for (var c_from in mailster.editor.colors.map) {
			html = mailster.util.replace(html, c_from, mailster.editor.colors.map[c_from]);
		}
		return html;
	}

	function addmodule() {
		var module = selector.data('current'),
			html = $(this).parent().find('script').html();
		insert(colorSwap(html), (module && module.is('module') ? module : false), true, true);
	}

	function up() {
		var module = $(this).parent().parent().parent().addClass('ui-sortable-fly-over'),
			prev = module.prev('module').addClass('ui-sortable-fly-under'),
			pos = mailster.util.top() - prev.height();

		module.css({
			'transform': 'translateY(-' + prev.height() + 'px)'
		});
		prev.css({
			'transform': 'translateY(' + module.height() + 'px)'
		});

		mailster.util.scroll(pos, function () {
			module.insertBefore(prev.css({
				'transform': ''
			}).removeClass('ui-sortable-fly-under')).css({
				'transform': ''
			}).removeClass('ui-sortable-fly-over');
			mailster.trigger('refresh');
			mailster.trigger('save');
		}, 250);
	}

	function down() {
		var module = $(this).parent().parent().parent().addClass('ui-sortable-fly-over'),
			next = module.next('module').addClass('ui-sortable-fly-under'),
			pos = mailster.util.top() + next.height();
		module.css({
			'transform': 'translateY(' + next.height() + 'px)'
		});
		next.css({
			'transform': 'translateY(-' + module.height() + 'px)'
		});
		mailster.util.scroll(pos, function () {
			module.insertAfter(next.css({
				'transform': ''
			}).removeClass('ui-sortable-fly-under')).css({
				'transform': ''
			}).removeClass('ui-sortable-fly-over');
			mailster.trigger('refresh');
			mailster.trigger('save');
		}, 250);
	}

	function duplicate() {
		var module = $(this).parent().parent().parent(),
			clone = module.clone().removeAttr('selected').hide();

		insert(clone, module, false, true);
	}

	function auto() {
		var module = $(this).parent().parent().parent();
		mailster.editbar.open({
			element: module,
			name: module.attr('label'),
			type: 'auto',
			offset: module.offset()
		});
	}

	function changeName() {
		var _this = $(this),
			value = _this.val(),
			module = _this.parent().parent();

		if (!value) {
			value = _this.attr('placeholder');
			_this.val(value);
		}

		module.attr('label', value);
	}

	function remove() {
		var module = $(this).parent().parent().parent();
		module.fadeTo(25, 0, function () {
			module.slideUp(100, function () {
				module.remove();
				mailster.trigger('refresh');
				if (!mailster.editor.$.modules.length) mailster.editor.$.container.html('');
				mailster.trigger('save');
			});
		});
	}

	function insert(html_or_clone, element, before, scroll) {

		var clone;

		if (typeof html_or_clone == 'string') {
			clone = $(html_or_clone);
		} else if (html_or_clone instanceof jQuery) {
			clone = $(html_or_clone);
			clone.find('single, multi, buttons').removeAttr('contenteditable spellcheck id dir style class');
		} else {
			return false;
		}

		if (!element && !mailster.editor.$.container.length) return false;

		if (element) {
			(before ? clone.hide().insertBefore(element) : clone.hide().insertAfter(element))
		} else {
			if ('footer' == mailster.editor.$.modules.last().attr('type')) {
				clone.hide().insertBefore(mailster.editor.$.modules.last());
			} else {
				clone.hide().appendTo(mailster.editor.$.container);
			}
		}

		mailster.editor.updateElements();
		mailster.editor.moduleButtons();

		clone.slideDown(100, function () {
			clone.css('display', 'block');
			mailster.trigger('refresh');
			mailster.trigger('save');
		});

		if (scroll) {
			var offset = clone.offset().top + mailster.$.template.offset().top - (mailster.$.window.height() / 2);
			mailster.util.scroll(offset);
		}

	}

	function codeView() {
		var module = $(this).parent().parent().parent();
		mailster.editbar.open({
			element: module,
			name: module.attr('label'),
			type: 'codeview',
			offset: module.offset()
		});
	}

	function toggleModules() {
		mailster.$.templateWrap.toggleClass('show-modules');
		mailster.modules.showSelector = !mailster.modules.showSelector;
		window.setUserSetting('mailstershowmodules', mailster.modules.showSelector ? 1 : 0);
		setTimeout(function () {
			mailster.trigger('resize');
		}, 200);
	}

	function searchModules() {
		module_thumbs.hide();
		selector.find("li:contains('" + $(this).val() + "')").show();
	}

	function initFrame() {

		var currentmodule,
			pre_dropzone = $('<dropzone></dropzone>'),
			post_dropzone = pre_dropzone.clone(),
			dropzones = pre_dropzone.add(post_dropzone);

		mailster.editor.$.document
			.off('.mailster')
			.on('click.mailster', 'button.up', up)
			.on('click.mailster', 'button.down', down)
			.on('click.mailster', 'button.auto', auto)
			.on('click.mailster', 'button.duplicate', duplicate)
			.on('click.mailster', 'button.remove', remove)
			.on('click.mailster', 'button.codeview', codeView)
			.on('change.mailster', 'input.modulelabel', changeName);

		selector
			.off('.mailster')
			.on('dragstart.mailster', 'li', function (startevent) {

				//required for Firefox
				startevent.originalEvent.dataTransfer.setData('Text', this.id);

				mailster.modules.dragging = true;

				mailster.editor.$.body.addClass('drag-active');

				mailster.editor.$.container
					.on('dragenter.mailster', function (event) {
						var selectedmodule = $(event.target).closest('module');
						if (!selectedmodule.length || currentmodule && currentmodule[0] === selectedmodule[0]) return;
						currentmodule = selectedmodule;
						post_dropzone.appendTo(currentmodule);
						pre_dropzone.prependTo(currentmodule);
						setTimeout(function () {
							post_dropzone.addClass('visible');
							pre_dropzone.addClass('visible');
							mailster.editor.$.modules.removeClass('drag-up drag-down');
							selectedmodule.prevAll('module').addClass('drag-up');
							selectedmodule.nextAll('module').addClass('drag-down')
						}, 1);
					})
					.on('dragover.mailster', function (event) {
						event.preventDefault();
					})
					.on('drop.mailster', function (event) {
						var html = $(startevent.target).find('script').html();
						insert(colorSwap(html), mailster.editor.$.modules.length ? (currentmodule && currentmodule[0] === mailster.editor.$.container ? false : currentmodule) : false, pre_dropzone[0] === event.target, false, true);
						event.preventDefault();
					});

				dropzones
					.on('dragenter.mailster', function (event) {
						$(this).addClass('drag-over');
					})
					.on('dragleave.mailster', function (event) {
						$(this).removeClass('drag-over');
					});

			})
			.on('dragend.mailster', 'li', function (event) {
				currentmodule = null;
				mailster.editor.$.body.removeClass('drag-active');
				dropzones.removeClass('visible drag-over').remove();
				mailster.editor.$.modules.removeClass('drag-up drag-down');

				mailster.editor.$.container
					.off('dragenter.mailster')
					.off('dragover.mailster')
					.off('drop.mailster');

				dropzones
					.off('dragenter.mailster')
					.off('dragleave.mailster');

				mailster.modules.dragging = false;

			});
	}

	$('.meta-box-sortables').on("sortstop", function (event, ui) {
		if (ui.item[0] === mailster.dom.template) {
			mailster.editor.$.body.addClass('reload-page');
		}
	});

	mailster.$.template
		.on('click', 'a.toggle-modules', toggleModules)
		.on('keydown', 'a.addmodule', function (event) {
			if (13 == event.which) {
				addmodule.call(this);
			}
		})
		.on('click', 'a.addmodule', addmodule)
		.on('click', '#module-search-remove', function () {
			search.val('').focus().trigger('keyup');
			return false;
		});

	search
		.on('keyup', searchModules)
		.on('focus', function () {
			search.select();
		});


	mailster.events.push('editorLoaded', initFrame);

	return mailster;

}(mailster || {}, jQuery, window, document));
// end modules


// block Details
mailster = (function (mailster, $, window, document) {

	"use strict";

	var googledata = {
		unknown_cities: [],
		geodata: null,
		map: null,
		options: {
			legend: false,
			region: 'world',
			resolution: 'countries',
			datalessRegionColor: '#ffffff',
			enableRegionInteractivity: true,
			colors: ['#d7f1fc', mailster.colors.main],
			backgroundColor: {
				fill: 'none',
				stroke: null,
				strokeWidth: 0
			},
		}

	};

	mailster.$.details = $('#mailster_details .inside');

	mailster.details = mailster.details || {};

	mailster.details.$ = {};
	mailster.details.$.subject = $('#mailster_subject');
	mailster.details.$.preheader = $('#mailster_preheader');
	mailster.details.$.from = $('#mailster_from');
	mailster.details.$.from_name = $('#mailster_from-name');
	mailster.details.$.replyto = $('#mailster_reply-to');

	mailster.$.title.on('change', function () {
		if (!mailster.details.$.subject.val()) mailster.details.$.subject.val($(this).val());
	});

	mailster.$.details
		.on('click', '.default-value', function () {
			var _this = $(this);
			$('#' + _this.data('for')).val(_this.data('value'));
		})
		.on('click', '#show_recipients', function () {
			var $this = $(this),
				list = $('#recipients-list'),
				loader = $('#recipients-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			mailster.util.ajax('get_recipients', {
				id: mailster.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_clicks', function () {
			var $this = $(this),
				list = $('#clicks-list'),
				loader = $('#clicks-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			mailster.util.ajax('get_clicks', {
				id: mailster.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_environment', function () {
			var $this = $(this),
				list = $('#environment-list'),
				loader = $('#environment-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			mailster.util.ajax('get_environment', {
				id: mailster.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_geolocation', function () {
			var $this = $(this),
				list = $('#geolocation-list'),
				loader = $('#geolocation-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			mailster.util.ajax('get_geolocation', {
				id: mailster.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();

				googledata.geodata = response.geodata;
				googledata.unknown_cities = response.unknown_cities;

				list.html(response.html).slideDown(100, function () {

					google.load('visualization', '1.0', {
						packages: ['geochart', 'corechart'],
						mapsApiKey: mailster.l10n.google ? mailster.l10n.google.key : null,
						callback: function () {
							var hash;

							googledata.map = new google.visualization.GeoChart(document.getElementById('countries_map'));
							google.countrydata = google.visualization.arrayToDataTable(response.countrydata);

							if (location.hash && (hash = location.hash.match(/region=([A-Z]{2})/))) {
								regionClick(hash[1]);
							} else {
								drawMap(google.countrydata);
							}

							google.visualization.events.addListener(googledata.map, 'regionClick', regionClick);

						}
					});

					$('a.zoomout').on('click', function () {
						showWorld();
						return false;
					});

					$('#countries_table').find('tbody').find('tr').on('click', function () {
						var code = $(this).data('code');
						(code == 'unknown' || !code) ?
						showWorld(): regionClick(code);

						return false;
					});

				});

			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_errors', function () {
			var $this = $(this),
				list = $('#error-list'),
				loader = $('#error-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			mailster.util.ajax('get_errors', {
				id: mailster.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_countries', function () {
			$('#countries_wrap').toggle();
			return false;
		})
		.on('click', '.load-more-receivers', function () {
			var $this = $(this),
				page = $this.data('page'),
				types = $this.data('types'),
				orderby = $this.data('orderby'),
				order = $this.data('order'),
				loader = $this.next().css('display', 'inline');

			mailster.util.ajax('get_recipients_page', {
				id: mailster.campaign_id,
				types: types,
				page: page,
				orderby: orderby,
				order: order
			}, function (response) {
				loader.hide();
				if (response.success) {
					$this.parent().parent().replaceWith(response.html);
				}
			}, function (jqXHR, textStatus, errorThrown) {
				detailbox.removeClass('loading');
			});

			return false;
		})
		.on('click', '.recipients-limit', function (event) {
			if (event.altKey) {
				$('input.recipients-limit').prop('checked', false);
				$(this).prop('checked', true);
			}
		})
		.on('change', '.recipients-limit, select.recipients-order', function (event) {

			var list = $('#recipients-list'),
				loader = $('#recipients-ajax-loading'),
				types = $('input.recipients-limit:checked').map(function () {
					return this.value
				}).get(),
				orderby = $('select.recipients-order').val(),
				order = $('a.recipients-order').hasClass('asc') ? 'ASC' : 'DESC';

			loader.css('display', 'inline');
			$('input.recipients-limit').prop('disabled', true);

			mailster.util.ajax('get_recipients', {
				id: mailster.campaign_id,
				types: types.join(','),
				orderby: orderby,
				order: order
			}, function (response) {
				loader.hide();
				$('input.recipients-limit').prop('disabled', false);
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', 'a.recipients-order', function () {
			$(this).toggleClass('asc');
			$('select.recipients-order').trigger('change');
		})
		.on('click', '.show-receiver-detail', function () {
			var $this = $(this),
				id = $this.data('id'),
				detailbox = $('#receiver-detail-' + id).show();

			$this.parent().addClass('loading').parent().addClass('expanded');

			mailster.util.ajax('get_recipient_detail', {
				id: id,
				campaignid: mailster.campaign_id
			}, function (response) {
				$this.parent().removeClass('loading');
				if (response.success) {
					detailbox.find('div.receiver-detail-body').html(response.html).slideDown(100);
				}
			}, function (jqXHR, textStatus, errorThrown) {
				detailbox.removeClass('loading');
			});

			return false;
		})
		.on('click', '#stats label', function () {
			$('#recipients-list')
				.find('input').prop('checked', false)
				.filter('input.' + $(this).attr('class')).prop('checked', true)
				.trigger('change');
		});

	$.easyPieChart && mailster.$.details.find('.piechart').easyPieChart({
		animate: 2000,
		rotate: 180,
		barColor: mailster.colors.main,
		trackColor: mailster.colors.track,
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

	function showWorld() {
		var options = {
			'region': 'world',
			'displayMode': 'region',
			'resolution': 'countries',
			'colors': ['#D7E4E9', mailster.colors.main]
		};

		drawMap(google.countrydata, options);

		$('#countries_table').find('tr').removeClass('wp-ui-highlight');
		$('#mapinfo').hide();

		location.hash = '#region=';

	}

	function regionClick(event) {

		var options = {},
			region = event.region ? event.region : event,
			d, data;

		if (region.match(/-/)) return false;

		options['region'] = region;

		googledata.unknown_cities[region] ?
			$('#mapinfo').show().html(mailster.util.sprintf(mailster.l10n.campaigns.unknown_locations, googledata.unknown_cities[region])) :
			$('#mapinfo').hide();

		d = googledata.geodata[region] ? googledata.geodata[region] : [];

		options['resolution'] = 'provinces';
		options['displayMode'] = 'markers';
		options['dataMode'] = 'markers';
		options['colors'] = ['#4EBEE9', mailster.colors.main];

		data = new google.visualization.DataTable()
		data.addColumn('number', 'Lat');
		data.addColumn('number', 'Long');
		data.addColumn('string', 'tooltip');
		data.addColumn('number', 'Value');
		data.addColumn({
			type: 'string',
			role: 'tooltip'
		});

		data.addRows(d);

		$('#countries_table').find('tr').removeClass('wp-ui-highlight');
		$('#country-row-' + region).addClass('wp-ui-highlight');

		location.hash = '#region=' + region
		drawMap(data, options);

	}

	function drawMap(data, options) {
		options = $.extend(googledata.options, options);
		googledata.map.draw(data, options);
		$('a.zoomout').css({
			'visibility': (options['region'] != 'world' ? 'visible' : 'hidden')
		});
	}

	function regTo3dig(region) {
		var regioncode = region;
		$.each(regions, function (code, regions) {
			if ($.inArray(region, regions) != -1) regioncode = code;
		})
		return regioncode;
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Details


// block Template
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.clickmap = mailster.clickmap || {};

	mailster.clickmap.$ = {};
	mailster.clickmap.$.popup = $('#clickmap-stats');

	mailster.$.template
		.on('click', 'a.getplaintext', function () {
			var oldval = mailster.$.excerpt.val();
			mailster.$.excerpt.val(mailster.l10n.campaigns.loading);
			mailster.util.ajax('get_plaintext', {
				html: mailster.editor.getContent()
			}, function (response) {
				mailster.$.excerpt.val(response);
			}, function (jqXHR, textStatus, errorThrown) {
				mailster.$.excerpt.val(oldval);
			}, 'HTML');
		})
		.on('change', '#plaintext', function () {
			var checked = $(this).is(':checked');
			mailster.$.excerpt.prop('disabled', checked)[checked ? 'addClass' : 'removeClass']('disabled');
		})
		.on('mouseenter', 'a.clickbadge', function () {
			var _this = $(this),
				_position = _this.position(),
				p = _this.data('p'),
				link = _this.data('link'),
				clicks = _this.data('clicks'),
				total = _this.data('total');

			mailster.clickmap.$.popup.find('.piechart').data('easyPieChart').update(p);
			mailster.clickmap.$.popup.find('.link').html(link);
			mailster.clickmap.$.popup.find('.clicks').html(clicks);
			mailster.clickmap.$.popup.find('.total').html(total);
			mailster.clickmap.$.popup.stop().fadeIn(100).css({
				top: _position.top - 85,
				left: _position.left - (mailster.clickmap.$.popup.width() / 2 - _this.width() / 2)
			});

		})
		.on('mouseleave', 'a.clickbadge', function () {
			mailster.clickmap.$.popup.stop().fadeOut(400);
		});

	mailster.clickmap.updateBadges = function (stats) {
		mailster.$.templateWrap.find('.clickbadge').remove();
		var stats = stats || $('#mailster_click_stats').data('stats'),
			total = parseInt(stats.total, 10);

		if (!total) return;

		$.each(stats.clicks, function (href, countobjects) {

			$.each(countobjects, function (index, counts) {

				var link = mailster.$.iframe.contents().find('a[href="' + href.replace('&amp;', '&') + '"]').eq(index);

				if (link.length) {
					link.css('display', 'inline-block');

					var offset = link.offset(),
						top = offset.top,
						left = offset.left + 5,
						percentage = (counts.clicks / total) * 100,
						v = (percentage < 1 ? '&lsaquo;1' : Math.round(percentage)) + '%',
						badge = $('<a class="clickbadge ' + (percentage < 40 ? 'clickbadge-outside' : '') + '" data-p="' + percentage + '" data-link="' + href + '" data-clicks="' + counts.clicks + '" data-total="' + counts.total + '"><span style="width:' + (Math.max(0, percentage - 2)) + '%">' + (percentage < 40 ? '&nbsp;' : v) + '</span>' + (percentage < 40 ? ' ' + v : '') + '</a>')
						.css({
							top: top,
							left: left
						}).appendTo(mailster.$.templateWrap);

				}

			});
		});
	}

	mailster.editable && window.EmojiButton && mailster.events.push('documentReady', function () {
		$('.emoji-selector')
			.on('click', 'button', function () {
				var input = document.querySelector('#' + $(this).data('input')),
					picker = new EmojiButton({
						emojiVersion: '3.0',
						showVariants: false,
						zIndex: 1000,
					});

				picker.togglePicker(this);
				picker.on('emoji', function (emoji) {
					var caretPos = input.selectionStart;
					input.value = input.value.substring(0, caretPos) + emoji + input.value.substring(caretPos);
					setTimeout(function () {
						input.focus();
						input.setSelectionRange(caretPos + 1, caretPos + 1);
					}, 10);
				});
				return false;
			});
	});

	!mailster.editable && mailster.events.push('documentReady', function () {
		$.easyPieChart && mailster.clickmap.$.popup.find('.piechart').easyPieChart({
			animate: 2000,
			rotate: 180,
			barColor: mailster.colors.main,
			trackColor: mailster.colors.track,
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
	})

	!mailster.editable && mailster.events.push('iframeLoaded', function () {
		mailster.$.iframe.height(Math.max(500, mailster.dom.iframe.contentWindow.document.body.scrollHeight));
		mailster.clickmap.updateBadges();
		mailster.$.iframecontents && mailster.$.iframecontents.on('click', 'a', function () {
			window.open(this.href);
			return false;
		});

	});

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Template


// block Submit
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$.submit = $('#mailster_submitdiv .inside');

	mailster.submit = mailster.submit || {};

	mailster.$.submit
		.on('change', '#use_pwd', function () {
			$('#password-wrap').slideToggle(200).find('input').focus().select();
			$('#post_password').prop('disabled', !$(this).is(':checked'));
		})
		.on('click', '.sendnow-button', function () {
			if (!confirm(mailster.l10n.campaigns.send_now)) return false;
		});

	mailster.submit.$ = {};

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Submit


// block Delivery
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$.delivery = $('#mailster_delivery .inside');

	mailster.delivery = mailster.delivery || {};
	mailster.delivery.$ = {};

	mailster.$.delivery
		.on('change', 'input.timezone', function () {
			$('.active_wrap').toggleClass('timezone-enabled');
		})
		.on('change', 'input.autoresponder-timezone', function () {
			$('.autoresponderfield-mailster_autoresponder_timebased').toggleClass('timezone-enabled');
		})
		.on('change', 'input.userexactdate', function () {
			$(this).parent().parent().parent().find('span').addClass('disabled');
			$(this).parent().find('span').removeClass('disabled');
		})
		.on('change', '#autoresponder-post_type', function () {
			var cats = $('#autoresponder-taxonomies');
			cats.find('select').prop('disabled', true);
			mailster.util.ajax('get_post_term_dropdown', {
				labels: false,
				names: true,
				posttype: $(this).val()
			}, function (response) {
				if (response.success) {
					cats.html(response.html);
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);

			});
		})
		.on('click', '.category-tabs a', function () {
			var _this = $(this),
				href = _this.attr('href');

			mailster.$.delivery.find('.tabs-panel').hide();
			mailster.$.delivery.find('.tabs').removeClass('tabs');
			_this.parent().addClass('tabs');
			$(href).show();
			$('#mailster_is_autoresponder').val((href == '#autoresponder') ? 1 : '');
			return false;
		})
		.on('click', '.mailster_sendtest', function () {
			var $this = $(this);

			loader(true);
			$this.prop('disabled', true);
			mailster.trigger('save');

			mailster.util.ajax('send_test', {
				formdata: mailster.$.form.serialize(),
				to: $('#mailster_testmail').val() ? $('#mailster_testmail').val() : $('#mailster_testmail').attr('placeholder'),
				content: mailster.$.content.val(),
				head: mailster.$.head.val(),
				plaintext: mailster.$.excerpt.val()

			}, function (response) {

				loader(false);
				$this.prop('disabled', false);
				mailster.util.tempMsg(response.msg, (!response.success ? 'error' : 'updated'), $this.parent());
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);
				$this.prop('disabled', false);
				mailster.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());

			})
		})
		.on('change', '#mailster_data_active', function () {
			$(this).is(':checked') ?
				$('.active_wrap').addClass('disabled') :
				$('.active_wrap').removeClass('disabled');

			$('.deliverydate, .deliverytime').prop('disabled', !$(this).is(':checked'));

		})
		.on('change', '#mailster_data_autoresponder_active', function () {
			$(this).is(':checked') ?
				$('.autoresponder_active_wrap').addClass('disabled') :
				$('.autoresponder_active_wrap').removeClass('disabled');

		})
		.on('click', '.mailster_spamscore', function () {
			var $this = $(this),
				progress = $('#spam_score_progress').removeClass('spam-score').slideDown(200),
				progressbar = progress.find('.bar');

			loader(true);
			$this.prop('disabled', true);
			$('.score').html('');
			mailster.trigger('save');
			progressbar.css('width', '20%');

			mailster.util.ajax('send_test', {
				spamtest: true,
				formdata: mailster.$.form.serialize(),
				to: $('#mailster_testmail').val() ? $('#mailster_testmail').val() : $('#mailster_testmail').attr('placeholder'),
				content: mailster.$.content.val(),
				head: mailster.$.head.val(),
				plaintext: mailster.$.excerpt.val()

			}, function (response) {

				if (response.success) {
					progressbar.css('width', '40%');
					checkSpamScore(response.id, 1);
				} else {
					loader(false);
					progress.slideUp(200);
					mailster.util.tempMsg(response.msg, 'error', $this.parent());
				}
			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
				$this.prop('disabled', false);
				mailster.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());

			})

		})
		.on('blur', 'input.deliverytime', function () {
			mailster.$.document.unbind('.mailster_deliverytime');
		})
		.on('focus, click', 'input.deliverytime', function (event) {
			var $this = $(this),
				input = $(this)[0],
				l = $this.offset().left,
				c = 0,
				startPos = 0,
				endPos = 2;

			if (event.clientX - l > 23) {
				c = 1,
					startPos = 3,
					endPos = 5;
			}
			mailster.$.document.unbind('.mailster_deliverytime')
				.on('keypress.mailster_deliverytime', function (event) {
					if (event.keyCode == 9) {
						return (c = !c) ? !mailster.util.selectRange(input, 3, 5) : (event.shiftKey) ? !mailster.util.selectRange(input, 0, 2) : true;
					}
				})
				.on('keyup.mailster_deliverytime', function (event) {
					if ($this.val().length == 1) {
						$this.val($this.val() + ':00');
						mailster.util.selectRange(input, 1, 1);
					}
					if (document.activeElement.selectionStart == 2) {
						if ($this.val().substr(0, 2) > 23) {
							$this.trigger('change');
							return false;
						}
						mailster.util.selectRange(input, 3, 5);
					}
				});
			mailster.util.selectRange(input, startPos, endPos);

		})
		.on('change', 'input.deliverytime', function () {
			var $this = $(this),
				val = $this.val(),
				time;
			$this.addClass('inactive');
			if (!/^\d+:\d+$/.test(val)) {

				if (val.length == 1) {
					val = "0" + val + ":00";
				} else if (val.length == 2) {
					val = val + ":00";
				} else if (val.length == 3) {
					val = val.substr(0, 2) + ":" + val.substr(2, 3) + "0";
				} else if (val.length == 4) {
					val = val.substr(0, 2) + ":" + val.substr(2, 4);
				}
			}
			time = val.split(':');

			if (!/\d\d:\d\d$/.test(val) && val != "" || time[0] > 23 || time[1] > 59) {
				$this.val('00:00').focus();
				mailster.util.selectRange($this[0], 0, 2);
			} else {
				$this.val(val);
			}
		})
		.on('change', '#mailster_autoresponder_action', function () {
			$('#autoresponder_wrap').removeAttr('class').addClass('autoresponder-' + $(this).val());
		})
		.on('change', '#time_extra', function () {
			$('#autoresponderfield-mailster_timebased_advanced').slideToggle();
		})
		.on('click', '.mailster_autoresponder_timebased-end-schedule', function () {
			$(this).is(':checked') ?
				$('.mailster_autoresponder_timebased-end-schedule-field').slideDown() :
				$('.mailster_autoresponder_timebased-end-schedule-field').slideUp();
		})
		.on('change', '.mailster-action-hooks', function () {
			var val = $(this).val();
			$('.mailster-action-hook').val(val);
			if (!val) {
				$('.mailster-action-hook').focus();
			}
		})
		.on('change', '.mailster-action-hook', function () {
			var val = $(this).val();
			if (!$(".mailster-action-hooks option[value='" + val + "']").length) {
				$('.mailster-action-hooks').append('<option>' + val + '</option>');
			}
			$('.mailster-action-hooks').val(val);
		})
		.on('click', '.mailster-total', function () {
			mailster.trigger('updateCount');
		})
		.on('change', '#list_extra', function () {
			if ($(this).is(':checked')) {
				$('#mailster_list_advanced').slideDown();
			} else {
				$('#mailster_list_advanced').slideUp();
			}
			$('#list-checkboxes').find('input.list').eq(0).trigger('change');
		})
		.on('focus', 'input.datepicker', function () {
			$(this).removeClass('inactive').trigger('click');
		})
		.on('blur', 'input.datepicker', function () {
			$('.deliverydate').html($(this).val());
			$(this).addClass('inactive');
		});

	$.datepicker && mailster.$.delivery
		.find('input.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: new Date(),
			firstDay: mailster.l10n.campaigns.start_of_week,
			showWeek: true,
			dayNames: mailster.l10n.campaigns.day_names,
			dayNamesMin: mailster.l10n.campaigns.day_names_min,
			monthNames: mailster.l10n.campaigns.month_names,
			prevText: mailster.l10n.campaigns.prev,
			nextText: mailster.l10n.campaigns.next,
			showAnim: 'fadeIn',
			onClose: function () {
				var date = $(this).datepicker('getDate');
				$('.deliverydate').html($(this).val());
			}
		});

	$.datepicker && $('input.datepicker.nolimit').datepicker("option", "minDate", null);

	mailster.$.delivery.find('input.datepicker').not('.hasDatepicker').prop('readonly', false);

	mailster.events.push('documentReady', function () {
		//switch to autoresponder if referer is right or post_status is set
		if (/post_status=autoresponder/.test($('#referredby').val()) || /post_status=autoresponder/.test(location.search)) {
			mailster.$.delivery.find('a[href="#autoresponder"]').click();
		}

	});

	(function () {

		var t, x, h, m, l, usertime = new Date(),
			elements = $('.time'),
			deliverytime = $('.deliverytime').eq(0),
			activecheck = $('#mailster_data_active'),
			servertime = parseInt(elements.data('timestamp'), 10) * 1000,
			seconds = false,
			offset = servertime - usertime.getTime() + (usertime.getTimezoneOffset() * 60000);

		var delay = (seconds) ? 1000 : 20000;

		function set() {
			t = new Date();

			usertime = t.getTime();
			t.setTime(usertime + offset);
			h = t.getHours();
			m = t.getMinutes();

			if (mailster.enabled && x && m != x[1] && !activecheck.is(':checked')) {
				deliverytime.val(zero(h) + ':' + zero(m));
			}
			x = [];
			x.push(t.getHours());
			x.push(t.getMinutes());
			if (seconds) x.push(t.getSeconds());
			l = x.length;
			for (var i = 0; i < l; i++) {
				x[i] = zero(x[i]);
			};
			elements.html(x.join('<span class="blink">:</span>'));
			setTimeout(function () {
				set();
			}, delay);
		}

		function zero(value) {
			if (value < 10) {
				value = '0' + value;
			}
			return value;
		}

		set();

	})();


	function loader(show) {
		if (null == show || true === show) {
			$('#delivery-ajax-loading').css('display', 'inline');
		} else {
			$('#delivery-ajax-loading').hide();
		}
	}

	function checkSpamScore(id, round) {

		var $button = $('.mailster_spamscore'),
			progress = $('#spam_score_progress'),
			progressbar = progress.find('.bar');

		mailster.util.ajax('check_spam_score', {
			ID: id,
		}, function (response) {

			if (response.score) {
				loader(false);
				$button.prop('disabled', false);
				progress.addClass('spam-score');
				progressbar.css('width', (parseFloat(response.score) * 10) + '%');

				$('.score').html('<strong>' + mailster.util.sprintf(mailster.l10n.campaigns.yourscore, response.score) + '</strong>:<br>' + mailster.l10n.campaigns.yourscores[Math.floor((response.score / 10) * mailster.l10n.campaigns.yourscores.length)]);
			} else {

				if (round <= 5 && !response.abort) {
					var percentage = (round * 10) + 50;
					progressbar.css('width', (percentage) + '%');
					setTimeout(function () {
						checkSpamScore(id, ++round);
					}, round * 400);
				} else {

					loader(false);
					$button.prop('disabled', false);
					progressbar.css('width', '100%');
					progress.slideUp(200);
					mailster.util.tempMsg(response.msg, 'error', $button.parent(), function () {
						progressbar.css('width', 0);
					});

				}

			}
		}, function (jqXHR, textStatus, errorThrown) {
			loader(false);
			$this.prop('disabled', false);
			mailster.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());
			var msg = $('<div class="error"><p>' + textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});
		})
	}
	return mailster;

}(mailster || {}, jQuery, window, document));
// end Delivery


// block Receivers
mailster = (function (mailster, $, window, document) {

	"use strict";

	var updateCountTimout;

	mailster.$.receivers = $('#mailster_receivers .inside');

	mailster.receivers = mailster.receivers || {};

	mailster.receivers.$ = {};
	mailster.receivers.$.conditions = $('.mailster-conditions-thickbox');
	mailster.receivers.$.conditionsOutput = $('#mailster_conditions_render');
	mailster.receivers.$.total = $('.mailster-total');

	mailster.$.receivers
		.on('change', 'input.list', function () {
			mailster.trigger('updateCount');
		})
		.on('change', '#all_lists', function () {
			$('#list-checkboxes').find('input.list').prop('checked', $(this).is(':checked'));
			mailster.trigger('updateCount');
		})
		.on('change', '#ignore_lists', function () {
			var checked = $(this).is(':checked');
			$('#list-checkboxes').each(function () {
				(checked) ?
				$(this).slideUp(200): $(this).slideDown(200);
			});
			mailster.trigger('updateCount');
		})
		.on('click', '.edit-conditions', function () {
			tb_show(mailster.l10n.campaigns.edit_conditions, '#TB_inline?x=1&width=720&height=520&inlineId=receivers-dialog', null);
			return false;
		})
		.on('click', '.remove-conditions', function () {
			if (confirm(mailster.l10n.campaigns.remove_conditions)) {
				$('#receivers-dialog').find('.mailster-conditions-wrap').empty();
				mailster.trigger('updateCount');
			}
			return false;
		})
		.on('click', '.mailster-total', function () {
			mailster.trigger('updateCount');
		})
		.on('click', '.create-new-list', function () {
			var $this = $(this).hide();
			$('.create-new-list-wrap').slideDown();
			$('.create-list-type').trigger('change');
			return false;
		})
		.on('click', '.create-list', function () {
			var $this = $(this),
				listtype = $('.create-list-type'),
				name = '';
			if (listtype.val() == -1) return false;

			if (name = prompt(mailster.l10n.campaigns.enter_list_name, mailster.util.sprintf(mailster.l10n.campaigns.create_list, listtype.find(':selected').text(), $('#title').val()))) {

				loader(true);

				mailster.util.ajax('create_list', {
					name: name,
					listtype: listtype.val(),
					id: mailster.campaign_id
				}, function (response) {
					loader(false);
					mailster.util.tempMsg(response.msg, (!response.success ? 'error' : 'updated'), $('.create-new-list-wrap'));
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
					mailster.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $('.create-new-list-wrap'));
				});
			}

			return false;
		})
		.on('change', '.create-list-type', function () {
			var listtype = $(this);

			if (listtype.val() == -1) return false;
			listtype.prop('disabled', true);

			loader(true);

			mailster.util.ajax('get_create_list_count', {
				listtype: listtype.val(),
				id: mailster.campaign_id
			}, function (response) {
				listtype.prop('disabled', false);
				loader(false, response.count);

			}, function (jqXHR, textStatus, errorThrown) {
				listtype.prop('disabled', false);
				loader(false, '');
			});
		})
		.on('click', '.mailster-total', function () {
			$('.create-list-type').trigger('change');
		});


	window.tb_remove && mailster.receivers.$.conditions
		.on('click', '.close-conditions', tb_remove);


	mailster.editable && mailster.events.push('documentReady', function () {
		mailster.trigger('updateCount');
	});

	mailster.events.push('documentReady', function () {
		mailster.trigger('enable');
	});

	mailster.editable && mailster.events.push('updateCount', function () {

		clearTimeout(updateCountTimout);
		updateCountTimout = setTimeout(function () {
			var lists = [],
				conditions = [],
				inputs = $('#list-checkboxes').find('input, select'),
				listinputs = $('#list-checkboxes').find('input.list'),
				extra = $('#list_extra'),
				data = {},
				groups = $('.mailster-conditions-wrap > .mailster-condition-group'),
				i = 0;

			$.each(listinputs, function () {
				var id = $(this).val();
				if ($(this).is(':checked')) lists.push(id);
			});

			data.id = mailster.campaign_id;
			data.lists = lists;
			data.ignore_lists = $('#ignore_lists').is(':checked');

			$.each(groups, function () {
				var c = $(this).find('.mailster-condition');
				$.each(c, function () {
					var _this = $(this),
						value,
						field = _this.find('.condition-field').val(),
						operator = _this.find('.mailster-conditions-operator-field.active').find('.condition-operator').val();

					if (!operator || !field) return;

					value = _this.find('.mailster-conditions-value-field.active').find('.condition-value').map(function () {
						return $(this).val();
					}).toArray();
					if (value.length == 1) {
						value = value[0];
					}
					if (!conditions[i]) {
						conditions[i] = [];
					}

					conditions[i].push({
						field: field,
						operator: operator,
						value: value,
					});
				});
				i++;
			});

			data.operator = $('select.mailster-list-operator').val();
			data.conditions = conditions;

			loader(true);

			mailster.trigger('disable');

			mailster.util.ajax('get_totals', data, function (response) {
				mailster.trigger('enable');
				loader(false, response.totalformatted);
				mailster.receivers.$.conditionsOutput.html(response.conditions);
			}, function (jqXHR, textStatus, errorThrown) {
				mailster.trigger('enable');
				loader(false, '?');
			});
		}, 10);

	})

	function loader(show, html) {
		if (null == show || true === show) {
			mailster.receivers.$.total.addClass('loading');
		} else {
			mailster.receivers.$.total.removeClass('loading');
		}
		if (null != html) {
			mailster.receivers.$.total.html(html);
		}
	}

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Receivers


// block Options
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$.options = $('#mailster_options .inside');

	mailster.options = mailster.options || {};

	mailster.options.$ = {};
	mailster.options.$.colorInputs = mailster.$.options.find('input.color');

	mailster.$.options
		.on('click', '.wp-color-result', function () {
			$(this).closest('li.mailster-color').addClass('open');
		})
		.on('click', 'a.default-value', function () {
			var el = $(this).prev().find('input'),
				color = el.data('default');

			el.wpColorPicker('color', color);
			return false;
		})
		.on('click', 'ul.colorschema', function () {
			var colorfields = mailster.$.options.find('input.color'),
				li = $(this).find('li.colorschema-field');

			mailster.trigger('disable');

			$.each(li, function (i) {
				var color = li.eq(i).data('hex');
				colorfields.eq(i).wpColorPicker('color', color);
			});

			mailster.trigger('enable');

		})
		.on('click', 'a.savecolorschema', function () {
			var colors = $.map(mailster.$.options.find('.color'), function (e) {
				return $(e).val();
			});

			loader(true);

			mailster.util.ajax('save_color_schema', {
				template: $('#mailster_template_name').val(),
				colors: colors
			}, function (response) {
				loader(false);
				if (response.success) {
					$('.colorschema').last().after($(response.html).hide().fadeIn());
				}
			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
			})

		})
		.on('click', '.colorschema-delete', function () {

			if (confirm(mailster.l10n.campaigns.delete_colorschema)) {

				var schema = $(this).parent().parent();

				loader(true);

				mailster.util.ajax('delete_color_schema', {
					template: $('#mailster_template_name').val(),
					hash: schema.data('hash')
				}, function (response) {
					loader(false);
					if (response.success) {
						schema.fadeOut(100, function () {
							schema.remove()
						});
					}
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
				});

			}

			return false;

		})
		.on('click', '.colorschema-delete-all', function () {

			if (confirm(mailster.l10n.campaigns.delete_colorschema_all)) {

				var schema = $('.colorschema.custom');

				loader(true);

				mailster.util.ajax('delete_color_schema_all', {
					template: $('#mailster_template_name').val(),
				}, function (response) {
					loader(false);
					if (response.success) {
						schema.fadeOut(100, function () {
							schema.remove()
						});
					}
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
				});

			}

			return false;

		})
		.on('change', '#mailster_version', function () {
			var val = $(this).val();
			_changeElements(val);
		})
		.on('change', 'input.color', function (event, ui) {
			var _this = $(this);
			var _val = Color(_this.val()).toString().toUpperCase();
			var _from = _this.data('value');
			var original = _this.data('default-color');

			_this.val(_val);

			mailster.util.changeColor(_from, _val, _this, original);
		});

	$.wp.wpColorPicker && mailster.options.$.colorInputs.wpColorPicker({
		color: true,
		width: 250,
		mode: 'hsl',
		palettes: $('.colors').data('original'),
		change: function (event, ui) {
			$(this).val(ui.color.toString()).trigger('change');
		},
		clear: function (event, ui) {}
	});

	function loader(show) {
		if (null == show || true === show) {
			$('#colorschema-ajax-loading').css('display', 'inline');
		} else {
			$('#colorschema-ajax-loading').hide();
		}
	}


	return mailster;

}(mailster || {}, jQuery, window, document));
// end Options


// block Attachments
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$.attachments = $('#mailster_attachments .inside');

	mailster.attachments = mailster.attachments || {};

	mailster.attachments.$ = {};

	mailster.$.attachments
		.on('click', '.delete-attachment', function (event) {
			event.preventDefault();
			$(this).parent().remove();
		})
		.on('click', '.add-attachment', function (event) {
			event.preventDefault();
			if (!wp.media.frames.mailster_attachments) {
				wp.media.frames.mailster_attachments = wp.media({
					title: mailster.l10n.campaigns.add_attachment,
					button: {
						text: mailster.l10n.campaigns.add_attachment,
					},
					multiple: false
				});
				wp.media.frames.mailster_attachments.on('select', function () {
					var attachment = wp.media.frames.mailster_attachments.state().get('selection').first().toJSON(),
						el = $('.mailster-attachment').eq(0).clone();
					el.find('img').attr('src', attachment.icon);
					el.find('.mailster-attachment-label').html(attachment.filename);
					el.find('input').attr('name', 'mailster_data[attachments][]').val(attachment.id);
					el.appendTo('.mailster-attachments');

				});
			}
			wp.media.frames.mailster_attachments.open();
		});

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Attachments



// block heartbeat
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.events.push('documentReady', function () {
		if (typeof wp != 'undefined' && wp.heartbeat) wp.heartbeat.interval('fast');
	})
	mailster.$.document
		.on('heartbeat-send', function (e, data) {

			if (mailster.editable) {
				if (data && data['wp_autosave'] && mailster.editor) {
					data['wp_autosave']['content'] = mailster.editor.getContent();
					data['wp_autosave']['excerpt'] = mailster.$.excerpt.val();
					data['mailsterdata'] = mailster.$.datafields.serialize();
				}
			} else {
				if (data['wp_autosave'])
					delete data['wp_autosave'];

				data['mailster'] = {
					page: 'edit',
					id: mailster.campaign_id
				};
			}

		})
		.on('heartbeat-tick', function (e, data) {

			if (mailster.editable || !data.mailster || !data.mailster[mailster.campaign_id]) return;

			var _data = data.mailster[mailster.campaign_id],
				stats = $('#stats').find('.verybold'),
				charts = $('#stats').find('.piechart'),
				progress = $('.progress'),
				p = (_data.sent / _data.total * 100);

			$('.hb-sent').html(_data.sent_f);
			$('.hb-deleted').html(_data.deleted_f);
			$('.hb-opens').html(_data.opens_f);
			$('.hb-clicks').html(_data.clicks_f);
			$('.hb-clicks_total').html(_data.clicks_total_f);
			$('.hb-unsubs').html(_data.unsubs_f);
			$('.hb-bounces').html(_data.bounces_f);
			$('.hb-geo_location').html(_data.geo_location);

			$.each(_data.environment, function (type) {
				$('.hb-' + type).html((this.percentage * 100).toFixed(2) + '%');
			});

			if ($('#stats_opens').length) $('#stats_opens').data('easyPieChart').update(Math.round(_data.open_rate));
			if ($('#stats_clicks').length) $('#stats_clicks').data('easyPieChart').update(Math.round(_data.click_rate));
			if ($('#stats_unsubscribes').length) $('#stats_unsubscribes').data('easyPieChart').update(Math.round(_data.unsub_rate));
			if ($('#stats_bounces').length) $('#stats_bounces').data('easyPieChart').update(Math.round(_data.bounce_rate));

			progress.find('.bar').width(p + '%');
			progress.find('span').eq(1).html(_data.sent_formatted);
			progress.find('span').eq(2).html(_data.sent_formatted);
			progress.find('var').html(Math.round(p) + '%');

			mailster.clickmap.updateBadges(_data.clickbadges);

			if (_data.status != $('#original_post_status').val() && !$('#mailster_status_changed_info').length) {

				$('<div id="mailster_status_changed_info" class="error inline"><p>' + mailster.util.sprintf(mailster.l10n.campaigns.statuschanged, '<a href="post.php?post=' + mailster.campaign_id + '&action=edit">' + mailster.l10n.campaigns.click_here + '</a></p></div>'))
					.hide()
					.prependTo('#postbox-container-2')
					.slideDown(200);
			}

		});


	return mailster;

}(mailster || {}, jQuery, window, document));
// end heartbeat