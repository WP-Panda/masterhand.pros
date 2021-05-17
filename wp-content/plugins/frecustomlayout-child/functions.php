<?php
	function frechild_add_scripts() {
		if( is_singular( 'project' ) || is_author() ){
	    	wp_enqueue_style( 'custom-css-child',get_stylesheet_directory_uri() . '/css/child-css.css');
		}
	   // wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
	}
	add_action( 'wp_enqueue_scripts', 'frechild_add_scripts' );
