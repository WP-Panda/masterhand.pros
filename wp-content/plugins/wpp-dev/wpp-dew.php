<?php
	/*
		Plugin Name: WPP DEV
		Plugin URI: https://wppanda.com?target=wpp_dev
		Description: Функции для разработки
		Author: Максим WP Panda
		Author URI: https://wppanda.com
		Text Domain: wpp-dev
		Domain Path: /languages/
		Version: 0.0.2
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	define( 'WPP_LOCAL', true );
	#define( 'WPP_PANDA', '212.87.172.192' );
	define( 'WPP_PANDA', '127.0.0.1' );

	if ( ! function_exists( 'wpp_admin_notice' ) ) :
		/**
		 * Message in admin
		 *
		 * @since 0.0.1
		 */
		function wpp_admin_notice() {
			printf( '<div class="notice noticewarning"><p>%s</p></div>', __( 'ATTENTION! DO NOT DEACTIVATE WPP DEV. WE WILL TURN IT OFF AFTER WORK IS COMPLETE !', 'wpp-dev' ) );
		}

	endif;

	add_action( 'admin_notices', 'wpp_admin_notice' );

	require_once 'functions/init.php';
	require_once 'libs/init.php';