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


	protected static function insert( $data ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . self::tbl_msg, $data );
		return $wpdb->insert_id;
	}


}