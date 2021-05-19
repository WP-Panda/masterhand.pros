<?php
/**
 * Custom functions for the Orion Plugin.
 * Contains definition of constants, file includes and enqueuing stylesheets and scripts.
 *
 * @package Orion SMS OTP verification
 */

/* Define Constants */
define( 'IHS_OTP_URI', plugins_url( 'orion-sms-otp-verification' ) );
define( 'IHS_OTP_PATH', plugin_dir_path( __FILE__ ) );
define( 'IHS_OTP_JS_URI', plugins_url( 'orion-sms-otp-verification' ) . '/vendor/js' );
define( 'IHS_OTP_CSS_URI', plugins_url( 'orion-sms-otp-verification' ) . '/css' );


if ( ! function_exists( 'ihs_generate_translated_msgs' ) ) {
	/**
	 * Get the text for translation in an array.
	 *
	 * @return {array} $ihs_wpml_messages
	 */
	function ihs_generate_translated_msgs() {

		$ihs_wpml_messages = array(
			'mobile_box_label' 	=> __('Mobile Number with Country Code (required)', 'orion-sms-orion-sms-otp-verification'),
			'otp_required' => __('OTP( required )', 'orion-sms-orion-sms-otp-verification'),
			'send_otp'			=> __('Send Verification Code', 'orion-sms-orion-sms-otp-verification'),
			'otp_verify_label'	=> __('Code Verification', 'orion-sms-orion-sms-otp-verification'),
			'verify_otp'		=> __('Verify Code', 'orion-sms-orion-sms-otp-verification'),
			'resend_otp'		=> __('Resend Code', 'orion-sms-orion-sms-otp-verification'),
			'reset_pwd_via_otp' => __('Reset Password via OTP', 'orion-sms-orion-sms-otp-verification'),
			'mobile_no_required' => __('Mobile Number (required)', 'orion-sms-orion-sms-otp-verification'),
			'send_new_password'	=> __('Send New Password', 'orion-sms-orion-sms-otp-verification'),
			'sending'	=> __('Sending...', 'orion-sms-orion-sms-otp-verification'),
			'sending_otp'	=> __('Sending Verification Code...', 'orion-sms-orion-sms-otp-verification'),
			'verifying'	=> __('Verifying...', 'orion-sms-orion-sms-otp-verification'),
			'ok'	=> __('OK', 'orion-sms-orion-sms-otp-verification'),
			'reset_password'	=> __('Reset Password', 'orion-sms-orion-sms-otp-verification'),
			'alerts'			=> array(
				'pls_verify_otp_first' => __( 'Please verify OTP first', 'orion-sms-orion-sms-otp-verification' ),
				'pls_enter_req_field' => __( 'Please Enter the required fields', 'orion-sms-orion-sms-otp-verification' ),
				'pls_enter_otp' => __( 'Enter Verification Code', 'orion-sms-orion-sms-otp-verification' ),
				'thx_for_verification' => __( 'Thank you for verification', 'orion-sms-orion-sms-otp-verification' ),
				'otp_enter_incorrect' => __( 'OTP Entered is Incorrect', 'orion-sms-orion-sms-otp-verification' ),
				'tried_too_many_times' => __( 'You have tried too many times', 'orion-sms-orion-sms-otp-verification' ),
				'otp_sent_to_mobile' => __( 'OTP sent to your mobile', 'orion-sms-orion-sms-otp-verification' ),
				'enter_mobile_no' => __( 'Enter the mobile number', 'orion-sms-orion-sms-otp-verification' ),
				'enter_crt_mobile_no' => __( 'Enter the correct Mobile Number', 'orion-sms-orion-sms-otp-verification' ),
				'enter_country_code' => __( 'Enter the Country Code', 'orion-sms-orion-sms-otp-verification' ),
				'verification_incorrect' => __( 'Verification code is incorrect', 'orion-sms-orion-sms-otp-verification' ),
				'error_try_again' => __( 'Error: Please try again', 'orion-sms-orion-sms-otp-verification' ),
				'sending_new_pass' => __( 'Sending new password...', 'orion-sms-orion-sms-otp-verification' ),
				'new_pass_sent' => __( 'New password sent to your mobile', 'orion-sms-orion-sms-otp-verification' ),
			),
		);

		return $ihs_wpml_messages;
	}

}

