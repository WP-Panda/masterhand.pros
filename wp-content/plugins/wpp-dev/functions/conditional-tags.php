<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_PANDA
	 * Date: 09.03.2019
	 * Time: 12:08
	 */

	if ( ! function_exists( 'is_wpp_panda' ) ) :

		/**
		 * А WP panda то или нет
		 *
		 * @since 0.0.1
		 *
		 * @return bool
		 */
		function is_wpp_panda() {

			if ( defined( 'WPP_LOCAL' ) && WPP_LOCAL === true ) {
				return true;
			}


			$ip = '';

			if ( isset( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
				$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
			} else if ( isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
				$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
			} else if ( isset( $_SERVER[ 'HTTP_X_FORWARDED' ] ) ) {
				$ip = $_SERVER[ 'HTTP_X_FORWARDED' ];
			} else if ( isset( $_SERVER[ 'HTTP_FORWARDED_FOR' ] ) ) {
				$ip = $_SERVER[ 'HTTP_FORWARDED_FOR' ];
			} else if ( isset( $_SERVER[ 'HTTP_FORWARDED' ] ) ) {
				$ip = $_SERVER[ 'HTTP_FORWARDED' ];
			} else if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
				$ip = $_SERVER[ 'REMOTE_ADDR' ];
			} else {
				$ip = 'UNKNOWN';
			}

			return ( $ip === WPP_PANDA ) ? true : false;

		}

	endif;
