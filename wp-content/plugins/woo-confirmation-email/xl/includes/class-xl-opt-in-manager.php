<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * OptIn manager class is to handle all scenarios occurs for opting the user
 * @author: XLPlugins
 * @since 0.0.1
 * @package XLCore
 *
 */
class XL_optIn_Manager {

	public static $optIn_state;
	public static $optIn_detail_page_url = 'https://shop.xlplugins.com/optin';
	public static $should_show_optin = false;

	/**
	 * Initialization to execute several hooks
	 */
	public static function init() {

		//push notification for optin
		add_action( 'admin_init', array( __CLASS__, 'maybe_push_optin_notice' ), 15 );

		// track usage user callback
		add_action( 'xl_new_maybe_track_usage_scheduled', array( __CLASS__, 'maybe_track_usage' ) );

		//initializing schedules
		add_action( 'wp', array( __CLASS__, 'initiate_schedules' ) );

		add_action( 'admin_init', array( __CLASS__, 'maybe_clear_optin' ) );
		// For testing license notices, uncomment this line to force checks on every page load
		//  add_action( 'admin_init', array( __CLASS__, 'maybe_track_usage' ) );

		/** optin ajax call */
		add_action( 'wp_ajax_xlo_optin_call', array( __CLASS__, 'xlo_optin_call' ) );

		// optin yes track callback
		add_action( 'xl_optin_success_track_scheduled', array( __CLASS__, 'optin_track_usage' ), 10 );

		add_filter( 'cron_schedules', array( __CLASS__, 'register_weekly_schedule' ), 10 );
	}

	/**
	 * Set function to allow
	 */
	public static function Allow_optin() {
		update_option( 'xlp_is_opted', 'yes', false );

		//try to push data for once
		$data = self::collect_data();

		//posting data to api
		XL_API::post_tracking_data( $data );
	}

	/**
	 * Set function to block
	 */
	public static function block_optin() {
		update_option( 'xlp_is_opted', 'no', false );
	}

	public static function maybe_clear_optin() {
		if ( filter_input( INPUT_GET, 'xl_optin_refresh' ) == 'yes' && 'yes' !== get_option( 'xlp_is_opted', 'no' ) ) {
			self::reset_optin();
		}
	}

	/**
	 * Reset optin
	 */
	public static function reset_optin() {
		delete_option( 'xlp_is_opted' );
	}

	public static function update_optIn_referer( $referer ) {
		update_option( 'xl_optin_ref', $referer, false );
	}

	/**
	 * Checking the opt-in state and if we have scope for notification then push it
	 */
	public static function maybe_push_optin_notice() {
		if ( self::get_optIn_state() == false && apply_filters( 'xl_optin_notif_show', self::$should_show_optin ) ) {
			do_action( 'maybe_push_optin_notice_state_action' );
		}
	}

	/**
	 * Get current optin status from database
	 * @return type
	 */
	public static function get_optIn_state() {
		if ( self::$optIn_state !== null ) {
			return self::$optIn_state;
		}

		return self::$optIn_state = get_option( 'xlp_is_opted' );
	}

	/**
	 * Callback function to run on schedule hook
	 */
	public static function maybe_track_usage() {

		/** checking optin state */
		if ( 'yes' == self::get_optIn_state() ) {
			$data = self::collect_data();

			//posting data to api
			XL_API::post_tracking_data( $data );
		}
	}

	/**
	 * Collect some data and let the hook left for our other plugins to add some more info that can be tracked down
	 * <br/>
	 * @return array data to track
	 */
	public static function collect_data() {
		global $wpdb, $woocommerce;

		$installed_plugs = XL_addons::get_installed_plugins();

		$active_plugins = get_option( 'active_plugins' );

		$licenses = XL_licenses::get_instance()->get_data();
		$theme    = array();

		$get_theme_info      = wp_get_theme();
		$theme['name']       = $get_theme_info->get( 'Name' );
		$theme['uri']        = $get_theme_info->get( 'ThemeURI' );
		$theme['version']    = $get_theme_info->get( 'Version' );
		$theme['author']     = $get_theme_info->get( 'Author' );
		$theme['author_uri'] = $get_theme_info->get( 'AuthorURI' );
		$ref                 = get_option( 'xl_optin_ref', '' );
		$sections            = array();
		if ( class_exists( 'WooCommerce' ) ) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();

			foreach ( $payment_gateways as $gateway_key => $gateway ) {
				if ( 'yes' === $gateway->enabled ) {
					$sections[] = esc_html( $gateway_key );
				}
			}
			/* WordPress information. */
		}