if ( ! function_exists( 'ihs_otp_enqueue_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts.
	 */
	function ihs_otp_enqueue_scripts( $hook ) {
		wp_enqueue_style( 'ihs_otp_styles', IHS_OTP_URI . '/style.css' );

		$ihs_wpml_messages = ihs_generate_translated_msgs();



		// Get all the selector and input name data from the database.

		$form_selector = get_option( 'ihs_otp_form_selector' );
		$submit_btn_selector = get_option( 'ihs_otp_submit_btn-selector' );
		$input_required	= get_option( 'ihs_otp_mobile_input_required' );
		$mobile_input_name = get_option( 'ihs_otp_mobile_input_name' );

		// On woocommerce checkout page
		if ( class_exists( 'WooCommerce' ) && is_checkout() ) {

			$selector_array = array(
				'form_selector'       => trim( '.checkout.woocommerce-checkout' ),
				'submit_btn_selector' => trim( get_option( 'ihs_otp_woo_submit_btn-selector' ) ),
				'input_required'      => ( trim( get_option( 'ihs_otp_woo_mobile_input_name' ) ) == '' ) ? 'Yes' : 'No',
				'mobile_input_name'   => trim( get_option( 'ihs_otp_woo_mobile_input_name' ) ),
				'is_woo_commerce_checkout_page' => true
			);

		}else{
			$selector_array = array(
				'form_selector'       => trim( $form_selector ),
				'submit_btn_selector' => trim( $submit_btn_selector ),
				'input_required'      => ( trim( $mobile_input_name ) == '' ) ? 'Yes' : 'No',
				'mobile_input_name'   => trim( $mobile_input_name ),
				'is_woo_commerce_checkout_page' => false

			);
		}


		wp_enqueue_script( 'ihs_otp_main_js', IHS_OTP_JS_URI . '/main.js', array( 'jquery' ), microtime(), true );
		wp_enqueue_script( 'ihs_otp_reset_password_js', IHS_OTP_JS_URI . '/reset-password.js', array( 'jquery' ), microtime(), true );

		wp_localize_script(
			'ihs_otp_main_js', 'otp_obj', array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'          => wp_create_nonce( 'ihs_otp_nonce_action_name' ),
				'selectors'           => $selector_array,
				'ihs_country_code'    => get_option( 'ihs_otp_country_code' ),
				'ihs_mobile_length'   => get_option( 'ihs_mobile_length' ),
				'wpml_messages'		  => $ihs_wpml_messages,

			)
		);

		wp_localize_script(
				'ihs_otp_reset_password_js', 'reset_pass_obj', array(
					'ajax_url'          => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'        => wp_create_nonce( 'ihs_otp_nonce_reset_pass' ),
					'form_selector'     => get_option( 'ihs_otp_login_form_selector' ),
					'country_code'      => get_option( 'ihs_otp_mob_country_code' ),
					'ihs_mobile_length' => get_option( 'ihs_mobile_length' ),
					'login_input_name'  => get_option( 'ihs_otp_login_form_input_name' ),
					'wpml_messages'		  => $ihs_wpml_messages,
				)
			);
	}
}

add_action( 'wp_enqueue_scripts', 'ihs_otp_enqueue_scripts' );

