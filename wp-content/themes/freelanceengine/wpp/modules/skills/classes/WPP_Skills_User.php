<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Skills_User extends WPP_Skills {

	protected static $_instance = null;
	protected static $meta_key = '_wpp_skills';
	protected $lk_tbl;
	protected $sk_tbl;

	public function __construct() {
		parent::__construct();
		$this->lk_tbl = $this->db->prefix . self::tbl_likes;
		$this->sk_tbl = $this->db->prefix . self::tbl_skill;
	}

	public static function getInstance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Установка скиллов в профиль пользователя
	 *
	 * @param      $skills
	 * @param null $user_id
	 */
	public function set_user_skills_meta( $skills, $user_id = null ) {
		$user_id = $user_id ?? $this->user;
		update_user_meta( $user_id, self::$meta_key, $skills );
	}

	/**
	 * Список скилов пользователяж
	 *
	 * @param null $user_id
	 *
	 * @return array|bool
	 */
	public function get_user_skill_list( $user_id = null ) {

		$user_id = $user_id ?? $this->user;

		$skills_list = $this->get_user_skills_meta( $user_id );

		if ( ! empty( $skills_list ) ) {

			$list = [];

			foreach ( $skills_list as $skill ) {

				$skill_data = WPP_Skills_Actions::getInstance()->get_skill( $skill, 'id' );

				$list[ $skill_data->id ] = [
					'title' => $skill_data->title,
					'id'    => $skill,
					'count' => $this->get_likes_count_for_user_skill( $skill )
				];
			}

			return $list;
		}

		return false;
	}

	/**
	 * Получение скиллов из профиля пользователя
	 *
	 * @param null $user_id
	 *
	 * @return bool|mixed
	 */
	public function get_user_skills_meta( $user_id = null ) {
		$user_id = $user_id ?? $this->user;
		$skills  = get_user_meta( $user_id, self::$meta_key, true );

		return $skills ?? false;
	}

	/**
	 * Получение количествыа лайков скила у одного человека
	 *
	 * @param      $skill_id
	 * @param null $user_id
	 *
	 * @return null|string
	 */
	public function get_likes_count_for_user_skill( $skill_id, $user_id = null ) {
		$user_id = $user_id ?? $this->user;
		if ( is_page_template( 'page-profile.php' ) ) {
			$count = $this->db->get_var( sprintf( "SELECT COUNT(*) FROM %s WHERE `skill_id` = '%s' AND `likes_id` = '%s'", $this->lk_tbl, $skill_id, $user_id ) );
		} else {
			$count = $this->db->get_var( sprintf( "SELECT COUNT(*) FROM %s WHERE `skill_id` = '%s' AND `liker_id` = '%s'", $this->lk_tbl, $skill_id, $user_id ) );
		}

		return $count;
	}

	public function count_user_skills( $user_id ) {

		$skills = get_user_meta( $user_id, self::$meta_key, true );

		return ! empty( $skills ) ? count( $skills ) : 0;
	}

	/**
	 * Соличество оценок скилов юзером юзера, можно использовать для проверки,
	 * оценивался юзер или нет
	 *
	 * @param $user_target
	 * @param null $user_id
	 *
	 * @return null|string
	 */
	public function is_emdorsment( $user_target, $user_id = null ) {
		$user_id = $user_id ?? $this->user;
		$count   = $this->db->get_var( sprintf( "SELECT COUNT(*) FROM %s WHERE `likes_id` = '%s' AND `liker_id` = '%s'", $this->lk_tbl, $user_target, $user_id ) );

		return $count;
	}

}