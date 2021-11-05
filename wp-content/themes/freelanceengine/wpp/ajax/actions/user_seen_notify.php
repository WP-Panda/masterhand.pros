<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Удаление значка - просмотрено при открытии листа
 *
 * @author ThanhTu
 */
function wpp_user_seen_notify() {
	global $user_ID;
	$result = update_user_meta( $user_ID, 'wpp_new_notify', 0 );
	$return = [ 'success' => true ];
	if ( is_wp_error( $result ) ) {
		$return = [
			'success' => false
		];
	}
	wp_send_json( $return );
}

add_action( 'wp_ajax_fre-user-seen-notify', 'wpp_user_seen_notify' );