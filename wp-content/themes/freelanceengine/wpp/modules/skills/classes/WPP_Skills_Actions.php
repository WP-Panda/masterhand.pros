<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 13.06.2021
 * Time: 19:24
 */

class WPP_Skills_Actions extends WPP_Skills {

	protected static $_instance = null;
	protected $sk_tbl;
	protected $lk_tbl;
	protected $time;

	public function __construct() {
		parent::__construct();

		$this->sk_tbl = $this->db->prefix . self::tbl_skill;
		$this->lk_tbl = $this->db->prefix . self::tbl_likes;
		$this->time   = current_time( 'mysql' );
	}

	public static function getInstance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Создание скила
	 *
	 * @param $skill
	 *
	 * @return bool|false|int
	 */
	public function create_skill( $skill ) {

		$skill  = $this->sanitize_skill( $skill );
		$insert = false;

		if ( ! empty( $skill ) ) {

			$check = $this->get_skill( $skill );

			if ( empty( $check ) ) {
				$insert = $this->db->insert( $this->sk_tbl, [
					'date'    => $this->time,
					'title'   => $skill,
					'user_id' => $this->user
				] );
			}
		}

		return $insert;

	}

	/**
	 * Очистка скила
	 *
	 * @param $skill
	 *
	 * @return bool|null|string
	 */
	public function sanitize_skill( $skill ) {

		$skill = wpp_clean_str( $skill );

		if ( ! empty( $skill ) && ' ' !== $skill ) {
			return $skill;
		} else {
			return false;
		}

	}

	/**
	 * Получение скила
	 *
	 * @param $skill
	 *
	 * @return array|null|object|void
	 */
	public function get_skill( $skill, $type = null ) {

		$skill = $this->sanitize_skill( $skill );

		$field = ! empty( $type ) ? sprintf( '`%s`', $type ) : '`title`';

		return $this->db->get_row( sprintf( "SELECT * FROM %s WHERE %s= '%s'", $this->sk_tbl, $field, $skill ) );

	}

	/**
	 * Добавить оценку скила
	 *
	 * @param $likes_id
	 * @param $skill_id
	 *
	 * @return array
	 */
	public function add_likes( $likes_id, $skill_id ) {
		$user_id = $user_id ?? $this->user;

		$check = $this->is_endorse( $likes_id, $skill_id );

		if ( empty( $check ) ) {
			$insert = $this->db->insert( $this->lk_tbl, [
				'likes_id' => $likes_id,
				'skill_id' => $skill_id,
				'liker_id' => $user_id,
				'date'     => $this->time
			] );

			if ( ! empty( $insert ) ) {
				return [ 'msg' => 'OK' ];
			} else {
				return [ 'error' => true, 'msg' => 'Undefened Error' ];
			}

		} else {
			return [ 'error' => true, 'msg' => 'Likes to Bee' ];
		}

	}

	/**
	 *  Лайкнуто или нет
	 *
	 * @param $likes_id
	 * @param $skill_id
	 *
	 * @return array|null|object|void
	 */
	public function is_endorse( $likes_id, $skill_id ) {
		$user_id = $user_id ?? $this->user;
		$check   = $this->db->get_row( sprintf( "SELECT * FROM %s WHERE `skill_id`='%s' AND `liker_id` = '%s' AND `likes_id` = '%s'", $this->lk_tbl, $skill_id, $user_id, $likes_id ) );

		return $check;
	}

	public function remove_likes( $likes_id, $skill_id ) {
		$user_id = $user_id ?? $this->user;
		$check   = $this->is_endorse( $likes_id, $skill_id );

		if ( ! empty( $check ) ) {
			$insert = $this->db->delete( $this->lk_tbl, [
				'likes_id' => $likes_id,
				'skill_id' => $skill_id,
				'liker_id' => $user_id
			] );

			if ( ! empty( $insert ) ) {
				return [ 'msg' => 'OK' ];
			} else {
				return [ 'error' => true, 'msg' => 'Undefened Error' ];
			}

		} else {
			return [ 'error' => true, 'msg' => 'Likes to Bee' ];
		}
	}

}