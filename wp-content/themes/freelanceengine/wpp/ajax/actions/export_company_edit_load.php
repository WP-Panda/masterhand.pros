<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Загрузка формы редактирования копании
	 */
	function wpp_export_company_edit_load() {

		if ( empty( $_POST[ 'id' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 3 ) ] );
		} else {
			$_ID = (int) $_POST[ 'id' ];
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpp_company_data';
		$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE`id`=" . $_ID );

		$out = '';

		if ( ! empty( $result ) ) {

			foreach ( $result as $key => $value ) {

				if ( $key === 'date' || $key === 'rating_count' ) {
					continue;
				}
				if ( $key === 'id' ) {
					$out .= sprintf( '<input type="hidden" name="%s" value="%s">', $key, $value );
				} else {
					$out .= sprintf( '<div class="wpp-form-row wpp-6"><label>%s<br><input type="text" name="%s" value="%s"></label></div>', str_replace( '_', ' ', $key ), $key, $value );
				}
			}
		}

		wp_send_json_success( [ 'content' => $out ] );


	}