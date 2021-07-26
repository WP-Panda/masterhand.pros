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
function wpp_interval_export() {


	parse_str( $_POST['form'], $data );


	$company_args = [
		'per_page' => 'all'
	];

	#поиск
	if ( ! empty( $data['s'] ) ) {
		$company_args['s'] = $data['s'];
	}

	#интервал
	if ( ! empty( $data['ids_str'] ) ) {
		$company_args['interval'] = $data['ids_str'];
	}

	#сортировка
	$company_args['orderby'] = ( ! empty( $data['orderby'] ) ) ? $data['orderby'] : 'id'; //If no sort, default to title
	$company_args['order']   = ( ! empty( $data['order'] ) ) ? $data['order'] : 'asc'; //If no order, default to asc

	$query_c = new WPP_Company_Query( $company_args );
	$_result = $query_c->get_companies();


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