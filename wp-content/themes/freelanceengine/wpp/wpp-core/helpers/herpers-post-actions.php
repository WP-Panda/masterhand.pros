<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 09.08.2019
 * Time: 21:55
 */


// Delete from Front-End Link
function wpp_delete_post_link( $post_id, $text = 'Delete This', $title = "Delete" ) {

	if ( ! wpp_fr_user_is_admin() ) {
		return;
	}

	$text   = sprintf( '<img class="wpp-trash-icon" src="%s" alt="">', get_template_directory_uri() . '/assets/img/icons/trash.svg' );
	$return = sprintf( '<a class="wpp-del-post" href="%s" target="_blank">%s</a>', get_delete_post_link( $post_id ), $text );

	echo $return;
}

//Redirect after delete post in frontend
add_action( 'trashed_post', 'trash_redirection_frontend' );
function trash_redirection_frontend( $post_id ) {
	global $wp;
	if ( filter_input( INPUT_GET, 'frontend', FILTER_VALIDATE_BOOLEAN ) ) {
		wp_redirect( home_url( $wp->request ) );
		exit;
	}
}


function wpp_edit_post_link( $post_id, $class = 'wpp-ed-post' ) {

	if ( ! $url = get_edit_post_link( $post_id ) ) {
		return;
	}

	$text = sprintf( '<img class="wpp-ed-icon" src="%s" alt="">', get_template_directory_uri() . '/assets/img/icons/edit.svg' );

	echo sprintf( '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '" target="_blank">' . $text . '</a>' );


}