<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class XLWUEV_Woocommerce_Confirmation_Email_Public {

	private static $ins = null;
	public $pl_url;
	public $my_account_id;
	private $user_id;
	private $email_id;
	public $is_checkout_page = false;
	public $user_login;
	public $user_email;
	public $is_user_verified = '';
	public $is_user_created = false;
	public $is_notice_shown_at_order_received_page = false;
	public $is_user_already_verified = false;
	public $is_new_user_email_sent = false;
	public $is_user_made_from_myaccount_page = false;
	public $should_verification_email_be_send = true;
	public $should_notice_be_shown = true;

	public function __construct() {
		$this->my_account = get_option( 'woocommerce_myaccount_page_id' );

		if ( '' === $this->my_account ) {
			$this->my_account = get_option( 'page_on_front' );
		}

		$this->pl_url = untrailingslashit( plugin_dir_url( XLWUEV_PLUGIN_FILE ) );
		add_shortcode( 'wcemailverificationcode', array( $this, 'wc_email_verification_code' ) );
		add_shortcode( 'wcemailverificationmessage', array( $this, 'wuev_shortcode_xlwuev_verification_page' ) );
		add_action( 'woocommerce_created_customer_notification', array( $this, 'new_user_registration_from_registration_form' ), 10, 3 );
		add_filter( 'woocommerce_registration_redirect', array( $this, 'redirect_new_user' ) );
		add_action( 'wp', array( $this, 'authenticate_user_by_email' ) );
		add_action( 'wp', array( $this, 'show_notification_message' ) );
		add_action( 'wp', array( $this, 'resend_verification_email' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wuev_public_js' ) );
		add_action( 'wp_login', array( $this, 'custom_form_login_check' ), 10, 1 );
		add_action( 'user_register', array( $this, 'custom_form_user_register' ), 10, 1 );
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'new_user_registeration_from_checkout_form' ), 10, 2 );
		add_action( 'woocommerce_checkout_process', array( $this, 'set_checkout_page' ), 11, 1 );
		add_action( 'woocommerce_email_footer', array( $this, 'append_content_before_woocommerce_footer' ), 9, 1 );
		add_action( 'woocommerce_register_post', array( $this, 'woocommerce_my_account_page' ), 10, 1 );
		add_action( 'set_auth_cookie', array( $this, 'custom_form_login_check_with_cookie' ), 10, 5 );
		add_filter( 'send_email_change_email', array( $this, 'unverify_user_account' ), 99, 2 );
	}

	public static function instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/*
	 * This function sets the is_user_made_from_myaccount_page to true if user is made from myaccount page of woocommerce.
	 */
	public function woocommerce_my_account_page( $username ) {
		$this->is_user_made_from_myaccount_page = true;
	}

	/**
	 * This function is executed when a new user is made from the woocommerce registration form in the myaccount page.
	 * Its hooked into 'woocommerce_registration_auth_new_customer' filter.
	 *
	 * @param $customer
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function new_user_registration_from_registration_form( $user_id, $new_customer_data = array(), $password_generated = false ) {
		if ( false === $this->is_new_user_email_sent && $this->should_verification_email_be_send ) {
			$this->new_user_registration( $user_id );
			$this->is_new_user_email_sent = true;
		}
	}

	/*
	 * This function is executed when a new user is made from the checkout page of the woocommerce.
	 * Its hooked into 'woocommerce_checkout_update_user_meta' action.
	 */
	public function new_user_registeration_from_checkout_form( $customer_id, $data ) {
		if ( is_array( $data ) && count( $data ) > 0 ) {
			if ( '0' != $customer_id ) {
				if ( '1' == $data['createaccount'] ) {
					if ( false === $this->is_new_user_email_sent && $this->should_verification_email_be_send ) {
						$this->new_user_registration( $customer_id );
						$this->is_new_user_email_sent = true;
					}
				}
			}
		}
	}

	/*
	 * This function sends a new verification email upon user registration from any custom registration form.
	 */
	public function custom_form_user_register( $user_id ) {
		$user   = get_user_by( 'id', $user_id );
		$status = get_user_meta( (int) $user_id, 'wcemailverified', true );

		if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
			if ( 'true' !== $status ) {
				if ( false === $this->is_new_user_email_sent ) {
					if ( false === $this->is_checkout_page && false === $this->is_user_made_from_myaccount_page && $this->should_verification_email_be_send ) {
						XlWUEV_Common::$is_user_made_from_custom_form = true;
						$this->new_user_registration( $user_id );
						$this->is_new_user_email_sent = true;
					}
				}
			}
		}
	}

	/*
	 * This function is executed just after a new user is made from woocommerce registration form in myaccount page.
	 * Its hooked into 'woocommerce_registration_redirect' filter.
	 * If restrict user setting is enabled from the plugin settings screen, then this function will logs out the user.
	 */
	public function redirect_new_user( $redirect ) {
		if ( true === $this->is_new_user_email_sent && false === XlWUEV_Common::$is_xlwuev_resend_link_clicked && defined( 'WC_DOING_AJAX' ) === false && false === is_order_received_page() ) {
			$redirect                = add_query_arg( array(
				'xlrm' => base64_encode( $this->user_id ),
			), $redirect );
			$is_xlwuev_restrict_user = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );
			if ( '1' == $is_xlwuev_restrict_user ) {
				wp_logout();
			}
		}

		return $redirect;
	}

	/*
	 * This function verifies the user when the user clicks on the verification link in its email.
	 * If automatic login setting is enabled in plugin setting screen, then the user is forced loggedin.
	 */
	public function authenticate_user_by_email() {

		if ( isset( $_GET['woo_confirmation_verify'] ) && '' !== $_GET['woo_confirmation_verify'] ) { // WPCS: input var ok, CSRF ok.
			$user_meta = explode( '@', base64_decode( $_GET['woo_confirmation_verify'] ) ); // WPCS: input var ok, CSRF ok.
			if ( 'true' === get_user_meta( (int) $user_meta[1], 'wcemailverified', true ) ) {
				$this->is_user_already_verified = true;
			}

			$verified_code = get_user_meta( (int) $user_meta[1], 'wcemailverifiedcode', true );

			if ( ! empty( $verified_code ) && $verified_code === $user_meta[0] ) {
				XlWUEV_Common::$wuev_user_id = (int) $user_meta[1];
				$allow_automatic_login       = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_automatic_user_login' );

				update_user_meta( (int) $user_meta[1], 'wcemailverified', 'true' );
				do_action( 'xlwuev_on_email_verification', (int) $user_meta[1] );

				if ( '1' == $allow_automatic_login ) {
					$this->please_login_email_message();
				} elseif ( '2' == $allow_automatic_login ) {
					$this->allow_automatic_login( (int) $user_meta[1] );
					$this->please_login_email_message();
				}
			}
		}
	}

	/*
	 * This function shows the notification messages based on get parameters.
	 * Shows messages for new user registration, user restriction, verification success message, message in user dashboard.
	 */
	public function show_notification_message() {
		if ( isset( $_GET['xlrm'] ) && '' !== $_GET['xlrm'] ) { // WPCS: input var ok, CSRF ok.
			XlWUEV_Common::$wuev_user_id = base64_decode( $_GET['xlrm'] ); // WPCS: input var ok, CSRF ok.
			$registration_message        = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_registration_message' ) );
			if ( false === wc_has_notice( $registration_message, 'notice' ) ) {
				wc_add_notice( $registration_message, 'notice' );
			}
		} elseif ( ! is_admin() && is_user_logged_in() && defined( 'WC_DOING_AJAX' ) === false ) {
			global $current_user;
			$user_roles = $current_user->roles;
			$user_role  = array_shift( $user_roles );

			if ( 'customer' === $user_role ) {
				$user_id                               = get_current_user_id();
				XlWUEV_Common::$wuev_user_id           = $user_id;
				XlWUEV_Common::$wuev_myaccount_page_id = $this->my_account;
				$this->is_user_verified                = get_user_meta( $user_id, 'wcemailverified', true );
				$order_received_page                   = is_order_received_page();
				$order_pay_page                        = is_checkout_pay_page();

				if ( false === $order_received_page && empty( $this->is_user_verified ) && '1' == XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' ) ) {
					if ( false === $order_pay_page ) {
						wp_logout();
						$this->please_confirm_email_message( $user_id );
						// if not order , then redirect to myaccount page
						if ( false === $order_received_page ) {
							$redirect_url = add_query_arg( array(
								'xlsm' => base64_encode( $user_id ),
							), get_the_permalink( $this->my_account ) );
							wp_safe_redirect( $redirect_url );
							exit;
						}
					}
				}

				if ( '2' == XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' ) ) {
					$this->please_confirm_email_message( $user_id );
				}

				if ( $order_received_page ) {
					$order_id = $this->get_order_id();

					if ( 'true' !== $this->is_user_verified ) {
						if ( false === WC()->session->has_session() ) {
							WC()->session->set_customer_session_cookie( true );
						}

						$this->should_notice_be_shown = apply_filters( 'xlwuev_order_details', true, $order_id );
						$registration_message         = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_registration_message' ) );

						if ( false === wc_has_notice( $registration_message, 'notice' ) && $this->should_notice_be_shown ) {
							wc_add_notice( $registration_message, 'notice' );
						}
					}
				}
			}
		}
		if ( isset( $_GET['xlsm'] ) && '' !== $_GET['xlsm'] ) { // WPCS: input var ok, CSRF ok.
			XlWUEV_Common::$wuev_user_id = base64_decode( $_GET['xlsm'] ); // WPCS: input var ok, CSRF ok.
			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			$message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_email_error_message_not_verified_outside' ) );
			if ( false === wc_has_notice( $message, 'notice' ) ) {
				wc_add_notice( $message, 'notice' );
			}
		}
		if ( isset( $_GET['xlvm'] ) && '' !== $_GET['xlvm'] ) { // WPCS: input var ok, CSRF ok.
			$success_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_success_message' ) );
			wc_add_notice( $success_message, 'notice' );
		}
	}

	/*
	 * Return order id from get parameter
	 */
	public function get_order_id() {
		if ( isset( $_GET['order-received'] ) ) { // WPCS: input var ok, CSRF ok.
			$order_id = $_GET['order-received']; // WPCS: input var ok, CSRF ok.
		} else {
			$url           = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // WPCS: input var ok, CSRF ok.
			$template_name = strpos( $url, '/order-received/' ) === false ? '/view-order/' : '/order-received/';
			if ( strpos( $url, $template_name ) !== false ) {
				$start      = strpos( $url, $template_name );
				$first_part = substr( $url, $start + strlen( $template_name ) );
				$order_id   = substr( $first_part, 0, strpos( $first_part, '/' ) );
			}
		}

		return $order_id;
	}

	/*
	 * This function localizes the plugin version and plugin settings.
	 */
	public function wuev_public_js() {
		wp_enqueue_script( XLWUEV_SLUG . '-custom-js', $this->pl_url . '/assets/js/woo-confirmation-email-admin.js', false, XLWUEV_VERSION, true );
		$wuev_version = array(
			'plugin_version' => XLWUEV_VERSION,
		);
		wp_localize_script( XLWUEV_SLUG . '-custom-js', 'xlwuev', $wuev_version );
		wp_localize_script( XLWUEV_SLUG . '-custom-js', 'xlwuev_settings', preg_replace( '/\\\\/', '', json_encode( XlWUEV_Common::$plugin_settings ) ) );
	}

	/**
	 * This function appends the verification link to the bottom of the welcome email of woocommerce.
	 *
	 * @param $emailclass_object
	 */
	public function append_content_before_woocommerce_footer( $emailclass_object ) {
		if ( isset( $emailclass_object->id ) && ( 'customer_new_account' === $emailclass_object->id ) ) {

			$verification_email_type = XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_verification_type' );
			if ( '2' == XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_verification_method' ) ) {
				if ( '2' == $verification_email_type ) {
					$user_id                               = $emailclass_object->object->data->ID;
					$this->user_id                         = $user_id;
					XlWUEV_Common::$wuev_user_id           = $user_id;
					XlWUEV_Common::$wuev_user_login        = $emailclass_object->object->data->user_login;
					XlWUEV_Common::$wuev_display_name      = $emailclass_object->object->data->display_name;
					XlWUEV_Common::$wuev_user_email        = $emailclass_object->object->data->user_email;
					XlWUEV_Common::$wuev_myaccount_page_id = $this->my_account;
					$is_secret_code_present                = get_user_meta( $user_id, 'wcemailverifiedcode', true );

					if ( '' === $is_secret_code_present ) {
						$secret_code = md5( $user_id . time() );
						update_user_meta( $user_id, 'wcemailverifiedcode', $secret_code );
					}
					$email_body = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_email_body' ) );
					$email_body = apply_filters( 'the_content', $email_body );
					echo $email_body;

					if ( false === XlWUEV_Common::$is_test_email ) {
						do_action( 'xlwuev_trigger_after_email', $emailclass_object->object->data->user_email );
					}
				}
			}
		}
	}

	/*
	 * This function gets executed from different places when ever a new user is registered or resend verifcation email is sent.
	 */
	public function new_user_registration( $user_id ) {
		$current_user                          = get_user_by( 'id', $user_id );
		$this->user_id                         = $current_user->ID;
		$this->email_id                        = $current_user->user_email;
		$this->user_login                      = $current_user->user_login;
		$this->user_email                      = $current_user->user_email;
		$this->is_user_created                 = true;
		XlWUEV_Common::$wuev_user_login        = $current_user->user_login;
		XlWUEV_Common::$wuev_display_name      = $current_user->display_name;
		XlWUEV_Common::$wuev_user_email        = $current_user->user_email;
		XlWUEV_Common::$wuev_user_id           = $current_user->ID;
		XlWUEV_Common::$wuev_myaccount_page_id = $this->my_account;
		$is_secret_code_present                = get_user_meta( $this->user_id, 'wcemailverifiedcode', true );

		if ( '' === $is_secret_code_present ) {
			$secret_code = md5( $this->user_id . time() );
			update_user_meta( $user_id, 'wcemailverifiedcode', $secret_code );
		}

		XlWUEV_Common::code_mail_sender( $current_user->user_email );
		$this->is_new_user_email_sent = true;

	}

	/*
	 * This function executes just after the user logged in. If restrict user setting is enabled in the plugin settings screen, the the user is force
	 * logged out.
	 */
	public function custom_form_login_check( $user_login ) {
		$user = get_user_by( 'login', $user_login );
		if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
			if ( 'true' !== get_user_meta( $user->ID, 'wcemailverified', true ) ) {
				$is_force_login_enabled = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );

				if ( '1' == $is_force_login_enabled ) {
					wp_logout();
					if ( false === is_order_received_page() && false === $this->is_checkout_page ) {
						$redirect_url = add_query_arg( array(
							'xlsm' => base64_encode( $user->ID ),
						), apply_filters( 'xlwuev_custom_form_login_check_redirect_url', get_the_permalink( $this->my_account ) ) );
						wp_safe_redirect( $redirect_url );
						exit;
					}
				}
			}
		}
	}

	/*
	 * This function executes just after if the user is force logged in. If restrict user setting is enabled in the plugin settings screen, the the user is force
	 * logged out.
	 */
	public function custom_form_login_check_with_cookie( $auth_cookie, $expire, $expiration, $user_id, $scheme ) {
		$order_received_page   = is_order_received_page();
		$order_pay_page        = is_checkout_pay_page();
		$allow_automatic_login = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_automatic_user_login' );

		if ( false == $order_received_page && false == $order_pay_page && '1' == $allow_automatic_login ) {
			$user                      = get_user_by( 'ID', $user_id );
			$user_registered_timestamp = strtotime( $user->data->user_registered );
			$current_timestamp         = time();

			if ( $current_timestamp - $user_registered_timestamp < 60 ) {
				$is_new_user = true;
			} else {
				$is_new_user = false;
			}

			if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
				if ( 'true' !== get_user_meta( $user->ID, 'wcemailverified', true ) ) {
					$is_force_login_enabled = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );

					if ( '1' == $is_force_login_enabled ) {
						wp_clear_auth_cookie();
						if ( false === is_order_received_page() && false === $this->is_checkout_page ) {
							if ( $is_new_user ) {
								$redirect_url = add_query_arg( array(
									'xlrm' => base64_encode( $user->ID ),
								), get_the_permalink( $this->my_account ) );
							} else {
								$redirect_url = add_query_arg( array(
									'xlsm' => base64_encode( $user->ID ),
								), get_the_permalink( $this->my_account ) );
							}

							$error_validation_page = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_verification_error_page' );
							if ( '2' == $error_validation_page ) {
								$error_validation_page_id = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_verification_error_page_id' );
								$redirect_url             = add_query_arg( array(
									'xlsm' => base64_encode( $user->ID ),
								), get_the_permalink( $error_validation_page_id ) );
							}

							wp_safe_redirect( $redirect_url );
							exit;
						}
					}
				}
			}
		}
	}

	/*
	 * This function unverifies a user's email because the user had changed its email ID. So user has to again verify its email.
	 */

	public function unverify_user_account( $bool, $user ) {
		if ( $bool ) {
			delete_user_meta( $user['ID'], 'wcemailverified' );
		}

		return $bool;
	}

	/*
	 * This function sets the is_checkout_page to true if the current page is woocommerce checkout page.
	 */
	public function set_checkout_page() {
		$this->is_checkout_page = true;
		if ( isset( $_POST['payment_method'] ) && '' != $_POST['payment_method'] ) {
			$this->should_verification_email_be_send = apply_filters( 'xlwuev_order_payment_method', true, $_POST ); // WPCS: input var ok, CSRF ok.
		}
	}

	/*
	 * This function adds woocommerce notices.
	 */
	public function please_confirm_email_message( $user_id ) {
		if ( false === WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		if ( empty( $this->is_user_verified ) ) {
			if ( $this->is_user_created ) {
				if ( true === $this->is_checkout_page ) {
					$registration_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_registration_message' ) );
					if ( false === is_order_received_page() ) {
						wc_add_notice( $registration_message, 'notice' );
					}
				}
			} else {
				$is_xlwuev_restrict_user = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );
				$message                 = '';
				if ( '1' == $is_xlwuev_restrict_user ) {
					$message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_email_error_message_not_verified_outside' ) );

				} elseif ( '2' == $is_xlwuev_restrict_user ) {
					$message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_email_error_message_not_verified_inside' ) );
				}
				if ( '' !== $message ) {
					if ( false == wc_has_notice( $message, 'notice' ) ) {
						wc_add_notice( $message, 'notice' );
					}
				}
			}
		}
	}

	/*
	 * This function shows the verification success messages.
	 */
	public function please_login_email_message() {
		if ( false === WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		$verified = get_user_meta( XlWUEV_Common::$wuev_user_id, 'wcemailverified', true );

		if ( 'true' === $verified && $this->is_user_already_verified ) {
			$already_verified_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_verification_already_done' ) );
			wc_add_notice( $already_verified_message, 'notice' );
		} else {
			$success_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_success_message' ) );
			if ( '1' == XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_verification_page' ) ) {
				wc_add_notice( $success_message, 'notice' );
			} else {
				$hyperlink = add_query_arg( array(
					'xlvm' => true,
				), get_the_permalink( XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_verification_page_id' ) ) );
				wp_safe_redirect( $hyperlink );
				exit();
			}
		}
	}

	/**
	 * This function sends a new verification email to user if the user clicks on 'resend verification email' link.
	 * If the email is already verified then it redirects to my-account page
	 */
	public function resend_verification_email() {
		if ( isset( $_GET['wc_confirmation_resend'] ) && '' !== $_GET['wc_confirmation_resend'] ) { // WPCS: input var ok, CSRF ok.
			$user_id = base64_decode( $_GET['wc_confirmation_resend'] ); // WPCS: input var ok, CSRF ok.

			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			$verified = get_user_meta( $user_id, 'wcemailverified', true );

			if ( 'true' === $verified ) {
				$already_verified_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_verification_already_done' ) );
				wc_add_notice( $already_verified_message, 'notice' );
			} else {
				XlWUEV_Common::$wuev_user_id                  = $user_id;
				XlWUEV_Common::$wuev_myaccount_page_id        = $this->my_account;
				XlWUEV_Common::$is_xlwuev_resend_link_clicked = true;
				$this->new_user_registration( $user_id );
				$new_verification_link = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_new_verification_link' ) );
				wc_add_notice( $new_verification_link, 'notice' );
			}
		}
	}

	/**
	 * This function generates the verification link from the shortocde [wcemailverificationcode] and returns the link.
	 * @return string
	 */
	public function wc_email_verification_code() {
		$secret      = get_user_meta( $this->user_id, 'wcemailverifiedcode', true );
		$create_link = $secret . '@' . $this->user_id;
		$hyperlink   = add_query_arg( array(
			'woo_confirmation_verify' => base64_encode( $create_link ),
		), get_the_permalink( $this->my_account ) );
		$link_text   = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_new_verification_link_text' ) );
		$link        = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

		return $link;
	}

	/**
	 * This function adds the verification message on the custom verification page selected by the admin under misc settings tab of the plugin.
	 */
	public function wuev_shortcode_xlwuev_verification_page() {
		if ( isset( $_GET['xlvm'] ) && '' != $_GET['xlvm'] ) { // WPCS: input var ok, CSRF ok.
			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			wc_add_notice( __( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_success_message' ), 'woo-confirmation-email' ), 'notice' );
		}
	}

	/**
	 * @param mixed $user_id
	 */
	public function set_user_id( $user_id ) {
		$this->user_id = $user_id;
	}

	/*
	 * This function force login a user.
	 */
	public function allow_automatic_login( $user_id ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
	}
}

XLWUEV_Woocommerce_Confirmation_Email_Public::instance();
