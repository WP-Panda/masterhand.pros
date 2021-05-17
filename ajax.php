<?php
/** Load WordPress Bootstrap */
require_once( dirname(__FILE__) . '/wp-load.php' );

header( 'Content-Type: text/html; charset=UTF-8' );
header( 'X-Robots-Tag: noindex' );


$action = ( isset( $_REQUEST['action'] ) ) ? $_REQUEST['action'] : '';

if ( is_user_logged_in() ) {
	// If no action is registered, return a Bad Request response.
	if (has_action("wp_ajax_{$action}")) {


		do_action("wp_ajax_{$action}");
	}
}
wp_die('0', 400);