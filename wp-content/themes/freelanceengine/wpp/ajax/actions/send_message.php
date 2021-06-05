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
			$lexer        = new \nadar\quill\Lexer( $data[ 'message_text' ] );
			$message_text = $lexer->render();
		} else {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
		}

		if ( ! empty( $data[ 'media-ids' ] ) ) {
			$media_ids = explode( ',', $data[ 'media-ids' ] );
			$media_ids = array_filter( $media_ids );

			$thumb_id = array_shift( $media_ids );
		}

		if ( ! empty( $data[ 'title' ] ) ) {
			$title = esc_attr( $data[ 'title' ] );
		} else {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
		}

		if ( ! empty( $data[ 'term' ] ) ) {
			$term = absint( $data[ 'term' ] );
		}

		$insert_array = [
			#'post_status'   => 'draft',
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_author'   => get_current_user_id(),
			'post_title'    => $title,
			'post_content'  => $message_text,
			'post_category' => [ $term ]

		];


		$post_id = wp_insert_post( wp_slash( $insert_array ) );

		if ( ! empty( $post_id ) && ! empty( $thumb_id ) ) {
			$thumb = set_post_thumbnail( $post_id, absint( $thumb_id ) );
			if ( ! empty( $media_ids ) ) {
				update_post_meta( $post_id, '_wpp_post_gallery', implode( ',', $media_ids ) );
			}
		}


		wp_send_json_success( [ 'post_id'  => $post_id,
		                        'insert'   => $insert_array,
		                        'data'     => $data,
		] );

	}