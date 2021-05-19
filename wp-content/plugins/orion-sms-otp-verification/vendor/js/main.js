(function ($) {
    "use strict";

    function OtpFunc(args) {
        this.otp_obj = args;
        this.si = args.special_index;
        this.wpml = args.wpml_messages;
        this.mobileInputElement = '';
        this.mobileInputSelector = '';
        this.submitBtnSelector = '';
        this.mobileOtpInputEl = '';
        this.sendOtpBtnEl = '';
        this.otpEntered = '';
        this.mobileNumberUsed = '';
        this.countryCode = '';
        this.verifyOtpBtnEl = '';
        this.resendOtpBtn = '';
        this.otpHashSent = '';
        this.api_used = '';
        this.route_used = '';
        this.countryCodeSelector = '#ihs-country-code' + this.si + ' .ihs-country-code';
        this.mobileVerified = false;
    }


    /**
     * The init function
     */
    OtpFunc.prototype.init = function () {
        this.buildFormSelector();
        this.insertAlertBox();

        // If neither submit button selector or form button selector is filled , then return
        if ('' === this.formElement && '' === this.formSelector) {
        }

        this.addRequiredInputFields();
        this.bindEvents();
    };

    OtpFunc.prototype.insertAlertBox = function () {
        if ($('#oc-alert-container').get().length === 0) {
            $('body').append('<div id="oc-alert-container"></div>');
        }
    },

        /**
         * ShowAlert Function
         *
         * @param message
         * @param background
         */
        OtpFunc.prototype.showAlert = function (message, background, isLoading) {
            background = (background) ? background : '#3089cf';
            $('.oc-alert-pop-up').remove();
            var alertContainer = document.querySelector('#oc-alert-container'),
                htmlEntityIcon = ('#1cca57' === background) ? '✔' : 'ⓘ',
                okBtn = '<div class="oc-alert-pop-up-close">' + otp_obj.wpml_messages.ok + '</div>',
                alertEl;

            alertEl = '<div class="oc-alert-pop-up">' +
                '<div class="oc-alert-pop-up-message">' + '<span class="oc-i-icon-pop-up" style="color: ' + background + ' !important">' + htmlEntityIcon + '</span>' + message + '</div>' +
                okBtn +
                '</div>';

            $(alertEl).css('background', background);
            $(alertContainer).html(alertEl);

            $('.oc-alert-pop-up-close').on('click', function (event) {
                $('.oc-alert-pop-up').fadeOut();
            });
        };

    /**
     * Set values for form selector and submit button selector.
     */
    OtpFunc.prototype.buildFormSelector = function () {

        this.userFormSelector = this.otp_obj.form_selector;
        this.submitBtnSelector = this.otp_obj.submit_btn_selector;

        /**
         * Find the parent form element for the submit button selector.
         * And if the parent form element is found add a class 'ihs_si_form' to it.
         */
        this.formElement = $(this.submitBtnSelector).parents('form');
        if (this.formElement.length) {
            this.formElement.addClass('ihs_si_form' + this.si);
        }

        /**
         * If user has not entered form selector then, add selector 'form.ihs_si_form',
         * otherwise use the one provided by him.
         * @type {string}
         */
        this.formSelector = (!this.userFormSelector) ? 'form.ihs_si_form' + this.si : this.userFormSelector;
    };

    /**
     * Binds Events.
     */
    OtpFunc.prototype.bindEvents = function () {
        var that = this,
            body = $('body');
        that.checkIfMobileVerified = that.checkIfMobileVerified.bind(this);

        body.on('click', this.submitBtnSelector, that.checkIfMobileVerified);
        body.on('submit', ('.ihs_si_form' + this.si), that.checkIfMobileVerified);

        /**
         * When the SEND OTP button is clicked.
         *
         */
        that.sendOtpAndCreateVerifyOtpButton = that.sendOtpAndCreateVerifyOtpButton.bind(this);

        var parentSelector = (this.otp_obj.is_woo_commerce_checkout_page) ? '.woocommerce-input-wrapper' : '.ihs_si_form' + this.si;
        $(parentSelector).on('click', ('#ihs-send-otp-btn' + this.si), that.sendOtpAndCreateVerifyOtpButton);

        /**
         * When the resend OTP Button is clicked it will remove verify otp button and send the otp and again show verify otp btn.
         */
        that.handleResendOtp = that.handleResendOtp.bind(this);
        $(parentSelector).on('click', ('#ihs-resend-otp-btn-id' + this.si), that.handleResendOtp);

        /**
         * When VERIFY OTP button is clicked to check the value of the otp entered.
         */
        that.preHandleOtpVerification = that.preHandleOtpVerification.bind(this);
        $(parentSelector).on('click', ('#ihs-submit-otp-btn' + this.si), that.preHandleOtpVerification);
    };

    /**
     * Check if the mobile is verified.
     *
     * @param event
     * @return {boolean}
     */
    OtpFunc.prototype.checkIfMobileVerified = function (event) {
        var that = this;
        if (!this.mobileVerified) {
            event.preventDefault();
            this.showAlert(this.wpml.alerts.pls_verify_otp_first, '#943734');
            return false;
        }
    };

    /**
     * Handle Resend OTP function.
     *
     * @param event
     */
    OtpFunc.prototype.handleResendOtp = function (event) {
        this.reCreateSendOtpButton();
        this.sendOtpAndCreateVerifyOtpButton();
    };

    /**
     * Sets up the required variables before calling handleOtpVerification function.
     *
     * @param event
     */
    OtpFunc.prototype.preHandleOtpVerification = function (event) {
        var otpInputEl = $('#ihs-mobile-otp' + this.si),
            otpInputElVal = otpInputEl.val();

        if (otpInputElVal) {
            this.handleOtpVerification(otpInputElVal);
        } else {
            this.showAlert(this.wpml.alerts.pls_enter_otp, '#943734');
        }
    };

    /**
     * This function is called when the form submit button is clicked.
     *
     * @param event
     */
    OtpFunc.prototype.sendOtpAndCreateVerifyOtpButton = function (event) {

        var mobEl = $(this.mobileInputSelector),
            mobElVal = mobEl.val(),
            countryCodeEl = $(this.countryCodeSelector),
            countryCodeElVal = countryCodeEl.val(),
            isNoError,
            isAllSelected = (this.otp_obj.ihs_country_code && 'ALL' === this.otp_obj.ihs_country_code) ? true : '',
            errorArray = [],
            mobileLengthDatabase = parseInt(this.otp_obj.ihs_mobile_length, 10);

        isNoError = this.mobileAndCountryCodeValidation(mobElVal, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray);

        // If no errors send Ajax request for this.
        if (!isNoError) {
            $('#ihs-verify-otp-popup-container' + this.si).addClass('ihs-otp-hide');
            $('#ihs-mobile-otp' + this.si).removeClass('ihs-otp-hide');
            this.sendOtpAjaxRequest(mobElVal, countryCodeElVal);
        }
    };

    /**
     * Resend OTP
     * Show send Otp verification button and hide the Verify OTP Button for Resending this.
     */
    OtpFunc.prototype.reCreateSendOtpButton = function () {
        // Hide the Send OTP button once OTP is sent and disable moble input field
        $('#ihs-send-otp-btn' + this.si).show();
        $(this.verifyOtpBtnEl).addClass('ihs-otp-hide');
        $('.ihs-otp-required.ihs-h' + this.si).addClass('ihs-otp-hide');
        $('#ihs-resend-otp-btn-id' + this.si).addClass('ihs-otp-hide');
        $(this.mobileInputSelector).attr('readonly', false);
        $(this.countryCodeSelector).attr('readonly', false);
        $(this.countryCodeSelector).css('color', '#555');
        $(this.mobileInputSelector).css('opacity', '1');
        $('#ihs-mobile-otp' + this.si).val('');
    };

    /**
     * Checks which api is used msp91 or twilio and then calls the respective functions to verify the otp enetered.
     *
     * @param otpInputElVal
     */
    OtpFunc.prototype.handleOtpVerification = function (otpInputElVal) {
        this.showAlert(this.wpml.verifying, '#1cca57', true);
        if ('msg91' === this.api_used) {
            this.verifyMsg91Otp(otpInputElVal)
        } else if ('twilio' === this.api_used) {
            this.verifyTwilioOtp(otpInputElVal)
        }
    };

    /**
     * Checks if the otp entered is same as the one sent.
     *
     * @param otpInputElVal
     */
    OtpFunc.prototype.verifyMsg91Otp = function (otpInputElVal) {

        if ('otp-route' === this.route_used) {
            this.verifyMsg91OtpViaOtpRoute(otpInputElVal)
        } else {
            this.verifyMsg91OtpViaTransactionalRoute(otpInputElVal);
        }

    };

    OtpFunc.prototype.verifyMsg91OtpViaOtpRoute = function (otpInputElVal) {
        var request,
            that = this;

        request = $.post(
            this.otp_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
            {
                action: 'ihs_verify_msg91',
                security: this.otp_obj.ajax_nonce,
                data: {
                    mob: this.mobileNumberUsed,
                    country_code: this.countryCode,
                    otp_entered: otpInputElVal
                }
            }
        );

        request.done(function (response) {
            var res = response.data.response;

            // If success
            if (res.success) {
                that.mobileVerified = true;
                that.showAlert(that.wpml.alerts.thx_for_verification, '#1cca57');
$('.oc-alert-pop-up').fadeOut(500);
                // Hide all otp buttons on success verification
                $('.ihs-otp-required.ihs-h' + that.si).fadeOut(500);
                $('#ihs-resend-otp-btn-id' + that.si).fadeOut(500);
								
                $('#ihs-verify-otp-popup-container' + that.si).fadeOut(500);
                that.verifyOtpBtnEl.fadeOut(500);

            } else if ('otp_expired' === res.error_message) {

                // The code is expired or already verification.
                that.reCreateSendOtpButton();
                that.showAlert(res.error_message, '#943734');

            } else if ('otp_not_verified' === res.error_message) {

                that.showAlert(that.wpml.alerts.verification_incorrect, '#943734');

            } else {
                that.showAlert(res.error_message, '#943734');
            }
        });
    };

    OtpFunc.prototype.verifyMsg91OtpViaTransactionalRoute = function (otpInputElVal) {

        var request,
            that = this;

        request = $.post(
            this.otp_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
            {
                action: 'ihs_verify_msg91_otp_transactional',
                security: this.otp_obj.ajax_nonce,
                data: {
                    otp_hash_sent: this.otpHashSent,
                    mob: this.mobileNumberUsed,
                    otp_entered: otpInputElVal
                }
            }
        );

        request.done(function (response) {
            var res = response.data.response;

            // If success
            if (res.success) {
                that.mobileVerified = true;
                that.showAlert(that.wpml.alerts.thx_for_verification, '#1cca57');
$('.oc-alert-pop-up').fadeOut(500);
                // Hide all otp buttons on success verification
                $('.ihs-otp-required.ihs-h' + that.si).fadeOut(500);
                $('#ihs-resend-otp-btn-id' + that.si).fadeOut(500);
                $('#ihs-verify-otp-popup-container' + that.si).fadeOut(500);
                that.verifyOtpBtnEl.fadeOut(500);

            } else if ('otp_expired' === res.error_message) {

                // The code is expired or already verification.
                that.reCreateSendOtpButton();
                that.showAlert(res.error_message, '#943734');

            } else if ('otp_not_verified' === res.error_message) {

                that.showAlert(that.wpml.alerts.verification_incorrect, '#943734');

            } else {
                that.showAlert(res.error_message, '#943734');
            }
        });
    };

    /**
     * Sends an ajax request to check if the otp entered is correct.
     * If its correct we will get the response as true.
     *
     * @param otpInputElVal
     */
    OtpFunc.prototype.verifyTwilioOtp = function (otpInputElVal) {

        var request,
            that = this;


        request = $.post(
            this.otp_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
            {
                action: 'ihs_verify_twilio',
                security: this.otp_obj.ajax_nonce,
                data: {
                    mob: this.mobileNumberUsed,
                    country_code: this.countryCode,
                    otp_entered: otpInputElVal
                }
            }
        );

        request.done(function (response) {
            var res = response.data.response;

            // If success
            if (res.success) {
                that.mobileVerified = true;
                that.showAlert(that.wpml.alerts.thx_for_verification, '#1cca57');
$('.oc-alert-pop-up').fadeOut(500);
                //new
                $(otp_obj.selectors.submit_btn_selector).prop('disabled', false)
                $(otp_obj.selectors.submit_btn_selector).prop('style', false)
                //new

                // Hide all otp buttons on success verification
                $('#ihs-verify-otp-popup-container' + that.si).fadeOut(500);
                $('.ihs-otp-required.ihs-h' + that.si).fadeOut(500);
                $('#ihs-resend-otp-btn-id' + that.si).fadeOut(500);
                that.verifyOtpBtnEl.fadeOut(500);

            } else if ('60023' === res.error_code) {

                // The code is expired or already verification.
                that.reCreateSendOtpButton();
                that.showAlert(res.error_message, '#943734');

            } else if ('60022' === res.error_code) {

                that.showAlert(that.wpml.alerts.verification_incorrect, '#943734');

            } else if ('60003' === res.error_code) {

                that.showAlert(that.wpml.alerts.tried_too_many_times, '#943734');
            } else {
                that.showAlert(res.error_message, '#943734');
            }
        });
    };

    /**
     * Return true if there are errors.
     *
     * @param mobElVal
     * @param isAllSelected
     * @param mobileLengthDatabase
     * @param countryCodeElVal
     * @param errorArray
     */
    OtpFunc.prototype.mobileAndCountryCodeValidation = function (mobElVal, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray) {
        if (!mobElVal) {
            errorArray.push(this.wpml.alerts.enter_mobile_no);
        }

        if (mobElVal && !isAllSelected) {
            // Checks the mobile digit needs to be at least no. of digit user has entered
            if (mobileLengthDatabase && mobileLengthDatabase !== mobElVal.length) {
                errorArray.push(this.wpml.alerts.enter_crt_mobile_no);
            }
            if (!mobileLengthDatabase && mobElVal.length < 5) {
                errorArray.push(this.wpml.alerts.enter_crt_mobile_no);
            }
        }

        if (!countryCodeElVal) {
            errorArray.push(this.wpml.alerts.enter_country_code);
        }

        if (errorArray.length) {
            var errorMessages = errorArray.join('</br>');
            this.showAlert(errorMessages, '#943734');
        }

        return errorArray.length;
    };

    /**
     * Create and append the required input fields.
     */
    OtpFunc.prototype.addRequiredInputFields = function () {
        var mobileInputName = 'ihs-mobile',
            countryCodeInputName, countryCode,
            createOtpFieldsWithMobInput = this.otp_obj.input_required,
            htmlEl, countryCodeHtmlCont;
        countryCodeInputName = 'ihs-country-code';
        countryCode = (this.otp_obj.ihs_country_code && 'ALL' !== this.otp_obj.ihs_country_code) ? '+' + this.otp_obj.ihs_country_code : '';

        countryCodeHtmlCont = '<div id="ihs-country-code' + this.si + '" class="ihs-country-code-exis-mob ihs-country-code-h">' +
            '<div class="ihs-country-inp-wrap">' +
            '<input type="text" name="' + countryCodeInputName + '" value="' + countryCode + '" class="ihs-country-code" required placeholder="e.g. +91" aria-invalid="false" ' + readOnly + ' maxlength="5">' +
            '</div>' +
            '</div>';

        if ('Yes' === createOtpFieldsWithMobInput) {
            createOtpFieldsWithMobInput = true;
        } else if ('No' === createOtpFieldsWithMobInput) {
            createOtpFieldsWithMobInput = false;
        } else {
            createOtpFieldsWithMobInput = false;
        }
        this.mobileInputName = mobileInputName;

        if (!createOtpFieldsWithMobInput) {
            var mobileInputNm = this.otp_obj.mobile_input_name;

            if (mobileInputNm) {

                var mobInpSelector = this.formSelector + ' input[name="' + mobileInputNm + '"]';
                htmlEl = this.createMobileInputAndOtherFields(mobileInputNm);
                $(htmlEl.allOtpHtml).insertAfter(mobInpSelector);


                this.mobileInputSelector = htmlEl.mobileInputNameSelector;
                this.mobileInputElement = this.setInputElVariables(htmlEl.mobileInputNameSelector);
                this.setOtpInputElementVar();


                // Add country code input field before existing mobile no. and add a class to existing mob input field
                $(countryCodeHtmlCont).insertBefore(this.mobileInputSelector);
                $(this.mobileInputSelector).addClass('ihs-existing-mob-inp-fld');
                $(this.mobileInputSelector).css('width', 'calc(100% - 5rem)');

                //new
                $(otp_obj.selectors.submit_btn_selector).prop('disabled', true)
                $(otp_obj.selectors.submit_btn_selector)[0].style.backgroundColor = 'inherit'
                $(otp_obj.selectors.submit_btn_selector)[0].style.color = 'inherit'
                //new

            } else {
                htmlEl = this.createMobileInputAndOtherFields(mobileInputName);
                $(htmlEl.allOtpHtml).insertAfter(htmlEl.mobileInputNameSelector);
                this.mobileInputSelector = htmlEl.mobileInputNameSelector;
                this.mobileInputElement = this.setInputElVariables(htmlEl.mobileInputNameSelector);
                this.setOtpInputElementVar();

                // Add country code input field before existing mobile no. and add a class to existing mob input field
                $(countryCodeHtmlCont).insertBefore(this.mobileInputSelector);
                $(this.mobileInputSelector).addClass('ihs-existing-mob-inp-fld');
                $(this.mobileInputSelector).css('width', 'calc(100% - 5rem)');
            }

        } else {
            var readOnly,
                mobAndCountryCodeContent = '',
                countryCodeAndMobileInputEl, submitBtnSelector,
                mobileInpName = 'ihs-mobile';
            readOnly = (countryCode) ? 'readonly' : '';
            countryCodeAndMobileInputEl = '<div id="ihs-country-code' + this.si + '" class="ihs-mobile-no-lab ihs-country-code-h">' + this.wpml.mobile_box_label + '<br>\n' +
                '<div class="ihs-country-inp-wrap">' +
                '<input type="text" name="' + countryCodeInputName + '" value="' + countryCode + '" class="ihs-country-code" required placeholder="e.g. +91" aria-invalid="false" ' + readOnly + ' maxlength="5">' +
                '</div>' +
                '<div class="ihs-mob-inp-wrap">' +
                '<input type="text" name="' + mobileInpName + '" value="" class="ihs-mb-inp-field" aria-required="true" aria-invalid="false">' +
                '</div>' +
                '</div>',
                submitBtnSelector = this.formSelector + ' input[type="submit"]';
            htmlEl = this.createMobileInputAndOtherFields(mobileInputName);
            mobAndCountryCodeContent = '<div class="ihs-mob-country-wrapper">' + countryCodeAndMobileInputEl + '</div>';
            mobAndCountryCodeContent += htmlEl.allOtpHtml;
            this.mobileInputSelector = '#ihs-country-code' + this.si + ' .ihs-mb-inp-field';
            this.countryCodeSelector = '#ihs-country-code' + this.si + ' .ihs-country-code';
            // $( mobileInputEl ).insertBefore( submitBtnSelector );
            $('.ihs_si_form' + this.si).append(mobAndCountryCodeContent);
            this.setOtpInputElementVar();
            this.mobileInputElement = this.setInputElVariables('#ihs-mobile-number' + this.si);
        }
    };

    OtpFunc.prototype.setOtpInputElementVar = function () {
        this.mobileOtpInputEl = this.setInputElVariables('#ihs-mobile-otp' + this.si);
        this.mobileOtpHiddenInputEl = this.setInputElVariables('#ihs-otp-hidden' + this.si);
        this.sendOtpBtnEl = this.setInputElVariables('#ihs-send-otp-btn' + this.si);
        this.verifyOtpBtnEl = this.setInputElVariables('#ihs-submit-otp-btn' + this.si);
    };

    /**
     * Sets the value of an element.
     *
     * @param elementSelector
     * @return {*|HTMLElement} elementSelector Element Selector.
     */
    OtpFunc.prototype.setInputElVariables = function (elementSelector) {
        return $(elementSelector);
    };

    /**
     * Creates markup for OTP input fields and submit button.
     *
     * @param mobileInputName
     * @return {obj} htmlEl Contains markup for OTP input fields and submit button.
     */
    OtpFunc.prototype.createMobileInputAndOtherFields = function (mobileInputName) {

        var htmlEl = {},
            otpInputEl = '<span id="ihs-otp-required' + this.si + '" class="ihs-otp-required ihs-h' + this.si + ' ihs-otp-hide">' + this.wpml.otp_verify_label + this.mobileNumberUsed + '\n' +
                '<div class="wrap ihs-otp">' +
                '<input type="text" id="ihs-mobile-otp' + this.si + '" name="ihs-otp" value="" size="40" class=" ihs-otp-hide ihs-mobile-otp-h" aria-required="true" placeholder="Enter OTP" aria-invalid="false">' +
                '</div>' +
                '</span>',
            sendOtpBtn = '<div class="ihs-otp-btn" id="ihs-send-otp-btn' + this.si + '">' + this.wpml.send_otp + '</div>',
            submitOtpBtn = '<div class="ihs-otp-btn ihs-otp-hide" id="ihs-submit-otp-btn' + this.si + '">' + this.wpml.verify_otp + '</div>',
            resendOtpBtn = '<div><a href="javascript:void(0);" class="ihs-otp-btn-resend ihs-otp-hide" id="ihs-resend-otp-btn-id' + this.si + '">' + this.wpml.resend_otp + '</a></div>',
            minimizeIcon = '<span class="ihs-close">&times;</span>';
        htmlEl.allOtpHtml = sendOtpBtn + '<span id="ihs-verify-otp-popup-container' + this.si + '" class="ihs-verify-otp-popup-container ihs-otp-hide">' + otpInputEl + resendOtpBtn + submitOtpBtn + '</span>';
        htmlEl.mobileInputNameSelector = this.formSelector + ' input[name="' + mobileInputName + '"]';
        return htmlEl;
    };

    /**
     * OTP function Sends Ajax request to send OTP
     * @param {string} mobileNumber
     * @param {string} countryCode
     */
    OtpFunc.prototype.sendOtpAjaxRequest = function (mobileNumber, countryCode) {
        var that = this;
        $('#ihs-send-otp-btn' + that.si).hide();
        this.showAlert(this.wpml.sending_otp, '#1cca57', true);
        var request = $.post(
            that.otp_obj.ajax_url,
            {
                action: 'ihs_otp_ajax_hook',
                security: that.otp_obj.ajax_nonce,
                data: {
                    mob: mobileNumber,
                    country_code: countryCode
                }
            }
        );

        request.done(function (response) {

            // Set the otp value and the type of api used from the response.
            that.otpHashSent = response.data.otp_hash;
            that.api_used = response.data.api;
            that.route_used = response.data.route;
            that.mobileNumberUsed = response.data.mobile;
            that.countryCode = response.data.country_code;

            if (response.data.success === true) {

                $('#ihs-send-otp-btn' + that.si).hide();
                $(that.verifyOtpBtnEl).removeClass('ihs-otp-hide');
                $('.ihs-otp-required.ihs-h' + that.si).removeClass('ihs-otp-hide');
                $('#ihs-resend-otp-btn-id' + that.si).removeClass('ihs-otp-hide');
                $('#ihs-verify-otp-popup-container' + that.si).removeClass('ihs-otp-hide');
                $(that.mobileInputSelector).attr('readonly', true);
                $(that.countryCodeSelector).attr('readonly', true);
                $(that.countryCodeSelector).css('color', '#b3b0b0');
                $(that.mobileInputSelector).css('opacity', '0.5');

            } else {
                $('#ihs-send-otp-btn' + that.si).show();
                that.showAlert(response.data.error_message, '#943734');

            }
        });
    };


    if ('undefined' !== typeof otp_obj) {

        var temp_obj = {
            ajax_url: otp_obj.ajax_url,
            ajax_nonce: otp_obj.ajax_nonce,
            form_selector: otp_obj.selectors.form_selector,
            submit_btn_selector: otp_obj.selectors.submit_btn_selector,
            input_required: otp_obj.selectors.input_required,
            mobile_input_name: otp_obj.selectors.mobile_input_name,
            ihs_country_code: otp_obj.ihs_country_code,
            ihs_mobile_length: otp_obj.ihs_mobile_length,
            special_index: 1,
            wpml_messages: otp_obj.wpml_messages,
            is_woo_commerce_checkout_page: otp_obj.selectors.is_woo_commerce_checkout_page
        };

        var selector = otp_obj.selectors.submit_btn_selector;

        if ($(selector).get().length > 0) {
            var otpFormFunc = new OtpFunc(temp_obj);
            otpFormFunc.init();
        }

    }

})(jQuery);
