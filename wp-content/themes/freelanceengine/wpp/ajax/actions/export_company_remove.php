<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Удаление компании
	 */
	function wpp_export_company_remove() {

		if ( empty( $_POST[ 'id' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 3 ) ] );
		}

		$_ID = $_POST[ 'id' ];

		company_delete( $_ID );

		wp_send_json_success( [ 'msg' => 'OK' ] );

	}