<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	$array = [
		'profile-meta'
	];

	foreach ( $array as $file ) :
		require_once $file . '.php';
	endforeach;