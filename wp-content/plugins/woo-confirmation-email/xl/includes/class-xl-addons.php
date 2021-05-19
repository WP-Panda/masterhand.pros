<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Basic class that do operations and get data from wp core
 * @since 1.0.0
 * @package XLCore
 * @author XLPlugins
 */
class XL_addons {

	public static $installed_addons = array();
	public static $update_available = array();

	/**
	 * Getting all installed plugin that has xl header within
	 * @return array Addons
	 */
	public static function get_installed_plugins() {
		if ( ! empty( self::$installed_addon ) ) {
			return self::$installed_addon;
		}
		wp_cache_delete( 'plugins', 'plugins' );
		$plugins     = self::get_plugins( true );
		$plug_addons = array();
		foreach ( $plugins as $plugin_file => $plugin_data ) {
			if ( isset( $plugin_data['XL'] ) && $plugin_data['XL'] ) {
				$plug_addons[ $plugin_file ] = $plugin_data;
			}
		}
		self::$installed_addons = $plug_addons;

		return $plug_addons;
	}

	/**
	 * Play it safe and require WP's plugin.php before calling the get_plugins() function.
	 *
	 * @return array An array of installed plugins.
	 */
	public static function get_plugins( $clear_cache = false ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		if ( $clear_cache || ! self::plugins_have_xl_plugin_header( $plugins ) ) {
			$plugins = get_plugins();
		}

		return $plugins;
	}

	/**
	 * Checking Plugin header and Trying to find out the one with the header `XL`
	 *
	 * @param Array $plugins array of available plugins
	 *
	 * @return mixed
	 */
	public static function plugins_have_xl_plugin_header( $plugins ) {
		$plugin = reset( $plugins );

		return $plugin && isset( $plugin['XL'] );
	}

	/**
	 * Get All the plugins whose update is enqueued in WordPress
	 * @return array Set of plugins whose update is available
	 */
	public static function get_all_updates() {
		if ( ! empty( self::$update_available ) ) {
			return self::$update_available;
		}

		$get_all = get_site_transient( 'update_plugins' );

		self::$update_available = $get_all->response;

		return self::$update_available;
	}

}
