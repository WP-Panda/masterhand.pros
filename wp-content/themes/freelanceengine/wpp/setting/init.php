<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	$array = [ 'route' ];

	foreach ( $array as $file ) :
		require_once $file . '.php';
	endforeach;
