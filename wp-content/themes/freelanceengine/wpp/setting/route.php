<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	function wpp_endpoints( $points ) {
		$points[ 'companies' ] = [
			'title'    => __( 'Item Title' ),
			'icons'    => '',
			'template' => WPP_THEME_DIR . '/template/wpp/company.php',
			'order'    => 5,
			'caps'     => 'manage_options',
			'places'   => EP_ROOT
		];

		do_action( 'qm/debug', $points );

		return $points;
	}


	add_filter( 'wpp_pf_endpoints_args', 'wpp_endpoints' );