<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Импорт компаний
	 */
	function wpp_c_upload_file() {

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}


		if ( empty( $_FILES[ 'wpp_company_import_file' ] ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 1 ) ] );
		}

		$_WPP_FILE = $_FILES[ 'wpp_company_import_file' ];

		$ext_flag = wpp_validate_file_ext( $_WPP_FILE[ 'name' ], [ 'csv' ] );

		if ( empty( $ext_flag ) ) {
			wp_send_json_error( [ 'msg' => wpp_message_codes( 2 ) . ' ' . pathinfo( $_WPP_FILE[ 'name' ], PATHINFO_EXTENSION ) ] );
		}

		add_filter( 'upload_dir', 'wpp_alter_the_upload_dir' );

		$id = media_handle_upload( 'wpp_company_import_file', 0 );

		if ( is_wp_error( $id ) ) {
			wp_send_json_error( [ 'msg' => $id->get_error_messages() ] );
		}

		$_companies = wpp_str_getcsv( get_attached_file( $id ) );

		$flag_array = $_companies[ 0 ];
		unset( $_companies[ 0 ] );

		$data_array = [];
		foreach ( $_companies as $_key => $company ) {
			foreach ( $flag_array as $key => $val ) {
				$data_array[ $_key ][ $val ] = $company[ $key ];
			}
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpp_company_data';

		$new = $updated = 0;

		foreach ( $data_array as $one_row ) {

			$one_row = array_filter( $one_row );


			//замена запятой на точку для sql float
			$one_row[ 'rating' ] = str_replace( ',', '.', $one_row[ 'rating' ] );

			//проверка наличия компании в списке
			if ( ! empty( $one_row[ 'id' ] ) ) :
				$check = $wpdb->get_row( "SELECT * FROM $table_name WHERE `id` = " . $one_row[ 'id' ] );
			else:
				$check = false;
			endif;

			//добавление компании в список
			if ( empty( $check ) ) {
				$one_row[ 'date' ] = current_time( 'mysql' );

				$max               = $wpdb->get_results( "SELECT `id` FROM $table_name ORDER BY `id` DESC LIMIT 1" );
				if ( ! empty( $max ) ) {
					$one_row[ 'id' ] = (int) $max[ 0 ]->id + 1;
				} else {
					$one_row[ 'id' ] = 10000001;
				}
				$insert = $wpdb->insert( $table_name, $one_row );
				if ( empty( $insert ) ) {
					wpp_d_log( $one_row );
				}
				$new ++;
			} else {
				//обновление компании в списке
				$wpdb->update( $table_name, $one_row, [ 'id' => $one_row[ 'id' ] ] );

				$updated ++;
			}

		}

		wp_send_json_success( [ 'msg' => '<p>Added ' . $new . '<br>Updated ' . $updated ] );
	}