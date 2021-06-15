<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_filter( 'manage_pages_columns', 'wpp_page_column_views' );
add_action( 'manage_pages_custom_column', 'wpp_page_custom_column_views', 5, 2 );
function wpp_page_column_views( $defaults ) {
	$defaults['page-layout'] = __( 'Template' );

	return $defaults;
}

function wpp_page_custom_column_views( $column_name, $id ) {
	if ( $column_name === 'page-layout' ) {
		$set_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
		if ( $set_template == 'default' ) {
			echo 'Default';
		}
		$templates = get_page_templates();
		ksort( $templates );
		foreach ( array_keys( $templates ) as $template ) :
			if ( $set_template == $templates[ $template ] ) {
				echo $template;
			}
		endforeach;
	}
}


/*
* get post by slug
*/
function wpp_get_post_by_slug( $slug, $post_type = 'post', $unique = true ) {
	$args     = array(
		'name'           => $slug,
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 1
	);
	$my_posts = get_posts( $args );
	if ( $my_posts ) {
		//echo 'ID on the first post found ' . $my_posts[0]->ID;
		if ( $unique ) {
			return $my_posts[0];
		} else {
			return $my_posts;
		}
	}

	return false;
}


if ( ! function_exists( 'wpp_fr_word_safe_break' ) ) :
	/**
	 * Разбить строку на 2 части
	 */

	function wpp_fr_word_safe_break( $str ) {
		$middle = mb_strrpos( mb_substr( $str, 0, floor( strlen( $str ) / 2 ), 'UTF-8' ), ' ', null, 'UTF-8' ) + 1;

		return [
			mb_substr( $str, 0, $middle - 1, 'UTF-8' ),
			mb_substr( $str, $middle, null, 'UTF-8' )
		];

	}
endif;


if ( ! function_exists( 'wpp_fr_string_to_phone_number' ) ) :
	/**
	 * перевод строки в телефонный номер
	 *
	 * @param $val
	 *
	 * @return string
	 */
	function wpp_fr_string_to_phone_number( $val ) {

		$convert = [
			'a' => 2,
			'b' => 2,
			'c' => 2,
			'd' => 3,
			'e' => 3,
			'f' => 3,
			'g' => 4,
			'h' => 4,
			'i' => 4,
			'j' => 5,
			'k' => 5,
			'l' => 5,
			'm' => 6,
			'n' => 6,
			'o' => 6,
			'p' => 7,
			'q' => 7,
			'r' => 7,
			's' => 7,
			't' => 8,
			'u' => 8,
			'v' => 8,
			'w' => 9,
			'x' => 9,
			'y' => 9,
			'z' => 9
		];

		return strtr( preg_replace( '/[^a-zA-Z0-9]/', '', strtolower( $val ) ), $convert );

	}

endif;

if ( ! function_exists( 'wpp_fr_term_has_children' ) ) :
	/**
	 * проверка наличия дочерних
	 */

	function wpp_fr_term_has_children( $term_id = 0, $taxonomy = 'category' ) {
		$children = get_categories( [ 'child_of' => $term_id, 'taxonomy' => $taxonomy ] );

		return ( $children );
	}
endif;

if ( ! function_exists( 'wpp_pf_home_logo_url' ) ) :

	/**
	 * Урл главной страницы для не ссылаться сама на себя
	 */
	function wpp_pf_get_home_logo_url() {
		$url = get_home_url();
		if ( is_home() || is_front_page() ) {
			$url = 'javascript:void(0);';
		}

		return $url;
	}
endif;