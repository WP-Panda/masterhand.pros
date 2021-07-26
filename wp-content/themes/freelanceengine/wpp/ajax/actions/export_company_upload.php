<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Экспорт компании
 */
function wpp_export_company_upload() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpp_company_data';

	$_result = $wpdb->get_results( "SELECT * FROM $table_name" );

	$n   = 1;
	$out = [];
	foreach ( $_result as $key => $val ) {

		foreach ( $val as $key_item => $val_item ) {
			if ( $key_item === 'date' ) {
				continue;
			}
			if ( $n === 1 ) {

				$out[0][] = $key_item;

			}

			if ( $key_item === 'rating' ) {
				$val_item = str_replace( '.', ',', $val_item );
			}

			if ( empty( $val_item ) ) {
				$val_item = '';
			}

			$out[ $n ][] = $val_item;
		}

		$n ++;
	}

	$dir = wp_upload_dir();

	$wrte_file_preff = '/wpp/company/export/' . date( 'd_M_Y_H_i_s' ) . '.csv';


	$fp = fopen( $dir['basedir'] . $wrte_file_preff, 'w' );

	foreach ( $out as $fields ) {
		fputcsv( $fp, $fields, ',' );
	}

	fclose( $fp );

	wp_send_json_success( [ 'msg' => sprintf( '<a href="%s">%s</a>', $dir['baseurl'] . $wrte_file_preff, __( 'Download export file', 'wpp' ) ) ] );


}
