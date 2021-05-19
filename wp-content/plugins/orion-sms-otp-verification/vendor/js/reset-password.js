(function ($) {
	"use strict";

	var resetOtp = {

		/**
		 * Init Function.
		 */
		init: function () {
			if (!reset_pass_obj.form_selector) {
				return;
			}
			this.insertAlertBox();
			resetOtp.Select = reset_pass_obj.form_selector;
			resetOtp.loginInputName = reset_pass_obj.login_input_name;

			resetOtp.mobileLength = reset_pass_obj.ihs_mobile_length;
			resetOtp.countryCode = reset_pass_obj.country_code;
			resetOtp.Selector = resetOtp.buildFormSelector();
			resetOtp.addRequiredInputFields();
			resetOtp.bindEvents();
		},

		insertAlertBox: function () {
			$('body').append('<div id="oc-alert-container"></div>');
		},

		/**
		 * ShowAlert Function
		 *
		 * @param message
		 * @param background
		 */
		showAlert: function (message, background) {
			background = (background) ? background : '#3089cf';
			$('.oc-alert-pop-up').remove();
			var alertContainer = document.querySelector('#oc-alert-container'),
				htmlEntityIcon = ('#23282d' === background) ? '✔' : 'ⓘ',
				alertEl = '<div class="oc-alert-pop-up">' +
					'<div class="oc-alert-pop-up-message">' + '<span class="oc-i-icon-pop-up">' + htmlEntityIcon + '</span>' + message + '</div>' +
					'<div class="oc-alert-pop-up-close" style="background: ' + background + ' !important" >' + reset_pass_obj.wpml_messages.ok + '</div>' +
					'</div>';

			$(alertEl).css('background', background);
			$(alertContainer).html(alertEl);

			$('.oc-alert-pop-up-close').on('click', function (event) {
				$('.oc-alert-pop-up').fadeOut();
			});
		},

		/**
		 * Set values for form selector and submit button selector.
		 */
		buildFormSelector: function () {
			resetOtp.userFormSelector = reset_pass_obj.form_selector;
			resetOtp.loginInputName = reset_pass_obj.login_input_name;

			var formSelector = resetOtp.userFormSelector + ' input[name="' + resetOtp.loginInputName + '"]';
			/**
			 * Find the parent form element for the submit button selector.
			 * And if the parent form element is found add a class 'ihs_si_form' to it.
			 */
			resetOtp.formElement = $(formSelector).parents('form');
			if (resetOtp.formElement.length) {
				resetOtp.formElement.addClass('ihs_log_form');
			}

			resetOtp.formSelector = 'form.ihs_log_form';
			return resetOtp.formSelector;
		},

		/**
		 * Add required Input Fields.
		 */
		addRequiredInputFields: function () {
			resetOtp.resetPassLink = '<a class="ihs-otp-password-reset-link btn" href="javascript:void(0)">' + reset_pass_obj.wpml_messages.reset_password + '</a>';
			$(resetOtp.Selector).append(resetOtp.resetPassLink);
		},

		/**
		 * Bind Events.
		 */
		bindEvents: function () {
			$('.ihs-otp-password-reset-link').on('click', function () {
				var sendPassBtn, content,
					countryCode = (resetOtp.countryCode && 'ALL' !== resetOtp.countryCode) ? '+' + resetOtp.countryCode : '',
					readOnly = (countryCode) ? 'readonly' : '';
				if (!resetOtp.countryCode) {
					resetOtp.countryCode = '+91';
				}

				var mobileInputEl = '<br>' +
					'<label id="ihs-otp-reset-pass-input"> ' + reset_pass_obj.wpml_messages.mobile_no_required + '<br>\n' +
					'<div id="ihs-country-code" class="ihs-country-code-exis-mob">' +
					'<div class="ihs-country-inp-wrap">' +
					'<span class="">' +
					'   <input type="text" name="' + 'ihs-country-code' + '" value="' + countryCode + '" class="wpcf7-form-control ihs-country-code ihs-reset-country-code" required placeholder="e.g. +91" aria-invalid="false" readonly maxlength="5">' +
					'</span> ' +
					'</div>' +
					'</div>' +
					'<div>' +
					'<span class="">' +
					'<input type="number" name="ihs-otp-reset-pass-input" value="" class="ihs-otp-reset-pass-input" aria-required="true" aria-invalid="false">' +
					'</span> ' +
					'</div>' +
					'</label>';
				sendPassBtn = '<div class="ihs-otp-send-pass-btn" id="ihs-otp-send-pass-btn">' + reset_pass_obj.wpml_messages.send_new_password + '</div>';
				content = mobileInputEl + sendPassBtn;
				$(resetOtp.Selector).append(content);
				$('.ihs-otp-password-reset-link').remove();
			});
			$(resetOtp.Selector).on('click', '.ihs-otp-send-pass-btn', function () {
				var mobileNumber = $('.ihs-otp-reset-pass-input').val(),
					isNoError,
					countryCodeEl = $('.ihs-reset-country-code'),
					countryCodeElVal = countryCodeEl.val(),
					mobileLengthDatabase = parseInt(reset_pass_obj.ihs_mobile_length, 10),
					isAllSelected = (resetOtp.countryCode && 'ALL' === resetOtp.countryCode) ? true : '',
					errorArray = [];

				// Validate for no error
				isNoError = resetOtp.mobileAndCountryCodeValidation(mobileNumber, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray);

				// If no error call ajax request function.
				if (!isNoError) {
					resetOtp.sendNewPassAjaxRequest(mobileNumber, countryCodeElVal);
				}
			});
		},
		/**
		 * Return true if there are errors.
		 *
		 * @param mobElVal
		 * @param isAllSelected
		 * @param mobileLengthDatabase
		 * @param countryCodeElVal
		 * @param errorArray
		 */
		mobileAndCountryCodeValidation: function (mobElVal, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray) {
			if (!mobElVal) {
				errorArray.push(reset_pass_obj.wpml_messages.alerts.enter_mobile_no);
			}

			if (mobElVal && !isAllSelected) {
				// Checks the mobile digit needs to be at least no. of digit user has entered
				if (mobileLengthDatabase && mobileLengthDatabase !== mobElVal.length) {

					errorArray.push(reset_pass_obj.wpml_messages.alerts.enter_crt_mobile_no);
				}
				if (!mobileLengthDatabase && mobElVal.length < 5) {
					errorArray.push(reset_pass_obj.wpml_messages.alerts.enter_crt_mobile_no);
				}
			}

			if (!countryCodeElVal) {
				errorArray.push(reset_pass_obj.wpml_messages.alerts.enter_country_code);
			}

			if (errorArray.length) {
				var errorMessages = errorArray.join('</br>');
				resetOtp.showAlert(errorMessages, '#B93A32');
			}

			return errorArray.length;
		},

		/**
		 * Send New Password Ajax Request.
		 *
		 * @param {int} mobileNumber
		 * @param {string} countryCodeElVal
		 */
		sendNewPassAjaxRequest: function (mobileNumber, countryCodeElVal) {

			resetOtp.showAlert(reset_pass_obj.wpml_messages.alerts.sending_new_pass, '#23282d');
			$('#ihs-otp-send-pass-btn').hide();

			var request = $.post(
				reset_pass_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'ihs_otp_reset_ajax_hook',
					security: reset_pass_obj.ajax_nonce,
					data: {
						mob: mobileNumber,
						country_code: countryCodeElVal
					}
				}
			);

			request.done(function (response) {

				if (response.data.otp_pin_sent_to_js) {
					resetOtp.showAlert(reset_pass_obj.wpml_messages.alerts.new_pass_sent, '#23282d');
					$('#ihs-otp-reset-pass-input').hide();
					$('#ihs-otp-send-pass-btn').hide();
				} else {
					otp.showAlert(reset_pass_obj.wpml_messages.alerts.error_try_again, '#23282d');
				}
			});
		}
	},

		selector = 'form' + reset_pass_obj.form_selector;
	if ($(selector)) {
		resetOtp.init();
	}

})(jQuery);
