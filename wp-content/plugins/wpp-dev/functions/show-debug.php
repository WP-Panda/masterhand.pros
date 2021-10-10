<?php
/**
 * Created by PhpStorm.
 * User: WP_PANDA
 * Date: 09.03.2019
 * Time: 13:09
 */

if ( ! function_exists( 'wpp_dump' ) ) :

	/**
	 * var_dump for wp-panda
	 *
	 * @since 0.0.1
	 *
	 * @param $data
	 */
	function wpp_dump( $data ) {
		if ( is_wpp_panda() ) {
			echo '<pre>';
			var_dump( $data );
			echo '</pre>';
		}
	}

endif;

if ( ! function_exists( 'wpp_d_log' ) ) :
	/**
	 * echo log in file
	 *
	 * @since 0.0.1
	 *
	 * @param $log
	 */
	function wpp_d_log( $log ) {
		//if ( is_wpp_panda() ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
		//}
	}
endif;

if ( ! function_exists( 'wpp_console' ) ) :
	/**
	 * Вывод в консоль
	 */
	function wpp_console( $data ) {
		return new Wpp_Console_Log( $data );
	}
endif;

if ( ! function_exists( 'wpp_console' ) ) :
	/**
	 * Складывю всякое в глобальне переменную для показать в дебаг баре
	 */
	function wpp_d_bar( $data, $desc = '' ) {

		if ( empty( $GLOBALS['wpp_debug'] ) ) {
			$GLOBALS['wpp_debug'] = [];
		}

		$GLOBALS['wpp_debug'][ $desc ] = $data;

	}
endif;

function wpp_d_action( $data, $text = '' ) {
	if ( is_wpp_panda() ) {
		echo '<pre>';
		echo !empty($text) ? $text : '';
		var_dump( $data );
		echo '</pre>';
	}
}

add_action( 'wpp_dump', 'wpp_d_action', 10, 2 );