<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Rating_Install {

	/**
	 * Таблица для лога
	 */
	const tbl_rating_log = 'wpp_rating_log';

	/**
	 * Версия
	 */
	const v = '0.0.1';

	/**
	 * Контрольная опция
	 */
	const option_control = 'rating_v';

	/**
	 * Пользователь
	 */
	public $user = '';


	/**
	 * WPP_Skills constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->db     = $wpdb;
		$this->prefix = $this->db->prefix;

		$this->tbl_rating_log = $this->prefix . self::tbl_rating_log;

		$this->user = get_current_user_id();

		//add_action( 'init', [ __CLASS__, 'create_table' ] );

	}


	/**
	 * Cоздание таблицы для базы
	 *
	 * @return bool
	 */
	public function create_table() {

		$option = get_option( self::option_control );

		if ( ! empty( $option ) && (int) $option === self::v ) {
			return false;
		}

		$data = self::table_structure();

		foreach ( $data as $str ) {
			$this->db->query( $str );
		}

		update_option( self::option_control, self::v );

	}

	/**
	 * Структура таблиц
	 *
	 * @return string
	 */
	private function table_structure() {


		$table_name = $this->db->prefix . self::tbl_rating_log;


		$charset_collate = $this->db->get_charset_collate();

		$data = [];

		$data[] = "CREATE TABLE IF NOT EXISTS {$table_name} (
					`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, /*id*/
				    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',	/*date*/
					`key` TEXT  NOT NULL DEFAULT '', /* ключ */
			  		`val` bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* значение */	
					`user_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* id ользователя */
					`liker_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* id того, кто добавил */
					`project_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* id проекта */
				PRIMARY KEY  (id)
							) {$charset_collate};";

		return $data;

	}
}

//(new WPP_Rating_Install)->create_table();