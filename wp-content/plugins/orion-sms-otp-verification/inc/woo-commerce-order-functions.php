<?php
/**
 * Custom functions for sending Order SMS
 *
 * @package Orion SMS OTP Verification
 */

// Use the REST API Client to make requests to the Twilio REST API
//use Twilio\Rest\Client;


require IHS_OTP_PATH . 'vendor/twilio-php-master/Twilio/index.php';

if ( ! function_exists( 'ihs_get_order_details' ) ) {
    function ihs_get_order_details( $order_id ) {
	    $order = wc_get_order( $order_id );
	    $order_data = $order->get_data();
	    $ihs_order = array();
	    $ihs_order['id'] = $order_id;
	    $ihs_order['total'] = (int) $order_data['total'] + (int) $order_data['total_tax'];
	    $ihs_order['status'] = $order_data['status'];
	    $ihs_order['billing_name'] = $order_data['billing']['first_name'];
	    $ihs_order['phone'] = $order_data['billing']['phone'];
	    return $ihs_order;
    }
}

if ( ! function_exists( 'ihs_send_order_sms_pending' ) ) {
	/**
	 * Send SMS when order status changes to 'Order Pending'
	 * @param $order_id
	 */
	function ihs_send_order_sms_pending( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_pending' ) ) ) ? get_option( 'ihs_order_pending' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_pending_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_failed' ) ) {
	/**
	 * Send SMS when order status changes to 'Failed'
	 * @param $order_id
	 */
	function ihs_send_order_sms_failed( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_failed' ) ) ) ? get_option( 'ihs_order_failed' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_failed_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_hold' ) ) {
	/**
	 * Send SMS when order status changes to 'Order on Hold'
	 * @param $order_id
	 */
	function ihs_send_order_sms_hold( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_hold' ) ) ) ? get_option( 'ihs_order_hold' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_hold_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_processing' ) ) {
	/**
	 * Send SMS when order status changes to 'Order Processing'
	 * @param $order_id
	 */
	function ihs_send_order_sms_processing( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_processing' ) ) ) ? get_option( 'ihs_order_processing' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_processing_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_completed' ) ) {
	/**
	 * Send SMS when order status changes to 'Order Completed'
	 * @param $order_id
	 */
	function ihs_send_order_sms_completed( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_completed' ) ) ) ? get_option( 'ihs_order_completed' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_completed_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_refunded' ) ) {
	/**
	 * Send SMS when order status changes to 'Order Refunded'
	 * @param $order_id
	 */
	function ihs_send_order_sms_refunded( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_refunded' ) ) ) ? get_option( 'ihs_order_refunded' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_refunded_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}
if ( ! function_exists( 'ihs_send_order_sms_cancelled' ) ) {
	/**
	 * Send SMS when order status changes to 'Order Cancelled'
	 * @param $order_id
	 */
	function ihs_send_order_sms_cancelled( $order_id ) {
		$admin_mob_no = ( ! empty( get_option( 'ihs_admin_mob_no' ) ) ) ? get_option( 'ihs_admin_mob_no' ) : '';
		$order_pending_arr = ( ! empty( get_option( 'ihs_order_cancelled' ) ) ) ? get_option( 'ihs_order_cancelled' ) : '';
		$data = ihs_create_order_msg( $order_id, 'ihs_order_cancelled_template' );

		// If admin has selected admin as option to send sms, then send SMS to Admin
		if ( $order_pending_arr['admin'] && ! empty( $data['message'] && $admin_mob_no ) ) {
			ihs_send_order_sms( $data['message'], $admin_mob_no );
		}
		// If admin has selected customer as option to send sms, then send SMS to Customer
		if ( $order_pending_arr['customer'] && ! empty( $data['message'] ) && ! empty( $data['customer_phone'] ) ) {
			// Send SMS to Customer
			ihs_send_order_sms( $data['message'], $data['customer_phone'] );
		}
	}
}


add_action( 'woocommerce_order_status_pending', 'ihs_send_order_sms_pending', 10, 1);
add_action( 'woocommerce_order_status_failed', 'ihs_send_order_sms_failed', 10, 1);
add_action( 'woocommerce_order_status_on-hold', 'ihs_send_order_sms_hold', 10, 1);
add_action( 'woocommerce_order_status_processing', 'ihs_send_order_sms_processing', 10, 1);
add_action( 'woocommerce_order_status_completed', 'ihs_send_order_sms_completed', 10, 1);
add_action( 'woocommerce_order_status_refunded', 'ihs_send_order_sms_refunded', 10, 1);
add_action( 'woocommerce_order_status_cancelled', 'ihs_send_order_sms_cancelled', 10, 1);

if ( ! function_exists( 'ihs_create_order_msg' ) ) {
	/**
	 * Creates Order Message.
	 *
	 * @param $order_id
	 * @param $input_name
	 *
	 * @return array
	 */
	function ihs_create_order_msg( $order_id, $input_name ) {
		$order_details = ihs_get_order_details( $order_id );
		$user_message = ! empty( get_option( $input_name ) ) ? get_option( $input_name ) : '';

		$sms_placeholders = [ "{order_id}", "{order_total}", "{order_status}", "{billing_name}" ];
		$order_status = ucfirst( $order_details['status'] );
		$sms_placeholder_value = [ $order_id, $order_details['total'], $order_status, $order_details['billing_name'] ];
		$message = str_replace( $sms_placeholders, $sms_placeholder_value, $user_message );
		$message = ( ! empty( $message ) ) ? $message : '';

		return array(
			'message' => $message,
			'customer_phone' => $order_details['phone']
		);
	}
}

if ( ! function_exists( 'ihs_send_order_sms' ) ) {

	/**
	 * Handles Sending Order SMS.
	 *
	 * @param $message
	 * @param $phone
	 */
	function ihs_send_order_sms( $message, $phone ) {
		$api_used =  ( get_option( 'ihs_api_type' ) ) ? get_option( 'ihs_api_type' ) : 'otp';
		// If twilio api used
		if ( 'twilio' === $api_used ) {
			ihs_send_order_sms_by_twilio( $message, $phone );

		} else if ( 'msg91' === $api_used ) {
			ihs_send_order_sms_by_msg91( $message, $phone );
		}
	}
}

if ( ! function_exists( 'ihs_send_order_sms_by_msg91' ) ) {
	/**
	 * Send order SMS using MSG91 route.
	 *
	 * @param {String} $message Message.
	 * @param {String} $phone Phone No.
	 *
	 * @return bool
	 */
	function ihs_send_order_sms_by_msg91( $message, $phone ) {
		$curl         = curl_init();
		$sender_id    = get_option( 'ihs_otp_woo_sender_id' );
		$auth_key     = get_option( 'ihs_otp_auth_key' );
		$country_code = get_option( 'ihs_otp_woo_country_code' );
		curl_setopt_array( $curl, array(
			CURLOPT_URL            => "http://api.msg91.com/api/v2/sendsms",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => "{ \"sender\": \"$sender_id\", \"route\": \"4\", \"country\": \"$country_code\", \"sms\": [ { \"message\": \"$message\", \"to\": [ \"$phone\" ] } ] }",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER     => array(
				"authkey: $auth_key",
				"content-type: application/json"
			),
		) );

		$response = curl_exec( $curl );
		$err      = curl_error( $curl );
		curl_close( $curl );

		if ( $err ) {
			//echo "cURL Error #:" . $err;
		} else {
			$success = true;

			return true;
		}
	}
}

if ( ! function_exists( 'ihs_send_order_sms_by_twilio' ) ) {
	/**
	 * Send order SMS using TWILIO route.
	 *
	 * @param {String} $message Message.
	 * @param {String} $phone Phone No.
	 *
	 * @return bool
	 */
	function ihs_send_order_sms_by_twilio( $message, $phone ) {

	// Your Account SID and Auth Token from twilio.com/console
		$sid = ( ! empty( get_option( 'ihs_twilio_sid_key' ) ) ) ? get_option( 'ihs_twilio_sid_key' ) : '';
		$token = ( ! empty( get_option( 'ihs_twilio_auth_token' ) ) ) ? get_option( 'ihs_twilio_auth_token' ) : '';
		$twilio_mob_no = ( ! empty( get_option( 'ihs_twilio_phone_number' ) ) ) ? get_option( 'ihs_twilio_phone_number' ) : '';
		$country_code = get_option( 'ihs_otp_woo_country_code' );
		$mob_no_with_country_code = '+' . $country_code . $phone;

		$my_ihs_class = new IHS_Send_Programmable_SMS();
		$my_ihs_class->ihs_send_msg_using_twilio( $sid, $token, $mob_no_with_country_code, $twilio_mob_no, $message );
		return true;
	}
}