if ( ! function_exists( 'ihs_otp_enqueue_admin_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts for admin.
	 *
	 * @param {string} $hook Hook.
	 */
	function ihs_otp_enqueue_admin_scripts( $hook ) {
		if ( 'toplevel_page_orion-sms-otp-verification/inc/admin-settings' === $hook
		|| 'orion-otp_page_ihs_otp_plugin_woocommerce_settings_page' === $hook ) {
			wp_enqueue_style( 'ihs_otp_admin_font_awesome', '//use.fontawesome.com/releases/v5.0.13/css/all.css' );
			wp_enqueue_style( 'ihs_otp_admin_bootstrap_styles', '//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' );
			wp_enqueue_style( 'ihs_otp_admin_styles', IHS_OTP_CSS_URI . '/admin.css' );
			wp_enqueue_script( 'ihs_otp_admin_script', IHS_OTP_JS_URI . '/admin.js', array( 'jquery' ), '', true );
		}
	}
	add_action( 'admin_enqueue_scripts', 'ihs_otp_enqueue_admin_scripts' );
}


if ( ! function_exists( 'ihs_otp_ajax_handler' ) ) {
	/**
	 * Send OTP .
	 */
	function ihs_otp_ajax_handler() {
		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}

		$mobile_number = $_POST['data']['mob'];
		$woo_commerce_query = ( ! empty( $_POST['data']['woo_commerce'] ) ) ? $_POST['data']['woo_commerce'] : false;
		$country_code_from_form = $_POST['data']['country_code'];
		$country_code_from_form = str_replace( '+', '', $country_code_from_form );
		$otp_country_cod = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_otp_woo_country_code' ) : get_option( 'ihs_otp_country_code' );
		$country_code = ( $country_code_from_form ) ? $country_code_from_form : $otp_country_cod;

		$otp_pin = mt_rand( 100000, 500000 );

		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$message_template = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_otp_woo_msg_template' ) : get_option( 'ihs_otp_msg_template' );
		$api_used =  ( get_option( 'ihs_api_type' ) ) ? get_option( 'ihs_api_type' ) : 'otp';
		$route_used =  ( get_option( 'ihs_mgs_route' ) ) ? get_option( 'ihs_mgs_route' ) : 'otp-route';


		// Send the OTP and you should get a boolean response from the ihs_send_otp function.
		$response = ihs_send_otp( $mobile_number, $country_code, $otp_pin, $message_template, $woo_commerce_query );

		$hash = '';

		// If response is true which means message was sent
		if ( $response['success'] === true ) {

			// Send OTP if the api used is msg91, if its twilio then don't send the otp
			$otp_pin = ( 'msg91' === $api_used ) ? $otp_pin : '';
			$is_otp_send = true;

			if( 'msg91' === $api_used ){
				// Hash for Transactional Route
				$str_mobile = strval( trim( $mobile_number ) );
				$str_otp = strval( trim( $otp_pin ) );
				$hash = md5( $str_mobile.$str_otp );
			}


		} else {
			$is_otp_send = false;
			$otp_pin = '';
		}


		wp_send_json_success(
			array(
				'otp_hash' => $hash,
				//'otp_pin_sent_to_js' => $otp_pin,
				'otp_sent' => strval( $is_otp_send ),
				'mobile' => strval( $mobile_number ),
				'country_code' => strval( $country_code ),
				'api' => $api_used,
				'route' => $route_used,
				'success' => $response['success'],
				'error_message' => $response['error_message'],
				//'data_recieved_from_js'    => $_POST,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
	add_action( 'wp_ajax_nopriv_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
}

if ( ! function_exists( 'ihs_send_otp' ) ) {
	/**
	 * Send Otp after checking the route and  the api to be used
	 *
	 * @param {int}    $mob_number Mobile number.
	 * @param {int}    $country_code Country Code.
	 * @param {string} $otp_pin Otp pin.
	 * @param {string} $message_template Message Template.
	 *
	 * @return {mixed} $response Response or Error.
	 */
	function ihs_send_otp( $mob_number, $country_code, $otp_pin, $message_template, $woo_commerce_query ) {

		$auth_key = get_option( 'ihs_otp_auth_key' );
		$otp_length = strlen( $otp_pin );
		$message = str_replace( '{OTP}', $otp_pin, $message_template );
		$sender_id = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_otp_woo_sender_id' ) : get_option( 'ihs_otp_sender_id' );
		$country_code = str_replace( '+', '', $country_code );
		$mob_number_with_country_code = '+' . $country_code . $mob_number;

		$route = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_woo_mgs_route' ) : get_option( 'ihs_mgs_route' );
		$api_used =  ( get_option( 'ihs_api_type' ) ) ? get_option( 'ihs_api_type' ) : 'otp';

		// If twilio api used
		if ( 'twilio' === $api_used ) {

			// Will return true if the otp was sent successfully.
			return ihs_send_otp_via_twilio( $mob_number, $country_code, $message );
		} else {

			// If msg91 used, check which route used and then send otp.
			if ( 'otp-route' === $route ) {
				$message = str_replace( '{OTP}', '##OTP##', $message_template );
				// Will return true if the otp was sent successfully.
				return ihs_send_otp_via_otp_route(
					$otp_length, $auth_key, $message,
					$sender_id, $mob_number_with_country_code, $otp_pin );
			} else if ( 'transactional-route' === $route ) {

				// Will return true if the otp was sent successfully.
				return ihs_send_otp_via_transactional_route(
					$auth_key, $message, $sender_id, $mob_number, $country_code
				);
			}
		}

	}
}

if ( ! function_exists( 'ihs_send_otp_via_twilio' ) ) {
	/**
	 * Send the OTP via twilio api
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 * @param {int}    $mob_number Mobile number.
	 * @param {int}    $country_code Country Code.
	 * @param {string}    $message Text Message
	 *
	 * @return {boolean} Returns true if the message sent successfully
	 */
	function ihs_send_otp_via_twilio( $mob_number, $country_code, $message ) {
		$api_key = ( get_option( 'ihs_twilio_api_key' ) ) ? get_option( 'ihs_twilio_api_key' ) : '';

		$return_val = array();
		$return_val['success'] = false;
		$return_val['error_code'] = '';
		$return_val['error_message'] = '';

		if ( ! $api_key ) {
			$return_val['error_message'] = __('API key not found!', 'orion-sms-orion-sms-otp-verification');
			return $return_val;
		}

		$url = "https://api.authy.com/protected/json/phones/verification/start";
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'api_key' => $api_key,
					'via' => 'sms',
					'phone_number' => $mob_number,
					'country_code' => $country_code
				),
				'cookies' => array()
			)
		);



		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$return_val['error_message'] = $error_message;
		} else {
			$decoded_response = json_decode( $response['body'] );

			if( $decoded_response->success === true ){
				$return_val['success'] = true;
			} else if( $decoded_response->error_code === '60033' ){
				$return_val['error_message'] = __('Phone number is invalid', 'orion-sms-orion-sms-otp-verification');
			} else if( $decoded_response->error_code === '60001' ){
				$return_val['error_message'] = __('Invalid API key', 'orion-sms-orion-sms-otp-verification');
			} else if( $decoded_response->error_code === '60082' ){
				$return_val['error_message'] = __('Cannot send SMS to landline phone numbers', 'orion-sms-orion-sms-otp-verification');
			} else {
				$return_val['error_message'] = __($decoded_response->message, 'orion-sms-orion-sms-otp-verification');
			}
		}

		return $return_val;
	}
}

