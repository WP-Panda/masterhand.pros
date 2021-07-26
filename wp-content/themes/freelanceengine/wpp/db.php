<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


class Wpp_Base_Data {


	private static $table_name = 'wpp_company_data';
	private static $table_v = 6;


	public static function init() {

		add_action( 'init', [ __CLASS__, 'wpp_create_table' ] );
	}


	/**
	 * Cоздание таблицы для базы
	 *
	 * @return bool
	 */
	public static function wpp_create_table() {

		$option = get_option( 'wpp_table_create' );

		if ( ! empty( $option ) && (int) $option === self::$table_v ) {
			return false;
		}

		global $wpdb;

		$wpdb->query( self::table_structure() );

		update_option( 'wpp_table_create', self::$table_v );

	}


	/**
	 * Структура таблиц
	 *
	 * @return string
	 */
	private static function table_structure() {

		global $wpdb;

		$table_name = $wpdb->prefix . self::$table_name;

		$charset_collate = $wpdb->get_charset_collate();

		$data = "CREATE TABLE IF NOT EXISTS {$table_name} (
					`id` bigint(20) UNSIGNED NOT NULL,
				    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',	
					`title` TEXT  NOT NULL DEFAULT '',
					`rating` float(22) UNSIGNED NOT NULL DEFAULT 0,
					`rating_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	
					`cat`  bigint(20) UNSIGNED NOT NULL DEFAULT 0,	
					`phone` VARCHAR(20) NOT NULL DEFAULT '',	
					`email` VARCHAR(100) NOT NULL DEFAULT '',	
					`country` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
					`state` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	
					`city` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
					`sub` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	
					`address` TEXT  NOT NULL DEFAULT '',	
					`site` TEXT  NOT NULL DEFAULT '',
					`register` int(1)  NOT NULL DEFAULT 0,
					`email_count`  bigint(20) UNSIGNED NOT NULL DEFAULT 0,
					`users_mailer` TEXT NOT NULL DEFAULT '',
				PRIMARY KEY  (id)
							) {$charset_collate};";

		return $data;

	}


}


Wpp_Base_Data::init();


/**
 * Получение используцемого списка ID->Категория проекта для компаний
 *
 * @param null $parent - id родительской категории для получения дочерних
 *
 * @return array
 */
function wpp_get_company_category( $parent = null ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wpp_company_data';
	$select_cat = [];


	if ( empty( $parent ) ) {
		$pluck = 'cat';
		$_cats = $wpdb->get_results( "SELECT DISTINCT `cat` FROM $table_name", ARRAY_A );
	} else {
		$_cats = $wpdb->get_results( "SELECT DISTINCT `sub` FROM $table_name WHERE `cat` = $parent", ARRAY_A );
		$pluck = 'sub';
	}


	if ( ! empty( $_cats ) ) {

		$_uniq_cats = wp_list_pluck( $_cats, $pluck );
		$_uniq_cats = array_filter( $_uniq_cats );

		foreach ( $_uniq_cats as $_cat_id ) {

			$select_cat[ $_cat_id ] = get_term( $_cat_id )->name;
		}

		asort( $select_cat );

	}

	return $select_cat;


}

/**
 * Получение используцемого списка ID->Категория проекта для компаний
 *
 * @param null $parent - id родительской категории для получения дочерних
 *
 * @return array
 */
function wpp_get_location_category( $parent = null ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wpp_company_data';
	$select_cat = [];


	if ( empty( $parent ) ) {
		$pluck = 'country';
		$_cats = $wpdb->get_results( "SELECT DISTINCT `country` FROM $table_name", ARRAY_A );
	} else {
		$_cats = $wpdb->get_results( "SELECT DISTINCT `sub` FROM $table_name WHERE `cat` = $parent", ARRAY_A );
		$pluck = 'sub';
	}


	if ( ! empty( $_cats ) ) {

		$_uniq_cats = wp_list_pluck( $_cats, $pluck );
		$_uniq_cats = array_filter( $_uniq_cats );

		foreach ( $_uniq_cats as $_cat_id ) {

			$select_cat[ $_cat_id ] = get_term( $_cat_id )->name;
		}

		asort( $select_cat );

	}

	return $select_cat;


}

/**
 * Получение данных геообъекта по ID ли по названию
 *
 * @param $id - id или название геобъекта
 * @param $type - тип геобъекта
 *
 * @return null|string
 */
function wpp_get_data_location( $id, $type ) {
	global $wpdb;


	$preff = 'city' === $type ? 'cities' : ( 'country' === $type ? 'countries' : 'states' );

	$table_name = $wpdb->prefix . 'location_' . $preff;

	$cell  = is_integer( (int) $id ) && ! empty( (int) $id ) ? '`name`' : '`id`';
	$where = is_integer( (int) $id ) && ! empty( (int) $id ) ? '`id`' : '`name`';
	$id    = esc_sql( $id );

	return $wpdb->get_var( sprintf( "SELECT $cell FROM $table_name WHERE $where = '%s'", $id ) );

}

/**
 * Получение объекта локации по строке или ID
 *
 * @param null $parent
 * @param string $type
 *
 * @return array|null
 */
function wpp_get_company_location( $parent = null, $type = 'country' ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wpp_company_data';
	$select_cat = [];


	if ( empty( $parent ) ) {
		$_cats = $wpdb->get_results( "SELECT DISTINCT `country` FROM $table_name", ARRAY_A );
	} else {
		if ( $type === 'state' ) {
			$_cats = $wpdb->get_results( "SELECT DISTINCT `state` FROM $table_name WHERE `country` = $parent", ARRAY_A );
		} else if ( $type === 'city' ) {
			$_cats = $wpdb->get_results( "SELECT DISTINCT `city` FROM $table_name WHERE `state` = $parent", ARRAY_A );
		} else {
			return null;
		}
	}


	if ( ! empty( $_cats ) ) {

		$_uniq_cats = wp_list_pluck( $_cats, $type );
		$_uniq_cats = array_filter( $_uniq_cats );

		foreach ( $_uniq_cats as $_cat_id ) {
			$select_cat[ $_cat_id ] = wpp_get_data_location( $_cat_id, $type );
		}

		asort( $select_cat );

	}

	return $select_cat;


}

/**
 * Получение Города
 *
 * @param $id
 *
 * @return null|string
 */
function wpp_get_city( $id ) {
	return wpp_get_data_location( $id, 'city' );
}

/**
 * Получение Страны
 *
 * @param $id
 *
 * @return null|string
 */
function wpp_get_country( $id ) {
	return wpp_get_data_location( $id, 'country' );
}

/**
 * Получение Штата
 *
 * @param $id
 *
 * @return null|string
 */
function wpp_get_state( $id ) {
	return wpp_get_data_location( $id, 'state' );
}

/**
 * Получение списка iтатов у той же странгы у кторого используется текущий штат
 *
 * @param $id
 *
 * @return array|bool
 */
function wpp_get_parent_state_country_states_list( $id ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wpp_company_data';

	$sql = sprintf( "SELECT DISTINCT `state` FROM %s WHERE `country` = (SELECT DISTINCT `country` FROM `wp_wpp_company_data` WHERE `state` = '%s')", $table_name, $id );

	$result = $wpdb->get_results( $sql, ARRAY_A );


	return ! empty( $result ) ? wp_list_pluck( $result, 'state' ) : false;

}

/**
 * Список штатов
 *
 * @param $country_ID
 *
 * @return array
 */
function wpp_list_pluck_states( $country_ID ) {
	$out = [];
	global $wpdb;
	$table_name     = $wpdb->prefix . 'wpp_company_data';
	$table_name_loc = $wpdb->prefix . 'location_states';

	$sities_id = $wpdb->get_results( sprintf( "SELECT `state` FROM %s WHERE `country` = '%s'", $table_name, $country_ID ), ARRAY_A );

	if ( ! empty( $sities_id ) ) {


		$states_ids = wp_list_pluck( $sities_id, 'state' );
		$states_ids = array_unique( $states_ids );

		foreach ( $states_ids as $state_id ) {

			$name = $wpdb->get_var( sprintf( "SELECT `name` FROM $table_name_loc WHERE `id` = %s", $state_id ) );

			if ( ! empty( $name ) ) {
				$out[ $state_id ] = $name;
			}

		}

	}

	return $out;
}

/**
 * Список городов
 *
 * @param $country_ID
 *
 * @return array
 */
function wpp_list_pluck_cities( $state_ID ) {
	$out = [];
	global $wpdb;
	$table_name     = $wpdb->prefix . 'wpp_company_data';
	$table_name_loc = $wpdb->prefix . 'location_cities';

	$sities_id = $wpdb->get_results( sprintf( "SELECT `city` FROM %s WHERE `state` = '%s'", $table_name, $state_ID ), ARRAY_A );

	if ( ! empty( $sities_id ) ) {


		$states_ids = wp_list_pluck( $sities_id, 'city' );
		$states_ids = array_unique( $states_ids );

		foreach ( $states_ids as $state_id ) {

			$name = $wpdb->get_var( sprintf( "SELECT `name` FROM $table_name_loc WHERE `id` = %s", $state_id ) );

			if ( ! empty( $name ) ) {
				$out[ $state_id ] = $name;
			}

		}

	}

	return $out;
}