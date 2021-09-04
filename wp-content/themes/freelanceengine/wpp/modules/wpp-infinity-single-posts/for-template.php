<?php
// Молчание - золото
defined( 'ABSPATH' ) || exit;

/**
 * кастомная функция для подключения файлов
 * @param       $file
 * @param array $template_args
 * @param array $cache_args
 *
 * @since   1.0.0
 * @version 1.0.6
 *
 * @return bool|string
 */
function wpp_get_template_part( $file, $args = [], $cache_args = [] ) {
	$args       = wp_parse_args( $args );
	$cache_args = wp_parse_args( $cache_args );
	if ( $cache_args ) {

		foreach ( $args as $key => $value ) {

			if ( is_scalar( $value ) || is_array( $value ) ) {
				$cache_args[ $key ] = $value;
			} else if ( is_object( $value ) && method_exists( $value, 'get_id' ) ) {
				$cache_args[ $key ] = call_user_func( 'get_id', $value );
			}

		}

		if ( ( $cache = wp_cache_get( $file, serialize( $cache_args ) ) ) !== false ) {

			if ( ! empty( $args['return'] ) ) {
				return $cache;
			}

			echo $cache;

		}
	}


	$file_handle = $file;
	do_action( 'wpp_start_get_template', 'wpp_template_part::' . $file_handle );

	$template = get_stylesheet_directory() . '/' . $file . '.php';

	if ( ! file_exists( $template ) ) {

		$fallback = __DIR__ . '/' . $file . '.php';
		$template = file_exists( $fallback ) ? $fallback : null;

	}

	if ( is_file( $template ) ) {

		ob_start();
		$return = require( $template );
		$data   = ob_get_clean();
		do_action( 'wpp_end_get_template', 'wpp_template_part::' . $file_handle );
		if ( $cache_args ) {
			wp_cache_set( $template, $data, serialize( $cache_args ), 3600 );
		}
		if ( ! empty( $template_args['return'] ) ) {
			if ( $return === false ) {
				return false;
			} else {
				return $data;
			}
		}
		echo $data;
	} else {
		return false;
	}
}
