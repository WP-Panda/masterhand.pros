<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	$array = [
		'wpp_paginate'                 => true,
		'wpp_filter_company'           => true,
		'wpp_send_quote_company'       => false,
		'wpp_c_upload_file'            => false,
		'wpp_export_company_upload'    => false,
		'wpp_export_company_edit_load' => false,
		'wpp_export_company_edit'      => false,
		'wpp_export_company_remove'    => false,
		'wpp_interval_export'          => false
	];


	foreach ( $array as $key => $val ) {

		add_action( 'wp_ajax_' . $key, $key );

		if ( (bool) $val === true ) {
			add_action( 'wp_ajax_nopriv_' . $key, $key );
		}


		require_once str_replace( 'wpp_', '', $key ) . '.php';
	}


