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
			echo json_encode( [ 'status' => 'OK' ] );
		} else {
			echo json_encode( [ 'status' => 'FAILED' ] );
		}
	}

	die();
}