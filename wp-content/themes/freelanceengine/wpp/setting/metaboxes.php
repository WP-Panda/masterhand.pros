<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


function wpp_post_gallery_meta_boxes( $post_types ) {

	$post_types[] = 'post';

	return $post_types;

}

add_filter( 'wpp_post_gallery_types', 'wpp_post_gallery_meta_boxes' );