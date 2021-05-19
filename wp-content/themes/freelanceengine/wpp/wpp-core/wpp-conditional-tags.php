<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	function wpp_is_ajax() {
		return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
	}