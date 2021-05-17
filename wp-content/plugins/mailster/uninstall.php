<?php

// if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb, $wp_roles;

if ( is_network_admin() && is_multisite() ) {

	$old_blog = $wpdb->blogid;
	$blogids  = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

} else {

	$blogids = array( false );

}

foreach ( $blogids as $blog_id ) {

	if ( $blog_id ) {
		switch_to_blog( $blog_id );
	}

	require WP_PLUGIN_DIR . '/' . WP_UNINSTALL_PLUGIN;

	if ( ! class_exists( 'UpdateCenterPlugin' ) ) {
		require_once MAILSTER_DIR . 'classes/UpdateCenterPlugin.php';
	}

	UpdateCenterPlugin::add(
		array(
			'licensecode' => mailster()->license(),
			'remote_url'  => apply_filters( 'mailster_updatecenter_endpoint', 'https://update.mailster.co/' ),
			'plugin'      => MAILSTER_SLUG,
			'slug'        => 'mailster',
			'autoupdate'  => mailster_option( 'autoupdate', true ),
		)
	);

	mailster()->uninstall();

}

if ( $blog_id ) {
	switch_to_blog( $old_blog );
}
