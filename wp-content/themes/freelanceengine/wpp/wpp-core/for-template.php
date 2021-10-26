<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 *
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

		$fallback = get_template_directory() . '/' . $file . '.php';
		$template = file_exists( $fallback ) ? $fallback : null;

	}

	/*if ( empty( $template ) ) {
		$template = wpp_fr()->plugin_path() . '/' . $file . '.php';

	}*/

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


function wpp_sanitize_paginate_link( $link ) {

	$link_preff = explode( '#038;', $link );
	if ( ! empty( $link_preff[0] ) && ! wp_doing_ajax() ) {
		return $link_preff[0];
	} else {
		return $link;
	}
}


add_filter( 'paginate_links', 'wpp_sanitize_paginate_link' );

function wpp_custom_paginate_base( $_url ) {
	$url       = strtok( $_url, '?' );
	$clear_str = str_replace( [ 'http://', 'https://' ], '', $url );

	$data_array = explode( '/', $clear_str );


	$data_reversed = array_values( array_filter( array_reverse( $data_array ) ) );

	$_page = ( $data_reversed[1] === 'page' && ! empty( (int) $data_reversed[0] ) ) ? (int) $data_reversed[0] : 1;

	$_paginate_base = str_replace( [ 'page/' . $_page . '/', 'page/' . $_page ], '', $_POST['page'] );

	return (object) [ 'paginate_base' => $_paginate_base, 'page' => $_page ];

}


/**
 * Экшен для темплэйта
 */
function wpp_action_template() {
	global $template;
	$template = explode( '.', basename( $template ) )[0];

	do_action( 'wpp_' . $template );
}


/**
 * Подключение
 */

function wpp_require( $array, $path ) {
	if ( ! empty( $array ) ) :
		foreach ( $array as $one ) :
			$_file = $path . DIRECTORY_SEPARATOR . $one . '.php';

			if ( file_exists( $_file ) ) :
				require_once $_file;
			endif;

		endforeach;
	endif;

}