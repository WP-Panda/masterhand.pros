<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Замена дирректории загрузки для каждого пользователя
 * @param $path
 *
 * @return mixed
 */
function wpp_user_upload_dir( $path ) {

	// set our own replacements
	if ( is_user_logged_in() ) {
		$current_user       = wp_get_current_user();
		$sub_dir             = $current_user->ID;
		$path['basedir'] = ABSPATH  . 'media';
		$path['baseurl'] = get_home_url() . '/media';
		$path['subdir'] = '/' . $sub_dir;
		$path['url']    = $path['baseurl'] . $path['subdir'];
		$path['path']   = $path['basedir'] . $path['subdir'];
	}

	return $path;
}

add_filter( 'upload_dir', 'wpp_user_upload_dir' );