if ( ! function_exists( 'ihs_handle_entered_otp_verification_msg91' ) ) {
	/**
	 * Verify if the OTP entered is correct via msg91 api
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 */
	function ihs_handle_entered_otp_verification_msg91() {

		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}

		$mob_number = $_POST['data']['mob'];
		$country_code = $_POST['data']['country_code'];
		$otp_entered = $_POST['data']['otp_entered'];

		$response = ihs_verify_entered_otp_msg91( $mob_number, $country_code, $otp_entered );

		wp_send_json_success(
			array(
				'response' => $response,
				//'data_recieved_from_js'    => $_POST,
			)
		);
	}
	add_action( 'wp_ajax_ihs_verify_msg91', 'ihs_handle_entered_otp_verification_msg91' );
	add_action( 'wp_ajax_nopriv_ihs_verify_msg91', 'ihs_handle_entered_otp_verification_msg91' );

}

if ( ! function_exists( 'ihs_handle_entered_otp_verification_msg91_transactional' ) ) {
	/**
	 * Verify if the OTP entered is correct via msg91 api using transactional route
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 */
	function ihs_handle_entered_otp_verification_msg91_transactional() {

		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}

		$otp_hash_sent = trim($_POST['data']['otp_hash_sent']);
		$mob_number = trim($_POST['data']['mob']);
		$otp_entered = trim($_POST['data']['otp_entered']);

		$str_mobile = strval( trim( $mob_number ) );
		$str_otp = strval( trim( $otp_entered ) );

		$new_hash = md5( $str_mobile.$str_otp );


		$response = array(
				'error_code' => '',
				'error_message' => '',
				'success' => false
			);

		if( $new_hash === $otp_hash_sent ){
			$response['success'] = true;
		}else{
			$response['error_message'] = 'otp_not_verified';
		}

		wp_send_json_success(
			array(
				'response' => $response,
				//'data_recieved_from_js'    => $_POST,
			)
		);
	}
	add_action( 'wp_ajax_ihs_verify_msg91_otp_transactional', 'ihs_handle_entered_otp_verification_msg91_transactional' );
	add_action( 'wp_ajax_nopriv_ihs_verify_msg91_otp_transactional', 'ihs_handle_entered_otp_verification_msg91_transactional' );

}

