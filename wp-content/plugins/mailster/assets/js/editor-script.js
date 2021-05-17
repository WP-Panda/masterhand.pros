// not in an iframe
if (parent.window === window) {

	var campaign_id;
	if (campaign_id = location.search.match(/id=(\d+)/i)[1]) {
		window.location = location.protocol + '//' + location.host + location.pathname.replace('admin-ajax.php', 'post.php') + '?post=' + campaign_id + '&action=edit';
	}
}

document.getElementsByTagName("html")[0].className += ' mailster-loading';
window.mailster = parent.window.mailster || {};

// block
mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.editor = mailster.editor || {};

	mailster.editor.loaded = false;

	mailster.editor.$ = mailster.editor.$ || {};
	mailster.editor.$.html = $('html');
	mailster.editor.$.body = $('body');

	$(window).on('load', function () {

		mailster.editor.updateElements();

		mailster.editor.$.html.removeClass('mailster-loading');
		mailster.editor.$.body
			.on('click', 'a', function (event) {
				event.preventDefault();
			})
			.on('click', function (event) {
				mailster.modules.selected && mailster.modules.selected.removeAttr('selected');
			})
			.on('click', 'module', function (event) {
				if ('MODULE' == event.target.nodeName) {
					var module = $(this);
					event.stopPropagation();
					if (mailster.modules.selected[0] != module[0]) {
						mailster.trigger('selectModule', module);
					}
				}
			})
			.on('click', 'button.addbutton', function () {
				var data = $(this).data(),
					element = decodeURIComponent(data.element.data('tmpl')) || '<a href="" editable label="Button"></a>';

				mailster.editbar.open({
					type: 'btn',
					offset: data.offset,
					element: $(element).attr('tmpbutton', '').appendTo(data.element),
					name: data.name
				});
				return false;
			})
			.on('click', 'button.addrepeater', function () {
				var data = $(this).data();

				if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
					var table = data.element.closest('table'),
						index = data.element.prevAll().length;
					for (var i = table[0].rows.length - 1; i >= 0; i--) {
						$(table[0].rows[i].cells[index]).clone().insertAfter(table[0].rows[i].cells[index]);
					}
				} else {
					data.element.clone().insertAfter(data.element);
				}

				mailster.trigger('save');
				mailster.trigger('refresh');

				return false;
			})
			.on('click', 'button.removerepeater', function () {
				var data = $(this).data();

				if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
					var table = data.element.closest('table'),
						index = data.element.prevAll().length;
					for (var i = table[0].rows.length - 1; i >= 0; i--) {
						$(table[0].rows[i].cells[index]).remove();
					}
				} else {
					data.element.remove();
				}

				mailster.trigger('save');
				mailster.trigger('refresh');

				return false;
			});

		if (!mailster.editor.loaded) {
			mailster.editor.loaded = true;
			mailster.trigger('editorLoaded');
		}

	});

	mailster.editor.getFrameContent = function () {

		if (!mailster.editor.$.body.length) {
			return false;
		}

		var body = mailster.editor.$.body[0],
			clone, content, bodyattributes, attrcount, s = '';

		clone = $('<div>' + body.innerHTML + '</div>');

		clone.find('.mce-tinymce, .mce-widget, .mce-toolbar-grp, .mce-container, .screen-reader-text, .ui-helper-hidden-accessible, .wplink-autocomplete, modulebuttons, mailster, #mailster-editorimage-upload-button, button, .a11y-speak-intro-text').remove();

		clone.find('single, multi, module, modules, buttons').removeAttr('contenteditable spellcheck id dir style class selected');
		content = mailster.util.trim(clone.html().replace(/\u200c/g, '&zwnj;').replace(/\u200d/g, '&zwj;'));

		bodyattributes = body.attributes || [];
		attrcount = bodyattributes.length;

		if (attrcount) {
			while (attrcount--) {
				s = ' ' + bodyattributes[attrcount].name + '="' + mailster.util.trim(bodyattributes[attrcount].value) + '"' + s;
			}
		}
		s = mailster.util.trim(
			s
			.replace(/(webkit |wp\-editor|mceContentBody|position: relative;|cursor: auto;|modal-open| spellcheck="(true|false)")/g, '')
			.replace(/(class="(\s*)"|style="(\s*)")/g, '')
		);

		return mailster.$.head.val() + "\n<body" + (s ? ' ' + s : '') + ">\n" + content + "\n</body>\n</html>";
	}

	mailster.editor.cleanup = function () {

		// remove some third party elements
		mailster.editor.$.document.find('#a11y-speak-assertive, #a11y-speak-polite, #droplr-chrome-extension-is-installed').remove();

	}

	mailster.editor.getStructure = function (html) {
		var parts = html.match(/([^]*)<body([^>]*)?>([^]*)<\/body>([^]*)/m);

		return {
			parts: parts ? parts : ['', '', '', '<multi>' + html + '</multi>'],
			content: parts ? parts[3] : '<multi>' + html + '</multi>',
			head: parts ? mailster.util.trim(parts[1]) : '',
			bodyattributes: parts ? $('<div' + (parts[2] || '') + '></div>')[0].attributes : ''
		};
	}

	mailster.editor.getContent = function () {
		return mailster.$.content.val() || mailster.editor.getFrameContent();
	}

	mailster.editor.setContent = function (content, delay, saveit, extrastyle) {

		var structure = mailster.editor.getStructure(content),
			attrcount = structure.bodyattributes.length,
			head = mailster.editor.$.document.find('head'),
			headstyles = head.find('link');

		mailster.$.head.val(structure.head);
		if (!extrastyle) {
			extrastyle = '';
		}
		head[0].innerHTML = structure.head.replace(/([^]*)<head([^>]*)?>([^]*)<\/head>([^]*)/m, '$3' + extrastyle);
		head.append(headstyles);

		mailster.editor.$.body[0].innerHTML = structure.content;

		if (attrcount) {
			while (attrcount--) {
				mailster.editor.$.body[0].setAttribute(structure.bodyattributes[attrcount].name, structure.bodyattributes[attrcount].value)
			}
		}

		mailster.$.content.val(content);

		if (typeof saveit == 'undefined' || saveit === true) {
			mailster.trigger('save');
		}

		setTimeout(function () {
			mailster.trigger('redraw');
		}, 100);
	}

	mailster.editor.getHeight = function () {
		return Math.max(500, mailster.editor.$.body.outerHeight());
	}


	mailster.editor.resize = function () {
		if (!mailster.editor.loaded) return false;
		setTimeout(function () {
			mailster.$.iframe.attr("height", mailster.editor.getHeight());
			mailster.trigger('resize');
		}, 50);
	}

	mailster.editor.colors = mailster.$.options.find('.colors').data();

	function initFrame() {
		mailster.$.templateWrap.removeClass('load');
		mailster.trigger('iframeLoaded');
		makeEditable();
	}

	function makeEditable() {

		mailster.editor.$.document.find('.content.mailster-btn').remove();
		var modulehelper = null;

		if (!mailster.editor.$.document) return;

		mailster.editor.$.document
			//.off('.mailster')
			.on('click.mailster', 'img[editable]', function (event) {
				event.stopPropagation();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.mailster', 'module td[background],module th[background]', function (event) {
				event.stopPropagation();
				modulehelper = true;
			})
			.on('click.mailster', 'td[background], th[background]', function (event) {
				event.stopPropagation();
				if (!modulehelper && event.target != this) return;
				modulehelper = null;

				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.mailster', 'a[editable]', function (event) {
				event.stopPropagation();
				event.preventDefault();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 40,
					left = offset.left,
					name = $this.attr('label'),
					type = 'btn';

				mailster.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});


			})

		if (!mailsterdata.inline) {
			mailster.editor.$.container
				.on('click.mailster', 'multi, single', function (event) {
					event.stopPropagation();
					var $this = $(this),
						offset = $this.offset(),
						top = offset.top + 40,
						left = offset.left,
						name = $this.attr('label'),
						type = $this.prop('tagName').toLowerCase();

					mailster.editbar.open({
						'offset': offset,
						'type': type,
						'name': name,
						'element': $this
					});
				});
		}

		mailster.editor.cleanup();

	}

	mailster.events = mailster.events || [];

	mailster.events.push('refresh', mailster.editor.resize);
	mailster.events.push('editorLoaded', initFrame, mailster.editor.resize);
	mailster.events.push('redraw', makeEditable);


	// legacy buttons
	mailster.editor.$.body.find('div.modulebuttons').remove();
	(mailster.isrtl) ? mailster.editor.$.html.attr('mailster-is-rtl', ''): mailster.editor.$.html.removeAttr('mailster-is-rtl');

	return mailster;

}(mailster || {}, jQuery, window, document));
// end block


