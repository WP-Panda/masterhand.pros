<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Удаление нотиса
 *
 * @author ThanhTu
 */
function wpp_notify_remove() {
	global $user_ID;
	$request = $_REQUEST;

	$return = [ 'success' => false ];

	$data = [ 'id' => $request['ID'], 'user_id' => $user_ID ];

	$del = WPP_Messages::delete( $data );


	if ( ! empty( $del ) ) {
		$return = [
			'success' => true
		];

		WPP_Notis::update_notify_count( $user_ID, true );
	}

	wp_send_json( $return );
}