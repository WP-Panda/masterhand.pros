<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

Class Wpp_Module_Base {

	/**
	 * The current screen.
	 *
	 * @since 3.1.0
	 * @var WP_Screen
	 */
	protected $screen;
	protected $_args;

	public function __construct( $args = [] ) {

		$this->_args = wp_parse_args( $args, [
			'page_title' => __( 'Module Title', WPP_TEXT_DOMAIN ),
			'menu_title' => __( 'Module Title', WPP_TEXT_DOMAIN ),
			'capability' => 'manage_options',
			'menu_slug'  => 'module-slug',
			'function'   => 'module_functions',
			'icon_url'   => get_stylesheet_directory_uri() . '/wpp/modules/asset/image/gear.png',
			'position'   => 100
		] );

		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
	}


	public function add_menu_page() {

		add_menu_page(
			$this->_args['page_title'],
			$this->_args['menu_title'],
			$this->_args['capability'],
			$this->_args['menu_slug'],
			$this->_args['function'],
			$this->_args['icon_url'],
			$this->_args['position']
		);

	}
}