if ( ! function_exists( 'ihs_verify_entered_otp_msg91' ) ) {
	/**
	 * Verify if the OTP entered is correct via msg91 api
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 * @param {string}    $mob_number Mobile number.
	 * @param {string}    $country_code Country Code.
	 * @param {string}    $otp_entered Otp entered
	 *
	 * @return {boolean} Returns true if the message sent successfully
	 */
	function ihs_verify_entered_otp_msg91( $mob_number, $country_code, $otp_entered ) {
		$api_key = ( get_option( 'ihs_otp_auth_key' ) ) ? get_option( 'ihs_otp_auth_key' ) : '';

		if ( ! $api_key ) {
			return;
		}
		$mob_number = '+' . $country_code . $mob_number;

		$url = "https://control.msg91.com/api/verifyRequestOTP.php?authkey=$api_key&mobile=$mob_number&otp=$otp_entered";
		$response = wp_remote_post( $url, array(
				'method' => 'GET',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(
					"content-type: application/x-www-form-urlencoded"
				),
				'body' => array(),
				'cookies' => array(),
			)
		);
		$decoded_response = json_decode( $response['body'] );

		if ( is_wp_error( $response ) ) {
			$error_message = $decoded_response->message;
			return array(
				'error_code' => '',
				'error_message' => $error_message,
				'success' => false
			);
		} else {

			if ( 'success' === $decoded_response->type  ) {
				// If no error
				return array(
					'error_code' => '',
					'error_message' => '',
					'success' => true
				);
			} else {
				$error_message = $decoded_response->message;
				return array(
					'error_code' => '',
					'error_message' => $error_message,
					'success' => false
				);
			}
		}

	}
}

if ( ! function_exists( 'ihs_handle_entered_otp_verification_twilio' ) ) {
	/**
	 * Verify if the OTP entered is correct via twilio api
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 */
	function ihs_handle_entered_otp_verification_twilio() {

		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}

		$mob_number = $_POST['data']['mob'];
		$country_code = $_POST['data']['country_code'];
		$otp_entered = $_POST['data']['otp_entered'];

		$response = ihs_verify_entered_otp_twilio( $mob_number, $country_code, $otp_entered );

		wp_send_json_success(
			array(
				'response' => $response,
				//'data_recieved_from_js'    => $_POST,
			)
		);
	}
	add_action( 'wp_ajax_ihs_verify_twilio', 'ihs_handle_entered_otp_verification_twilio' );
	add_action( 'wp_ajax_nopriv_ihs_verify_twilio', 'ihs_handle_entered_otp_verification_twilio' );

}

if ( ! function_exists( 'ihs_verify_entered_otp_twilio' ) ) {
	/**
	 * Verify if the OTP entered is correct via twilio api
	 *
	 * Mobile Number $mob_number is without country code and country code $country_code is without plus sign.
	 *
	 * @param {string}    $mob_number Mobile number.
	 * @param {string}    $country_code Country Code.
	 * @param {string}    $otp_entered Otp entered
	 *
	 * @return {boolean} Returns true if the message sent successfully
	 */
	function ihs_verify_entered_otp_twilio( $mob_number, $country_code, $otp_entered ) {
		$api_key = ( get_option( 'ihs_twilio_api_key' ) ) ? get_option( 'ihs_twilio_api_key' ) : '';

		if ( ! $api_key ) {
			return;
		}

		$url = "https://api.authy.com/protected/json/phones/verification/check";
		$response = wp_remote_post( $url, array(
				'method' => 'GET',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(
					'X-Authy-Api-Key'=> $api_key
				),
				'body' => array(
					'phone_number' => $mob_number,
					'country_code' => $country_code,
					'verification_code' => $otp_entered
				),
				'cookies' => array(),
			)
		);
		$decoded_response = json_decode( $response['body'] );

		if ( is_wp_error( $response ) || ! $decoded_response->success ) {
			$error_message = $decoded_response->message;
			return array(
				'error_code' => $decoded_response->error_code,
				'error_message' => $error_message,
				'success' => false
			);
		} else if ( $decoded_response->success ) {
			// If no error
			return array(
				'error_code' => '',
				'error_message' => '',
				'success' => true
			);
		}

	}
}

