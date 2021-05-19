<?php
/**
 * Plugin states handler that tells about different plugin states
 * @package XLCore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'PW_Plugin_states' ) ) :

	/**
	 * PW_Plugin_states Class
	 */
	class PW_Plugin_states {

		public $pluginbasename;
		public $is_in_repo;
		public $is_installed;
		public $is_active;
		public $is_license_exists;
		public $is_update_available;
		public $is_license_expired;
		public $is_license_invalid;
		public $is_license_active;
		public $Edd_slug;

		public function __construct( XL_addon $config ) {
			//We will see what should come inside this
		}

		/**
		 * Function to detect if module exists in current WordPress
		 * Getting all the installed plugins based on "XL" Custom header
		 * Then check whether it exists or not
		 * @return True on success & false otherwise
		 */
		public function is_plugin_installed() {

			if ( ! is_null( $this->is_installed ) ) {
				return $this->is_installed;
			}
			$getAllInstalled = XL_addons::get_installed_plugins();

			if ( array_key_exists( $this->pluginbasename, $getAllInstalled ) ) {
				$this->is_installed = true;

				return true;
			}
			$this->is_installed = false;

			return false;
		}

		/**
		 * Function to detect if the given module is currently active or not
		 * We use WordPress core function `is_plugin_active` to wrap it over
		 * @return boolean True on Success , False otherwise
		 */
		public function is_plugin_activated() {
			if ( ! is_null( $this->is_active ) ) {
				return $this->is_active;
			}

			$getStatus       = is_plugin_active( $this->pluginbasename );
			$this->is_active = $getStatus;

			return $this->is_active;
		}

		/**
		 * Function to detect if any update is available for the given module
		 * @return boolean True on success False otherwise
		 */
		public function is_update_available() {
			if ( ! is_null( $this->is_update_available ) ) {
				return $this->is_update_available;
			}
			$get_all_updates = XL_addons::get_all_updates();

			if ( array_key_exists( $this->pluginbasename, $get_all_updates ) ) {
				$this->is_update_available = true;

				return true;
			}
			$this->is_update_available = false;

			return false;
		}

		public function is_plugin_in_wp() {
			if ( $this->is_in_repo ) {
				return true;
			}

			return false;
		}

		public function is_license_required() {

		}

		/**
		 * Checking Database for licenses for this product
		 * If it doesn't exists means, either no entry has been made Or It is deactivated in this site
		 * @return boolean True on success False otherwise
		 */
		public function is_license_exists() {
			if ( ! is_null( $this->is_license_exists ) ) {
				return $this->is_license_exists;
			}
			if ( get_option( $this->Edd_slug . '_license_active' ) == false ) {
				$this->is_license_exists = false;
			} else {
				$this->is_license_exists = true;
			}

			return $this->is_license_exists;
		}

		/**
		 * Function to check if the license for this product has been expired
		 * @return type
		 */
		public function is_license_expired() {
			if ( ! is_null( $this->is_license_expired ) ) {
				return $this->is_license_expired;
			}
			$get_licensed_data = get_option( $this->Edd_slug . 'license_data' );
			if ( $get_licensed_data ) {
				if ( isset( $get_licensed_data->error ) && $get_licensed_data->error == 'expired' ) {
					$this->is_license_expired = true;
				} else {
					$this->is_license_expired = false;
				}
			} else {
				$this->is_license_expired = false;
			}

			return $this->is_license_expired;
		}


		/**
		 * Checking the license data and if we have appropriate error message the declared this plugin as invalid license
		 * @return type
		 */
		public function is_license_invalid() {
			if ( ! is_null( $this->is_license_invalid ) ) {
				return $this->is_license_invalid;
			}
			$get_licensed_data = get_option( $this->Edd_slug . 'license_data' );
			if ( $get_licensed_data ) {
				if ( isset( $get_licensed_data->error ) && ( $get_licensed_data->error == 'missing' || $get_licensed_data->error == 'license_not_activable' ) ) {
					$this->is_license_invalid = true;
				} else {
					$this->is_license_invalid = false;
				}
			} else {
				$this->is_license_invalid = false;
			}

			return $this->is_license_expired;
		}

		/**
		 * Checking if license is currently active
		 * Parsing the saved data
		 * @return type
		 */
		public function is_license_active() {
			if ( ! is_null( $this->is_license_active ) ) {
				return $this->is_license_active;
			}
			$get_licensed_data = get_option( $this->Edd_slug . 'license_data' );
			if ( $get_licensed_data ) {
				if ( isset( $get_licensed_data->success ) && $get_licensed_data->success == true ) {
					$this->is_license_active = true;
				} else {
					$this->is_license_active = false;
				}
			} else {
				$this->is_license_active = false;
			}

			return $this->is_license_active;
		}
	}

endif;
