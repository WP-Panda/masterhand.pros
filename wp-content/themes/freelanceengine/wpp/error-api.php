<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Коды ошибок
 *
 * @param null $code
 *
 * @return string|
 */
function wpp_message_codes( $code = null ) {

	if ( empty( $code ) ) {
		$code = 404;
	}

	switch ( $code ) {
		case 1:
			$out = __( 'file not transferred', 'wpp' );
			break;
		case 2:
			$out = __( 'invalid file extension', 'wpp' );
			break;
		case 3:
			$out = __( 'ID is empty', 'wpp' );
			break;
		case 4:
			$out = __( 'Send data is empty', 'wpp' );
			break;
		case 5:
			$out = __( 'invalid ID', 'wpp' );
			break;
		case 6:
			$out = __( 'invalid ID', 'wpp' );
			break;
		case 404:
		default:
			$out = __( 'unknown error', 'wpp' );
			break;
	}

	return $out;

}