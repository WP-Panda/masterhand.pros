<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @author XLPlugins
 * @package XLCore
 */
if ( ! class_exists( 'XL_Transient' ) ) {
	class XL_Transient {

		protected static $instance;

		/**
		 * XL_Transient constructor.
		 */
		public function __construct() {

		}

		/**
		 * Creates an instance of the class
		 * @return XL_Transient
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Set the transient contents by key and group within page scope
		 *
		 * @param $key
		 * @param $value
		 * @param int $expiration | default 1 hour
		 * @param string $plugin_short_name
		 */
		public function set_transient( $key, $value, $expiration = 3600, $plugin_short_name = 'finale' ) {

			$transient_key   = '_xlcore_transient_' . $plugin_short_name . '_' . $key;
			$transient_value = array(
				'time'  => time() + (int) $expiration,
				'value' => $value,
			);

			if ( class_exists( 'Xl_File_Api' ) ) {
				$file_api = new Xl_File_Api( $plugin_short_name . '-transient' );
				$file_api->touch( $transient_key );
				if ( $file_api->is_writable( $transient_key ) && $file_api->is_readable( $transient_key ) ) {
					$transient_value = maybe_serialize( $transient_value );
					$file_api->put_contents( $transient_key, $transient_value );
				} else {
					// xl file api folder not writable
					update_option( $transient_key, $transient_value, false );
				}
			} else {
				// xl file api method not available
				update_option( $transient_key, $transient_value, false );
			}
		}

		/**
		 * Get the transient contents by the transient key or group.
		 *
		 * @param $key
		 * @param string $plugin_short_name
		 *
		 * @return bool|mixed
		 */
		public function get_transient( $key, $plugin_short_name = 'finale' ) {

			$transient_key = '_xlcore_transient_' . $plugin_short_name . '_' . $key;

			if ( class_exists( 'Xl_File_Api' ) ) {
				$file_api = new Xl_File_Api( $plugin_short_name . '-transient' );
				if ( $file_api->is_writable( $transient_key ) && $file_api->is_readable( $transient_key ) ) {
					$data  = $file_api->get_contents( $transient_key );
					$data  = maybe_unserialize( $data );
					$value = $this->get_value( $transient_key, $data );
					if ( false === $value ) {
						$file_api->delete( $transient_key );
					}

					return $value;
				}
			}

			// xl file api method not available
			$data = get_option( $transient_key, false );
			if ( false === $data ) {
				return false;
			}

			return $this->get_value( $transient_key, $data, true );
		}

		public function get_value( $transient_key, $data, $db_call = false ) {
			$current_time = time();
			if ( is_array( $data ) && isset( $data['time'] ) ) {
				if ( $current_time > (int) $data['time'] ) {
					if ( true === $db_call ) {
						delete_option( $transient_key );
					}

					return false;
				} else {
					return $data['value'];
				}
			}

			return false;
		}


		/**
		 * Delete the transient by key
		 *
		 * @param $key
		 * @param string $plugin_short_name
		 */
		public function delete_transient( $key, $plugin_short_name = 'finale' ) {
			$transient_key = '_xlcore_transient_' . $plugin_short_name . '_' . $key;

			if ( class_exists( 'Xl_File_Api' ) ) {
				$file_api = new Xl_File_Api( $plugin_short_name . '-transient' );

				if ( $file_api->exists( $transient_key ) ) {
					$file_api->delete_file( $transient_key );
				}
			}

			// removing db transient
			delete_option( $transient_key );
		}

		/**
		 * Delete all the transients
		 *
		 * @param string $plugin_short_name
		 */
		public function delete_all_transients( $plugin_short_name = '' ) {
			global $wpdb;

			/** removing db transient */
			$query = "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '%_xlcore_transient_{$plugin_short_name}%'";
			$wpdb->query( $query );

			/** removing files if file api exist */
			if ( class_exists( 'Xl_File_Api' ) ) {
				$file_api = new Xl_File_Api( $plugin_short_name . '-transient' );
				$file_api->delete_all( $plugin_short_name . '-transient', true );
			}
		}


		/**
		 * Delete all xlplugins plugins transients
		 *
		 * @param string $plugin_short_name
		 */
		public function delete_force_transients() {
			global $wpdb;

			/** removing db transient */
			$query = "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '%_xlcore_transient_%'";
			$wpdb->query( $query );

			/** removing files if file api exist */
			if ( class_exists( 'Xl_File_Api' ) ) {
				$file_api = new Xl_File_Api( 'finale-transient' );

				$upload      = wp_upload_dir();
				$folder_path = $upload['basedir'] . '/xlplugins';
				$file_api->delete_folder( $folder_path, true );
			}
		}
	}
}
