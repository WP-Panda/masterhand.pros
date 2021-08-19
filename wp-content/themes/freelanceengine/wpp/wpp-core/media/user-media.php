<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Замена дирректории загрузки
 *
 * @param $path
 *
 * @return mixed
 */
function wpp_user_upload_dir( $path ) {

	// для каждого пользователя
	if ( is_user_logged_in() ) {

		$current_user    = wp_get_current_user();
		$sub_dir         = $current_user->ID;
		$path['basedir'] = ABSPATH . 'media';
		$path['baseurl'] = get_home_url() . '/media';
		$path['subdir']  = '/' . $sub_dir;
		$path['url']     = $path['baseurl'] . $path['subdir'];
		$path['path']    = $path['basedir'] . $path['subdir'];

	}

	return $path;
}


function wpp_user_project_upload( $path ) {

	if ( is_user_logged_in() ) {
		$sub_dir         = get_queried_object_id();
		$path['basedir'] = ABSPATH . 'media/projects';
		$path['baseurl'] = get_home_url() . '/media/projects';
		$path['subdir']  = '/' . $sub_dir;
		$path['url']     = $path['baseurl'] . $path['subdir'];
		$path['path']    = $path['basedir'] . $path['subdir'];
	}

	return $path;
}


//function up_sid() {

	//if ( is_singular('project') ) {
		//add_filter( 'upload_dir', 'wpp_user_project_upload' );
	//} else {
		add_filter( 'upload_dir', 'wpp_user_upload_dir' );
	//}
//}


//add_action( 'init', 'up_sid' );



