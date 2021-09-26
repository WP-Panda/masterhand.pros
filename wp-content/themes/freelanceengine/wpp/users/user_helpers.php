<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * set default user roles for social login
 *
 * функция из темы, похожк отображает лэйблы ролей
 *
 * @author Tambh
 */
add_filter( 'ae_social_login_user_roles_default', 'fre_default_user_roles' );

if ( ! function_exists( 'fre_default_user_roles' ) ) {

	function fre_default_user_roles( $default_role ) {
		return [
			FREELANCER => __( 'Professional', ET_DOMAIN ),
			EMPLOYER   => __( 'Client', ET_DOMAIN )
		];
	}

}

/**
 * Получение Ролкей текущего пользовавтеля
 *
 * @return array|bool
 */
function wpp_fre_get_curent_user_roles() {

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();

		return ( array ) $user->roles;
	} else {
		return false;
	}

}

/**
 * Проверка Фрилансер ли
 *
 * @return bool
 */
function wpp_fre_is_freelancer() {
	$role = wpp_fre_get_curent_user_roles();

	if ( ! empty( $role ) ) {
		return in_array( FREELANCER, $role ) ?? false;
	}

	return false;
}

/**
 * Проверка заказчик ли
 *
 * @return bool
 */
function wpp_fre_is_employer() {
	$role = wpp_fre_get_curent_user_roles();
	if ( ! empty( $role ) ) {
		return in_array( EMPLOYER, $role ) ?? false;
	}

	return false;
}