<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	if ( ! function_exists( 'wpp_validate_file_ext' ) ) :

		/**
		 * Проверка расширения файла
		 *
		 * @param       $file
		 * @param array $exts
		 *
		 * @return bool
		 */
		function wpp_validate_file_ext( $file, $exts = [] ) {

			$ext = pathinfo( $file, PATHINFO_EXTENSION );

			return in_array( $ext, $exts );

		}

	endif;


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


	if ( ! function_exists( 'wpp_clean_str' ) ) :

		/**
		 * Очистка строки, оставляет только цифры, буквы, и одиночные пробелы
		 *
		 * @param $str
		 *
		 * @return null|string
		 */
		function wpp_clean_str( $str ) {
			$str = preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/u", '', $str );
			$str = preg_replace( '/\s{2,}/', ' ', $str );

			return $str;
		}

	endif;