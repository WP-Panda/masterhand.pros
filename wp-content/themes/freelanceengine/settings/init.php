<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	$dir = __DIR__ . DIRECTORY_SEPARATOR . 'options';


	$scan = scandir( $dir );


	unset( $scan[ 0 ], $scan[ 1 ] ); //unset . and ..
	foreach ( $scan as $file ) {
		require_once $dir . DIRECTORY_SEPARATOR . $file;
	}