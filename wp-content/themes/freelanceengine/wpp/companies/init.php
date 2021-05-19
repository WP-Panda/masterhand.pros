<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	$files = [
		'menu-items',
		'assets',
		'company-functions',
		'WPP_Company_Query',
		'pages/actions',
		'pages/data-table',
		'labels'
	];

	foreach ( $files as $file ) :
		require_once __DIR__ . '/' . $file . '.php';
	endforeach;