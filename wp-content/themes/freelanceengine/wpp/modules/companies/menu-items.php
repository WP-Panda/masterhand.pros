<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'wpp_menu_page' );

function wpp_menu_page() {

	add_menu_page( 'Company List', 'Company', 'manage_options', 'wpp_company', 'wpp_company_table', 'dashicons-star-half', 5 );

	add_submenu_page( 'wpp_company', 'Company Actions', 'Company Actions', 'manage_options', 'wpp_company_actions', 'wpp_action_liat_page' );

}