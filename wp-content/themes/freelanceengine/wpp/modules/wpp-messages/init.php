<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 *
 */
$array = [
	'WPP_Messages',
	'WPP_Messages_Install',
	'WPP_Notis'
];

wpp_require( $array, __DIR__ );