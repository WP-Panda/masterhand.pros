<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

//(new WPP_Messages_Install)->create_table();

class WPP_Messages_Install extends WPP_Messages {

	protected static $_instance = null;

	public function __construct() {
		parent::__construct();
	}

	public static function getInstance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
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


		$table_name_1 = $this->db->prefix . self::tbl_msg;
		//$table_name_2 = $this->db->prefix . self::tbl_mails;

		$charset_collate = $this->db->get_charset_collate();

		$data = [];

		$data[] = "CREATE TABLE IF NOT EXISTS {$table_name_1} (
					`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, /*id*/
					`user_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* id юзера */
					`post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	/* id записи */
				    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					`title` TEXT  NOT NULL DEFAULT '',
					`text` TEXT  NOT NULL DEFAULT '', 
					`group` TEXT  NOT NULL DEFAULT '',
					`seen` int(1) UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY  (id)
							) {$charset_collate};";


		return $data;

	}

}