<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$array = [ 'social','constantes', 'theme_class', 'route', 'metaboxes' ];

foreach ( $array as $file ) :
	require_once $file . '.php';
endforeach;
