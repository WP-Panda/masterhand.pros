<?php

// remove campaigns from Visual Composer
add_filter( 'vc_settings_exclude_post_type', 'mailster_vc_settings_exclude_post_type' );
function mailster_vc_settings_exclude_post_type( $post_types ) {
	$post_types[] = 'newsletter';
	return $post_types;
};

// add Visual Composer shortcodes
if ( defined( 'WPB_VC_VERSION' ) ) {
	add_filter( 'mailster_strip_shortcode_tags', 'mailster_add_vc_shortcode_tags' );
	function mailster_add_vc_shortcode_tags( $shortcode_tags ) {
		$shortcode_tags[] = 'vc_([a-z_]+)';
		return $shortcode_tags;
	};
}

// do not cache newsletter homepage on WP Rocket
add_filter( 'rocket_cache_reject_uri', 'mailster_rocket_cache_reject_uri' );
function mailster_rocket_cache_reject_uri( $uri ) {

	if ( $link = get_permalink( mailster_option( 'homepage' ) ) ) {
		$uri[] = '(.*)/' . basename( $link ) . '/(.*)';
	}
	return $uri;
};


// do stuff on newsletter homepage updated
add_action( 'mailster_update_homepage', 'mailster_maybe_flush_rocket_cache' );
function mailster_maybe_flush_rocket_cache( $post ) {

	// WP Rocket
	function_exists( 'flush_rocket_htaccess' ) && flush_rocket_htaccess();
	function_exists( 'rocket_generate_config_file' ) && rocket_generate_config_file();

};

// WP Offload S3 - disabled
add_action( '_as3cf_init', 'mailster_disable_as3cf_on_content' );
function mailster_disable_as3cf_on_content( $as3cf ) {
	// remove this filter so images paths stay the same
	remove_filter( 'content_save_pre', array( $as3cf->filter_s3, 'filter_post' ) );
};


// no support for Elementor Page Builder.
add_filter( 'pre_update_option_elementor_cpt_support', 'mailster_pre_update_option_elementor_cpt_support' );
function mailster_pre_update_option_elementor_cpt_support( $cpt_support ) {

	if ( $pos = array_search( 'newsletter', $cpt_support ) ) {
		mailster_notice( sprintf( esc_html__( 'Mailster Campaigns do not support the %s.', 'mailster' ), 'Elementor Page Builder' ), 'error', true );
		unset( $cpt_support[ $pos ] );
		$cpt_support = array_values( $cpt_support );
	}

	return $cpt_support;
};

// no support for Beaver Builder.
add_filter( 'fl_builder_admin_settings_post_types', 'mailster_fl_builder_admin_settings_post_types' );
function mailster_fl_builder_admin_settings_post_types( $post_types ) {

	if ( isset( $post_types['newsletter'] ) ) {
		unset( $post_types['newsletter'] );
	}

	return $post_types;
};

// no support for Fusion Builder.
add_filter( 'pre_update_option_fusion_builder_settings', 'mailster_pre_update_option_fusion_builder_settings' );
function mailster_pre_update_option_fusion_builder_settings( $settings ) {

	if ( isset( $settings['post_types'] ) && $pos = array_search( 'newsletter', $settings['post_types'] ) ) {
		unset( $settings['post_types'][ $pos ] );
		$settings['post_types'] = array_values( $settings['post_types'] );
	}

	return $settings;
};

// no support for Cornerstone Page Builder.
add_filter( 'pre_update_option_cornerstone_settings', 'mailster_pre_update_option_cornerstone_settings' );
function mailster_pre_update_option_cornerstone_settings( $settings ) {

	if ( isset( $settings['allowed_post_types'] ) && $pos = array_search( 'newsletter', $settings['allowed_post_types'] ) ) {
		mailster_notice( sprintf( esc_html__( 'Mailster Campaigns do not support the %s.', 'mailster' ), 'Cornerstone Page Builder' ), 'error', true );
		unset( $settings['allowed_post_types'][ $pos ] );
		$settings['allowed_post_types'] = array_values( $settings['allowed_post_types'] );
	}

	return $settings;
};
