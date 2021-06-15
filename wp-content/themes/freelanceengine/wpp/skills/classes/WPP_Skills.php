<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	class WPP_Skills{

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

			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ] );

		}

		/**
		 * Скрипты
		 */
		public static function assets() {

			if ( is_page_template( 'page-profile.php' ) || is_page_template( 'pages/page-profile.php' ) || is_author()) :
				wp_enqueue_script( 'select-2', get_template_directory_uri() . '/wpp/skills/assets/js/select2.full.min.js', [ 'jquery' ], time(), true );
				wp_enqueue_script( 'wpp-skills', get_template_directory_uri() . '/wpp/skills/assets/js/skills.js', [ 'select-2' ], time(), true );


				wp_enqueue_style( 'wpp-skills', get_template_directory_uri() . '/wpp/skills/assets/css/select2.min.css', [], time(), 'all' );
				wp_enqueue_style( 'wpp-skill', get_template_directory_uri() . '/wpp/skills/assets/css/endorse_skill.css', [], time(), 'all' );
			endif;

		}

	}