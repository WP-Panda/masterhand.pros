<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Массив настроек полей социальных сетей
 * @return mixed|void
 */
function wpp_enj_social_field_array() {

	$social = [
		[
			'id'          => 'whatsapp',
			'label'       => __( 'WhatsApp', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'WhatsApp', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'telegram',
			'label'       => __( 'Telegram', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Telegram', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'linkedin',
			'label'       => __( 'LinkedIn', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'LinkedIn', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'viber',
			'label'       => __( 'Viber', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Viber', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'facebook',
			'label'       => __( 'Facebook', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Facebook', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'skype',
			'label'       => __( 'Skype', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Skype', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => 'wechat',
			'label'       => __( 'WeChat', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'WeChat', WPP_TEXT_DOMAIN ),
		],
	];

	return $social;

}

add_filter( 'wpp_social_fields_array', 'wpp_enj_social_field_array' );