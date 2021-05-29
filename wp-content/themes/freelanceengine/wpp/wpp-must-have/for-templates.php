<?php
	/**
	 * File Description
	 *
	 * @author  WP Panda
	 *
	 * @package Time, it needs time
	 * @since   1.0.0
	 * @version 1.0.0
	 */

	if ( !defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Список меток поста с тайтлом задаваемым в описании метки
	 *
	 * @param null $post_id - ID поста для которого получить метки
	 * @param null $before  - что-то до списка меток
	 * @param null $sep     - разделитель между метками
	 * @param null $after   - что-то после списка меток
	 *
	 * @return bool|string|WP_Error
	 */
	function wpp_get_post_tags_with_title( $post_id = null, $before = null, $sep = null, $after = null ) {

		if ( empty( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		}

		$post_tags = get_the_tags( (int)$post_id );

		if ( empty( $post_tags ) ) {
			return false;
		}

		$links = [];

		foreach ( $post_tags as $tag ) {
			$link = get_term_link( (int)$tag->term_id, 'post_tag' );

			if ( is_wp_error( $link ) ) {
				return $link;
			}

			$links[] = sprintf( '<a href="%s" rel="tag" title="%s">%s</a>', esc_url( $link ), $tag->description, $tag->name );
		}

		$tag_links = apply_filters( "term_links-post_tag", $links );

		return $before . join( $sep, $tag_links ) . $after;

	}

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
		$args = wp_parse_args( $args );
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

				if ( !empty( $args[ 'return' ] ) ) {
					return $cache;
				}

				echo $cache;

			}
		}


		$file_handle = $file;
		do_action( 'wpp_start_get_template', 'wpp_template_part::' . $file_handle );

		$template = get_stylesheet_directory() . '/' . $file . '.php';

		if ( !file_exists( $template ) ) {

			$fallback = get_template_directory() . '/' . $file . '.php';
			$template = file_exists( $fallback ) ? $fallback : null;

		}

		if ( empty( $template ) ) {
			$template = wpp_fr()->plugin_path() . '/' . $file . '.php';

		}

		if ( is_file( $template ) ) {

			ob_start();
			$return = require( $template );
			$data = ob_get_clean();
			do_action( 'wpp_end_get_template', 'wpp_template_part::' . $file_handle );
			if ( $cache_args ) {
				wp_cache_set( $template, $data, serialize( $cache_args ), 3600 );
			}
			if ( !empty( $template_args[ 'return' ] ) ) {
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