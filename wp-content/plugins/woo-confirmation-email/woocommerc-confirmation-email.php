<?php
/**
 * Plugin Name: User Email Verification for WooCommerce
 * Description: Verify user email address when user goes to registration
 * Author: XLPlugins
 * Author URI: https://xlplugins.com/
 * Version: 3.5.0
 * Text Domain: woo-confirmation-email
 * Domain Path: /languages/i18n/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * XL: True
 * Requires at least: 4.2.1
 * Tested up to: 5.2.2
 * WC requires at least: 2.6.0
 * WC tested up to: 3.6.4
 *
 * User Email Verification for WooCommerce is free software.
 * You can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * User Email Verification for WooCommerce is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with User Email Verification for WooCommerce. If not, see <http://www.gnu.org/licenses/>.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'XLWUEV_Core' ) ) :
	class XLWUEV_Core {

		/**
		 * @var XLWUEV_Core
		 */
		public static $_instance = null;
		private static $_registered_entity = array(
			'active'   => array(),
			'inactive' => array(),
		);

		/**
		 * @var XLWUEV_XL_Support
		 */
		public $xl_support;

		/**
		 * @var bool Dependency check property
		 */
		private $is_dependency_exists = true;

		public function __construct() {

			/**
			 * Load important variables and constants
			 */
			$this->define_plugin_properties();

			/**
			 * Load dependency classes like woo-functions.php
			 */
			$this->load_dependencies_support();

			/**
			 * Run dependency check to check if dependency available
			 */
			$this->do_dependency_check();
			if ( $this->is_dependency_exists ) {

				/**
				 * Loads all the hooks
				 */
				$this->load_hooks();

				/**
				 * Initiates and loads XL start file
				 */
				$this->load_xl_core_classes();

				/**
				 * Include common classes
				 */
				$this->include_commons();

				/**
				 * Initialize common hooks and functions
				 */
				$this->initialize_common();

				/**
				 * Maybe load admin if admin screen
				 */
				$this->maybe_load_admin();
			}
		}

		public function define_plugin_properties() {
			/** DEFINING CONSTANTS */
			define( 'XLWUEV_SLUG', 'woo-confirmation-email' );
			define( 'XLWUEV_VERSION', '3.5.0' );
			define( 'XLWUEV_DIR', __DIR__ );
			define( 'XLWUEV_MIN_WC_VERSION', '2.6' );
			define( 'XLWUEV_TEXTDOMAIN', 'woo-confirmation-email' );
			define( 'XLWUEV_FULL_NAME', 'WooCommerce User Email Verification' );
			define( 'XLWUEV_SHORTNAME_NAME', 'WC Email Verification' );
			define( 'XLWUEV_PLUGIN_FILE', __FILE__ );
			define( 'XLWUEV_SHORT_SLUG', 'xlwuev' );
			define( 'XLWUEV_PLUGIN_DIR', __DIR__ );
			define( 'XLWUEV_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'XLWUEV_PURCHASE', 'xlplugin' );
		}

		public function load_dependencies_support() {
			/** Setting up Woocommerce Dependency Classes */
			require_once( 'woo-includes/woo-functions.php' );
		}

		public function do_dependency_check() {
			if ( ! xlwuev_is_woocommerce_active() ) {
				add_action( 'admin_notices', array( $this, 'xlwuev_wc_not_installed_notice' ) );
				$this->is_dependency_exists = false;
			}
		}


		public function load_hooks() {
			/** Initializing XL Core */
			add_action( 'init', array( $this, 'xlwuev_xl_init' ), 8 );

			add_action( 'plugins_loaded', array( $this, 'xlwuev_register_classes' ), 1 );

			/** Initializing Functionality */
			add_action( 'plugins_loaded', array( $this, 'xlwuev_init' ), 0 );

			/** Initialize Localization */
			add_action( 'init', array( $this, 'xlwuev_init_localization' ) );

			/** Redirecting Plugin to the settings page after activation */
			add_action( 'activated_plugin', array( $this, 'xlwuev_settings_redirect' ) );
		}

		public function xlwuev_xl_init() {
			XL_Common::include_xl_core();
		}

		public function load_xl_core_classes() {

			/** Setting Up XL Core */
			require_once( 'start.php' );
		}

		public function include_commons() {
			/** Loading Common Class */
			require 'includes/class-xlwuev-common.php';
			require 'includes/xlwuev-xl-support.php';
			require 'includes/xlwuev-logging.php';
			/**
			 * Register shutdown to catch fatal errors.
			 */
			register_shutdown_function( array( $this, 'collect_errors' ) );
		}

		public function initialize_common() {
			/** Firing Init to init basic Functions */
			XlWUEV_Common::init();
		}

		public function maybe_load_admin() {
			/* ----------------------------------------------------------------------------*
			 * Dashboard and Administrative Functionality
			 * ---------------------------------------------------------------------------- */
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				require_once( plugin_dir_path( XLWUEV_PLUGIN_FILE ) . 'admin/class-xlwuev-woocommerce-confirmation-email-admin.php' );
			}
		}

		public function xlwuev_register_classes() {
			$load_classes = self::get_registered_class();
			if ( is_array( $load_classes ) && count( $load_classes ) > 0 ) {
				foreach ( $load_classes as $access_key => $class ) {
					$this->$access_key = $class::get_instance();
				}
			}
			do_action( 'xlwuev_loaded' );
		}

		public static function get_registered_class() {
			return self::$_registered_entity['active'];
		}

		public static function register( $short_name, $class, $overrides = null ) {

			//Ignore classes that have been marked as inactive
			if ( in_array( $class, self::$_registered_entity['inactive'], true ) ) {
				return;
			}

			//Mark classes as active. Override existing active classes if they are supposed to be overridden
			$index = array_search( $overrides, self::$_registered_entity['active'], true );
			if ( false !== $index ) {
				self::$_registered_entity['active'][ $index ] = $class;
			} else {
				self::$_registered_entity['active'][ $short_name ] = $class;
			}

			//Mark overridden classes as inactive.
			if ( ! empty( $overrides ) ) {
				self::$_registered_entity['inactive'][] = $overrides;
			}
		}

		/**
		 * @return null|XLWUEV_Core
		 */
		public static function get_instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}


		public function xlwuev_init_localization() {
			load_plugin_textdomain( XLWUEV_TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Added redirection on plugin activation
		 *
		 * @param $plugin
		 */
		public function xlwuev_settings_redirect( $plugin ) {
			if ( xlwuev_is_woocommerce_active() && class_exists( 'Woocommerce' ) ) {
				if ( plugin_basename( __FILE__ ) === $plugin ) {
					wp_safe_redirect( add_query_arg( array(
						'page' => 'woo-confirmation-email',
					), admin_url( 'admin.php' ) ) );
					exit;
				}
			}
		}

		/**
		 * Checking Woocommerce dependency and then loads further
		 * @return bool false on failure
		 */
		public function xlwuev_init() {
			/* ----------------------------------------------------------------------------*
			 * Public Functionality
			 * ---------------------------------------------------------------------------- */
			if ( xlwuev_is_woocommerce_active() && class_exists( 'Woocommerce' ) ) {
				require_once( plugin_dir_path( XLWUEV_PLUGIN_FILE ) . 'public/class-xlwuev-woocommerce-confirmation-email-public.php' );
			}
		}

		/* ********* REGISTERING NOTICES ******************* */
		public function xlwuev_wc_not_installed_notice() {
			?>
            <div class="error">
                <p>
					<?php
					echo __( 'WooCommerce is not installed or activated. WooCommerce User Email Verification is a WooCommerce Extension and would only work if WooCommerce is activated. Please install the WooCommerce Plugin first.', 'woo-confirmation-email' );
					?>
                </p>
            </div>
			<?php
		}

		/**
		 * Collect PHP fatal errors and save it in the log file so that it can be later viewed
		 * @see register_shutdown_function
		 */
		public function collect_errors() {
			$error = error_get_last();
			if ( E_ERROR === $error['type'] ) {
				XlWUEV_Common::$is_force_debug = true;
				xlwuev_force_log( time() . ' : ' . $error['message'] . PHP_EOL, 'fatal_errors.txt' );
			}
		}
	}

endif;


if ( ! function_exists( 'XLWUEV_Core' ) ) {

	/**
	 * Global Common function to load all the classes
	 * @return XLWUEV_Core
	 */
	function xlwuev_core() {
		return XLWUEV_Core::get_instance();
	}
}

/**
 * From here starts the execution of the plugin.
 */
$GLOBALS['XLWUEV_Core'] = xlwuev_core();
register_activation_hook( __FILE__, array(
	'XLWUEV_Woocommerce_Confirmation_Email_Admin',
	'activate_plugins_wc_email',
) );
