<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	define( 'WPP_HOME', get_home_url() );

	/**
	 * @param $methods
	 *
	 * @return array
	 */

	function wpp_remove_xmlrpc_methods( $methods ) {

		# Отключениe через .htaccetss

		#<Files xmlrpc.php>
		#	Order Allow,Deny
		#	Deny from all
		#</Files>


		# Отключениe через nginx
		#location ~* ^/xmlrpc.php$ {
		#   return 403;
		#}


		return [];
	}

	add_filter( 'xmlrpc_methods', 'remove_xmlrpc_methods' );

	if ( ! defined( 'WP_POST_REVISIONS' ) ) :
		// 5 ревизий для записей
		define( 'WP_POST_REVISIONS', 5 );
	endif;