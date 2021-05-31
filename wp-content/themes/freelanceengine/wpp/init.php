<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	# количество компаний на страницу
	if ( ! defined( 'COMPANY_PER_PAGE' ) ) :
		define( 'COMPANY_PER_PAGE', 10 );
	endif;

	$deny_params = [ 'cat', 'sub', 'country', 'state', 'city', 'string' ];

	# разрешенные гет параметры для фильтра
	if ( ! defined( 'FIlTER_DENY_PARAMS' ) ) :
		define( 'FIlTER_DENY_PARAMS', $deny_params );
	endif;

	if ( ! defined( 'WPP_THEME_DIR' ) ) {
		define( 'WPP_THEME_DIR', get_template_directory() );
	}

	$array = [
		'wpp-core/init',
		'helpers/init',
		'users/init',
		'fixes',
		'php_ext',
		'db',
		'companies/init',
		'error-api',
		'helpers',
		'ajax/actions/init',
		'setting/init'
	];

	foreach ( $array as $file ) :
		require_once $file . '.php';
	endforeach;


	add_action( 'wp_ajax_handle_dropped_media', 'handle_dropped_media' );

	// if you want to allow your visitors of your website to upload files, be cautious.
	add_action( 'wp_ajax_nopriv_handle_dropped_media', 'handle_dropped_media' );


	function handle_dropped_media() {
		//status_header( 200 );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		if ( ! empty( $_FILES ) ) {

			$_files = [
				"name"     => $_FILES['file'][ 'name' ][ 0 ],
				"type"     => $_FILES['file'][ 'type' ][ 0 ],
				"tmp_name" => $_FILES['file'][ 'tmp_name' ][ 0 ],
				"error"    => $_FILES['file'][ 'error' ][ 0 ],
				"size"     => $_FILES['file'][ 'size' ][ 0 ]
			];


			$_FILES = [ 'file' => $_files ];

			foreach ( $_FILES as $file => $array ) {

				if ( $_FILES[ $file ][ 'error' ] !== UPLOAD_ERR_OK ) { // If there is some errors, during file upload
					wp_send_json_error( [
						'message' => __( 'Error:' ) . $file[ 'error' ]
					] );
				}



				$post_id = 0; // Set post ID to attach uploaded image to specific post

				$attachment_id = media_handle_upload( $file, $post_id );


				if ( is_wp_error( $attachment_id ) ) { // Check for errors during attachment creation
					wp_send_json_error([
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

	add_action( 'wp_ajax_handle_deleted_media', 'handle_deleted_media' );

	function handle_deleted_media() {

		if ( isset( $_REQUEST[ 'media_id' ] ) ) {
			$post_id = absint( $_REQUEST[ 'media_id' ] );

			$status = wp_delete_attachment( $post_id, true );

			if ( $status ) {
				echo json_encode( [ 'status' => 'OK' ] );
			} else {
				echo json_encode( [ 'status' => 'FAILED' ] );
			}
		}

		die();
	}