if ( ! function_exists( 'ihs_send_otp_via_otp_route' ) ) {
	/**
	 * Send OTP via OTP route.
	 *
	 * @param $otp_length
	 * @param $auth_key
	 * @param $message
	 * @param $sender_id
	 * @param $mob_number
	 * @param $otp_pin
	 *
	 * @return bool
	 */
	function ihs_send_otp_via_otp_route( $otp_length, $auth_key, $message,
		$sender_id, $mob_number, $otp_pin ) {

		$message = urlencode( $message );


		$expiration_time = '10'; // in secs
		$url = "http://control.msg91.com/api/sendotp.php?authkey=$auth_key&message=$message&sender=$sender_id&mobile=$mob_number&otp_expiry=$expiration_time";
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array(),
				'cookies' => array()
			)
		);

		$return_val = array(
			'success' => false,
			'error_code' => '',
			'error_message' => ''
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$return_val['error_message'] = $error_message;
		}else{

			$res_body = (array)json_decode( $response['body'] );

			if( $res_body['type'] === 'success' ){
				$return_val['success'] = true;
			}else{
				$return_val['error_message'] = 'API Error';
			}

		}

		return $return_val;
	}
}


if ( ! function_exists( 'ihs_send_otp_via_transactional_route' ) ) {
	function ihs_send_otp_via_transactional_route (
		$auth_key, $message, $sender_id, $mob_number, $country_code
	) {

		$curl = curl_init();
		$phone = $mob_number;

		include_once 'unicode-file.php';

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		$return_val = array();
		$return_val['success'] = false;
		$return_val['error_code'] = '';
		$return_val['error_message'] = '';

		if ( $err ) {
			$return_val['error_message'] = $err;
		}else{

			$curl_response = simplexml_load_string( strtolower($response) );
			$curl_response = ( array )$curl_response;

			if( isset($curl_response['type']) && $curl_response['type'] === 'success' ){
				$return_val['success'] = true;
			}else{
				$return_val['error_message'] = __('API Error', 'orion-sms-orion-sms-otp-verification');
			}
		}

		return $return_val;

	}
}

