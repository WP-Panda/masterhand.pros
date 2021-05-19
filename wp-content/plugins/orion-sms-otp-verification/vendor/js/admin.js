(function ( $ ) {
	"use strict";

	var otpAdminSettings = {
		/**
		 * Init function.
		 */
		init: function () {
			otpAdminSettings.hideMobileInputNameField();
			otpAdminSettings.bindEvents();
		},

		/**
		 * Events
		 */
		bindEvents: function () {
		$( '.ihs-otp-mob-input' ).on( 'click', otpAdminSettings.toggleMobileInputField );
		},

		/**
		 * Toggles the mobile input field on the click on Create mobile input field.
		 */
		toggleMobileInputField: function ( event ) {

			// Uncheck all inputs and check the one which is clicked.
			$( '.ihs-otp-mob-input' ).removeAttr( 'checked' );
			$( this ).attr( 'checked', true );

			if ( 'checked' === $( this ).attr( 'checked' ) ) {
				var inputVal = $( this ).val();
				if ( 'No' === inputVal ){
					$( '#ihs_otp_mobile_input_name' ).removeClass( 'ihs-otp-hide' );
					$( '.ihs_otp_mob_input_name.config-input-class' ).attr( 'required', true );
				} else if ( 'Yes' === inputVal ) {
					$( '#ihs_otp_mobile_input_name' ).addClass( 'ihs-otp-hide' );
					$( '.ihs_otp_mob_input_name' ).val( '' );
					$( '.ihs_otp_mob_input_name.config-input-class' ).removeAttr( 'required' );
				}
			}
		},

		hideMobileInputNameField: function () {
			/**
			 * If the input is already selected yes, then hide the mobile input name field.
			 */
			var isYesBtnSelected = $( '.ihs-otp-mob-input.ihs-yes' ).attr( 'checked' );
			if ( isYesBtnSelected ) {
				$( '#ihs_otp_mobile_input_name' ).addClass( 'ihs-otp-hide' );
				$( '.ihs_otp_mob_input_name' ).val( '' );
				$( '.ihs_otp_mob_input_name.config-input-class' ).removeAttr( 'required' );
			}
		},
	};

	otpAdminSettings.init();

})( jQuery );
