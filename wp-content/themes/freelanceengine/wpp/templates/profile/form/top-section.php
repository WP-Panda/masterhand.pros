<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract($args);

$default = [
	[
		'id'          => 'post_content',
		'label'       => __( 'About me', WPP_TEXT_DOMAIN ),
		'value'       => $about ?? '',
		'placeholder' => __( 'About me', WPP_TEXT_DOMAIN ),
		'wrap_class'  => sprintf( 'col-md-%1$s col-lg-%1$s col-sm-12 col-xs-12 fre-input-field', $class ),
		'type'        => 'editor'
	]
];

if ( fre_share_role() || wpp_fre_is_freelancer() ) :
	$default[] = [
		'id'          => 'hour_rate',
		'label'       => __( 'Rate', WPP_TEXT_DOMAIN ),
		'value'       => $hour_rate ?? '',
		'placeholder' => __( 'Your rate', WPP_TEXT_DOMAIN ),
		'wrap_class'  => 'col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-hourly-field',
		'label_class' => 'fre-field-title ratelbl',
		'type'        => 'number',
		'conditional' => [
			'compare' => 'or',
			'for'     => [
				'role',
				'freelancer'
			]
		]
	];
endif;

$default[] = [
	'id'         => 'clear_1',
	'type'       => 'clear',
	'wrap_class' => 'clearfix'
];

$default[] = [
	'id'          => 'display_name',
	'label'       => __( 'Name', WPP_TEXT_DOMAIN ),
	'value'       => $display_name ?? '',
	'placeholder' => __( 'Your name', WPP_TEXT_DOMAIN ),
];

$default[] = [
	'id'          => 'user_email',
	'label'       => __( 'Email', WPP_TEXT_DOMAIN ) . $confirmed_email,
	'value'       => $user_data->user_email ?? '',
	'placeholder' => __( 'Your email', WPP_TEXT_DOMAIN ),
];


$data = new WPP_Form_Constructor( $default );
$data->parse_data();