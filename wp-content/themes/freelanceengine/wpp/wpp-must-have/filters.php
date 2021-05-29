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
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	/**
	 * Боди классы
	 */
	
	if ( ! function_exists( 'wpp_fr_body_class_filter' ) ) :
		
		function wpp_fr_body_class_filter( $classes ) {
			
			if ( is_page_template( 'pages/wpp-about-us-temp.php' ) ) {
				$classes[] = 'about-us-template';
			} elseif ( is_page_template( 'pages/wpp-contact-us-temp.php' ) ) {
				$classes[] = 'contact-us-template';
			}
			
			return apply_filters( 'wpp_fr_body_class', $classes );
			
		}
		
		add_filter( 'body_class', 'wpp_fr_body_class_filter' );
	
	endif;
	
	
	add_filter( 'gform_field_container', 'add_bootstrap_container_class', 10, 6 );
	function add_bootstrap_container_class( $field_container, $field, $form, $css_class, $style, $field_content ) {
		$id = $field->id;
		$field_id = is_admin() || empty( $form ) ? "field_{$id}" : 'field_' . $form['id'] . "_$id";
		return '<li id="' . $field_id . '" class="' . $css_class . ' form-group">{FIELD_CONTENT}</li>';
	}