// block Modules
mailster = (function (mailster, $, window, document) {

	"use strict";

	var changetimeout,
		change = false,
		uploader = false;

	mailster.events = mailster.events || [];

	mailster.events.push('editorLoaded',
		function () {
			mailster.events.push('refresh', updateElements, sortable, draggable);
			sortable();
			draggable();
			mailster.events.push('resize', buttons) && buttons();
			mailster.events.push('selectModule', select);
			typeof mOxie != 'undefined' && mailster.events.push('refresh', upload) && upload();
			typeof tinymce != 'undefined' && mailster.events.push('refresh', inlineEditor) && inlineEditor();
		}
	)

	$(document).ready(updateElements);

	function updateElements() {
		mailster.editor.$ = mailster.editor.$ || {};
		mailster.editor.$.document = $(document);
		mailster.editor.$.window = $(window);
		mailster.editor.$.html = $('html');
		mailster.editor.$.body = $('body');
		mailster.editor.$.container = $('modules');
		mailster.editor.$.modules = $('module');
		mailster.editor.$.images = $('img[editable]');
		mailster.editor.$.buttons = $('buttons');
		mailster.editor.$.repeatable = $('[repeatable]');
	}

	function moduleButtons() {

		var elements = $(mailsterdata.modules).add(mailster.editor.$.modules),
			mc = 0;

		//no modules at all
		if (!mailster.editor.$.modules.length) {
			//selector.remove();
			return;
		}

		elements = mailster.editor.$.modules;

		// add module buttons and add them to the list
		$.each(elements, function (j) {
			var $this = $(this);
			if ($this.is('module') && !$this.find('modulebuttons').length) {
				var name = $this.attr('label') || mailster.util.sprintf(mailster.l10n.campaigns.module, '#' + (++mc)),
					codeview = mailsterdata.codeview ? '<button class="mailster-btn codeview" title="' + mailster.l10n.campaigns.codeview + '"></button>' : '',
					auto = ($this.is('[auto]') ? '<button class="mailster-btn auto" title="' + mailster.l10n.campaigns.auto + '"></button>' : '');

				$('<modulebuttons>' + '<input class="modulelabel" type="text" value="' + name + '" placeholder="' + name + '" title="' + mailster.l10n.campaigns.module_label + '" tabindex="-1"><span>' + auto + '<button class="mailster-btn duplicate" title="' + mailster.l10n.campaigns.duplicate_module + '"></button><button class="mailster-btn up" title="' + mailster.l10n.campaigns.move_module_up + '"></button><button class="mailster-btn down" title="' + mailster.l10n.campaigns.move_module_down + '"></button>' + codeview + '<button class="mailster-btn remove" title="' + mailster.l10n.campaigns.remove_module + '"></button></span></modulebuttons>').prependTo($this);

			}
		});

	}

	function sortable() {
		if (mailster.editor.$.container.data('sortable')) mailster.editor.$.container.sortable('destroy');

		if (mailster.editor.$.modules.length < 2) return;

		mailster.editor.$.container.sortable({
			stop: function (event, ui) {
				event.stopPropagation();
				mailster.editor.$.container.removeClass('dragging');
				setTimeout(function () {
					mailster.trigger('refresh');
					mailster.trigger('save');
				}, 200);
			},
			start: function (event, ui) {
				event.stopPropagation();
				mailster.editor.$.container.addClass('dragging');
			},
			containment: 'body',
			revert: 100,
			axis: 'y',
			placeholder: "sortable-placeholder",
			items: "> module",
			delay: 20,
			distance: 5,
			scroll: true,
			scrollSensitivity: 10,
			forcePlaceholderSize: true,
			helper: 'clone',
			zIndex: 10000

		});
	}

	function draggable() {

		if (mailster.editor.$.images.data('draggable')) mailster.editor.$.images.draggable('destroy');
		if (mailster.editor.$.images.data('droppable')) mailster.editor.$.images.droppable('destroy');

		mailster.editor.$.images
			.draggable({
				helper: "clone",
				scroll: true,
				scrollSensitivity: 10,
				opacity: 0.7,
				zIndex: 1000,
				revert: 'invalid',
				addClasses: false,
				create: function (event, ui) {
					$(event.target).removeClass('ui-draggable-handle');
				},
				start: function () {
					mailster.editor.$.body.addClass('ui-dragging');
				},
				stop: function () {
					mailster.editor.$.body.removeClass('ui-dragging');
					mailster.trigger('refresh');

				}
			})
			.droppable({
				addClasses: false,
				over: function (event, ui) {
					$(event.target).addClass('ui-drag-over');
				},
				out: function (event, ui) {
					$(event.target).removeClass('ui-drag-over');
				},
				drop: function (event, ui) {
					var org = $(ui.draggable[0]),
						target = $(event.target),
						target_id, org_id, crop, copy;

					target.removeClass('ui-drag-over');

					if (!org.is('img') || !target.is('img')) return;

					target_id = target.attr('data-id') ? parseInt(target.attr('data-id'), 10) : null;
					org_id = org.attr('data-id') ? parseInt(org.attr('data-id'), 10) : null;
					crop = org.data('crop');
					copy = org.clone();

					org.addClass('mailster-loading');
					target.addClass('mailster-loading');

					mailster.util.getRealDimensions(org, function (org_w, org_h, org_f) {
						mailster.util.getRealDimensions(target, function (target_w, target_h, target_f) {

							if (event.altKey) {
								org.removeClass('mailster-loading');
								target.removeClass('mailster-loading');
							} else if (target_id) {

								mailster.util.ajax('create_image', {
									id: target_id,
									width: org_w,
									height: org_h,
									crop: org.data('crop'),
								}, function (response) {

									org.removeAttr('src').attr({
										'data-id': target_id,
										'title': target.attr('title'),
										'alt': target.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / org_f),
										'height': Math.round(response.image.height / org_f)
									}).data('id', target_id).removeClass('mailster-loading');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								org.removeAttr('src').attr({
									'data-id': 0,
									'title': target.attr('title'),
									'alt': target.attr('alt'),
									'src': target.attr('src'),
									'width': Math.round(org_w / org_f),
									'height': Math.round((org_w / (target_w / target_h)) / org_f)
								}).data('id', 0).removeClass('mailster-loading');

							}

							if (org_id) {
								mailster.util.ajax('create_image', {
									id: org_id,
									width: target_w,
									height: target_h,
									crop: target.data('crop'),
								}, function (response) {

									target.removeAttr('src').attr({
										'data-id': org_id,
										'title': org.attr('title'),
										'alt': org.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / target_f),
										'height': Math.round(response.image.height / target_f)
									}).data('id', org_id).removeClass('mailster-loading');

									mailster.trigger('refresh');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								target.removeAttr('src').attr({
									'data-id': 0,
									'title': copy.attr('title'),
									'alt': copy.attr('alt'),
									'src': copy.attr('src'),
									'width': Math.round(target_w / target_f),
									'height': Math.round((target_w / (org_w / org_h)) / target_f)
								}).data('id', 0).removeClass('mailster-loading');

							}

							if (!org_id && !target_id) mailster.trigger('refresh');

						});
					});

				}
			});
	}

	function buttons() {
		if (mailster.editor.$.buttons) {
			$.each(mailster.editor.$.buttons, function () {

				var $this = $(this),
					name = $this.attr('label'),
					offset = this.getBoundingClientRect(),
					top = offset.top + 0,
					left = offset.right + 0,
					btn, tmpl;

				if ($this.data('has-buttons')) return;

				btn = $('<button class="addbutton mailster-btn mailster-btn-inline" title="' + mailster.l10n.campaigns.add_button + '"></button>').appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				$this.data('has-buttons', true);

				if (!(tmpl = $this.data('tmpl'))) {
					if ($this.find('.textbutton').length) {
						tmpl = $this.find('.textbutton').last();
					} else if ($this.find('img').length) {
						tmpl = $this.find('a[editable]').last();
					} else {
						tmpl = $('<a href="" editable label="Button"></a>');
					}
					tmpl = $('<div/>').text(encodeURIComponent(tmpl[0].outerHTML)).html();
				}

				$this.attr('data-tmpl', tmpl).data('tmpl', tmpl);

			});
		}

		$('button.addrepeater, button.removerepeater').remove();

		if (mailster.editor.$.repeatable) {
			$.each(mailster.editor.$.repeatable, function () {
				var $this = $(this),
					module = $this.closest('module'),
					name = $this.attr('label'),
					moduleoffset = module[0].getBoundingClientRect(),
					offset = this.getBoundingClientRect(),
					add_top = offset.top - moduleoffset.top,
					add_left = offset.left,
					del_top = offset.top - moduleoffset.top + 18,
					del_left = offset.left,
					btn;

				if ('TH' == this.nodeName || 'TD' == this.nodeName) {
					add_top = 0;
					add_left = offset.width - 36;
					del_top = 0;
					del_left = offset.width - 18;
				}

				btn = $('<button class="addrepeater mailster-btn mailster-btn-inline" title="' + mailster.l10n.campaigns.add_repeater + '"></button>').css({
					top: add_top,
					left: add_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				btn = $('<button class="removerepeater mailster-btn mailster-btn-inline" title="' + mailster.l10n.campaigns.remove_repeater + '"></button>').css({
					top: del_top,
					left: del_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

			});
		}
	}

	function select(module) {
		if (!module.length) {
			return;
		}
		if (mailster.modules.selected) {
			mailster.modules.selected.removeAttr('selected');
		}
		mailster.modules.selected = module;
		mailster.modules.selected.attr('selected', true);
	}

	function upload() {
		$.each(mailster.editor.$.images, function () {

			var _this = $(this),
				dropzone;

			if (_this.data('has-dropzone')) return;

			dropzone = new mOxie.FileDrop({
				drop_zone: this,
			});

			dropzone.ondrop = function (e) {

				if (mailster.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');

				var file = dropzone.files.shift(),
					altkey = window.event && event.altKey,
					dimensions = [_this.width(), _this.height()],
					crop = _this.data('crop'),
					position = _this.offset(),
					upload = $('<upload><div class="mailster-upload-info"><div class="mailster-upload-info-bar"></div><div class="mailster-upload-info-text"></div></div></upload>'),
					preview = upload.find('.mailster-upload-info-bar'),
					previewtext = upload.find('.mailster-upload-info-text'),
					preloader = new mOxie.Image(file);

				preloader.onerror = function (e) {

					alert(mailster.l10n.campaigns.unsupported_format);

				}
				preloader.onload = function (e) {

					upload.insertAfter(_this);
					_this.appendTo(upload);

					file._element = _this;
					file._altKey = altkey;
					file._crop = crop;
					file._upload = upload;
					file._preview = preview;
					file._previewtext = previewtext;
					file._dimensions = [preloader.width, preloader.height, preloader.width / preloader.height];

					preloader.downsize(dimensions[0], dimensions[1]);
					preview.css({
						'background-image': 'url(' + preloader.getAsDataURL() + ')',
						'background-size': dimensions[0] + 'px ' + (crop ? dimensions[1] : dimensions[0] / file._dimensions[2]) + 'px'
					});

					uploader.addFile(file);
				};

				preloader.load(file);

			};
			dropzone.ondragenter = function (e) {
				if (mailster.modules.dragging) return;
				_this.addClass('ui-drag-over-file');
				if (window.event && event.altKey) _this.addClass('ui-drag-over-file-alt');
			};
			dropzone.ondragleave = function (e) {
				if (mailster.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};
			dropzone.onerror = function (e) {
				if (mailster.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};

			dropzone.init();

			_this.data('has-dropzone', true);

		});


		if (!uploader) {

			$('<button id="mailster-editorimage-upload-button" />').hide().appendTo('mailster');
			uploader = new plupload.Uploader(mailsterdata.plupload);

			uploader.bind('Init', function (up) {
				$('.moxie-shim').remove();
			});

			uploader.bind('FilesAdded', function (up, files) {

				var source = files[0].getSource();

				mailster.util.getRealDimensions(source._element, function (width, height, factor) {

					up.settings.multipart_params.width = width;
					up.settings.multipart_params.height = height;
					up.settings.multipart_params.factor = factor;
					up.settings.multipart_params.crop = source._crop;
					up.settings.multipart_params.altKey = source._altKey;
					up.refresh();
					up.start();
				});

			});

			uploader.bind('BeforeUpload', function (up, file) {});

			uploader.bind('UploadFile', function (up, file) {});

			uploader.bind('UploadProgress', function (up, file) {

				var source = file.getSource();

				source._preview.width(file.percent + '%');
				source._previewtext.html(file.percent + '%');

			});

			uploader.bind('Error', function (up, err) {
				var source = err.file.getSource();

				alert(err.message);

				source._element.insertAfter(source._upload);
				source._upload.remove();
			});

			uploader.bind('FileUploaded', function (up, file, response) {

				var source = file.getSource(),
					delay, height;

				try {
					response = JSON.parse(response.response);

					source._previewtext.html(mailster.l10n.campaigns.ready);
					source._element.on('load', function () {
						clearTimeout(delay);
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							mailster.trigger('refresh');
						});
					});

					height = Math.round(source._element.width() / response.image.asp);

					source._element.attr({
						'src': response.image.url,
						'alt': response.name,
						'height': height,
						'data-id': response.image.id || 0
					}).data('id', response.image.id || 0);

					source._preview.height(height);

					delay = setTimeout(function () {
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							mailster.trigger('refresh');
						});
					}, 3000);
				} catch (err) {
					source._preview.addClass('error').find('.mailster-upload-info-text').html(mailster.l10n.campaigns.error);
					alert(mailster.l10n.campaigns.error_occurs + "\n" + err.message);
					source._preview.fadeOut(function () {
						source._element.insertAfter(source._upload);
						source._upload.remove();
					});
				}

			});

			uploader.bind('UploadComplete', function (up, files) {});

			uploader.init();

		}
	}

	function inlineEditor() {
		tinymce.init($.extend(mailsterdata.tinymce.args, mailsterdata.tinymce.multi, {
			paste_preprocess: paste_preprocess,
			urlconverter_callback: urlconverter,
			setup: setup
		}));
		tinymce.init($.extend(mailsterdata.tinymce.args, mailsterdata.tinymce.single, {
			paste_preprocess: paste_preprocess,
			urlconverter_callback: urlconverter,
			setup: setup
		}));
	}

	function paste_preprocess(pl, o) {

		var str = o.content,
			allowed_tags = '<a><br><i><em><u><p><h1><h2><h3><h4><h5><h6><ul><ol><li>',
			key = '',
			allowed = false,
			matches = [],
			allowed_array = [],
			allowed_tag = '',
			i = 0,
			k = '',
			html = '',
			replacer = function (search, replace, str) {
				return str.split(search).join(replace);
			};
		if (allowed_tags) {
			allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
		}
		str += '';

		matches = str.match(/(<\/?[\S][^>]*>)/gi);
		for (key in matches) {
			if (isNaN(key)) {
				// IE7
				continue;
			}

			html = matches[key].toString();
			allowed = false;

			for (k in allowed_array) { // Init
				allowed_tag = allowed_array[k];
				i = -1;

				if (i != 0) {
					i = html.toLowerCase().indexOf('<' + allowed_tag + '>');
				}
				if (i != 0) {
					i = html.toLowerCase().indexOf('<' + allowed_tag + ' ');
				}
				if (i != 0) {
					i = html.toLowerCase().indexOf('</' + allowed_tag);
				}

				// Determine
				if (i == 0) {
					allowed = true;
					break;
				}
			}
			if (!allowed) {
				str = replacer(html, "", str);
			}
		}

		o.content = str;
		return str;
	}

	function setup(editor) {

		editor.addButton('mailster_mce_button', {
			title: mailster_mce_button.l10n.tags.title,
			type: 'menubutton',
			icon: 'icon mailster-tags-icon',
			menu: $.map(mailster_mce_button.tags, function (group, id) {
				return {
					text: group.name,
					menu: $.map(group.tags, function (name, tag) {
						return {
							text: name,
							onclick: function () {
								var poststuff = '',
									selection;
								switch (tag) {
								case 'webversion':
								case 'unsub':
								case 'forward':
								case 'profile':
									poststuff = 'link';
								case 'homepage':
									if (selection = editor.selection.getContent({
											format: "text"
										})) {
										editor.insertContent('<a href="{' + tag + poststuff + '}">' + selection + '</a>');
										break;
									}
								default:
									editor.insertContent('{' + tag + '} ');
								}
							}
						};

					})
				};
			})
		});

		editor.addButton('mailster_remove_element', {
			title: mailster_mce_button.l10n.remove.title,
			icon: 'icon mailster-remove-icon',
			onclick: function () {
				editor.targetElm.remove();
				editor.remove();
				mailster.trigger('save');
			}
		});

		editor
			.on('change', function (event) {
				var _self = this;
				clearTimeout(changetimeout);
				changetimeout = setTimeout(function () {
					var content = event.level.content,
						c = content.match(/rgb\((\d+), ?(\d+), ?(\d+)\)/g);
					if (c) {
						for (var i = c.length - 1; i >= 0; i--) {
							content = content.replace(c[i], mailster.util.rgb2hex(c[i]));
						}
						_self.bodyElement.innerHTML = content;
					}
					mailster.trigger('save');
					change = true;
				}, 100)
			})
			.on('keyup', function (event) {
				$(event.currentTarget).prop('spellcheck', true);
			})
			.on('click', function (event) {
				var module = $(event.currentTarget).closest('module');
				if (mailster.modules.selected[0] != module[0]) {
					mailster.trigger('selectModule', module);
				}
				event.stopPropagation();
				editor.focus();
			})
			.on('focus', function (event) {
				event.stopPropagation();
				editor.selection.select(editor.getBody(), true);
				if (mailster.editor.$.container.data('uiSortable')) mailster.editor.$.container.sortable('destroy');
			})
			.on('blur', function (event) {
				mailster.trigger('refresh');
			});
	}

	function urlconverter(url, node, on_save, name) {
		if ('_wp_link_placeholder' == url) {
			return url;
		} else if (/^https?:\/\/{.+}/g.test(url)) {
			return url.replace(/^https?:\/\//, '');
		} else if (/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(url)) {
			return 'mailto:' + url;
		}
		return this.documentBaseURI.toAbsolute(url, mailsterdata.tinymce.remove_script_host);
	}

	mailster.editor.updateElements = updateElements;
	mailster.editor.moduleButtons = moduleButtons;

	//mailster.events.push('refresh', refresh);
	mailster.events.push('editorLoaded', updateElements, moduleButtons);
	mailster.events.push('redraw', updateElements, moduleButtons);

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Modules


parent.window.mailster = mailster;