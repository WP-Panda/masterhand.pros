<?php
	/**
	 * @package    WPP_Core
	 * @subpackage Gallery Images
	 * @author     WP_Panda
	 * @version    2.0.0
	 *
	 * Необходимые функции файл подключается если расширение используется отдельно
	 * Без использования всего WPP_Core
	 */

	defined( 'ABSPATH' ) || exit;

	if ( ! function_exists( 'wpp_clean' ) ) :

		/**
		 * Очистка Данных
		 *
		 * @param $var
		 *
		 * @return array|string
		 */
		function wpp_clean( $var ) {
			if ( is_array( $var ) ) {
				return array_map( 'wpp_clean', $var );
			} else {
				return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
			}
		}

	endif;