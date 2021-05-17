<?php
/**
 * ME Admin Functions
 *
 * @author   EngineThemes
 * @category Function
 * @package  Admin/Functions
 * @since    1.0.1
 * @version  1.0.0
 */

/**
 * Filters admin notices
 *
 * Fires when admin site loaded.
 *
 * @category    Admin
 * @since       1.0.1
 */
function marketengine_admin_notice_filter( $notices ) {

	$payment_setting = marketengine_check_payment_setting();	
	$link = marketengine_menu_page_url('me-settings', 'payment-gateways');

	if( !$payment_setting && 'me-settings' === $_GET['page']) {
		$notices['payment_gateway_error'] = sprintf(__("Your site currently can't process payment yet, since your PayPal API hasn't been set up correctly.<br/>Please visit <a href='%s'>this page</a> to fix the issue."), $link );
	}

	return $notices;
}
add_filter('marketengine_admin_notices', 'marketengine_admin_notice_filter');


/**
 * Checks if payment gateway is correct
 *
 * @category    Admin
 * @since       1.0.1
 */
function marketengine_check_payment_setting() {
	$paypal_email = marketengine_option('paypal-receiver-email');
	$paypal_app_id = marketengine_option('paypal-app-api');
	$paypal_api_username = marketengine_option('paypal-api-username');
	$paypal_api_password = marketengine_option('paypal-api-password');
	$paypal_api_signature = marketengine_option('paypal-api-signature');

	return ( isset( $paypal_email ) && !empty( $paypal_email ) && is_email( $paypal_email )
			&& isset( $paypal_app_id ) && !empty( $paypal_app_id )
			&& isset( $paypal_api_username ) && !empty( $paypal_api_username )
			&& isset( $paypal_api_password ) && !empty( $paypal_api_password )
			&& isset( $paypal_api_signature ) && !empty( $paypal_api_signature ) );
}

/**
 * Get the url to access a particular menu page based on the slug it was registered with.
 *
 * If the slug hasn't been registered properly no url will be returned
 *
 * @since 1.0.1
 *
 * @global array $_parent_pages
 *
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param string $tab The tab name to refer to this menu (optional).
 * @param bool $echo Whether or not to echo the url - default is true
 * @return string the url
 */
function marketengine_menu_page_url( $menu_slug = 'me-settings', $tab = '', $echo = false) {
	global $_parent_pages;

	if ( isset( $_parent_pages[$menu_slug] ) ) {
		$parent_slug = $_parent_pages[$menu_slug];
		if ( $parent_slug && ! isset( $_parent_pages[$parent_slug] ) ) {
			$url = admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
		} else {
			$url = admin_url( 'admin.php?page=' . $menu_slug );
		}

		if( !empty($tab) ) {
			$url = add_query_arg( 'tab', $tab, $url );
		}

	} else {
		$url = '';
	}

	$url = esc_url($url);

	if ( $echo )
		echo $url;

	return $url;
}