<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 15.06.2021
 * Time: 18:50
 */

namespace WppMain;

class WppFr_Assets extends WppFr {


	/**
	 * WPP_Skills constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ] );
	}


	/**
	 * Скрипты
	 */
	public static function assets() {

		if ( is_page_template( 'page-profile.php' ) || is_page_template( 'pages/page-profile.php' ) || is_author() ) :
			wp_enqueue_script( 'select-2', get_template_directory_uri() . '/wpp/modules/skills/assets/js/select2.full.min.js', [ 'jquery' ], time(), true );
			wp_enqueue_script( 'wpp-skills', get_template_directory_uri() . '/wpp/modules/skills/assets/js/skills.js', [ 'select-2' ], time(), true );

			wp_enqueue_style( 'wpp-skills', get_template_directory_uri() . '/wpp/modules/skills/assets/css/select2.min.css', [], time(), 'all' );
			wp_enqueue_style( 'wpp-skill', get_template_directory_uri() . '/wpp/modules/skills/assets/css/endorse_skill.css', [], time(), 'all' );
		endif;

	}


}

new WppFr_Assets();