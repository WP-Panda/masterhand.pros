<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Date: 15.06.2021
	 * Time: 18:50
	 */

	namespace WppMain;

	class WppFr{

		/**
		 * Пользователь
		 *
		 * @var int
		 */
		public $user;

		/**
		 * База данных
		 *
		 * @var object
		 */
		public $db;

		/**
		 * Префикс базы данных
		 *
		 * @var string
		 */
		public $prefix;

		/**
		 * WPP_Skills constructor.
		 */
		public function __construct() {
			global $wpdb;

			$this->db     = $wpdb;
			$this->prefix = $this->db->prefix;
			$this->user = get_current_user_id();

		}

	}