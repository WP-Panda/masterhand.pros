<?php
/**
 * @package brabus.coms
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * генерация случайной строки
 *
 * @param null $count длиннв
 *
 * @return string
 */
function wpp_fr_rnd_string( $count = null ) {

	$size = ! empty( (int) $count ) ? (int) $count : 16;

	$bytes = random_bytes( $size );

	return bin2hex( $bytes );
}

function e_wpp_fr_rnd_string( $count = null ) {
	$size = ! empty( (int) $count ) ? (int) $count : 16;
	echo wpp_fr_rnd_string( $size );
}