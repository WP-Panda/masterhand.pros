<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * get user notification by
 *
 * @param snippet
 *
 * @since  snippet.
 * @author Dakachi
 */
function wpp_user_notification( $user_id = 0, $page = 1, $showposts = 10, $class = "dropdown-menu dropdown-menu-notifi dropdown-keep-open notification-list" ) {

	if ( ! $user_id ) {
		global $user_ID;
		$user_id = $user_ID;
	}

	$notifications = WPP_Notis::get( $user_id );


	$out = '';
	if ( ! empty( $notifications ) ) {

		foreach ( $notifications as $notify ) {

			$type      = '';
			$seenClass = ! empty( $notify->seen ) ? ' fre-notify-new' : '';
			$notify    = WPP_Notis::convert_notify( $notify );
			$project   = get_post( $notify->post_id );

			if ( ! $project || is_wp_error( $project ) ) {
				continue;
			}

			$post_excerpt = str_replace( '&amp;', '&', $notify->text );

			parse_str( $post_excerpt, $data );
			extract( $data );

			/*If wpp_private_message not active is continue*/
			if ( $type == 'new_private_message' ) {
				if ( ! function_exists( 'wpp_private_message_activate' ) ) {
					continue;
				}
			}

			$out .= sprintf( '<li class="notify-item item-%s%s%s" data-id="%s">%s</li>', $notify->id, ' ' . $type, $seenClass, $notify->id, $notify->content );
		}

	} else {

		if ( $class == 'fre-notification-list' ) {
			$out .= sprintf( '<li class="no-result"><span>%s</span></li>', __( 'There are no notifications found.', ET_DOMAIN ) );

		}

	}

	// Check is dropdown
	if ( $class !== 'fre-notification-list' ) {
		$out .= sprintf( '<li style="text-align: center;"><a class="view-more-notify" href="%s">%s</a></li>', et_get_page_link( 'list-notification' ), __( 'See all notifications', ET_DOMAIN ) );
	}

	printf( '<ul class="list_notify %s">%s</ul>', $class, $out );

	/*echo '<script type="data/json" class="postdata" >' . json_encode( $postdata ) . '</script>';*/
	// Check is not dropdown
	/*if ( $class === 'fre-notification-list' ) {
		// pagination
		echo '<div class="fre-paginations paginations-wrapper">';
		wpp_pagination( $wp_query, get_query_var( 'paged' ), 'page' );
		echo '</div>';
	}*/
}