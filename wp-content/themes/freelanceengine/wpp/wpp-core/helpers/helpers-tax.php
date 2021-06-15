<?php

	if ( !function_exists( 'wpp_fr_custom_tax_posts_array' ) ) :

		/**
		 * Получение всех постов таксономии
		 *
		 * @param      $tax
		 * @param      $post_type
		 * @param null $hide
		 *
		 * @return array
		 */
		function wpp_fr_custom_tax_posts_array( $tax, $post_type, $hide = null, $values = null, $flip = null, $all = null ) {

			$terms = get_terms( [
				'taxonomy'   => $tax,
				'hide_empty' => !empty( $hide ) ? false : true
			] );

			$post_args = [
				'posts_per_page' => -1,
				'post_type'      => $post_type,
				'tax_query'      => [
					'tx' => [
						'taxonomy' => $tax,
						'field'    => 'term_id',
					]
				]
			];

			$out = [];

			foreach ( $terms as $term ):
				$post_args[ 'tax_query' ][ 'tx' ][ 'terms' ] = $term->term_id;
				$my_posts = get_posts( $post_args );
				foreach ( $my_posts as $post ) :

					$variant = get_post_meta( $post->ID, 'ex_page_' . $term->term_id, true );

					#wpp_d_log('ffffffffffffffff ' .   $post->ID .  '   ex_page_' . $term->term_id  . ' = ' . $variant );

					/*					if ( empty( $all ) ) :
											$variant = get_post_meta( $post->ID, 'on_var', true );

											if ( empty( $variant ) ) {
												continue;
											}
										endif;*/

					$out[ $term->term_id ][ $post->ID ] = apply_filters( 'the_title', $post->post_title, $post->ID );
				endforeach;
			endforeach;

			if ( !empty( $values ) ) {
				$outs = [];
				foreach ( $out as $one ) {
					if ( !empty( $flip ) ) {
						$one = array_flip( $one );
					}
					$outs[] = array_values( $one );
				}
				return $outs;
			}
			return $out;
		}

	endif;