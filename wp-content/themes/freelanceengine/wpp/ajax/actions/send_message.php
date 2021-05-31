<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	function wpp_send_message() {

		if ( empty( $_POST[ 'data' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
		}

		parse_str( $_POST[ 'data' ], $data );


		if ( ! empty( $data[ 'message_text' ] ) ) {
			$lexer                  = new \nadar\quill\Lexer( $data[ 'message_text' ] );
			$data[ 'message_text' ] = $lexer->render();
		} else {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
		}


		if ( ! empty( $data[ 'media-ids' ] ) ) {

		}


		// вставляем запись в базу данных
		$post_id = wp_insert_post( wp_slash( [
			'post_status'  => 'draft',
			'post_type'    => 'post',
			'post_author'  => get_current_user_id(),
			'post_name'    => wp_trim_words( $data[ 'message_text' ], 6, '' ),
			'post_content' => $data[ 'message_text' ],
			'meta_input'   => [ 'meta_key' => 'meta_value' ],
		] ) );


		wp_send_json_success( [ 'data' => $data ] );

	}