		/** Product Count */
		$product_count          = array();
		$product_count_data     = wp_count_posts( 'product' );
		$product_count['total'] = $product_count_data->publish;

		$product_statuses = get_terms( 'product_type', array(
				'hide_empty' => 0,
			) );
		foreach ( $product_statuses as $product_status ) {
			$product_count[ $product_status->name ] = $product_status->count;
		}

		/** Order Count */
		$order_count      = array();
		$order_count_data = wp_count_posts( 'shop_order' );

		foreach ( wc_get_order_statuses() as $status_slug => $status_name ) {
			$order_count[ $status_slug ] = $order_count_data->{$status_slug};
		}

		$base_country = get_option( 'woocommerce_default_country', false );
		if ( false !== $base_country ) {
			$base_country = substr( $base_country, 0, 2 );
		}

		return apply_filters( 'xl_global_tracking_data', array(
				'url'              => home_url(),
				'email'            => get_option( 'admin_email' ),
				'installed'        => $installed_plugs,
				'active_plugins'   => $active_plugins,
				'license_info'     => $licenses,
				'theme_info'       => $theme,
				'users_count'      => self::get_user_counts(),
				'locale'           => get_locale(),
				'country'          => $base_country,
				'currency'         => get_woocommerce_currency(),
				'is_mu'            => is_multisite() ? 'yes' : 'no',
				'wp'               => get_bloginfo( 'version' ),
				'wc'               => $woocommerce->version,
				'php'              => phpversion(),
				'mysql'            => $wpdb->db_version(),
				'calc_taxes'       => get_option( 'woocommerce_calc_taxes' ),
				'guest_checkout'   => get_option( 'woocommerce_enable_guest_checkout' ),
				'product_count'    => $product_count,
				'order_count'      => $order_count,
				'xlcore_version'   => XL_Common::$current_version,
				'notification_ref' => $ref,
				'wc_gateways'      => $sections,
				'date'             => date( 'd.m.Y H:i:s' ),
			) );
	}

	/**
	 * Get user totals based on user role.
	 * @return array
	 */
	private static function get_user_counts() {
		$user_count          = array();
		$user_count_data     = count_users();
		$user_count['total'] = $user_count_data['total_users'];

		// Get user count based on user role
		foreach ( $user_count_data['avail_roles'] as $role => $count ) {
			$user_count[ $role ] = $count;
		}

		return $user_count;
	}

	/**
	 * Initiate schedules in order to start tracking data regularly
	 */
	public static function initiate_schedules() {
		/** Clearing scheduled hook */
		if ( wp_next_scheduled( 'xl_maybe_track_usage_scheduled' ) ) {
			wp_clear_scheduled_hook( 'xl_maybe_track_usage_scheduled' );
		}

		if ( ! wp_next_scheduled( 'xl_new_maybe_track_usage_scheduled' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly_xl', 'xl_new_maybe_track_usage_scheduled' );
		}
	}

	public static function register_weekly_schedule( $schedules ) {
		$schedules['weekly_xl'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Weekly XL' ),
		);

		return $schedules;
	}

	public static function xlo_optin_call() {
		if ( is_array( $_POST ) && count( $_POST ) > 0 ) {
			$_POST['domain'] = site_url();
			$_POST['ip']     = $_SERVER['REMOTE_ADDR'];
			XL_API::post_optin_data( $_POST );

			/** scheduling track call when success */
			if ( isset( $_POST['status'] ) && 'yes' == $_POST['status'] ) {
				wp_schedule_single_event( time() + 2, 'xl_optin_success_track_scheduled' );
			}
		}
		wp_send_json( array(
				'status' => 'success',
			) );
		exit;
	}

	/**
	 * Callback function to run on schedule hook
	 */
	public static function optin_track_usage() {

		/** update week day for tracking */
		$track_week_day = date( 'w' );
		update_option( 'xl_track_day', $track_week_day, false );

		$data = self::collect_data();

		//posting data to api
		XL_API::post_tracking_data( $data );
	}

}

// Initialization
XL_optIn_Manager::init();
