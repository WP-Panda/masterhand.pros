<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	function wpp_backend_assets() {
		if ( is_admin() ) :
			wp_enqueue_style( 'wpp-admin-classes', get_stylesheet_directory_uri() . '/wpp/assets/css/style.css' );
		endif;
	}


	add_action( 'admin_enqueue_scripts', 'wpp_backend_assets' );