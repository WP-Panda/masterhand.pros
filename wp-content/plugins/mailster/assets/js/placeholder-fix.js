jQuery(document).ready(function (jQuery) {

	"use strict"

	// fix for the missing placeholder feature in IE < 10
	if (!placeholderIsSupported()) {

		jQuery('body')
			.on('focus.mailster', 'form.mailster-form input[placeholder]', function () {
				var el = jQuery(this);
				if (el.val() == el.attr("placeholder"))
					el.val("");
			})
			.on('blur.mailster', 'form.mailster-form input[placeholder]', function () {
				var el = jQuery(this);
				if (el.val() == "")
					el.val(el.attr("placeholder"));

			})
			.on('submit.mailster', 'form.mailster-form', function () {
				var form = jQuery(this),
					inputs = form.find('input[placeholder]');


				jQuery.each(inputs, function () {
					var el = jQuery(this);
					if (el.val() == el.attr("placeholder"))
						el.val("");
				});

			})

		jQuery('form.mailster-form').find('input[placeholder]').trigger('blur.mailster');

	}

	function placeholderIsSupported() {
		var test = document.createElement('input');
		return ('placeholder' in test);
	}

});