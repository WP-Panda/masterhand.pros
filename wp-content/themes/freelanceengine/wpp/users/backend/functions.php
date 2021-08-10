<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * дополнительные колонки
 */

add_filter( 'manage_users_columns', 'add_users_comm_column', 4 );
add_filter( 'manage_users_custom_column', 'fill_users_comm_column', 5, 3 );
add_filter( 'manage_users_sortable_columns', 'add_users_comm_sortable_column' );
add_action( 'pre_user_query', 'add_users_comm_sort_query' );
add_action( 'pre_get_users', 'prefix_sort_by_expiration_date' );

# создаем новую колонку
function add_users_comm_column( $columns ) {
	$columns['activated']     = 'Activation';
	$columns['register_date'] = 'Registration date'; // добавляет дату реги

	return $columns;
}

# заполняем колонку данными
function fill_users_comm_column( $out, $column_name, $user_id ) {
	$userdata           = get_userdata( $user_id );
	$user_confirm_email = get_user_meta( $user_id, 'register_status', true );
	if ( 'activated' === $column_name ) {
		if ( ( ! empty( $user_confirm_email ) && $user_confirm_email !== 'confirm' ) || ( empty( $user_confirm_email ) ) ) {
			$out = 'Unactivated';
		} else {
			$out = 'Activated';
		}
	} elseif ( 'register_date' === $column_name ) {
		$out = mysql2date( 'j M Y', $userdata->user_registered );
	}

	return $out;
}

# добавляем возможность сортировать колонку
function add_users_comm_sortable_column( $sortable_columns ) {
	$sortable_columns['register_date'] = 'register_date';
	$sortable_columns['activated']     = 'activated';

	return $sortable_columns;
}

# сортировка колонки
function add_users_comm_sort_query( $user_query ) {
	$vars = $user_query->query_vars;
	if ( 'register_date' === $vars['orderby'] ) {
		$user_query->query_orderby = ' ORDER BY user_registered ' . $vars['order'];
	}
}

function prefix_sort_by_expiration_date( $query ) {
	if ( 'activated' == $query->get( 'orderby' ) ) {
		$query->set( 'orderby', [ 'meta_value', 'user_registered' => 'ASC' ] );
		$query->set( 'meta_key', 'register_status' );
	}
}

/**
 * Допполя в профиль
 */
add_action( 'show_user_profile', 'add_extra_social_links' );
add_action( 'edit_user_profile', 'add_extra_social_links' );
function add_extra_social_links( $user ) {

	$array = [ 'country', 'state', 'city' ];
	printf( '<h3>%s</h3>', __( 'Address', WPP_TEXT_DOMAIN ) );
	foreach ( $array as $key ) :
		printf( '<input type="text" name="%s" value="%s" class="regular-text"/>', $key, esc_attr( get_the_author_meta( $key, $user->ID ) ) );
	endforeach;
}

/**
 * Доп действия при регистрации Юзера
 */
add_action( 'user_register', 'wpp_enj_user_registration' );
function wpp_enj_user_registration( $user_id ) {

	// если Компания
	if ( ! empty( $_REQUEST['type_prof'] ) && COMPANY === $_REQUEST['type_prof'] ) {
		add_user_meta( $user_id, 'is_company', 1 );
		if ( ! empty( $_REQUEST['company_name'] ) ) {
			wp_update_user( [ 'ID' => $user_id, 'display_name' => esc_attr( $_REQUEST['company_name'] ) ] );
		}
	}

	//Локация
	update_user_meta( $user_id, 'country', $_POST['country'] );
	update_user_meta( $user_id, 'state', $_POST['state'] );
	update_user_meta( $user_id, 'city', $_POST['city'] );

	//статус и ключ
	$user = new WP_User( $user_id );
	update_user_meta( $user_id, 'register_status', 'unconfirm' );
	update_user_meta( $user_id, 'key_confirm', md5( $user->user_email ) );
}