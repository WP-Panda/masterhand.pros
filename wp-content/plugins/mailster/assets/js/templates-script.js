mailster = (function (mailster, $, window, document) {

	"use strict";

	var iframe = $('#mailster_iframe'),
		base = iframe.data('base'),
		templateeditor = $('#templateeditor'),
		templatecontent = $('textarea.editor'),
		uploadinfo = $('.uploadinfo'),
		codemirror;

	$('.upload-template').on('click', function () {
		$('.upload-field').show();
	})

	$('#mailster_templates')
		.on('click', '.screenshot a', function (event) {
			event.stopPropagation();
		})
		.on('click', '.edit', function () {
			var $this = $(this),
				$container = $this.closest('.mailster-box'),
				$templates = $('.mailster-box'),
				href = $this.attr('href'),
				slug = $this.data('slug');

			if ($this.parent().hasClass('disabled')) return false;

			$templates.removeClass('edit');

			$container.addClass('edit');

			var id = $container.data('id');
			var count = Math.floor(($('#mailster_templates').outerWidth()) / ($container.width() + 22));
			var pos = Math.floor(id / count) * count + count - 1;

			templateeditor.find('textarea').val('');
			templateeditor.find('h3').html($container.find('h3').html());
			templateeditor.slideDown();

			templateeditor.insertAfter($templates.eq(pos).length ? $templates.eq(pos) : $templates.last());
			mailster.util.scroll(templateeditor.offset().top - 50);

			$container.removeClass('loading');
			get_template_html(href);
			return false;
		})
		.on('change', '.template-file-selector-dd', function () {
			get_template_html($(this).val());
		})
		.on('click', '.remove-file', function () {

			var $this = $(this);

			if (confirm($this.data('confirm'))) {

				$('#templateeditor').addClass('loading');

				mailster.util.ajax('remove_template', {
					file: $this.data('file'),
				}, function (response) {
					$('#templateeditor').removeClass('loading');
					if (response.success) {

						$('.template-file-selector-dd').find(':selected').prev('option').prop('selected', true).trigger('change');

					}

				});

			}

			event.stopPropagation();
			return false;
		})
		.on('click', 'a.cancel', function () {
			templateeditor.slideUp();
			$('.mailster-box').removeClass('edit');
			return false;
		})
		.on('click', 'button.save, button.saveas', function () {
			var $this = $(this),
				loader = $('.template-ajax-loading'),
				content = codemirror.getValue(),
				message = $('span.message'),
				name;

			if ($this.is('.saveas') && !(name = prompt(mailster.l10n.templates.enter_template_name + ':', ''))) return false;

			loader.css({
				'display': 'inline'
			});
			$this.prop('disabled', true);

			mailster.util.ajax('set_template_html', {
				content: content,
				name: name,
				slug: $('#slug').val(),
				file: $('#file').val()
			}, function (response) {
				loader.hide();
				$this.prop('disabled', false);

				if (response.success) {
					message.fadeIn(10).html(response.msg).delay(2000).fadeOut();
					if (response.newfile) {
						get_template_html('mailster/' + response.newfile);
					}
				} else {
					alert(response.msg);
				}

			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
				$this.prop('disabled', false);

				alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');
			});

			return false;
		})
		.on('click', '.thickbox-preview', function () {

			var _this = $(this),
				name = _this.parent().attr('title'),
				slug = _this.data('slug'),
				href = _this.attr('href');

			$('#thickboxbox').find('iframe').attr('src', href);
			$('.thickbox-filelist').empty().hide();
			tb_show(name, '?&width=900&inlineId=thickboxbox&TB_inline', null);
			$('#TB_window').width(936).height(700).css('margin-left', -936 / 2).css('margin-top', -700 / 2);
			mailster.util.ajax('get_file_list', {
				slug: slug
			}, function (response) {
				if (response.success) {
					var html = '';
					$.each(response.files, function (file, data) {
						html += '<li>';
						if ('index.html' != file && 'notification.html' != file) {
							html += '<a href="" class="thickbox-remove-file mailster-icon" data-slug="' + response.slug + '" data-file="' + file + '"></a>';
						}
						html += '<a href="' + response.base + '/' + file + '" class="thickbox-file" style="background-image:url(' + data.screenshot + ')"></a><span>' + data.label + ' (' + file + ')</span></li>';
					});

					$('.thickbox-filelist').html(html).slideDown();

				} else {
					alert(response.msg);
				}

			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
				$this.prop('disabled', false);

				alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');
			});
			return false;
		})
		.on('click', 'a.activate', function () {

			var _this = $(this),
				license = '',
				oldlicense = _this.data('license') || '',
				slug = _this.data('slug');

			$('#template-' + slug).addClass('add-license').find('.license').val(oldlicense).focus().select();

			return false;
		})
		.on('click', 'a.download', function () {

			$(this).prop('disabled', true);
		})
		.on('click', 'a.update', function () {

			if (confirm(mailster.l10n.templates.update_note)) {
				$(this).closest('.mailster-box').addClass('loading');
				return true;
			}
			return false;
		})
		.on('click', 'a.deletion', function () {

			if (confirm(mailster.util.sprintf(mailster.l10n.templates.confirm_delete, '"' + $(this).data('name') + '"'))) {
				$(this).closest('.mailster-box').addClass('loading');
				return true;
			}
			return false;
		})
		.on('click', 'a.download, a.activatelink, .save-license', function () {

			$(this).closest('.mailster-box').addClass('loading');
		});

	mailster.$.document
		.on('click', 'a.thickbox-file', function () {

			$('.thickbox-iframe').attr('src', $(this).attr('href'));

			return false;
		})
		.on('click', '.thickbox-remove-file', function () {

			var $this = $(this),
				$el = $this.parent(),
				slug = $this.data('slug'),
				file = $this.data('file');

			if (confirm(mailster.util.sprintf(mailster.l10n.templates.delete_template_file, file, slug))) {

				$el.addClass('loading');

				mailster.util.ajax('remove_template', {
					file: slug + '/' + file,
				}, function (response) {
					if (response.success) {
						$el.slideUp();
					} else {
						$el.removeClass('loading');
					}
				});

			}

			event.stopPropagation();
			return false;
		});

	function uploader_init() {

		var uploader = new plupload.Uploader(wpUploaderInit);

		uploader.bind('Init', function (up) {
			var uploaddiv = $('#plupload-upload-ui');

			if (up.features.dragdrop && !$(document.body).hasClass('mobile')) {
				uploaddiv.addClass('drag-drop');
				$('#drag-drop-area').bind('dragover.wp-uploader', function () { // dragenter doesn't fire right :(
					uploaddiv.addClass('drag-over');
				}).bind('dragleave.wp-uploader, drop.wp-uploader', function () {
					uploaddiv.removeClass('drag-over');
				});
			} else {
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}

			if (up.runtime == 'html4')
				$('.upload-flash-bypass').hide();

		});

		uploader.init();

		uploader.bind('FilesAdded', function (up, files) {

			setTimeout(function () {
				up.refresh();
				up.start();
			}, 1);

		});

		uploader.bind('BeforeUpload', function (up, file) {});

		uploader.bind('UploadFile', function (up, file) {});

		uploader.bind('UploadProgress', function (up, file) {
			uploadinfo.html(mailster.util.sprintf(mailster.l10n.templates.uploading, file.percent + '%'));
		});

		uploader.bind('Error', function (up, err) {
			uploadinfo.html(err.message);
			up.refresh();
		});

		uploader.bind('FileUploaded', function (up, file, response) {
			response = JSON.parse(response.response);
			if (response.success) {
				location.reload();
			} else {
				uploadinfo.html(response.error);
			}
		});

		uploader.bind('UploadComplete', function (up, files) {});
	}

	function get_template_html(href) {
		var loader = $('.template-ajax-loading').css({
			'display': 'inline'
		});
		$('#templateeditor').addClass('loading');
		mailster.util.ajax('get_template_html', {
			href: href
		}, function (response) {
			loader.hide();
			$('#templateeditor').removeClass('loading');

			$('#file').val(response.file);
			$('#slug').val(response.slug);
			templatecontent.val(response.html);

			if (!codemirror) {
				var mixedMode = {
					name: "htmlmixed",
					scriptTypes: [{
						matches: /\/x-handlebars-template|\/x-mustache/i,
						mode: null
					}, {
						matches: /(text|application)\/(x-)?vb(a|script)/i,
						mode: "vbscript"
					}]
				};
				codemirror = mailster.util.CodeMirror.fromTextArea(templatecontent.get(0), {
					mode: mixedMode,
					tabMode: "indent",
					lineNumbers: true,
					autofocus: true
				});
			} else {
				codemirror.setValue(response.html);
			}
			var html = '<select class="template-file-selector-dd">',
				length = Object.keys(response.files).length,
				selected;

			$.each(response.files, function (version, files) {

				if (length > 1) html += '<optgroup label="' + version + '">';

				$.each(files, function (name, data) {

					if (name == response.file)
						selected = data;
					html += ' <option value="' + response.slug + '/' + name + '" ' + (name == response.file ? 'selected' : '') + ' data-slug="' + response.slug + '">' + data.label + ' (' + name + ')</option>';
				});

				if (length > 1) html += '</optgroup>';
			});
			html += '</select>';

			if (response.file != 'index.html' && response.file != 'notification.html') html += '<a class="remove-file mailster-icon" data-file="' + response.slug + '/' + response.file + '" data-confirm="' + mailster.util.sprintf(mailster.l10n.templates.delete_template_file, selected.label, selected.name) + '"></a>';

			templateeditor.find('.template-file-selector span').html(html);
		});
	}

	typeof wpUploaderInit == 'object' && mailster.events.push('documentReady', uploader_init);

	return mailster;

}(mailster || {}, jQuery, window, document));