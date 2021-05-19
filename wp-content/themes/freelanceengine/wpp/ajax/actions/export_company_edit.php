<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Редактирование компании
	 */
	function wpp_export_company_edit() {

		if ( empty( $_POST[ 'data' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
		}

		parse_str( $_POST[ 'data' ], $form_data );

		if ( empty( $form_data[ 'id' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 3 ) ] );
		}

		$_ID = $form_data[ 'id' ];
		unset( $form_data[ 'id' ] );

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpp_company_data';
		$check      = $wpdb->get_row( "SELECT * FROM $table_name WHERE `id` = " . $_ID );

		if ( empty( $check ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 5 ) ] );
		}

		$update = $wpdb->update( $table_name, $form_data, [ 'id' => $_ID ] );


		wp_send_json_success( [ 'msg' => 'OK' ] );

	}