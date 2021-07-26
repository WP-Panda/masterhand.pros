<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'WPP_FIX_V', '0.0.1' );


function wpp_body_classes( $classes ) {

	if ( is_page_template( 'page-top_companies_in_country.php' ) || is_page_template( 'pages/page-top_companies_in_country.php' ) ) {
		$classes[] = 'no-bb-paginate';
	}

	return $classes;
}

add_filter( 'body_class', 'wpp_body_classes' );