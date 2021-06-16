<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	require_once 'helpers.php';
	require_once 'classes/WPP_Skills.php';
	require_once 'classes/WPP_Skills_Install.php';
	require_once 'classes/WPP_Skills_Actions.php';
	require_once 'classes/WPP_Skills_User.php';


	function init_sclills() {
		WPP_Skills_Install::getInstance()->wpp_create_table();
	}

	add_action( 'init', 'init_sclills' );