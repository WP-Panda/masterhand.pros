<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @deprecated
 *
 * @return bool
 */
function wpp_is_ajax() {
	return wp_doing_ajax();
}