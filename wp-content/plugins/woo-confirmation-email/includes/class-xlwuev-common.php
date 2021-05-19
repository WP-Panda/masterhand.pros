<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XlWUEV_Common {

	public static $is_new_version_saved = false;
	public static $is_new_version_activated = false;
	public static $plugin_settings = null;
	public static $wuev_user_login = null;
	public static $wuev_display_name = null;
	public static $wuev_user_email = null;
	public static $wuev_user_id = null;
	public static $wuev_myaccount_page_id = null;
	public static $is_xlwuev_resend_link_clicked = false;
	public static $is_test_email = false;
	public static $is_test_email_sent = false;
	public static $is_user_made_from_custom_form = false;
	public static $is_force_debug = false;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_plugin_settings' ), 10 );
		add_filter( 'xlwuev_decode_html_content', array( __CLASS__, 'decode_html_content' ), 1 );
		add_filter( 'xlwuev_the_content', array( __CLASS__, 'add_do_shortcode' ) );

		/** Compatibility with social login, verify user if login via social login plugin */
		add_action( 'wc_social_login_before_create_user', array( __CLASS__, 'set_social_profile' ) );
		add_action( 'woocommerce_created_customer', array( __CLASS__, 'verify_user_forcefully' ), 10, 2 );
	}

	/*
	 * This function removes backslashes from the textfields and textareas of the plugin settings.
	 */
	public static function decode_html_content( $content ) {
		if ( empty( $content ) ) {
			return '';
		}
		$content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );

		return html_entity_decode( stripslashes( $content ) );
	}

	public static function add_do_shortcode( $content ) {
		return do_shortcode( $content );
	}

	public static function set_social_profile( $profile ) {
		add_filter( 'wc_social_login_' . $profile->get_provider_id() . '_new_user_data', array( __CLASS__, 'add_custom_data_for_user' ) );
	}

	public static function add_custom_data_for_user( $userdata ) {
		$userdata['xlwuev_verify_user'] = 'yes';

		return $userdata;
	}

	public static function verify_user_forcefully( $customer_id, $userdata ) {
		if ( isset( $userdata['xlwuev_verify_user'] ) && 'yes' === $userdata['xlwuev_verify_user'] ) {
			update_user_meta( $customer_id, 'wcemailverified', 'true' );
		}
	}

	/*
	 * This function saves all the settings into a property which is used everywhere in public and admin.
	 */
	public static function check_plugin_settings() {
		$plugin_tabs          = XlWUEV_Common::get_default_plugin_settings_tabs();
		$plugin_tabs_settings = array();
		foreach ( $plugin_tabs as $key1 => $value1 ) {
			$is_tab_have_settings = XlWUEV_Common::get_tab_options( $key1 );
			if ( is_array( $is_tab_have_settings ) && count( $is_tab_have_settings ) > 0 ) {
				$plugin_tabs_settings[ $key1 ] = $is_tab_have_settings;
			}
		}
		self::$plugin_settings = $plugin_tabs_settings;

		self::$is_new_version_activated = self::is_new_version_activated();
	}

	public static function get_default_plugin_settings_tabs() {
		$my_plugin_tabs = array(
			'wuev-email-template'    => __( 'Email Template', 'woo-confirmation-email' ),
			'wuev-test-email'        => __( 'Test Verification Email', 'woo-confirmation-email' ),
			'wuev-messages'          => __( 'Verification Messages', 'woo-confirmation-email' ),
			'wuev-bulk-verification' => __( 'Bulk Verification', 'woo-confirmation-email' ),
			'wuev-general-settings'  => __( 'Misc Settings', 'woo-confirmation-email' ),
		);

		return $my_plugin_tabs;
	}

	/*
	 * This function returns all the default options of the plugin.
	 */

	public static function get_tab_options( $tab_slug ) {
		$tab_options            = array();
		$plugin_default_options = self::get_default_plugin_options();

		if ( isset( $plugin_default_options[ $tab_slug ] ) ) {
			$default_options    = $plugin_default_options[ $tab_slug ];
			$default_options_db = self::get_plugin_saved_settings( $tab_slug );
			if ( '' === $default_options_db ) {
				$tab_options = $default_options;
			} else {
				foreach ( $default_options as $key1 => $value1 ) {
					if ( isset( $default_options_db[ $key1 ] ) && '' !== $default_options_db[ $key1 ] ) {
						$tab_options[ $key1 ] = $default_options_db[ $key1 ];
					} else {
						$tab_options[ $key1 ] = $value1;
					}
				}
			}
		}

		/**
		 * Compatibility with WPML
		 */
		if ( function_exists( 'icl_t' ) ) {
			if ( ! is_admin() ) {
				if ( is_array( $tab_options ) && count( $tab_options ) > 0 ) {
					$temp_tab_options = array();
					foreach ( $tab_options as $key1 => $value1 ) {
						if ( in_array( $key1, self::get_non_icl_settings(), true ) ) {
							$temp_tab_options[ $key1 ] = $value1;
						} else {
							$temp_tab_options[ $key1 ] = icl_t( 'admin_texts_' . $tab_slug, '[' . $tab_slug . ']' . $key1, $value1 );
						}
					}
					$tab_options = $temp_tab_options;
				}
			}
		}

		return $tab_options;
	}

	/*
	 * This function returns those fields which are bypassed in wpml for conversion in other languages.
	 */

	public static function get_default_plugin_options() {
		$xlwuev_email_body_editor = get_option( 'wc-email-header' );
		if ( '' === $xlwuev_email_body_editor ) {
			$email_template_header    = __( 'Demo Store Email Verification', 'woo-confirmation-email' );
			$email_template_body      = __( 'Please Verify your email Account', 'woo-confirmation-email' );
			$xlwuev_email_body_editor = '<html><body><table style="width: 700px; margin: auto; text-align: center; border: 1px solid #eee; font-family: sans-serif;"> <thead> <tr> <td style="color: white; font-size: 33px; background: #1266ae; text-align: center; padding: 26px 0px;">' . $email_template_header . '</td> </tr> </thead> <tbody> <tr> <td style="padding: 22px; font-size: 19px;">' . $email_template_body . '</td> </tr> <tr> <td style="padding: 0 22px 10px 22px; font-size: 19px;">{{wcemailverificationcode}}</td> </tr> </tbody> <tfoot> <tr> <td style="color: #000; padding: 15px; background: #e4e4e4;">{{sitename}}</td> </tr> </tfoot> </table></body></html>';
		}
		$default_options = array(
			'wuev-general-settings' => array(
				'xlwuev_restrict_user'                            => 1,
				'xlwuev_verification_page'                        => 1,
				'xlwuev_verification_page_id'                     => '',
				'xlwuev_verification_error_page'                  => 1,
				'xlwuev_verification_error_page_id'               => '',
				'xlwuev_email_error_message_not_verified_outside' => __( 'You need to verify your account before login. {{xlwuev_resend_link}}', 'woo-confirmation-email' ),
				'xlwuev_email_error_message_not_verified_inside'  => __( 'You need to verify your account. {{xlwuev_resend_link}}', 'woo-confirmation-email' ),
				'xlwuev_automatic_user_login'                     => 1,
			),
			'wuev-email-template'   => array(
				'xlwuev_verification_method' => 1,
				'xlwuev_verification_type'   => 1,
				'xlwuev_email_subject'       => __( 'Account Verification ({{xlwuev_display_name}})', 'woo-confirmation-email' ),
				'xlwuev_email_heading'       => __( 'Please Verify Your Email Account ({{xlwuev_display_name}})', 'woo-confirmation-email' ),
				'xlwuev_email_body'          => __( 'Please Verify your Email Account by clicking on the following link. {{wcemailverificationcode}}', 'woo-confirmation-email' ),
				'xlwuev_email_header'        => $xlwuev_email_body_editor,
			),
			'wuev-messages'         => array(
				'xlwuev_email_success_message'            => __( 'Your Email is verified!', 'woo-confirmation-email' ),
				'xlwuev_email_registration_message'       => __( 'We sent you a verification email. Check and verify your account. {{xlwuev_resend_link}}', 'woo-confirmation-email' ),
				'xlwuev_email_resend_confirmation'        => __( 'Resend Confirmation Email', 'woo-confirmation-email' ),
				'xlwuev_email_verification_already_done'  => __( 'Your Email is already verified', 'woo-confirmation-email' ),
				'xlwuev_email_new_verification_link'      => __( 'A new verification link is sent. Check email. {{xlwuev_resend_link}}', 'woo-confirmation-email' ),
				'xlwuev_email_new_verification_link_text' => __( 'Click here to verify', 'woo-confirmation-email' ),
			),

		);

		return $default_options;
	}

	/*
	 * This function returns all the tabs of the plugin.
	 */

	public static function get_plugin_saved_settings( $option_key ) {
		return get_option( $option_key );
	}

	/*
	 * This function returns the setting of a single tab.
	 */

	public static function get_non_icl_settings() {
		$non_icl_keys = array(
			'xlwuev_restrict_user',
			'xlwuev_verification_page',
			'xlwuev_verification_method',
			'xlwuev_verification_type',
			'xlwuev_automatic_user_login',
		);

		return $non_icl_keys;
	}

	/*
	 * This function returns the values of all the fields of a single tab.
	 * It return default values if user has not saved the tab.
	 */

	public static function is_new_version_activated() {
		return get_option( 'new_plugin_activated' );
	}

	public static function is_new_version_saved() {
		return get_option( 'is_new_version_saved', '0' );
	}

	public static function update_is_new_version() {
		update_option( 'is_new_version_saved', '1', false );
	}

	public static function code_mail_sender( $email ) {
		$result                      = false;
		$verification_email_template = XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_verification_method' );
		$email_subject               = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_email_subject' ) );
		$email_heading               = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_email_heading' ) );
		$email_body                  = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_email_body' ) );
		$verification_email_type     = XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_verification_type' );
		$email_body_temp             = $email_body;

		if ( '1' == $verification_email_template ) {
			$message    = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-email-template', 'xlwuev_email_header' ) );
			$email_body = apply_filters( 'xlwuev_the_content', $message );
		} elseif ( '2' == $verification_email_template ) {
			$email_body = apply_filters( 'xlwuev_the_content', $email_body );
			if ( '1' == $verification_email_type || XlWUEV_Common::$is_xlwuev_resend_link_clicked ) {
				$mailer = WC()->mailer();
				ob_start();
				$mailer->email_header( $email_heading );
				echo $email_body;
				$mailer->email_footer();
				$email_body            = ob_get_clean();
				$email_abstract_object = new WC_Email();
				$email_body            = apply_filters( 'woocommerce_mail_content', $email_abstract_object->style_inline( wptexturize( $email_body ) ) );
			}
		}

		if ( '2' == $verification_email_template && '2' == $verification_email_type && true == self::$is_user_made_from_custom_form ) {
			$email_body = $email_body_temp;
			$mailer     = WC()->mailer();
			ob_start();
			$mailer->email_header( $email_heading );
			echo $email_body;
			$mailer->email_footer();
			$email_body            = ob_get_clean();
			$email_abstract_object = new WC_Email();
			$email_body            = apply_filters( 'woocommerce_mail_content', $email_abstract_object->style_inline( wptexturize( $email_body ) ) );
		}

		$email_body = apply_filters( 'xlwuev_decode_html_content', $email_body );

		if ( false === XlWUEV_Common::$is_test_email ) {
			$filtered_values = apply_filters( 'xlwuev_modify_before_email', array(
				'email'         => $email,
				'email_subject' => $email_subject,
				'email_body'    => $email_body,
			) );
			extract( $filtered_values );
		}

		$mailer = WC()->mailer();
		if ( '1' == $verification_email_template || XlWUEV_Common::$is_xlwuev_resend_link_clicked || true === self::$is_user_made_from_custom_form ) {
			$result = $mailer->send( $email, $email_subject, $email_body );
		} elseif ( '2' == $verification_email_template && '1' == $verification_email_type ) {
			$result = $mailer->send( $email, $email_subject, $email_body );
		}

		if ( false === XlWUEV_Common::$is_test_email ) {
			do_action( 'xlwuev_trigger_after_email', $email );
		}

		return $result;
	}

	/**
	 * This function returns a single field setting of the plugin.
	 *
	 * @param $tab_slug
	 * @param $field_key
	 *
	 * @return mixed
	 */
	public static function get_setting_value( $tab_slug, $field_key ) {
		return XlWUEV_Common::$plugin_settings[ $tab_slug ][ $field_key ];
	}

	/**
	 * Maybe try and parse content to found the xlwuev merge tags
	 * And converts them to the standard wp shortcode way
	 * So that it can be used as do_shortcode in future
	 *
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	public static function maybe_parse_merge_tags( $content = '' ) {
		$get_all      = self::get_all_tags();
		$get_all_tags = wp_list_pluck( $get_all, 'tag' );

		//iterating over all the merge tags
		if ( $get_all_tags && is_array( $get_all_tags ) && count( $get_all_tags ) > 0 ) {
			foreach ( $get_all_tags as $tag ) {
				$matches = array();
				$re      = sprintf( '/\{{%s(.*?)\}}/', $tag );
				$str     = $content;

				//trying to find match w.r.t current tag
				preg_match_all( $re, $str, $matches );

				//if match found
				if ( $matches && is_array( $matches ) && count( $matches ) > 0 ) {

					//iterate over the found matches
					foreach ( $matches[0] as $exact_match ) {

						//preserve old match
						$old_match        = $exact_match;
						$single           = str_replace( '{{', '', $old_match );
						$single           = str_replace( '}}', '', $single );
						$get_parsed_value = call_user_func( array( __CLASS__, $single ) );
						$content          = str_replace( $old_match, $get_parsed_value, $content );
					}
				}
			}
		}

		return $content;
	}

	/*
	 * Mergetag callback for showing sitename.
	 */

	public static function get_all_tags() {
		$tags = array(
			array(
				'name' => __( 'User login', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_user_login',
			),
			array(
				'name' => __( 'User display name', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_display_name',
			),
			array(
				'name' => __( 'User email', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_user_email',
			),
			array(
				'name' => __( 'Verification link', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_user_verification_link',
			),
			array(
				'name' => __( 'Resend link', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_resend_link',
			),
			array(
				'name' => __( 'Verification link', 'woo-confirmation-email' ),
				'tag'  => 'wcemailverificationcode',
			),
			array(
				'name' => __( 'Site Myaccount Page', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_site_login_link',
			),
			array(
				'name' => __( 'Website Name', 'woo-confirmation-email' ),
				'tag'  => 'sitename',
			),
			array(
				'name' => __( 'Website Name with link', 'woo-confirmation-email' ),
				'tag'  => 'sitename_with_link',
			),
			array(
				'name' => __( 'Shows the verification link text', 'woo-confirmation-email' ),
				'tag'  => 'xlwuev_verification_link_text',
			),
		);

		return $tags;
	}

	/*
	 * Mergetag callback for showing sitename with link.
	 */

	/**
	 * Function to get timezone string by checking WordPress timezone settings
	 * @return mixed|string|void
	 */
	public static function wc_timezone_string() {

		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}

		// get UTC offset, if it isn't set then return UTC
		if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
			return 'UTC';
		}

		// get timezone using offset manual
		return XlWUEV_Common::get_timezone_by_offset( $utc_offset );
	}

	/*
	 * Mergetag callback for showing myaccount page with link.
	 */

	/**
	 * Function to get timezone string based on specified offset
	 *
	 * @param $offset
	 *
	 * @return string
	 * @see WCCT_Common::wc_timezone_string()
	 *
	 */
	public static function get_timezone_by_offset( $offset ) {
		switch ( $offset ) {
			case '-12':
				return 'GMT-12';
				break;
			case '-11.5':
				return 'Pacific/Niue'; // 30 mins wrong
				break;
			case '-11':
				return 'Pacific/Niue';
				break;
			case '-10.5':
				return 'Pacific/Honolulu'; // 30 mins wrong
				break;
			case '-10':
				return 'Pacific/Tahiti';
				break;
			case '-9.5':
				return 'Pacific/Marquesas';
				break;
			case '-9':
				return 'Pacific/Gambier';
				break;
			case '-8.5':
				return 'Pacific/Pitcairn'; // 30 mins wrong
				break;
			case '-8':
				return 'Pacific/Pitcairn';
				break;
			case '-7.5':
				return 'America/Hermosillo'; // 30 mins wrong
				break;
			case '-7':
				return 'America/Hermosillo';
				break;
			case '-6.5':
				return 'America/Belize'; // 30 mins wrong
				break;
			case '-6':
				return 'America/Belize';
				break;
			case '-5.5':
				return 'America/Belize'; // 30 mins wrong
				break;
			case '-5':
				return 'America/Panama';
				break;
			case '-4.5':
				return 'America/Lower_Princes'; // 30 mins wrong
				break;
			case '-4':
				return 'America/Curacao';
				break;
			case '-3.5':
				return 'America/Paramaribo'; // 30 mins wrong
				break;
			case '-3':
				return 'America/Recife';
				break;
			case '-2.5':
				return 'America/St_Johns';
				break;
			case '-2':
				return 'America/Noronha';
				break;
			case '-1.5':
				return 'Atlantic/Cape_Verde'; // 30 mins wrong
				break;
			case '-1':
				return 'Atlantic/Cape_Verde';
				break;
			case '+1':
				return 'Africa/Luanda';
				break;
			case '+1.5':
				return 'Africa/Mbabane'; // 30 mins wrong
				break;
			case '+2':
				return 'Africa/Harare';
				break;
			case '+2.5':
				return 'Indian/Comoro'; // 30 mins wrong
				break;
			case '+3':
				return 'Asia/Baghdad';
				break;
			case '+3.5':
				return 'Indian/Mauritius'; // 30 mins wrong
				break;
			case '+4':
				return 'Indian/Mauritius';
				break;
			case '+4.5':
				return 'Asia/Kabul';
				break;
			case '+5':
				return 'Indian/Maldives';
				break;
			case '+5.5':
				return 'Asia/Kolkata';
				break;
			case '+5.75':
				return 'Asia/Kathmandu';
				break;
			case '+6':
				return 'Asia/Urumqi';
				break;
			case '+6.5':
				return 'Asia/Yangon';
				break;
			case '+7':
				return 'Antarctica/Davis';
				break;
			case '+7.5':
				return 'Asia/Jakarta'; // 30 mins wrong
				break;
			case '+8':
				return 'Asia/Manila';
				break;
			case '+8.5':
				return 'Asia/Pyongyang';
				break;
			case '+8.75':
				return 'Australia/Eucla';
				break;
			case '+9':
				return 'Asia/Tokyo';
				break;
			case '+9.5':
				return 'Australia/Darwin';
				break;
			case '+10':
				return 'Australia/Brisbane';
				break;
			case '+10.5':
				return 'Australia/Lord_Howe';
				break;
			case '+11':
				return 'Antarctica/Casey';
				break;
			case '+11.5':
				return 'Pacific/Auckland'; // 30 mins wrong
				break;
			case '+12':
				return 'Pacific/Wallis';
				break;
			case '+12.75':
				return 'Pacific/Chatham';
				break;
			case '+13':
				return 'Pacific/Fakaofo';
				break;
			case '+13.75':
				return 'Pacific/Chatham'; // 1 hr wrong
				break;
			case '+14':
				return 'Pacific/Kiritimati';
				break;
			default:
				return 'UTC';
				break;
		}
	}

	protected static function sitename() {
		return get_bloginfo( 'name' );
	}

	protected static function sitename_with_link() {
		$hyperlink = site_url();
		$link_text = __( get_bloginfo( 'name' ), 'woo-confirmation-email' );
		$link      = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

		return $link;
	}

	protected static function xlwuev_site_login_link() {
		$hyperlink = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		$link_text = __( 'Login', 'woo-confirmation-email' );
		$link      = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

		return $link;
	}

	/*
	 * Mergetag callback for showing verification link.
	 */

	protected static function xlwuev_user_login() {
		return self::$wuev_user_login;
	}

	/*
	 * Mergetag callback for showing verification link text.
	 */

	protected static function xlwuev_display_name() {
		return self::$wuev_display_name;
	}

	/*
	 * Mergetag callback for showing verification link.
	 */

	protected static function xlwuev_user_email() {
		return self::$wuev_user_email;
	}

	/*
	 * Mergetag callback for showing resend verification link.
	 */

	protected static function xlwuev_user_verification_link() {
		$secret      = get_user_meta( self::$wuev_user_id, 'wcemailverifiedcode', true );
		$create_link = $secret . '@' . self::$wuev_user_id;
		$hyperlink   = add_query_arg( array(
			'woo_confirmation_verify' => base64_encode( $create_link ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );
		$link_text   = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_new_verification_link_text' ) );
		$link        = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

		return $link;
	}

	/*
	 * This function actually sends the verification email.
	 */

	protected static function xlwuev_verification_link_text() {
		$secret      = get_user_meta( self::$wuev_user_id, 'wcemailverifiedcode', true );
		$create_link = $secret . '@' . self::$wuev_user_id;
		$hyperlink   = add_query_arg( array(
			'woo_confirmation_verify' => base64_encode( $create_link ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );
		$link_text   = '<span style="padding: 2px 10px;font-style: italic;font-size: 12px;display: inline-block;width: 100%;">' . $hyperlink . '</span>';

		return $link_text;
	}

	protected static function wcemailverificationcode() {
		$secret      = get_user_meta( self::$wuev_user_id, 'wcemailverifiedcode', true );
		$create_link = $secret . '@' . self::$wuev_user_id;
		$hyperlink   = add_query_arg( array(
			'woo_confirmation_verify' => base64_encode( $create_link ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );
		$link_text   = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_new_verification_link_text' ) );
		$link        = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

		return $link;
	}

	protected static function xlwuev_resend_link() {
		$link                        = add_query_arg( array(
			'wc_confirmation_resend' => base64_encode( self::$wuev_user_id ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );
		$resend_confirmation_message = self::get_setting_value( 'wuev-messages', 'xlwuev_email_resend_confirmation' );
		$xlwuev_resend_link          = '<a href="' . $link . '">' . $resend_confirmation_message . '</a>';

		return $xlwuev_resend_link;
	}
}
