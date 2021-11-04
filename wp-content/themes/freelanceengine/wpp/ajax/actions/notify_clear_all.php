<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Удаление всех нотисов
 */
function wpp_notify_clear_all() {
	global  $user_ID;

	$return = [ 'success' => false ];

	$data = [ 'user_id' => $user_ID ];

	$del = WPP_Messages::delete( $data );

	if ( ! empty( $del ) ) {
		$return = [
			'success' => true
		];

		update_user_meta( $user_ID, 'wpp_new_notify', 0 );
	}

	wp_send_json( $return );
}