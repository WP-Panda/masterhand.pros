<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_handle_deleted_media() {

	if ( isset( $_REQUEST['media_id'] ) ) {
		$post_id = absint( $_REQUEST['media_id'] );

		$status = wp_delete_attachment( $post_id, true );

		if ( $status ) {
			wp_send_json( [ 'status' => 'OK' ] );
		} else {
			wp_send_json( [ 'status' => 'FAILED' ] );
		}
	}
}
