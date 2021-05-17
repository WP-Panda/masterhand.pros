// block Editbar
mailster = (function (mailster, $, window, document) {

	"use strict";

	var e = {},
		bar = mailster.$.editbar,
		base, contentheights = {
			'img': 0,
			'single': 80,
			'multi': 0,
			'btn': 0,
			'auto': 0,
			'codeview': 0
		},
		imagepreview = bar.find('.imagepreview'),
		imagewidth = bar.find('.imagewidth'),
		imageheight = bar.find('.imageheight'),
		imagecrop = bar.find('.imagecrop'),
		factor = bar.find('.factor'),
		highdpi = bar.find('.highdpi'),
		original = bar.find('.original'),
		imagelink = bar.find('.imagelink'),
		imageurl = bar.find('.imageurl'),
		orgimageurl = bar.find('.orgimageurl'),
		imagealt = bar.find('.imagealt'),
		singlelink = bar.find('.singlelink'),
		buttonlink = bar.find('.buttonlink'),
		buttonlabel = bar.find('.buttonlabel'),
		buttonalt = bar.find('.buttonalt'),
		buttonnav = bar.find('.button-nav'),
		buttontabs = bar.find('ul.buttons'),
		codemirror, codemirrorargs = {
			mode: {
				name: "htmlmixed",
				scriptTypes: [{
					matches: /\/x-handlebars-template|\/x-mustache/i,
					mode: null
				}, {
					matches: /(text|application)\/(x-)?vb(a|script)/i,
					mode: "vbscript"
				}]
			},
			tabMode: "indent",
			lineNumbers: true,
			viewportMargin: Infinity,
			autofocus: true
		},
		buttontype, current, currentimage, currenttext, currenttag, assetstype, assetslist, itemcount, checkForPostsTimeout, lastpostsargs, searchTimeout, checkRSSfeedInterval,
		searchstring = '',
		postsearch = $('#post-search'),
		imagesearch = $('#image-search'),
		imagesearchtype = $('[name="image-search-type"]');

	bar
		.on('keyup change', 'input.live', change)
		.on('keyup change', '#mailster-editor', change)
		.on('click', '.replace-image', replaceImage)
		.on('change', '.highdpi', toggleHighDPI)
		.on('click', 'button.save', save)
		.on('click', '.cancel', cancel)
		.on('click', 'a.remove', remove)
		.on('click', 'a.reload', loadPosts)
		.on('click', 'a.single-link-content', loadSingleLink)
		.on('click', 'a.add_image', openMedia)
		.on('click', 'a.add_image_url', openURL)
		.on('click', '.imagelist li', choosePic)
		.on('dblclick', '.imagelist li', save)
		.on('change', '#post_type_select input', loadPosts)
		.on('click', '.postlist li', choosePost)
		.on('click', '.load-more-posts', loadMorePosts)
		.on('click', 'a.btnsrc', changeBtn)
		.on('click', '.imagepreview', toggleImgZoom)
		.on('click', 'a.nav-tab', openTab)
		.on('change', 'select.check-for-posts', checkForPosts)
		.on('change paste', '#dynamic_rss_url', checkForPosts)
		.on('keyup change', '#post-search', searchPost)
		.on('keyup change', '#image-search', searchPost)
		.on('change', '[name="image-search-type"]', searchPost)
		.on('mouseenter', '#wp-mailster-editor-wrap, .imagelist, .postlist, .CodeMirror', disabledrag)
		.on('mouseleave', '#wp-mailster-editor-wrap, .imagelist, .postlist, .CodeMirror', enabledrag)
		.on('click', '.current-tag a', false)
		.on('keypress.mailster', function (event) {
			if (event.keyCode == 13 && event.target.nodeName.toLowerCase() != 'textarea') return false;
		})
		.on('keyup.mailster', function (event) {
			switch (event.keyCode) {
			case 27:
				cancel();
				return false;
				break;
			case 13:
				if (current.type != 'multi' && current.type != 'codeview') {
					save();
					return false;
				}
				break;
			}
		})

	mailster.util.getRealDimensions(mailster.$.iframe.contents().find("img").eq(0), function (w, h, f) {
		var ishighdpi = f >= 1.5;
		factor.val(f);
		highdpi.prop('checked', ishighdpi);
		(ishighdpi) ? bar.addClass('high-dpi'): bar.removeClass('high-dpi');
	});

	buttonnav.on('click', 'a', function () {
		$(this).parent().find('a').removeClass('nav-tab-active');
		$(this).parent().parent().find('ul.buttons').hide();
		var hash = $(this).addClass('nav-tab-active').attr('href');
		$('#tab-' + hash.substr(1)).show();
		return false;
	});

	imageurl.on('paste change', function (e) {
		var $this = $(this);
		setTimeout(function () {
			var url = dynamicImage($this.val()),
				img = new Image();
			if (url) {
				loader();
				img.onload = function () {
					imagepreview.attr('src', url);
					imageheight.val(Math.round(img.width / (img.width / img.height)));
					currentimage = {
						width: img.width,
						height: img.height,
						asp: img.width / img.height
					};
					loader(false);
				};
				img.onerror = function () {
					if (e.type != 'paste') alert(mailster.util.sprintf(mailster.l10n.campaigns.invalid_image, '"' + url + '"'));
				};
				img.src = url;
			}
		}, 1);
	});

	imagewidth.on('keyup change', function () {
		if (!imagecrop.is(':checked')) imageheight.val(Math.round(imagewidth.val() / currentimage.asp));
		adjustImagePreview();
	});
	imageheight.on('keyup change', function () {
		if (!imagecrop.is(':checked')) imagewidth.val(Math.round(imageheight.val() * currentimage.asp));
		adjustImagePreview();
	});
	imagecrop.on('change', function () {
		if (!imagecrop.is(':checked')) {
			imageheight.val(Math.round(imagewidth.val() / currentimage.asp));
			imagecrop.parent().removeClass('not-cropped');
		} else {
			imagecrop.parent().addClass('not-cropped');
		}
		adjustImagePreview();
	});

	$('#dynamic_embed_options_post_type').on('change', function () {

		var cats = $('#dynamic_embed_options_cats'),
			val = $(this).val();
		cats.find('select').prop('disabled', true);
		bar.find('.dynamic-rss')[val == 'rss' ? 'show' : 'hide']();
		loader();
		mailster.util.ajax('get_post_term_dropdown', {
			posttype: val
		}, function (response) {
			loader(false);
			if (response.success) {
				cats.html(response.html);
				if (currenttag && currenttag.terms) {
					var taxonomies = cats.find('.dynamic_embed_options_taxonomy_wrap');
					$.each(currenttag.terms, function (i, term) {
						if (!term) return;
						var term_ids = term.split(',');
						$.each(term_ids, function (j, id) {
							var select = taxonomies.eq(i).find('select').eq(j),
								last;
							if (!select.length) {
								last = taxonomies.eq(i).find('select').last();
								select = last.clone();
								select.insertAfter(last);
								$('<span> ' + mailster.l10n.campaigns.or + ' </span>').insertBefore(select);
							}
							select.val(id);
						});
					});
				}



			}
			checkForPosts();
		}, function (jqXHR, textStatus, errorThrown) {
			loader(false);
		});

	});

	mailster.events.documentReady.push(function () {
		bar.draggable && bar.draggable({
			'distance': 20,
			'axis': 'y'
		});
		if (mailster.util.isTinyMCE && tinymce.get('mailster-editor')) {
			if (tinymce.majorVersion >= 4) {

				tinymce.get('mailster-editor').on('keyup', function () {
					mceUpdater(this);
				});
				tinymce.get('mailster-editor').on('ExecCommand', function () {
					mceUpdater(this);
				});

			} else {
				tinymce.get('mailster-editor').onKeyUp.add(function () {
					mceUpdater(this);
				});
				tinymce.get('mailster-editor').onExecCommand.add(function () {
					mceUpdater(this);
				});
			}
		}
	});


	function disabledrag() {
		draggable(false);
	}

	function enabledrag() {
		draggable(true);
	}

	function draggable(bool) {
		if (bar.draggable) {
			if (bool !== false) {
				bar.draggable('enable');
			} else {
				bar.draggable('disable');
			}
		}
	}

	function openTab(id, trigger) {
		var $this;
		if (typeof id == 'string') {
			$this = base.find('a[href="' + id + '"]');
		} else {
			$this = $(this);
			id = $this.attr('href');
		}

		$this.parent().find('a.nav-tab').removeClass('nav-tab-active');
		$this.addClass('nav-tab-active');
		base.find('.tab').hide();
		base.find(id).show();

		if (id == '#dynamic_embed_options' && trigger !== false) $('#dynamic_embed_options_post_type').trigger('change');
		if (id == '#image_button') buttontype = 'image';
		if (id == '#text_button') buttontype = 'text';

		assetslist = base.find(id).find('.postlist').eq(0);
		return false;
	}


	function replaceImage() {
		loader();
		var f = factor.val(),
			w = current.element.width(),
			h = Math.round(w / 1.6),
			img = $('<img>', {
				'src': 'https://dummy.mailster.co/' + (w * f) + 'x' + (h * f) + '.jpg',
				'alt': current.content,
				'label': current.content,
				'width': w,
				'height': h,
				'border': 0,
				'editable': current.content
			});

		img[0].onload = function () {
			img.attr({
				'width': w,
				'height': h,
			}).removeAttr('style');
			close();
		};
		if (current.element.parent().is('a')) current.element.unwrap();
		if (!current.element.parent().is('td')) current.element.unwrap();
		current.element.replaceWith(img);
		return false;
	}


	function toggleHighDPI() {

		if ($(this).is(':checked')) {
			factor.val(2);
			bar.addClass('high-dpi');
		} else {
			factor.val(1);
			bar.removeClass('high-dpi');
		}
	}

	function checkForPosts() {
		clearInterval(checkForPostsTimeout);
		loader();
		checkForPostsTimeout = setTimeout(function () {

			var post_type = bar.find('#dynamic_embed_options_post_type').val(),
				content = bar.find('#dynamic_embed_options_content').val(),
				relative = bar.find('#dynamic_embed_options_relative').val(),
				taxonomies = bar.find('.dynamic_embed_options_taxonomy_wrap'),
				rss_url = $('#dynamic_rss_url').val(),
				postargs = {},
				extra = [];

			$.each(taxonomies, function (i) {
				var selects = $(this).find('select'),
					values = [];
				$.each(selects, function () {
					var val = parseInt($(this).val(), 10);
					if (val != -1 && $.inArray(val, values) == -1 && !isNaN(val)) values.push(val);
				});
				values = values.join(',');
				if (values) extra[i] = values;
			});
			postargs = {
				id: mailster.campaign_id,
				post_type: post_type,
				relative: relative,
				extra: extra,
				content: content,
				modulename: current.name,
				expect: current.elements.expects,
				rss_url: rss_url
			};

			if (JSON.stringify(postargs) === JSON.stringify(lastpostsargs)) {
				loader(false);
				return;
			}

			$('#dynamic_embed_options').find('h4.current-match').html('&hellip;');
			$('#dynamic_embed_options').find('div.current-tag').html('&hellip;');

			if ('rss' == post_type && !rss_url) {
				loader(false);
				return;
			}

			lastpostsargs = postargs;

			mailster.util.ajax('check_for_posts', postargs, function (response) {
				loader(false);
				if (response.success) {
					currenttext = response.pattern;
					$('#dynamic_embed_options').find('h4.current-match').html(response.title);
					$('#dynamic_embed_options').find('div.current-tag').text(response.pattern.title + "\n\n" + response.pattern[content]);
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);

			});

		}, 500);

	}

	function dynamicImage(val, w, h, c, o) {
		w = w || imagewidth.val();
		h = h || imageheight.val() || Math.round(w / 1.6);
		c = typeof c == 'undefined' ? imagecrop.prop(':checked') : c;
		o = typeof o == 'undefined' ? original.prop(':checked') : o;
		if (/^\{([a-z0-9-_,;:|~]+)\}$/.test(val)) {
			var f = factor.val();
			val = mailster.ajaxurl + '?action=mailster_image_placeholder&tag=' + val.replace('{', '').replace('}', '') + '&w=' + Math.abs(w) + '&h=' + Math.abs(h) + '&c=' + (c ? 1 : 0) + '&o=' + (o ? 1 : 0) + '&f=' + f;
		}
		return val;
	}

	function isDynamicImage(val) {
		if (-1 !== val.indexOf('?action=mailster_image_placeholder&tag=')) {
			var m = val.match(/&tag=([a-z0-9-_,;:|~]+)&/);
			return '{' + m[1] + '}';
		}
		return false;
	}

	function change(e) {
		if ((e.keyCode || e.which) != 27 && current)
			current.element.html($(this).val());
	}

	function loader(bool) {
		if (bool === false) {
			$('#editbar-ajax-loading').hide();
			bar.find('.buttons').find('button').prop('disabled', false);
		} else {
			$('#editbar-ajax-loading').css('display', 'inline');
			bar.find('.buttons').find('button').prop('disabled', true);
		}
	}

	function save() {

		if (current.type == 'img') {

			var is_img = current.element.is('img');

			if (imageurl.val()) {

				currentimage = {
					id: null,
					name: '',
					src: dynamicImage(imageurl.val()),
					width: currentimage.width,
					height: currentimage.height,
					asp: currentimage.width / currentimage.height
				};

			}

			if (currentimage) {

				loader();

				var f = factor.val() || 1,
					w = imagewidth.val(),
					h = imageheight.val(),
					c = imagecrop.is(':checked'),
					o = original.is(':checked'),
					attribute = is_img ? 'src' : 'background',
					style;

				current.element.attr({
					'data-id': currentimage.id,
				}).data('id', currentimage.id).addClass('mailster-loading');

				if (is_img) {
					current.element.attr({
						'src': currentimage.src,
						'alt': currentimage.name
					});
					if (!current.is_percentage) {
						current.element.attr('width', Math.round(w))
					}
					if (current.element.attr('height') && current.element.attr('height') != 'auto') {
						current.element.attr('height', Math.round(h))
					}
					if (c) {
						current.element.attr({
							'data-crop': true,
						}).data('crop', true);
					}
					if (o) {
						current.element.attr({
							'data-original': true,
						}).data('original', true);
					}
				}

				if (currentimage.src) {
					current.element.attr(attribute, currentimage.src);
					if (style = current.element.attr('style')) {
						current.element.attr('style', style.replace(/url\("?(.+)"?\)/, "url(\'" + currentimage.src + "\')"));
					}
				}

				mailster.util.ajax('create_image', {
					id: currentimage.id,
					original: o,
					src: currentimage.src,
					width: w * f,
					height: h * f,
					crop: c
				}, function (response) {

					loader(false);

					if (response.success) {
						imagepreview.attr('src', response.image.url);

						response.image.width = (response.image.width || currentimage.width) / f;
						response.image.height = response.image.width / (currentimage.asp);
						response.image.asp = currentimage.asp;

						currentimage = response.image;
						currentimage.name = imagealt.val();

						if (is_img) {
							current.element.one('load error', function (event) {
								if ('error' == event.type) {
									alert(mailster.util.sprintf(mailster.l10n.campaigns.invalid_image, response.image.url));
								}
								current.element.removeClass('mailster-loading');
								mailster.trigger('save');
							});

							current.element.attr({
								'alt': currentimage.name
							})
							if (!current.is_percentage) {
								current.element.attr('width', Math.round(imagewidth.val()))
							}
							if (current.element.attr('height') && current.element.attr('height') != 'auto') {
								current.element.attr('height', Math.round(imageheight.val()))
							}
							if (c) {
								current.element.attr({
									'data-crop': true,
								}).data('crop', true);
							}
							if (o) {
								current.element.attr({
									'data-original': true,
								}).data('original', true);
							}
						} else {

							current.element.removeClass('mailster-loading');
							var html = current.element.html(),
								is_root = html.match(/<modules/),
								reg;


							if (orgimageurl.val()) {
								if (is_root) {
									if (is_root = html.match(new RegExp('<v:background(.*)<\/v:background>', 's'))) {
										current.element.html(mailster.util.replace(html, is_root[0], mailster.util.replace(is_root[0], orgimageurl.val(), currentimage.url)));
									}
								} else {
									current.element.html(mailster.util.replace(html, orgimageurl.val(), currentimage.url));
									//remove id to re trigger tinymce
									current.element.find('single, multi').removeAttr('id');
								}
							}

						}
						current.element.removeAttr(attribute).attr(attribute, currentimage.url);

					} else {
						current.element.removeClass('mailster-loading');
					}
					imagealt.val('');

					close();

				}, function (jqXHR, textStatus, errorThrown) {

					loader(false);

				});

			} else {
				current.element.attr({
					'alt': imagealt.val()
				});

				close();
			}

			if (current.element.parent().is('a')) current.element.unwrap();
			var link = imagelink.val();
			if (link) current.element.wrap('<a href="' + link + '"></a>');

			return false;

		} else if (current.type == 'btn') {

			var link = buttonlink.val();
			if (!link && !confirm(mailster.l10n.campaigns.remove_btn)) return false;

			var btnsrc = base.find('a.active').find('img').attr('src');
			if (typeof btnsrc == 'undefined') {
				buttontype = 'text';
				if (!buttonlabel.val()) buttonlabel.val(mailster.l10n.campaigns.read_more);
			}

			current.element.removeAttr('tmpbutton');

			if ('image' == buttontype) {
				var f = factor.val();
				var img = new Image();
				img.onload = function () {

					if (!current.element.find('img').length) {
						var wrap = current.element.closest('.textbutton');
						var element = $('<a href="" editable label="' + current.name + '"><img></a>');
						(wrap.length) ? wrap.replaceWith(element): current.element.replaceWith(element);
						current.element = element;
					}
					current.element.find('img').attr({
						'src': btnsrc,
						'width': Math.round((img.width || current.element.width()) / f),
						'height': Math.round((img.height || current.element.height()) / f),
						'alt': buttonalt.val(),
					});

					(link) ? current.element.attr('href', link): current.element.remove();
					close();
				}
				img.src = btnsrc;

			} else {

				var wrap = current.element.closest('.textbutton'),
					label = buttonlabel.val();

				if (!wrap.length) {
					current.element.replaceWith('<table class="textbutton" align="left" role="presentation"><tr><td align="center" width="auto"><a href="' + link + '" editable label="' + label + '">' + label + '</a></td></tr></table>')
				} else {
					if (current.element[0] == wrap[0]) {
						current.element = wrap.find('a');
					}
					current.element.text(label);
				}

				if (link) {
					current.element.attr('href', link);
				} else {
					current.element.remove();
					wrap.remove();
				}
				close();

			}

			return false;

		} else if (current.type == 'auto') {

			var insertmethod = $('#embedoption-bar').find('.nav-tab-active').data('type'),
				position = current.element.data('position') || 0,
				contenttype, images = [],
				post_type, rss_url;

			current.element.removeAttr('data-tag data-rss').removeData('tag').removeData('data-rss');

			if ('dynamic' == insertmethod) {

				contenttype = bar.find('#dynamic_embed_options_content').val();
				post_type = bar.find('#dynamic_embed_options_post_type').val();
				rss_url = $('#dynamic_rss_url').val();

				currenttext.content = currenttext[contenttype];

				current.element.attr('data-tag', currenttext.tag).data('tag', currenttext.tag);

				if ('rss' == post_type) {
					current.element.attr('data-rss', rss_url).data('rss', rss_url);
				}

			} else {

				contenttype = $('.embed_options_content:checked').val();
				current.element.removeAttr('data-tag').removeData('tag');

			}

			if (currenttext) {

				if (current.elements.single.length) {

					current.elements.single.each(function (i, e) {
						var _this = $(this),
							expected = _this.attr('expect') || 'title',
							content = currenttext[expected] ? currenttext[expected] : '';

						if (content) _this.html(content);
					});

				}

				if (current.elements.multi.length) {

					current.elements.multi.each(function (i, e) {
						var _this = $(this),
							expected = _this.attr('expect') || contenttype,
							content = currenttext[expected] ? currenttext[expected] : '';

						if (content) _this.html(content);

					});

				}

				if (currenttext.link) {

					if (current.elements.buttons.length) {
						current.elements.buttons.eq(position).attr('href', currenttext.link);
					}

				} else {

					if (current.elements.buttons.parent().length && current.elements.buttons.parent()[0].nodeName == 'TD') {
						current.elements.buttons.eq(position).closest('.textbutton').remove();
					} else if (current.elements.buttons.length) {
						if (current.elements.buttons.eq(position).last().find('img').length) {
							current.elements.buttons.remove();
						}
					}

				}

				if (currenttext.image && current.elements.images.length) {
					if (!Array.isArray(currenttext.image)) {
						images[position] = currenttext.image;
					} else {
						images = currenttext.image;
					}

					currenttext.image = images;

					loader();

					current.elements.images.each(function (i, e) {

						if (!currenttext.image[i]) return;

						var imgelement = $(this);
						var f = factor.val();

						if ('static' == insertmethod) {
							mailster.util.ajax('create_image', {
								id: currenttext.image[i].id,
								original: original.is(':checked'),
								width: imgelement.width() * f,
								height: imgelement.height() * f,
								crop: imgelement.data('crop'),
							}, function (response) {

								if (response.success) {
									loader(false);

									if (original.is(':checked')) {
										imgelement.attr({
											'data-original': true,
										}).data('original', true);
									}
									if ('img' == imgelement.prop('tagName').toLowerCase()) {
										imgelement
											.attr({
												'data-id': currenttext.image[i].id,
												'src': response.image.url,
												'width': Math.round(response.image.width / f),
												'alt': currenttext.alt || currenttext.title[i]
											})
											.data('id', currenttext.image[i].id);

										if (imgelement.attr('height') && imgelement.attr('height') != 'auto') {
											imgelement.attr('height', Math.round(response.image.height / f));
										}

										if (imgelement.parent().is('a')) {
											imgelement.unwrap();
										}

										if (currenttext.link) {
											imgelement.wrap('<a>');
											imgelement.parent().attr('href', currenttext.link);
										}
									} else {
										var orgurl = imgelement.attr('background');
										imgelement
											.attr({
												'data-id': currenttext.image[i].id,
												'background': response.image.url,
											})
											.data('id', currenttext.image[i].id)
											.css('background-image', '');

										current.element.html(mailster.util.replace(current.element.html(), orgurl, response.image.url));

										//remove id to re trigger tinymce
										current.element.find('single, multi').removeAttr('id');
										mailster.trigger('save');
										mailster.trigger('refresh');

									}
								}
							}, function (jqXHR, textStatus, errorThrown) {

								loader(false);

							});

							return false;

							// dynamic
						} else if ('dynamic' == insertmethod) {

							var width = imgelement.width(),
								crop = imgelement.data('crop'),
								org = original.is(':checked'),
								height = crop ? imgelement.height() : null;

							if ('img' == imgelement.prop('tagName').toLowerCase()) {

								imgelement
									.removeAttr('data-id')
									.attr({
										'src': dynamicImage(currenttext.image[i], width, height, crop, org),
										'width': width,
										'alt': currenttext.alt || currenttext.title[i]
									})
									.removeData('id');
								if (imgelement.attr('height')) {
									imgelement.attr('height', height || Math.round(width / 1.6));
								}
							} else {
								var orgurl = imgelement.attr('background');
								imgelement
									.removeAttr('data-id')
									.attr({
										'background': dynamicImage(currenttext.image[i], width)
									})
									.removeData('id')
									.css('background-image', '');
								current.element.html(mailster.util.replace(current.element.html(), orgurl, dynamicImage(currenttext.image[i], width, height, crop, org)));
								//remove id to re trigger tinymce
								current.element.find('single, multi').removeAttr('id');
							}

						}

					});


				}

				position = position + 1 >= current.areas.length ? 0 : position + 1;

				current.element.data('position', position);

				mailster.$.iframe.contents().find("html")
					.find("img").each(function () {
						this.onload = function () {
							mailster.trigger('refresh');
						};
					});

			}

		} else if (current.type == 'multi') {

			if (mailster.util.isTinyMCE && tinymce.get('mailster-editor') && !tinymce.get('mailster-editor').isHidden()) {
				var content = tinymce.get('mailster-editor').getContent();
				content = content.replace('href="http://{', 'href="{'); //from tinymce if tag is used
				current.element.html(content);
			}

		} else if (current.type == 'single') {

			if (current.conditions) {
				current.aa = '<if';
				$.each($('.conditinal-area'), function () {
					current.aa = '';
				});
			}

			if (current.element.parent().is('a')) current.element.unwrap();
			var link = singlelink.val();
			if (link) current.element.wrap('<a href="' + link + '"></a>');

		} else if (current.type == 'codeview') {

			var html = codemirror.getValue();
			current.element.html(html);
			current.modulebuttons.prependTo(current.element);

		}

		close();
		return false;
	}

	function remove() {
		if (current.element.parent().is('a')) current.element.unwrap();
		if ('btn' == current.type) {
			var wrap = current.element.closest('.textbutton'),
				parent = wrap.parent();
			if (!wrap.length) {
				wrap = current.element;
			}
			if (parent.is('buttons') && !parent.find('.textbutton').length) {
				parent.remove();
			} else {
				wrap.remove();
			}
		} else if ('img' == current.type && 'img' != current.tag) {
			current.element.attr('background', '');
		} else {
			current.element.remove();
		}
		close();
		return false;
	}

	function cancel() {
		switch (current.type) {
		case 'img':
		case 'btn':
			if (current.element.is('[tmpbutton]')) {
				current.element.remove();
			}
			break;
		default:
			current.element.html(current.content);
			//remove id to re trigger tinymce
			current.element.find('single, multi').removeAttr('id');
		}
		close();
		return false;
	}

	function changeBtn() {
		var _this = $(this),
			link = _this.data('link');
		base.find('.btnsrc').removeClass('active');
		_this.addClass('active');

		buttonalt.val(_this.attr('title'));

		if (link) {
			var pos;
			buttonlink.val(link);
			if ((pos = (link + '').indexOf('USERNAME', 0)) != -1) {
				buttonlink.focus();
				selectRange(buttonlink[0], pos, pos + 8);
			};

		}
		return false;
	}

	function toggleImgZoom() {
		$(this).toggleClass('zoom');
	}

	function choosePic(event, el) {
		var _this = el || $(this),
			id = _this.data('id'),
			name = _this.data('name'),
			src = _this.data('src');

		if (!id) return;

		currentimage = {
			id: id,
			name: name,
			src: src
		};
		loader();

		base.find('li.selected').removeClass('selected');
		_this.addClass('selected');

		if (current.element.data('id') == id) {
			imagealt.val(current.element.attr('alt'));
		} else if (current.element.attr('alt') != name) {
			imagealt.val(name);
		}
		imageurl.val('');
		imagepreview.attr('src', '').on('load', function () {

			imagepreview.off('load');

			current.width = imagepreview.width();
			current.height = imagepreview.height();
			current.asp = _this.data('asp') || (current.width / current.height);

			currentimage.asp = current.asp;
			loader(false);

			if (!imagecrop.is(':checked')) imageheight.val(Math.round(imagewidth.val() / current.asp));

			adjustImagePreview();

		}).attr('src', src);

		return currentimage;
	}

	function adjustImagePreview() {
		var x = Math.round(.5 * (current.height - (current.width * (imageheight.val() / imagewidth.val())))) || 0,
			f = parseInt(factor.val(), 10);

		imagepreview.css({
			'clip': 'rect(' + (x) + 'px,' + (current.width * f) + 'px,' + (current.height * f - x) + 'px,0px)',
			'margin-top': (-1 * x) + 'px'
		});
	}

	function choosePost() {
		var _this = $(this),
			id = _this.data('id'),
			name = _this.data('name'),
			link = _this.data('link'),
			thumbid = _this.data('thumbid');

		if (current.type == 'btn') {

			buttonlink.val(link);
			buttonalt.val(name);
			base.find('li.selected').removeClass('selected');
			_this.addClass('selected')

		} else if (current.type == 'single') {

			singlelink.val(link);
			base.find('li.selected').removeClass('selected');
			_this.addClass('selected')

		} else {

			loader();
			mailster.util.ajax('get_post', {
				id: id,
				expect: current.elements.expects
			}, function (response) {
				loader(false);
				base.find('li.selected').removeClass('selected');
				_this.addClass('selected')
				if (response.success) {
					currenttext = response.pattern;
					base.find('.editbarinfo').html(mailster.l10n.campaigns.curr_selected + ': <span>' + currenttext.title + '</span>');
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);
				base.find('li.selected').removeClass('selected');

			});

		}
		return false;
	}

	function open(data) {

		current = data;
		var el = data.element,
			module = el.closest('module'),
			top = (type != 'img') ? data.offset.top : 0,
			name = data.name || '',
			type = data.type,
			content = mailster.util.trim(el.html()),
			condition = el.find('if'),
			conditions,
			position = current.element.data('position') || 0,
			carea, cwrap, offset,
			fac = 1;

		base = bar.find('div.type.' + type);

		bar.addClass('current-' + type);

		current.width = el.width();
		current.height = el.height();
		current.asp = current.width / current.height;
		current.crop = el.data('crop') ? el.data('crop') : false;
		current.tag = el.prop('tagName').toLowerCase();
		current.is_percentage = el.attr('width') && el.attr('width').indexOf('%') !== -1;
		current.content = content;

		currenttag = current.element.data('tag');
		searchstring = '';

		mailster.trigger('selectModule', module);

		if (condition.length) {

			conditions = {
				'if': null,
				'elseif': [],
				'else': null,
				'total': 0
			};
			conditions = [];

			bar.addClass('has-conditions');
			carea = base.find('.conditinal-area');
			cwrap = bar.find('.conditions').eq(0);
			cwrap.clone().prependTo(carea);

			$.each(condition.find('elseif'), function () {
				var _t = $(this),
					_c = _t.html();
				conditions.push({
					el: _t,
					html: _c,
					field: _t.attr('field'),
					operator: _t.attr('operator'),
					value: _t.attr('value')

				});
				_t.detach();
				carea.clone().val(_c).insertAfter(carea);
			})
			$.each(condition.find('else'), function () {
				var _t = $(this),
					_c = _t.html();
				conditions.push({
					el: _t,
					html: _c
				});
				_t.detach();
				carea.clone().val(_c).insertAfter(carea);
			})
			conditions.unshift({
				el: condition,
				html: condition.html(),
				field: condition.attr('field'),
				operator: condition.attr('operator'),
				value: condition.attr('value')
			});


		} else {
			bar.removeClass('has-conditions');
		}

		current.conditions = conditions;

		if (type == 'img') {

			original.prop('checked', current.original);
			imagecrop.prop('checked', current.crop).parent()[current.crop ? 'addClass' : 'removeClass']('not-cropped');
			searchstring = mailster.util.trim(imagesearch.val());

			factor.val(1);
			mailster.util.getRealDimensions(el, function (w, h, f) {
				var h = f >= 1.5;
				factor.val(f);
				highdpi.prop('checked', h);

				(h) ? bar.addClass('high-dpi'): bar.removeClass('high-dpi');

				fac = f;
			});


		} else if (type == 'btn') {

			if (el.find('img').length) {

				$('#button-type-bar').find('a').eq(1).trigger('click');
				var btnsrc = el.find('img').attr('src');

				if (buttonnav.length) {

					var button = bar.find("img[src='" + btnsrc + "']");

					if (button.length) {
						bar.find('ul.buttons').hide();
						var b = button.parent().parent().parent();
						bar.find('a[href="#' + b.attr('id').substr(4) + '"]').trigger('click');
					} else {
						$.each(bar.find('.button-nav'), function () {
							$(this).find('.nav-tab').eq(0).trigger('click');
						});
					}

				}

				buttonlabel.val(el.find('img').attr('alt'));
				mailster.util.getRealDimensions(el.find('img'), function (w, h, f) {
					var h = f >= 1.5;
					factor.val(f);
					highdpi.prop('checked', h);
					(h) ? bar.addClass('high-dpi'): bar.removeClass('high-dpi');

					fac = f;
				});

			} else {

				$('#button-type-bar').find('a').eq(0).trigger('click');
				buttonlabel.val(mailster.util.trim(el.text())).focus().select();
				buttonlink.val(current.element.attr('href'));
				bar.find('ul.buttons').hide();
			}

		} else if (type == 'auto') {

			openTab('#' + (currenttag ? 'dynamic' : 'static') + '_embed_options', true);
			searchstring = mailster.util.trim(postsearch.val());

			if (currenttag) {

				var parts = currenttag.substr(1, currenttag.length - 2).split(':'),
					extra = parts[1].split(';'),
					relative = extra.shift(),
					terms = extra.length ? extra : null;

				currenttag = {
					'post_type': parts[0],
					'relative': relative,
					'terms': terms
				};

				$('#dynamic_embed_options_post_type').val(currenttag.post_type).trigger('change');
				$('#dynamic_embed_options_relative').val(currenttag.relative).trigger('change');

			} else {

			}

		} else if (type == 'codeview') {

			var textarea = base.find('textarea'),
				clone = el.clone();

			current.modulebuttons = clone.find('modulebuttons');

			clone.find('modulebuttons').remove();
			clone.find('single, multi')
				.removeAttr('contenteditable spellcheck id dir style class');

			var html = mailster.util.trim(clone.html().replace(/\u200c/g, '&zwnj;').replace(/\u200d/g, '&zwj;'));
			textarea.show().html(html);

		}

		offset = mailster.$.template.offset().top + (current.offset.top - (mailster.$.window.height() / 2) + (current.height / 2));

		offset = Math.max(mailster.$.template.offset().top - 200, offset);

		mailster.util.scroll(offset, function () {

			bar.find('h4.editbar-title').html(name);
			bar.find('div.type').hide();

			bar.find('div.' + type).show();

			if (module.data('rss')) $('#dynamic_rss_url').val(module.data('rss'));

			//center the bar
			var baroffset = mailster.util.top() + (mailster.$.window.height() / 2) - mailster.$.template.offset().top - (bar.height() / 2);

			bar.css({
				top: baroffset
			});

			loader();

			if (type == 'single') {

				if (conditions) {

					$.each(conditions, function (i, condition) {
						var _b = base.find('.conditinal-area').eq(i);
						_b.find('select.condition-fields').val(condition.field);
						_b.find('select.condition-operators').val(condition.operator);
						_b.find('input.condition-value').val(condition.value);
						_b.find('input.input').val(condition.html)
					});

				} else {

					var val = content.replace(/&amp;/g, '&');

					singlelink.val('');

					if (current.element.parent().is('a')) {
						var href = current.element.parent().attr('href');
						singlelink.val(href != '#' ? href : '');
						loadSingleLink();

					} else if (current.element.find('a').length) {
						var link = current.element.find('a');
						if (val == link.text()) {
							var href = link.attr('href');
							val = link.text();
							singlelink.val(href != '#' ? href : '');
						}
					}

					base.find('input').eq(0).val(mailster.util.trim(val));

				}

			} else if (type == 'img') {

				var maxwidth = parseInt(el[0].style.maxWidth, 10) || el.parent().width() || el.width() || null;
				var maxheight = parseInt(el[0].style.maxHeight, 10) || el.parent().height() || el.height() || null;
				var src = el.attr('src') || el.attr('background');
				var url = isDynamicImage(src) || '';

				if (el.parent().is('a')) {
					imagelink.val(el.parent().attr('href').replace('%7B', '{').replace('%7D', '}'));
				} else {
					imagelink.val('');
				}

				imagealt.val(el.attr('alt'));
				imageurl.val(url);
				orgimageurl.val(src);

				el.data('id', el.attr('data-id'));

				$('.imageurl-popup').toggle(!!url);
				imagepreview
					.removeAttr('src')
					.attr('src', src);
				assetstype = 'attachment';
				assetslist = base.find('.imagelist');
				currentimage = {
					id: el.data('id'),
					src: src,
					width: el.width() * fac,
					height: el.height() * fac
				}
				currentimage.asp = currentimage.width / currentimage.height;
				loadPosts();
				adjustImagePreview();

			} else if (type == 'btn') {

				buttonalt.val(el.find('img').attr('alt'));
				if (el.attr('href')) buttonlink.val(el.attr('href').replace('%7B', '{').replace('%7D', '}'));

				assetstype = 'link';
				assetslist = base.find('.postlist').eq(0);
				loadPosts();

				$.each(base.find('.buttons img'), function () {
					var _this = $(this);
					_this.css('background-color', el.css('background-color'));
					(_this.attr('src') == btnsrc) ? _this.parent().addClass('active'): _this.parent().removeClass('active');

				});

			} else if (type == 'auto') {

				assetstype = 'post';
				assetslist = base.find('.postlist').eq(0);
				loadPosts();

				current.areas = current.element.find('[area]');
				if (!current.areas.length) {
					current.areas = current.element;
				}
				current.elements = {
					single: current.areas.eq(position).find('single'),
					multi: current.areas.eq(position).find('multi'),
					buttons: current.areas.eq(position).find('a[editable]'),
					images: current.areas.eq(position).find('img[editable], td[background], th[background]'),
					expects: current.areas.eq(position).find('[expect]').map(function () {
						return $(this).attr('expect');
					}).toArray()
				}

				if (current.areas.length > 1) {
					bar.find('.editbarposition').html(mailster.util.sprintf(mailster.l10n.campaigns.for_area, '#' + (position + 1))).show();
				} else {
					bar.find('.editbarposition').hide();
				}

			} else if (type == 'codeview') {

				if (codemirror) {
					codemirror.clearHistory();
					codemirror.setValue('');
					base.find('.CodeMirror').remove();
				}

			}

			bar.show(0, function () {

				if (type == 'single') {

					bar.find('input').focus().select();

				} else if (type == 'img') {

					imagewidth.val(current.width);
					imageheight.val(current.height);
					imagecrop.prop('checked', current.crop);

				} else if (type == 'btn') {

					imagewidth.val(maxwidth);
					imageheight.val(maxheight);

				} else if (type == 'multi') {

					$('#mailster-editor').val(content);

					if (mailster.util.isTinyMCE && tinymce.get('mailster-editor')) {
						tinymce.get('mailster-editor').setContent(content);
						tinymce.execCommand('mceFocus', false, 'mailster-editor');
					}

				} else if (type == 'codeview') {

					codemirror = mailster.util.CodeMirror.fromTextArea(textarea.get(0), codemirrorargs);

				}


			});

			loader(false);

		}, 100);


	}

	function loadPosts(event, callback) {

		var posttypes = $('#post_type_select').find('input:checked').serialize(),
			data = {
				type: assetstype,
				posttypes: posttypes,
				search: searchstring,
				imagetype: imagesearchtype.filter(':checked').val(),
				offset: 0
			};

		if (assetstype == 'attachment') {
			data.id = currentimage.id;
		}

		assetslist.empty();
		loader();

		mailster.util.ajax('get_post_list', data, function (response) {
			loader(false);
			if (response.success) {
				itemcount = response.itemcount;
				displayPosts(response.html, true);
				callback && callback();
			}
		}, function (jqXHR, textStatus, errorThrown) {

			loader(false);

		});
	}

	function loadMorePosts() {
		var $this = $(this),
			offset = $this.data('offset'),
			type = $this.data('type');

		loader();

		var posttypes = $('#post_type_select').find('input:checked').serialize();

		mailster.util.ajax('get_post_list', {
			type: type,
			posttypes: posttypes,
			search: searchstring,
			imagetype: imagesearchtype.filter(':checked').val(),
			offset: offset,
			itemcount: itemcount
		}, function (response) {
			loader(false);
			if (response.success) {
				itemcount = response.itemcount;
				$this.remove();
				displayPosts(response.html, false);
			}
		}, function (jqXHR, textStatus, errorThrown) {

			loader(false);

		});
		return false;
	}

	function searchPost() {
		var $this = $(this),
			temp = mailster.util.trim('attachment' == assetstype ? imagesearch.val() : postsearch.val());
		if ((!$this.is(':checked') && searchstring == temp)) {
			return false;
		}
		searchstring = temp;
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(function () {
			loadPosts();
		}, 500);
	}

	function loadSingleLink() {
		$('#single-link').slideDown(200);
		singlelink.focus().select();
		assetstype = 'link';
		assetslist = base.find('.postlist').eq(0);
		loadPosts();
		return false;

	}

	function displayPosts(html, replace, list) {
		if (!list) list = assetslist;
		if (replace) list.empty();
		if (!list.html()) list.html('<ul></ul>');

		list.find('ul').append(html);
	}

	function openURL() {
		$('.imageurl-popup').toggle();
		if (!imageurl.val() && currentimage.src.indexOf(location.origin) == -1 && currentimage.src.indexOf('dummy.mailster.co') == -1) {
			imageurl.val(currentimage.src);
		}
		imageurl.focus().select();
		return false;
	}

	function openMedia() {

		if (!wp.media.frames.mailster_editbar) {

			wp.media.frames.mailster_editbar = wp.media({
				title: mailster.l10n.campaigns.select_image,
				library: {
					type: 'image'
				},
				multiple: false
			});

			wp.media.frames.mailster_editbar.on('select', function () {
				var attachment = wp.media.frames.mailster_editbar.state().get('selection').first().toJSON(),
					el = $('img').data({
						id: attachment.id,
						name: attachment.name,
						src: attachment.url
					});
				loadPosts(null, function () {
					choosePic(null, el);
				});
			});
		}

		wp.media.frames.mailster_editbar.open();

	}

	function mceUpdater(editor) {
		clearTimeout(timeout);
		timeout = setTimeout(function () {
			if (!editor) return;
			var val = mailster.util.trim(editor.save());
			current.element.html(val);
		}, 100);
	}

	function close() {

		bar.removeClass('current-' + current.type).hide();
		loader(false);
		$('#single-link').hide();
		mailster.trigger('refresh');
		mailster.trigger('save');
		return false;

	}

	e.save = save;
	e.open = open;
	e.cancel = cancel;

	mailster.editbar = e;

	return mailster;

}(mailster || {}, jQuery, window, document));
// end Editbar