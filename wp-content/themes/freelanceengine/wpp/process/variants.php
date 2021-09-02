<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * массив вариаций
 * @deprecated
 * @param array|string $parent
 *
 * @return array
 */
function wpp_create_variant_array( $parent ) {

	if ( empty( $parent ) ) {
		return false;
	}

	if ( !is_array( $parent ) ) {
		$parent = explode( ',', $parent );
	}

	/**
	 * @todo  это если проходить целиком
	 */

	#таксономии опций
	$terms = get_terms( [
		'taxonomy'   => 'as_options',
		'hide_empty' => false,
	] );


	//посты таксономий
	$post_args = [
		'posts_per_page' => -1,
		'post_type'      => 'as_option',
		'tax_query'      => [
			'tx' => [
				'taxonomy' => 'as_options',
				'field'    => 'term_id',
				'terms'    => $parent
			]
		]
	];

	$single = $single_2 = [];


	$my_posts = get_posts( $post_args );

	foreach ( $my_posts as $post ) :

		$variant = $variant_2 = [];

		foreach ( $terms as $ter ) :
			$hh = get_post_meta( $post->ID, 'ex_page_' . $ter->term_id, false );
			if ( !empty( $hh ) ) :
				$variant[ $post->ID ][ $ter->term_id ] = $hh;
				$a = 0;
				foreach ( $hh as $one_id ) {
					$variant_2[ $post->ID ][ $ter->term_id ][ $a ] = get_the_title( $one_id );

					$a++;
				}

			endif;

		endforeach;

		if ( !empty( $variant ) ) {
			$comb = wpp_fr_array_combinate( array_shift( $variant ) );
			$comb_2 = wpp_fr_array_combinate( array_shift( $variant_2 ) );
			$single = array_merge( $single, $comb );
			$single_2 = array_merge( $single_2, $comb_2 );
		}

	endforeach;

	$variants_array = array_combine( array_unique( $single ), array_unique( $single_2 ) );

	return ( $variants_array );
}


/**
 * Быстрая смена Вариаций
 * @deprecated
 */
function change_post_var_parent() {
	check_ajax_referer( 'wpp-co-special-string', 'security' );
	$post_id = intval( $_POST[ 'post_id' ] );
	$val = intval( $_POST[ 'val' ] );
	$res = update_post_meta( $post_id, 'wpp_var_parent', $val );

	wp_send_json_success( [
		'message' => __( 'ok', 'wpp-fr' ),
		'res'     => $res,
		'val'     => $val,
		'id'      => $post_id
	] );


	die();
}