<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;
	if ( is_admin() ) {
		/** Absolute path to the WordPress directory. */
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', dirname( __FILE__ ) . '/' );
		}
		define( 'CONCATENATE_SCRIPTS', false );
	}

	define( "ET_UPDATE_PATH", "http://update.enginethemes.com/?do=product-update" );
	define( "ET_VERSION", '1.8.7' );
	if ( ! defined( 'ET_URL' ) ) {
		define( 'ET_URL', 'http://www.enginethemes.com/' );
	}
	if ( ! defined( 'ET_CONTENT_DIR' ) ) {
		define( 'ET_CONTENT_DIR', WP_CONTENT_DIR . '/et-content/' );
	}
	define( 'TEMPLATEURL', get_template_directory_uri() );
	$theme_name = 'freelanceengine';
	define( 'THEME_NAME', $theme_name );
	define( 'ET_DOMAIN', 'enginetheme' );
	define( 'MOBILE_PATH', TEMPLATEPATH . '/mobile/' );
	define( 'PROFILE', 'fre_profile' );
	define( 'PROJECT', 'project' );
	define( 'ADVERT', 'advert' );
	define( 'COMPANY', 'company' );
	define( 'BID', 'bid' );
	define( 'PORTFOLIO', 'portfolio' );
	define( 'EMPLOYER', 'employer' );
	define( 'FREELANCER', 'freelancer' );
	define( 'PRICE', 'price' );
	define( 'CURRENCY', 'currency' );
	// define( 'ALLOW_UNFILTERED_UPLOADS', true );
	if ( ! defined( 'THEME_CONTENT_DIR ' ) ) {
		define( 'THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . $theme_name );
	}
	if ( ! defined( 'THEME_CONTENT_URL' ) ) {
		define( 'THEME_CONTENT_URL', content_url() . '/et-content' . '/' . $theme_name );
	}
	// theme language path
	if ( ! defined( 'THEME_LANGUAGE_PATH' ) ) {
		define( 'THEME_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang/' );
	}
	if ( ! defined( 'ET_LANGUAGE_PATH' ) ) {
		define( 'ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang' );
	}
	if ( ! defined( 'ET_CSS_PATH' ) ) {
		define( 'ET_CSS_PATH', THEME_CONTENT_DIR . '/css' );
	}
	if ( ! defined( 'USE_SOCIAL' ) ) {
		define( 'USE_SOCIAL', 1 );
	}