<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Time: 2:39
	 */

	/**
	 * Хэдер содержимое хэдера конечной точки аккаунта
	 */
	add_action( 'wpp_fr_acc_header_content', 'wpp_pf_acc_endpoints_nav', 10 );

	/**
	 * подключение хэдера конечной точки аккаунта
	 */
	add_action( 'wpp_fr_acc_header', 'wpp_pf_acc_endpoints_header', 10 );

	/**
	 * Сообщени о запрете доступа не авторизованным пользователем
	 */
	add_action( 'wpp_fr_not_logged_template_content', 'wpp_fr_not_logged_template_message', 10 );