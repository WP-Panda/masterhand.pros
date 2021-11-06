<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Messages {

	/**
	 * Таблица про навыки
	 */
	const tbl_msg = 'wpp_messages';

	/**
	 * Таблица про лайки
	 */
	const tbl_mails = 'wpp_mails';

	/**
	 * Версия
	 */
	const v = '0.0.1';

	/**
	 * Контрольная опция
	 */
	const option_control = 'messages_v';

	/**
	 * Пользователь
	 */
	public $user = '';

	public $db = '';


	/**
	 * WPP_Skills constructor.
	 */
	public function __construct() {
		$this->user = get_current_user_id();
	}


	/**
	 * Отдача
	 *
	 * @param $data
	 *
	 * @return int
	 */
	protected static function insert( $data ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . self::tbl_msg, $data );
		wpp_d_log($wpdb->last_query);
		return $wpdb->insert_id;
	}

	/**
	 * Получение
	 *
	 * @param $user_id
	 * @param int $showposts
	 * @param int $paged
	 *
	 * @return mixed
	 */
	public static function get( $user_id, $showposts = 10, $paged = 1 ) {

		global $wpdb;
		$result = $wpdb->get_results( sprintf( "SELECT * FROM %s WHERE `user_id` = '%s' LIMIT 0, 10", $wpdb->prefix . self::tbl_msg, $user_id ) );



		return $result;

	}


	/**
	 * Удаление
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public static function delete( $data ) {
		global $wpdb;

		$out = $wpdb->delete( $wpdb->prefix . self::tbl_msg, $data );

		return ! empty( $out ) ? $out : false;
	}

}