<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


class WPP_Skills_Install extends WPP_Skills {

	protected static $_instance = null;

	public function __construct() {
		parent::__construct();

		//add_action( 'init', [ __CLASS__, 'wpp_create_table' ] );
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
	public function wpp_create_table() {

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


		$table_name_1 = $this->db->prefix . self::tbl_skill;
		$table_name_2 = $this->db->prefix . self::tbl_likes;

		$charset_collate = $this->db->get_charset_collate();

		$data = [];

		$data[] = "CREATE TABLE IF NOT EXISTS {$table_name_1} (
					`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, /*id*/
				    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',	/*date*/
					`title` TEXT  NOT NULL DEFAULT '', /* заголовок */
					`group` TEXT  NOT NULL DEFAULT '', /* группа */
					`user_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* id того, кто добавил */
					`count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	/* количество использований */
					`validate`  int(20) UNSIGNED NOT NULL DEFAULT 0, /* валидно или нет */	
				PRIMARY KEY  (id)
							) {$charset_collate};";


		$data[] = "CREATE TABLE IF NOT EXISTS {$table_name_2} (
					`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, /*id*/
				    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',	/*date*/
				    `skill_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,	/* id навыка */
					`liker_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* кто лайкнул  */
					`likes_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0, /* кого лайкнули  */
				PRIMARY KEY  (id)
							) {$charset_collate};";

		return $data;

	}

}