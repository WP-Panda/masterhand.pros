<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


function wpp_create_time_info( $post_ID ) {

	$code  = get_post_meta( $post_ID, 'create_time', true );
	$code = $code ?: 6;
	$array = wpp_br_create_time_array();
	if ( wpp_fr_user_is_admin() && ! is_single() && ! is_cart() && !is_home() && !is_front_page() ) {

		$options = '';
		foreach ( $array as $codes => $title ) {
			$options .= sprintf( '<option value="%s"%s>%s</option>', $codes, selected( $code, $codes, false ), $title );
		}
		printf( '<p style="margin-bottom: 5px;"><span><select class="wpp_create_period_date">%s</select></span></p>', $options );

	} else {

		$text = sprintf( '<span class="wpp_br_time_text">%s</span>', $array[ $code ] );
		$img  = sprintf( '<img class="wpp-br-box-image" src="%s/assets/img/icons/box-%s.svg" alt="">', get_template_directory_uri(), $code );

		printf( '<p class="wpp-br-time-p"><span>%s%s</span></p>', $img, $text );

	}
}


/**
 * NTCN
 */
function ghfg() {
	wp_add_dashboard_widget( 'wpp_dashboard_status', __( 'Wpp Status', 'woocommerce' ), 'wpp_status_widget' );
}

add_action( 'dashboard_widget', 'ghfg' );

function wpp_status_widget() {
	echo 'fffffff';
}