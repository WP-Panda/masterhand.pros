<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	function wpp_un_endorse() {

		if ( empty( $_POST[ 'uid' ] ) ) {
			wp_send_json_error( [ 'msg' => __( 'Undefened User', WPP_TEXT_DOMAIN ) ] );
		}

		if ( empty( $_POST[ 'skill' ] ) ) {
			wp_send_json_error( [ 'msg' => __( 'Undefened Skill', WPP_TEXT_DOMAIN ) ] );
		}

		$result = WPP_Skills_Actions::getInstance()->remove_likes( (int) $_POST[ 'uid' ], (int) $_POST[ 'skill' ] );

		if ( empty( $result ) ) {
			wp_send_json_error( [ 'msg' => __( 'Error', WPP_TEXT_DOMAIN ) ] );
		}

		if ( ! empty( $result[ 'error' ] ) ) {
			wp_send_json_error( [ 'msg' => $result[ 'msg' ] ] );
		}

		wp_send_json_success( [ 'msg' => $result[ 'msg' ] ] );

	}