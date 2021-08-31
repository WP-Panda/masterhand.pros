<?php
if ( defined( 'ABSPATH' ) && ! defined( 'WPP_MB_VER' ) ) {
	require_once dirname( __FILE__ ) . '/inc/loader.php';
	$wpp_mb_loader = new WPP_MB_Loader();
	$wpp_mb_loader->init();
}
