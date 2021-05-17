<?php
/*
Plugin Name: WP All Export - User Export Add-On Pro
Plugin URI: http://www.wpallimport.com/
Description: Export Users and User meta from WordPress. Requires WP All Export Pro.
Version: 1.0.4
Author: Soflyy
*/
/**
 * Plugin root dir with forward slashes as directory separator regardless of actuall DIRECTORY_SEPARATOR value
 * @var string
 */
define('PMUE_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));
/**
 * Plugin root url for referencing static content
 * @var string
 */
define('PMUE_ROOT_URL', rtrim(plugin_dir_url(__FILE__), '/'));
/**
 * Plugin prefix for making names unique (be aware that this variable is used in conjuction with naming convention,
 * i.e. in order to change it one must not only modify this constant but also rename all constants, classes and functions which
 * names composed using this prefix)
 * @var string
 */
define('PMUE_PREFIX', 'pmue_');

define('PMUE_VERSION', '1.0.4');

if ( class_exists('PMUE_Plugin') and PMUE_EDITION == "free"){

	function pmue_notice(){
		
		?>
		<div class="error"><p>
			<?php printf(__('Please de-activate and remove the free version of the User Export Add-On before activating the pro version.', 'wp_all_export_user_add_on'));
			?>
		</p></div>
		<?php

        deactivate_plugins( plugin_basename( __FILE__ ) );

	}

	add_action('admin_notices', 'pmue_notice');

}
else {

	define('PMUE_EDITION', 'paid');

	/**
	 * Main plugin file, Introduces MVC pattern
	 *
	 * @singletone
	 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
	 */

	final class PMUE_Plugin {
		/**
		 * Singletone instance
		 * @var PMUE_Plugin
		 */
		protected static $instance;

		/**
		 * Plugin root dir
		 * @var string
		 */
		const ROOT_DIR = PMUE_ROOT_DIR;
		/**
		 * Plugin root URL
		 * @var string
		 */
		const ROOT_URL = PMUE_ROOT_URL;
		/**
		 * Prefix used for names of shortcodes, action handlers, filter functions etc.
		 * @var string
		 */
		const PREFIX = PMUE_PREFIX;
		/**
		 * Plugin file path
		 * @var string
		 */
		const FILE = __FILE__;	

		/**
		 * Return singletone instance
		 * @return PMUE_Plugin
		 */
		static public function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		static public function getEddName(){
			return 'User Export Add-On';
		}

		/**
		 * Common logic for requestin plugin info fields
		 */
		public function __call($method, $args) {
			if (preg_match('%^get(.+)%i', $method, $mtch)) {
				$info = get_plugin_data(self::FILE);
				if (isset($info[$mtch[1]])) {
					return $info[$mtch[1]];
				}
			}
			throw new Exception("Requested method " . get_class($this) . "::$method doesn't exist.");
		}

		/**
		 * Get path to plagin dir relative to wordpress root
		 * @param bool[optional] $noForwardSlash Whether path should be returned withot forwarding slash
		 * @return string
		 */
		public function getRelativePath($noForwardSlash = false) {
			$wp_root = str_replace('\\', '/', ABSPATH);
			return ($noForwardSlash ? '' : '/') . str_replace($wp_root, '', self::ROOT_DIR);
		}

		/**
		 * Check whether plugin is activated as network one
		 * @return bool
		 */
		public function isNetwork() {
			if ( !is_multisite() )
			return false;

			$plugins = get_site_option('active_sitewide_plugins');
			if (isset($plugins[plugin_basename(self::FILE)]))
				return true;

			return false;
		}

		/**
		 * Class constructor containing dispatching logic
		 * @param string $rootDir Plugin root dir
		 * @param string $pluginFilePath Plugin main file
		 */
		protected function __construct() {

		    include_once 'src'.DIRECTORY_SEPARATOR.'Common'.DIRECTORY_SEPARATOR.'Bootstrap'.DIRECTORY_SEPARATOR.'Autoloader.php';
		    $autoloader = new \Pmue\Common\Bootstrap\Autoloader(self::ROOT_DIR, self::PREFIX);
			// create/update required database tables

			// register autoloading method
			spl_autoload_register(array($autoloader, 'autoload'));

			register_activation_hook(self::FILE, array($this, 'activation'));

			$autoloader->init();

			// register admin page pre-dispatcher
			add_action('admin_init', array($this, 'adminInit'));
			add_action('init', array($this, 'init'));

		}

		public function init(){
			$this->load_plugin_textdomain();
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp_all_import_user_add_on' );
			load_plugin_textdomain( 'wp_all_import_user_add_on', false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages" );
		}

		/**
		 * pre-dispatching logic for admin page controllers
		 */
		public function adminInit() {
			$input = new PMUE_Input();
			$adminDispatcher = new \Pmue\Common\Bootstrap\AdminDispatcher(self::PREFIX);
			$page = strtolower($input->getpost('page', ''));
            $action = $input->getpost('action', 'index');

            $adminDispatcher->dispatch($page, $action);
		}

		/**
		 * Dispatch shorttag: create corresponding controller instance and call its index method
		 * @param array $args Shortcode tag attributes
		 * @param string $content Shortcode tag content
		 * @param string $tag Shortcode tag name which is being dispatched
		 * @return string
		 */
		public function shortcodeDispatcher($args, $content, $tag) {

			$controllerName = self::PREFIX . preg_replace_callback('%(^|_).%', array($this, "replace_callback"), $tag);// capitalize first letters of class name parts and add prefix
			$controller = new $controllerName();
			if ( ! $controller instanceof PMUE_Controller) {
				throw new Exception("Shortcode `$tag` matches to a wrong controller type.");
			}
			ob_start();
			$controller->index($args, $content);
			return ob_get_clean();
		}

		public function replace_callback($matches){
			return strtoupper($matches[0]);
		}

		/**
		 * Plugin activation logic
		 */
		public function activation() {
			// Uncaught exception doesn't prevent plugin from being activated, therefore replace it with fatal error so it does.
			set_exception_handler(function($e){trigger_error($e->getMessage(), E_USER_ERROR);});
		}

		/**
		 * Method returns default import options, main utility of the method is to avoid warnings when new
		 * option is introduced but already registered imports don't have it
		 */
		public static function get_default_import_options()
        {
            $importOptions = new \Pmue\Common\Bootstrap\DefaultImportOptions();
            return $importOptions->getDefaultImportOptions();
        }
	}

	PMUE_Plugin::getInstance();

	// retrieve our license key from the DB
	$wpae_user_addon_options = get_option('PMXI_Plugin_Options');
	
	if (!empty($wpae_user_addon_options['info_api_url'])){
		// setup the updater
		$updater = new PMUE_Updater( $wpae_user_addon_options['info_api_url'], __FILE__, array(
				'version' 	=> PMUE_VERSION,		// current version number
				'license' 	=> false, // license key (used get_option above to retrieve from DB)
				'item_name' => PMUE_Plugin::getEddName(), 	// name of this plugin
				'author' 	=> 'Soflyy'  // author of this plugin
			)
		);
	}
		
}

