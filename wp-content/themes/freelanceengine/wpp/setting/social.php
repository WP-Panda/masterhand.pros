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
			'id'          => '_whatsapp',
			'label'       => __( 'WhatsApp', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'WhatsApp', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_telegram',
			'label'       => __( 'Telegram', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Telegram', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_linkedin',
			'label'       => __( 'LinkedIn', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'LinkedIn', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_viber',
			'label'       => __( 'Viber', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Viber', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_facebook',
			'label'       => __( 'Facebook', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Facebook', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_skype',
			'label'       => __( 'Skype', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'Skype', WPP_TEXT_DOMAIN ),
		],
		[
			'id'          => '_wechat',
			'label'       => __( 'WeChat', WPP_TEXT_DOMAIN ),
			'placeholder' => __( 'WeChat', WPP_TEXT_DOMAIN ),
		],
	];

	return $social;

}

add_filter( 'wpp_social_fields_array', 'wpp_enj_social_field_array' );

function fields_for_user_profile( $args ) {

	$social = wpp_enj_social_field_array();

	foreach ( $social as $one ) {
		$args[] = $one['id'];
	}

	return $args;
}

add_filter( 'wpp_user_data_fields', 'fields_for_user_profile' );