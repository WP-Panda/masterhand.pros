<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$array = ['functions'];

foreach ($array as $part) {
	require_once "{$part}.php";
}
