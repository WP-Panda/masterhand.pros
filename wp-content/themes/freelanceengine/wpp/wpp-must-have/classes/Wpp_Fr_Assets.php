<?php
/**
 * Handle frontend scripts
 *
 * @package WooCommerce/Classes
 * @version 2.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend scripts class.
 */
class Wpp_Fr_Assets {
	/**
	 * Contains an array of script handles registered by WPP.
	 *
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by WPP.
	 *
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by WPP.
	 *
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		#add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		#add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path Assets path.
	 *
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'wpp_fr_get_asset_url', plugins_url( $path, WPP_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 *
	 * @param  string $handle Name of the script. Should be unique.
	 * @param  string $path Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered script handles this script depends on.
	 * @param  string $version String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function register_script( $handle, $path, $deps = [ 'jquery' ], $version = WPP_FRAMEWORK, $in_footer = true ) {
		self::$scripts[] = $handle;
		$register = wp_register_script( $handle, $path, $deps, $version, $in_footer );
		#wpp_d_log( $handle . ' - ' . $register );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 *
	 * @param  string $handle Name of the script. Should be unique.
	 * @param  string $path Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered script handles this script depends on.
	 * @param  string $version String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WPP_FRAMEWORK, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 *
	 * @param  string $handle Name of the stylesheet. Should be unique.
	 * @param  string $path Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean $has_rtl If has RTL version to load too.
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = WPP_FRAMEWORK, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 *
	 * @param  string $handle Name of the stylesheet. Should be unique.
	 * @param  string $path Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean $has_rtl If has RTL version to load too.
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = WPP_FRAMEWORK, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters(
			'wpp_fr_enqueue_styles',
			[]
		);
	}

	/**
	 * Register all Wpp_Fr scripts.
	 */
	public static function register_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$register_scripts = [
			'fancybox' => [
				'src'     => self::get_asset_url( 'assets/libs/fancybox/jquery.fancybox' . $suffix . '.js' ),
				'deps'    => ['jquery'],
				'version' => '3.5.7',
			],
			'lightgallery' => [
				'src'     => self::get_asset_url( 'assets/libs/lightgallery/js/lightgallery-all' . $suffix . '.js' ),
				'deps'    => ['jquery'],
				'version' => '1.7.1',
			],
			'slick' => [
				'src'     => self::get_asset_url( 'assets/libs/slick/slick' . $suffix . '.js' ),
				'deps'    => ['jquery'],
				'version' => '1.8.1',
			],
			'swiper' => [
				'src'     => self::get_asset_url( 'assets/libs/swiper/js/swiper' . $suffix . '.js' ),
				'deps'    => ['jquery'],
				'version' => '4.5.0',
			],
			'background-check' => [
			#'src'     => self::get_asset_url( 'assets/libs/background-check/background-check' . $suffix . '.js' ),
			'src'     => self::get_asset_url( 'assets/libs/background-check/background-check.js' ),
			'deps'    => ['jquery'],
			'version' => '4.5.0',
			],
			'flickity' => [
			'src'     => self::get_asset_url( 'assets/libs/flickity/flickity.pkgd.js' ),
			'deps'    => ['jquery'],
			'version' => '2.1.1',
			],
			'clipboard' => [
				'src'     => self::get_asset_url( 'assets/libs/clipboard/clipboard'. $suffix . '.js'  ),
				'deps'    => ['jquery'],
				'version' => '2.0.6',
			],
			'select2' => [
				'src'     => self::get_asset_url( 'assets/libs/select2/select2.full.min.js'  ),
				'deps'    => ['jquery'],
				'version' => '4.0.1',
			],
			'fotorama' => [
				'src'     => self::get_asset_url( 'assets/libs/fotorama/fotorama.js'  ),
				'deps'    => ['jquery'],
				'version' => '3.6.4',
			]
		];

		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}

	}

	/**
	 * Register all WC sty;es.
	 */
	public static function register_styles() {

		$register_styles = [

			'fancybox' => [
				'src'     => self::get_asset_url( 'assets/libs/fancybox/jquery.fancybox.min.css' ),
				'deps'    => '',
				'version' => '3.5.7',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'lightgallery' => [
				'src'     => self::get_asset_url( 'assets/libs/lightgallery/css/lightgallery.min.css' ),
				'deps'    =>'',
				'version' => '1.7.1',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'slick' => [
				'src'     => self::get_asset_url( 'assets/libs/slick/slick.css' ),
				'deps'    => '',
				'version' => '1.8.1',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'slick-theme' => [
				'src'     => self::get_asset_url( 'assets/libs/slick/slick-theme.css' ),
				'deps'    => '',
				'version' => '1.8.1',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'swiper' => [
				'src'     => self::get_asset_url( 'assets/libs/swiper/css/swiper.css' ),
				'deps'    => '',
				'version' => '4.5.0',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'select2' => [
				'src'     => self::get_asset_url( 'assets/libs/select2/select2.min.css' ),
				'deps'    => '',
				'version' => '4.5.0',
				'media'   => 'all',
				'has_rtl' => true,
			],
			'fotorama' => [
				'src'     => self::get_asset_url( 'assets/libs/fotorama/fotorama.css'  ),
				'deps'    => '',
				'version' => '3.6.4',
				'media'   => 'all',
				'has_rtl' => true
			]

		];

		foreach ( $register_styles as $name => $props ) {
		//
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}

	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {

		/*global $post;*/

		if ( ! did_action( 'before_wpp_fr_init' ) ) {
			return;
		}
		self::register_scripts();
		self::register_styles();

		// CSS Styles.
		$enqueue_styles = self::get_styles();
		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}
				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}

	}

	/**
	 * Localize a WC script once.
	 *
	 * @since 2.3.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 *
	 * @param string $handle Script handle the data will be attached to.
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
			$data = self::get_script_data( $handle );
			if ( ! $data ) {
				return;
			}
			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 *
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		global $wp;
		switch ( $handle ) {
			default:
				$params = false;
		}
		$params = apply_filters_deprecated( $handle . '_params', array( $params ), '3.0.0', 'wpp_fr_get_script_data' );

		return apply_filters( 'woocommerce_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

Wpp_Fr_Assets::init();