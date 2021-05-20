<?php /**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

	defined( 'ABSPATH' ) || exit;

	extract( $args );

	if ( ! empty( $args[ 'pages' ] ) ) :

		$big = 999999999;
		// аякс пагинация
		if ( ! empty( $paginate_base ) ) {
			// перестановка нет параметров
			$url_params_regex = '/\?.*?$/';
			preg_match( $url_params_regex, $paginate_base, $url_params );
			$base = ! empty( $url_params[ 0 ] ) ? str_replace( $url_params[ 0 ], '', $paginate_base ) . 'page/%#%/' . $url_params[ 0 ] : esc_url( $paginate_base ) . 'page/%#%/';
			//обычная пагинация
		} else {
			$base = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
		}

		$current = empty( $current ) ? 1 : $current;
		$args    = [
			'base'      => $base,
			'format'    => '?paged=%#%',
			'current'   => max( 1, ( $current ) ? $current : get_query_var( 'paged' ) ),
			'total'     => $pages,
			'next_text' => __( 'next', ET_DOMAIN ) . ' <i class="fa fa-angle-right"></i>',
			'prev_text' => '<i class="fa fa-angle-left"></i> ' . __( 'prev', ET_DOMAIN ),
		];


		$links = paginate_links( $args );

		$_class = empty( (bool) $ajax_wpp ) ? '' : ' wpp-paginate';

		printf( '<div class="paginations%s">%s</div>', $_class, $links );
	endif;