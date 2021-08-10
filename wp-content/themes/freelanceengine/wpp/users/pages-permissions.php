<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 *
 * Контроль доступов к различным шаблонам темы
 */

defined( 'ABSPATH' ) || exit;

/**
 * Доступ только для фрилансеров
 */
function wpp_fre_template_deny() {
	if ( ! wpp_fre_is_freelancer() ) {
		wp_safe_redirect( get_home_url() );
	}
}

# Форма отправки сообщений только фрилансеры
add_action( 'wpp_page-know-how', 'wpp_fre_template_deny' );