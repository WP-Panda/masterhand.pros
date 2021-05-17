<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all available payment gateways from admin settings
 *
 * @since 1.0
 * @return array Array of payment gateways object
 */
function marketengine_get_available_payment_gateways() {
	$available_gateways =  array(
		'ppadaptive' => ME_PPAdaptive_Request::instance()
	);
	return apply_filters('marketengine_available_payment_gateways', $available_gateways);
}

/** 
 * Check a gateway is available or not
 * @since 1.0
 * @return bool
 */
function marketengine_is_available_payment_gateway($gateway) {
	$available_gateways = marketengine_get_available_payment_gateways();
	return isset($available_gateways[$gateway]);
}

/**
 * Retrive site currency settings
 * @return Array
 * @since 1.0
 */
function get_marketengine_currency() {
	$sign = marketengine_option('payment-currency-sign', '$');
    $code = marketengine_option('payment-currency-code', 'USD');
    $is_align_right = marketengine_option('currency-sign-postion') ? true : false;
    $label = marketengine_option('payment-currency-label', 'USD');
    return compact('sign', 'code', 'is_align_right', 'label');
}

add_filter('marketengine_currency_code', 'get_marketengine_currency');