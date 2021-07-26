<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


function wpp_found_labels( $found_posts_num ) {
	$found_labels = [
		'found_posts_num' => $found_posts_num,
		'plural'          => sprintf( __( '%s companies available', ET_DOMAIN ), '<span class="found_post">' . $found_posts_num . '</span>' ),
		'singular'        => sprintf( __( '%s company available', ET_DOMAIN ), '<span class="found_post">' . $found_posts_num . '</span>' ),
		'not_found'       => sprintf( __( 'There are no companies on this site!', ET_DOMAIN ), '<span class="found_post">' . $found_posts_num . '</span>' )
	];

	return apply_filters( 'wpp_company_found_labels', $found_labels );
}