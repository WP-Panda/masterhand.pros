<?php
/**
 * Load plugin's files with check for installing it as a standalone plugin or
 * a module of a theme / plugin. If standalone plugin is already installed, it
 * will take higher priority.
 *
 * @package Meta Box
 */

/**
 * Plugin loader class.
 *
 * @package Meta Box
 */
class WPP_MB_Loader {
	/**
	 * Define plugin constants.
	 */
	protected function constants() {
		// Script version, used to add version for scripts and styles.
		define( 'WPP_MB_VER', '4.17.3' );

		list( $path, $url ) = self::get_path( dirname( dirname( __FILE__ ) ) );

		// Plugin URLs, for fast enqueuing scripts and styles.
		define( 'WPP_MB_URL', $url );
		define( 'WPP_MB_JS_URL', trailingslashit( WPP_MB_URL . 'js' ) );
		define( 'WPP_MB_CSS_URL', trailingslashit( WPP_MB_URL . 'css' ) );

		// Plugin paths, for including files.
		define( 'WPP_MB_DIR', $path );
		define( 'WPP_MB_INC_DIR', trailingslashit( WPP_MB_DIR . 'inc' ) );
	}

	/**
	 * Get plugin base path and URL.
	 * The method is static and can be used in extensions.
	 *
	 * @link http://www.deluxeblogtips.com/2013/07/get-url-of-php-file-in-wordpress.html
	 * @param string $path Base folder path.
	 * @return array Path and URL.
	 */
	public static function get_path( $path = '' ) {
		// Plugin base path.
		$path       = wp_normalize_path( untrailingslashit( $path ) );
		$themes_dir = wp_normalize_path( untrailingslashit( dirname( get_stylesheet_directory() ) ) );

		// Default URL.
		$url = plugins_url( '', $path . '/' . basename( $path ) . '.php' );

		// Included into themes.
		if (
			0 !== strpos( $path, wp_normalize_path( WP_PLUGIN_DIR ) )
			&& 0 !== strpos( $path, wp_normalize_path( WPMU_PLUGIN_DIR ) )
			&& 0 === strpos( $path, $themes_dir )
		) {
			$themes_url = untrailingslashit( dirname( get_stylesheet_directory_uri() ) );
			$url        = str_replace( $themes_dir, $themes_url, $path );
		}

		$path = trailingslashit( $path );
		$url  = trailingslashit( $url );

		return array( $path, $url );
	}

	/**
	 * Bootstrap the plugin.
	 */
	public function init() {
		$this->constants();

		// Register autoload for classes.
		require_once WPP_MB_INC_DIR . 'autoloader.php';
		$autoloader = new WPP_MB_Autoloader();
		$autoloader->add( WPP_MB_INC_DIR, 'WPPMB_' );
		$autoloader->add( WPP_MB_INC_DIR, 'WPP_MB_' );
		$autoloader->add( WPP_MB_INC_DIR . 'about', 'WPP_MB_' );
		$autoloader->add( WPP_MB_INC_DIR . 'fields', 'WPP_MB_', '_Field' );
		$autoloader->add( WPP_MB_INC_DIR . 'walkers', 'WPP_MB_Walker_' );
		$autoloader->add( WPP_MB_INC_DIR . 'interfaces', 'WPP_MB_', '_Interface' );
		$autoloader->add( WPP_MB_INC_DIR . 'storages', 'WPP_MB_', '_Storage' );
		$autoloader->add( WPP_MB_INC_DIR . 'helpers', 'WPP_MB_Helpers_' );
		$autoloader->register();

		// Plugin core.
		$core = new WPP_MB_Core();
		$core->init();

		if ( is_admin() ) {
			$about = new WPP_MB_About();
			$about->init();
		}

		// Validation module.
		new WPP_MB_Validation();

		$sanitize = new WPP_MB_Sanitizer();
		$sanitize->init();

		$media_modal = new WPP_MB_Media_Modal();
		$media_modal->init();

		// WPML Compatibility.
		$wpml = new WPP_MB_WPML();
		$wpml->init();

		// Public functions.
		require_once WPP_MB_INC_DIR . 'functions.php';
	}
}
