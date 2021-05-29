<?php

/**
 * Class Wpp_Pf_Custom_Taxonomy
 * Регистрация таксономий
 */
class Wpp_Pf_Custom_Taxonomy {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'custom_post_type' ), 10 );
		add_action( 'wp_loaded', array( __CLASS__, 'unregister_post_types' ), 10 );
	}

	/**
	 * Registred Post Types
	 */
	public static function custom_post_type() {

		$post_types_default = [];
		$post_types         = apply_filters( 'wpp_pf_register_post_types', $post_types_default );

		if ( ! empty( $post_types ) ) :
			foreach ( $post_types as $post_type => $args ) :
				register_post_type( $post_type, $args );
			endforeach;
		endif;

	}

	/**
	 * Unregistred post Types
	 */
	public static function unregister_post_types() {
		$default_unregistred = [];
		$unregistred         = apply_filters( 'wpp_pf_unregistred_post_types', $default_unregistred );
		if ( ! empty( $unregistred ) ) :
			foreach ( $unregistred as $one_type ):
				unregister_post_type( $one_type );
			endforeach;
		endif;
	}

}

Wpp_Pf_Custom_Taxonomy::init();