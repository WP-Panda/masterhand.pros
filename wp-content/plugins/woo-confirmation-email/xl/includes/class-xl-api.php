<?php

/**
 * API handler for xl
 * @package XLCore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'XL_API' ) ) :

	/**
	 * XL_License Class
	 */
	class XL_API {

		public static $xl_api_url = 'https://xlplugins.com/';
		public static $is_ssl = false;

		/**
		 * Get all the plugins that can be pushed from the API
		 * @return Mixed False on failure and array on success
		 */
		public static function get_xl_list() {

			$xl_modules = get_transient( 'xl_get_modules' );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG == true ) {
				$xl_modules = '';
			}
			if ( ! empty( $xl_modules ) ) {
				return $xl_modules;
			}

			$api_params = self::get_api_args( array(
					'edd_action' => 'get_xl_plugins',
					'attrs'      => array(
						'meta_query' => array(
							array(
								'key'     => 'is_visible_in_dashboard',
								'value'   => 'yes',
								'compare' => '=',
							),
						),
					),
				) );

			$request_args = self::get_request_args( array(
					'timeout'   => 30,
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );

			$request = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			if ( is_wp_error( $request ) ) {
				return false;
			}

			$request = json_decode( wp_remote_retrieve_body( $request ) );

			if ( ! $request ) {
				return false;
			}

			$xl_modules = $request;

			set_transient( 'xl_get_modules', $request, 60 * 60 * 12 );

			return ! empty( $xl_modules ) ? $xl_modules : false;
		}

		/**
		 * Get single addon data from the saved transient
		 * <br> IT get used when installation happens for Paid plugin (non-repo)
		 *
		 * @param slug $get_addon
		 *
		 * @return type
		 */
		public static function get_xl_addon( $get_addon ) {
			$xl_modules = get_transient( 'xl_get_modules' );

			foreach ( $xl_modules->data as $plugin ) {
				$plugin_as_array = (array) $plugin;
				$current         = key( $plugin_as_array );

				if ( $plugin->$current->plugin_basename !== $get_addon ) {
					continue;
				} else {
					$addon_object = new XL_addon( $plugin );
					break;
				}
			}

			return ( $addon_object ) ? $addon_object : false;
		}

		/**
		 * Post tracking data to the Server
		 *
		 * @param type $data
		 *
		 * @return type
		 */
		public static function post_tracking_data( $data ) {

			if ( empty( $data ) ) {
				return;
			}

			$api_params = self::get_api_args( array(
					'edd_action' => 'get_tracking_data',
					'data'       => $data,
				) );

			$request_args = self::get_request_args( array(
					'timeout'   => 30,
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );

			$request = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			return $request;
		}

		public static function post_support_request( $data ) {

			if ( empty( $data ) ) {
				return;
			}

			$api_params = self::get_api_args( array(
					'edd_action' => 'submit_support_request',
					'data'       => $data,
				) );

			$request_args = self::get_request_args( array(
					'timeout'   => 30,
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );

			$request = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			if ( ! is_wp_error( $request ) ) {
				$request = json_decode( wp_remote_retrieve_body( $request ) );

				return $request;
			}

			return false;

		}

		/**
		 * Filter function to modify args
		 *
		 * @param type $args
		 *
		 * @return type
		 */
		public static function get_api_args( $args ) {

			return apply_filters( 'xl_api_call_agrs', $args );
		}

		/**
		 * Filter function for request args
		 *
		 * @param type $args
		 *
		 * @return type
		 */
		public static function get_request_args( $args ) {

			return apply_filters( 'xl_api_call_request_agrs', $args );
		}

		/**
		 * All the data about the deactivation popups
		 * @return type
		 */
		public static function post_deactivation_data() {

			$get_deactivation_data = array(
				'site'          => site_url(),
				'deactivations' => get_option( 'xl_uninstall_reasons' ),
			);

			$api_params = self::get_api_args( array(
					'edd_action' => 'get_deactivation_data',
					'data'       => $get_deactivation_data,
				) );

			$request_args = self::get_request_args( array(
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );

			$request = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			return $request;
		}

		/**
		 * All the data about the deactivation popups
		 *
		 * @param $deactivations
		 * @param $licenses
		 *
		 * @return array|WP_Error
		 */
		public static function post_deactivation_data_v2( $deactivations, $licenses ) {

			$get_deactivation_data = array(
				'site'          => site_url(),
				'deactivations' => $deactivations,

			);

			$api_params = self::get_api_args( array(
					'edd_action' => 'get_deactivation_data_v2',
					'data'       => $get_deactivation_data,
					'licenses'   => $licenses,
				) );

			$request_args = self::get_request_args( array(
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );

			$request = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			return $request;
		}


		/**
		 * Get for API url
		 *
		 * @param string $link
		 *
		 * @return string
		 */
		public static function get_api_url( $link ) {

			return apply_filters( 'xl_api_call_url', $link );
		}

		public static function get_xl_status() {
			//do a xl_status_check
			return true;
		}

		public static function post_optin_data( $data ) {

			if ( empty( $data ) ) {
				return;
			}

			$api_params   = self::get_api_args( array(
					'edd_action' => 'xlapi_optin',
					'data'       => $data,
				) );
			$request_args = self::get_request_args( array(
					'timeout'   => 30,
					'sslverify' => self::$is_ssl,
					'body'      => urlencode_deep( $api_params ),
				) );
			$request      = wp_remote_post( self::get_api_url( self::$xl_api_url ), $request_args );

			return $request;
		}

	}

endif; // end class_exists check
