<?php
	/**
	 * File Description
	 *
	 * @author  WP Panda
	 *
	 * @package Time, it needs time
	 * @since   1.0.4
	 * @version 1.0.4
	 */

	if ( !defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( !function_exists( 'wpp_fr_add_class_on_li' ) ) :

		/**
		 * Menu li custom class
		 */
		function wpp_fr_add_class_on_li( $classes, $item, $args ) {
			if ( !empty( $args->add_li_class ) ) {
				$classes[] = $args->add_li_class;
			}

			return $classes;
		}

	endif;

	add_filter( 'nav_menu_css_class', 'wpp_fr_add_class_on_li', 1, 3 );


	if ( !function_exists( 'wpp_fr_add_class_on_a' ) ) :
		/**
		 * Menu a custom class
		 */
		function wpp_fr_add_class_on_a( $atts, $item, $args, $depth ) {
			if ( empty( $atts[ 'class' ] ) ) {
				$atts[ 'class' ] = '';
			}
			if ( !empty( $args->add_a_class ) ) {
				$atts[ 'class' ] .= ' ' . $args->add_a_class;
			}

			if ( !empty( $args->add_a_curent_class ) && !empty( $item->current ) ) {
				$atts[ 'class' ] .= ' ' . $args->add_a_curent_class;
			}

			return $atts;
		}
	endif;
	add_filter( 'nav_menu_link_attributes', 'wpp_fr_add_class_on_a', 10, 4 );