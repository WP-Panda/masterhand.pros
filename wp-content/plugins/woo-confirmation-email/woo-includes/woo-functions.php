<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'XLWUEV_WC_Dependencies' ) ) {
	require_once 'class-xlwuev-wc-dependencies.php';
}

/**
 * WC Detection
 */
if ( ! function_exists( 'xlwuev_is_woocommerce_active' ) ) {
	function xlwuev_is_woocommerce_active() {
		return XLWUEV_WC_Dependencies::woocommerce_active_check();
	}

}
