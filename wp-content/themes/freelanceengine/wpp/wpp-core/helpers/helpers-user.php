<?php
/**
 * File Description
 *
 * @author  WP Panda
 *
 * @package auto.calk
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpp_fr_get_user_list' ) ) :
	/**
	 * Get user list
	 * Получение списка пользователей
	 *
	 * @param array $setting -  настройки        (необязательно)
	 * @param int $parent_user_id - родительский юзер (необязательно)
	 *
	 * @return array
	 */
	function wpp_fr_get_user_list( $setting = null, $parent_user_id = null ) {

		$args = array(
			'orderby' => 'display_name',
		);

		if ( ! empty( $parent_user_id ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => 'crm_user_parent',
					'value' => $parent_user_id
				)
			);
		}


		$size = ! empty( $setting['avatar_size'] ) ? esc_attr( $setting['avatar_size'] ) : 32;

		$users_out = [];

		$wp_user_query = new WP_User_Query( $args );
		$authors       = $wp_user_query->get_results();

		foreach ( $authors as $author ) {
			$author_info              = get_userdata( $author->ID );
			$users_out[ $author->ID ] = array(
				'user_id'      => $author_info->ID,
				'display_name' => $author_info->display_name,
				'user_email'   => $author_info->user_email,
				'role'         => $author_info->roles[0],
				'avatar'       => get_avatar( $author_info->user_email, $size )
			);
		}

		return $users_out;

	}
endif;

/**
 * Если пользователь - админ
 * @return bool
 */
function wpp_fr_user_is_admin() {
	return current_user_can( 'administrator' ) ? true : false;
}