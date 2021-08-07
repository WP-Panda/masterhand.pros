<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

/**
 * Вывод полей социальные сети
 */
$soc_data = apply_filters( 'wpp_social_fields_array', [] );
$default  = [];

if ( ! empty( $soc_data ) ) :
	foreach ( $soc_data as $one_field ) {
		$default[] = [
			'id'          => $one_field['id'],
			'label'       => $one_field['label'],
			//'value'       => $wpp_data->{$one_field['id']} ?? '',
			'value'       => get_user_meta( $user_ID, $one_field['id'], true ),
			'placeholder' => $one_field['placeholder']
		];
	}
endif;


if ( ! empty( $default ) ) :
	$data = new WPP_Form_Constructor( $default );
	$data->parse_data();
endif;