if ( ! function_exists( 'ihs_otp_send_new_pass' ) ) {
	/**
	 * Send New Password.
	 */
	function ihs_otp_send_new_pass() {

		if ( isset( $_POST['security'] ) ) {
			$nonce_val = sanitize_text_field( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_reset_pass' ) ) {
			wp_die();
		}

		$mobile_number = $_POST['data']['mob'];
		$country_code_from_form = $_POST['data']['country_code'];
		$country_code_from_form = str_replace( '+', '', $country_code_from_form );
		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$meta_key = get_option( 'ihs_otp_mob_meta_key' );
		$meta_key = sanitize_text_field( $meta_key );
		$message_template = get_option( 'ihs_otp_reset_template' );
		$api_used =  ( get_option( 'ihs_api_type' ) ) ? get_option( 'ihs_api_type' ) : 'otp';

		$country_code_prefix = get_option( 'ihs_otp_mob_country_code' );
		$is_saved_with_country_code = get_option( 'ihs_no_saved_with_country' );
		$sender_id = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_otp_woo_sender_id' ) : get_option( 'ihs_otp_sender_id' );
		$new_password = mt_rand( 100000, 500000 );
		$auth_key = get_option( 'ihs_otp_auth_key' );
		$otp_length = strlen( $new_password );

		if ( 'msg91' === $api_used ) {
			$response = ihs_reset_pass_msg91( $message_template, $mobile_number, $new_password, $country_code_from_form );
		} else if ( 'twilio' === $api_used ){
			$twilio_reset_pass_msg = str_replace( '{OTP}', $new_password, $message_template );
			$mob_with_country_code = '+' . $country_code_from_form . $mobile_number;
			$response =  ihs_reset_password_sms_by_twilio( $twilio_reset_pass_msg, $mob_with_country_code );
		}


		$database_mob_number = '';

		if ( ( 'Yes' === $is_saved_with_country_code ) && $response ) {
			$database_mob_number = '+' . $country_code_from_form . $mobile_number;
		} else if ( 'No' === $is_saved_with_country_code && $response ){
			$database_mob_number = $mobile_number;
		}
		$args = array(
			'meta_key' => $meta_key,
			'meta_value' => $database_mob_number,
		);

		$user_obj = get_users( $args );
		$user_id = $user_obj[0]->data->ID;

		/**
		 * If response is true which means message was sent and,
		 * If user exists update the new password for him.
		 */
		if ( $response && $user_id ) {
			wp_set_password( $new_password, $user_id );
			$is_otp_send = true;
		} else {
			$is_otp_send = false;
			$new_password = '';
		}

		wp_send_json_success(
			array(
				'otp_pin_sent_to_js' => true,
				'data_recieved_from_js'    => $_POST,
				'user_id' => $user_id,
				'mobile_no' => strval( $mobile_number ),
				'country_from_form' => $country_code_from_form,
				'msg' => $message_template,
				'api' => $api_used,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_reset_ajax_hook', 'ihs_otp_send_new_pass' );
	add_action( 'wp_ajax_nopriv_ihs_otp_reset_ajax_hook', 'ihs_otp_send_new_pass' );
}

if ( ! function_exists( 'ihs_reset_password_sms_by_twilio' ) ) {
	/**
	 * Send Reset Password SMS using TWILIO route.
	 *
	 * @param {String} $message Message.
	 * @param {String} $phone Phone No.
	 *
	 * @return bool
	 */
	function ihs_reset_password_sms_by_twilio( $message, $phone ) {

		// Your Account SID and Auth Token from twilio.com/console
		$tw_sid_key = get_option( 'ihs_twilio_sid_key' );
		$tw_auth_token = get_option( 'ihs_twilio_auth_token' );
		$tw_phone_number = get_option( 'ihs_twilio_phone_number' );
		$sid = ( ! empty( $tw_sid_key ) ) ? $tw_sid_key : '';
		$token = ( ! empty( $tw_auth_token ) ) ? $tw_auth_token : '';
		$twilio_mob_no = ( ! empty( $tw_phone_number ) ) ? $tw_phone_number : '';

		$my_ihs_class = new IHS_Send_Programmable_SMS();
		$my_ihs_class->ihs_send_msg_using_twilio( $sid, $token, $phone, $twilio_mob_no, $message );

		return true;
	}
}

if ( ! function_exists( 'ihs_reset_pass_msg91' ) ) {
	/**
	 * Send New reset password via OTP route.
	 *
	 * @param $message_template
	 * @param $mob_number
	 * @param $otp_pin
	 *
	 * @return bool
	 */
	function ihs_reset_pass_msg91( $message_template, $mob_number, $otp_pin, $country_code_from_form ) {

		$auth_key = get_option( 'ihs_otp_auth_key' );
		$otp_length = strlen( $otp_pin );
		$message = str_replace( '{OTP}', $otp_pin, $message_template );
		$sender_id = ( ! empty( $woo_commerce_query ) ) ? get_option( 'ihs_otp_woo_sender_id' ) : get_option( 'ihs_otp_sender_id' );
		$country_code = str_replace( '+', '', $country_code_from_form );
		$mob_number_with_country_code = '+' . $country_code . $mob_number;
		$message = urlencode( $message );


		$url = "http://control.msg91.com/api/sendotp.php?otp_length=$otp_length&authkey=$auth_key&message=$message&sender=$sender_id&mobile=$mob_number_with_country_code&otp=$otp_pin";
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array(),
				'cookies' => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
//			echo "Something went wrong: $error_message";
			return false;
		} else if ( 'OK' === $response['response']['message'] ) {
			return true;
		}
	}
}
