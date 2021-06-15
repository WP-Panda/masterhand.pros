<?php
/**
 * WppFramework Page Functions
 *
 * Functions related to pages and menus.
 *
 * @version  1.0.0
 */


/**
 * Retrieve page ids - used for myaccount, edit_address, shop, cart, checkout, pay, view_order, terms. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 *
 * @return int
 */
function wpp_fr_get_page_id( $page ) {

	$page = apply_filters( 'wpp_fr_' . $page . '_page_id', get_option( 'wpp_fr_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : false;
}

/**
 * Отображение кастомных страниц в админке в списке сипраниц
 *
 * @param $post_states
 * @param $post
 *
 * @return array
 */
function wpp_fr_post_states( $post_states, $post ) {

	$states = apply_filters( 'wpp_fr_post_states', [] );

	if ( array_key_exists( $post->ID, $states ) ) {
		$post_states[] = $states[ $post->ID ];
	}

	return $post_states;
}

add_filter( 'display_post_states', 'wpp_fr_post_states', 10, 2 );