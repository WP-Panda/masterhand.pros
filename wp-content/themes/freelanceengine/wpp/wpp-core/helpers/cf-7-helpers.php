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
	
	
	if ( ! function_exists( 'wpp_cf_7_forms_args' ) ) :
		/**
		 * Checked CF7 Plugin
		 *
		 * @return bool
		 */
		function wpp_cf_7_checked() {
			return class_exists( 'WPCF7_ContactForm' ) ? true : false;
		}
	endif;
	
	if ( ! function_exists( 'wpp_cf_7_forms_args' ) ) :
		/**
		 * Get all CF7 Forms
		 *
		 * @return array
		 */
		function wpp_cf_7_forms_args( $select = null ) {
			$check = wpp_cf_7_checked();
			$out = array ();
			if ( true !== $check ) {
				$out[] = __( 'Plugin Contact Form 7 is not Active', 'wpp-fr' );
			} else {
				$args = array (
					'post_type' => 'wpcf7_contact_form',
					'nopaging'  => true
				);
				
				$cf7_forms = get_posts( $args );
				
				if ( empty( $cf7_forms ) ) {
					$out[] = __( 'Plugin Contact Form 7 is not have Active Forms', 'wpp-fr' );
				} else {
					if ( ! empty( $select ) && true === $select ) {
						$out[] = __( 'Select Contact Form', 'wpp-fr' );
					}
					foreach ( $cf7_forms as $form ) {
						$out[ $form->ID ] = $form->post_title;
					}
				}
			}
			
			return $out;
		}
	endif;