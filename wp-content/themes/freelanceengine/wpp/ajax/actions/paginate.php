<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_paginate() {

	if ( empty( $_POST['page'] ) ) {
		wp_send_json_error( [ 'msg' => wpp_message_codes( 4 ) ] );
	}


	if ( empty( (int) $_POST['id'] ) ) {
		wp_send_json_error( [ 'msg' => wpp_message_codes( 5 ) ] );
	}


	$country = get_post_meta( $_POST['id'], 'company_in_country', true );


	parse_str( $_POST['data'], $data_params );

	/*if ( empty( $_POST[ 'data' ] ) ) {
		$base = explode( '?', $_POST[ 'page' ] )[ 0 ];
	} else {*/
	$base = $_POST['page'];
	//}


	$_paginate_data = wpp_custom_paginate_base( $base );

	$company_data = wpp_company_query( $country, $_paginate_data->page, $data_params );


	ob_start();
	wpp_get_template_part( 'wpp/templates/companies/company-list', [ 'companies' => $company_data['companies'] ] );
	$_posts = ob_get_clean();


	ob_start();
	wpp_get_template_part( 'wpp/templates/universal/paginate', [
		'pages'         => ceil( $company_data['found_posts_num'] / COMPANY_PER_PAGE ),
		'ajax_wpp'      => true,
		'current'       => $_paginate_data->page,
		'paginate_base' => $_paginate_data->paginate_base
	] );
	$_paginate = ob_get_clean();

	ob_start();
	wpp_get_template_part( 'wpp/templates/universal/found-indicator', $company_data['found_labels'] );
	$_founds = ob_get_clean();


	wp_send_json_success( [ 'posts' => $_posts, 'paginate' => $_paginate, 'founds' => $_founds ] );


	die();
}