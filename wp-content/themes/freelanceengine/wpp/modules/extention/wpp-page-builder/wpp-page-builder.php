<?php
	
	/* Do not access this file directly */
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	
	define( 'WPP_PB_VERSION', '1.0.2' );
	
	
	require_once 'includes/functions.php';
	
	if ( is_admin() ) {
		require_once 'includes/page-builder.php';
	}
	
	require_once 'includes/front-end.php';

