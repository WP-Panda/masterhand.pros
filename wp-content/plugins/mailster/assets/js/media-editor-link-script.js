mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.$.document
		.on('click', '.media-editor-link', function (event) {

			event.preventDefault();

			var _this = $(this),
				_img = _this.find('img'),
				_input = _this.find('input');

			if (!wp.media.frames.mailster_mediaeditorlink) {

				wp.media.frames.mailster_mediaeditorlink = wp.media({
					title: _this.data('title'),
					button: {
						text: _this.data('title'),
					},
					multiple: false
				});

			}

			wp.media.frames.mailster_mediaeditorlink.off('select').on('select', function () {
				var attachment = wp.media.frames.mailster_mediaeditorlink.state().get('selection').first().toJSON();

				_img.attr('src', attachment.url);
				_input.val(attachment.id);
				_this.addClass('media-editor-link-has-image');

			});

			wp.media.frames.mailster_mediaeditorlink.open();

		})
		.on('click', '.media-editor-link-remove', function (event) {

			event.preventDefault();
			event.stopPropagation();

			var _this = $(this).parent(),
				_img = _this.find('img'),
				_input = _this.find('input');

			_img.removeAttr('src');
			_input.val('');
			_this.removeClass('media-editor-link-has-image');


		});

	return mailster;

}(mailster || {}, jQuery, window, document));