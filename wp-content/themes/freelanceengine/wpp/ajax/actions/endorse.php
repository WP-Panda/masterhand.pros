<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_endorse() {
	/**
	 * todo en nfrb cnjbn lj,fdbnm ljg ghjdthre
	 */
	if ( empty( $_POST['uid'] ) ) {
		wp_send_json_error( [ 'msg' => __( 'Undefened User', WPP_TEXT_DOMAIN ) ] );
	}

	if ( empty( $_POST['skill'] ) ) {
		wp_send_json_error( [ 'msg' => __( 'Undefened Skill', WPP_TEXT_DOMAIN ) ] );
	}

	do_action( 'wpp_before_likes', (int) $_POST['uid'] );

	$result = WPP_Skills_Actions::getInstance()->add_likes( (int) $_POST['uid'], (int) $_POST['skill'] );

	if ( empty( $result ) ) {
		wp_send_json_error( [ 'msg' => __( 'Error', WPP_TEXT_DOMAIN ) ] );
	}

	if ( ! empty( $result['error'] ) ) {
		wp_send_json_error( [ 'msg' => $result['msg'] ] );
	}

	do_action( 'wpp_after_likes', (int) $_POST['uid'] );

	wp_send_json_success( [ 'msg' => $result['msg'] ] );

}