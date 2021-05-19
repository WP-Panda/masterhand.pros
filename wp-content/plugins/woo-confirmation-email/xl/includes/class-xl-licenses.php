<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin licenses data class / we do not handle license activation and deactivation at this class
 *
 * @author XLPlugins
 * @package XLCore
 */
class XL_licenses {

	public $plugins_list;
	protected static $instance;

	public function __construct() {
		//calling appropriate hooks by identifying the request
		$this->maybe_submit();

		$this->maybe_deactivate();

		/** Update license key autoload */
		$this->update_license_autoload();
	}

	public function get_plugins_list() {
		$this->plugins_list = apply_filters( 'xl_plugins_license_needed', array() );
	}

	public function get_data() {
		if ( ! is_null( $this->plugins_list ) ) {
			return $this->plugins_list;
		}
		$this->get_plugins_list();

		return $this->plugins_list;
	}

	/**
	 * Pass to submission
	 */
	public function maybe_submit() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'xl_activate-products' ) {
			do_action( 'xl_licenses_submitted', $_POST );
		}
	}

	/**
	 * Pass to deactivate hook
	 */
	public function maybe_deactivate() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'xl_deactivate-product' ) {
			do_action( 'xl_deactivate_request', $_GET );
		}
	}

	public function update_license_autoload() {
		global $wpdb;
		$xl_transient_obj = XL_Transient::get_instance();
		$key              = 'license_autoload';
		$transient_data   = $xl_transient_obj->get_transient( $key, 'core' );
		if ( false === $transient_data ) {
			$option_key_arr = array( '_license_active', 'license_data', 'xl_licenses_' );

			foreach ( $option_key_arr as $val ) {
				$query        = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->options . ' WHERE `option_name` LIKE %s', '%' . $val . '%' );
				$query_result = $wpdb->get_results( $query, ARRAY_A );
				if ( is_array( $query_result ) && count( $query_result ) > 0 ) {
					foreach ( $query_result as $option_data ) {
						$id           = $option_data['option_id'];
						$autoload     = $option_data['autoload'];
						$option_name  = $option_data['option_name'];
						$option_value = maybe_unserialize( $option_data['option_value'] );
						if ( 'no' == $autoload ) {
							delete_option( $option_name );
							update_option( $option_name, $option_value, true );
						}
					}
				}
			}

			$xl_transient_obj->set_transient( $key, 'modified on ' . time(), YEAR_IN_SECONDS, 'core' );
		}
	}

	/**
	 * Creates and instance of the class
	 * @return XL_licenses
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
