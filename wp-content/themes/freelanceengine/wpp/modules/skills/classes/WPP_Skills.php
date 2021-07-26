<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Skills {

	/**
	 * Таблица про навыки
	 */
	const tbl_skill = 'wpp_skills';

	/**
	 * Таблица про лайки
	 */
	const tbl_likes = 'wpp_likes';

	/**
	 * Версия
	 */
	const v = '0.0.5';

	/**
	 * Контрольная опция
	 */
	const option_control = 'skills_v';

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

		$this->tbl_skill = $this->prefix . self::tbl_skill;
		$this->tbl_likes = $this->prefix . self::tbl_likes;

		$this->user = get_current_user_id();


	}


}