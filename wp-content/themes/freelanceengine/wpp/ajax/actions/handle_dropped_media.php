<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_handle_dropped_media() {

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	if ( ! empty( $_FILES ) ) {

		$_files = [
			"name"     => $_FILES['file']['name'][0],
			"type"     => $_FILES['file']['type'][0],
			"tmp_name" => $_FILES['file']['tmp_name'][0],
			"error"    => $_FILES['file']['error'][0],
			"size"     => $_FILES['file']['size'][0]
		];


		$_FILES = [ 'file' => $_files ];

		foreach ( $_FILES as $file => $array ) {

			if ( $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) { // If there is some errors, during file upload
				wp_send_json_error( [
					'message' => __( 'Error:' ) . $file['error']
				] );
			}

			$post_id = 0; // Set post ID to attach uploaded image to specific post

			$attachment_id = media_handle_upload( $file, $post_id );

			if ( is_wp_error( $attachment_id ) ) { // Check for errors during attachment creation
				wp_send_json_error( [
					'message' => __( 'Error while processing file', 'mwp-dropform' ),
				] );
			} else {
				wp_send_json_success( [
					'attachment_id' => $attachment_id,
					'message'       => __( 'File uploaded', 'mwp-dropform' ),
				] );
			}
		}


		wp_send_json( [ 'status' => 'error', 'message' => __( 'There is nothing to upload!', 'mwp-dropform' ) ] );
	}
}