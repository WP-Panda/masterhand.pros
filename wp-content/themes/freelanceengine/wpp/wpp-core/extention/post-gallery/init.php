<?php
	/**
	 * @package    WPP_Core
	 * @subpackage Gallery Images
	 * @author     WP_Panda
	 * @version    2.0.0
	 *
	 * Подключение файлов
	 */

	defined( 'ABSPATH' ) || exit;

	if ( ! function_exists( 'wpp_clean' ) && ! defined( 'WPP_CORE' )  ) {

		require_once 'need-functions.php';

	}

	require_once 'Wpp_Fr_Post_Gallery.php';
	require_once 'helpers.php';