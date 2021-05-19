<?php

	/**
	 * Plugin Name:      Simple CSV/XLS Exporter
	 * Plugin URI:       https://wordpress.org/plugins/simple-csv-xls-exporter/
	 * Description:      Export posts to CSV or XLS, through a link from backend/frontend. Supports custom post types, WooCommerce products, custom taxonomies and fields. Check the plugin's FAQ for all possible options and plugin uses.
	 * Author:           Shambix
	 * Author URI:       https://www.shambix.com
	 * Version:          1.5.6
	 */

	/**
	 * Forked at https://github.com/Jany-M/simple-csv-xls-exporter
	 * Original author 2013  Ethan Hinson  (email : ethan@bluetent.com)
	*/

	/**
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * @project Simple CSV Exporter
	*/

	/** Prevents this file from being called directly */
	if(!function_exists("add_action")) {
		return;
	}

	define('SIMPLE_CSV_EXPORTER_VERSION', '1.5.6');
	define('SIMPLE_CSV_EXPORTER_TEXTDOMAIN', 'simple-csv-cls-exporter');
	define("SIMPLE_CSV_EXPORTER_PLUGIN_URL", plugin_dir_url(__FILE__));
	define('TEXTDOMAIN', SIMPLE_CSV_EXPORTER_TEXTDOMAIN); // Todo: Remove

	$upload_dir = wp_upload_dir();

	/** Define plugin path */
	if(!defined('SIMPLE_CSV_XLS_EXPORTER_PLUGIN_PATH')) {
		define('SIMPLE_CSV_XLS_EXPORTER_PLUGIN_PATH', plugin_dir_path(__FILE__));
	}

	/** Define plugin path to /process/ subdirectory */
	if(!defined('SIMPLE_CSV_XLS_EXPORTER_PROCESS')) {
		define('SIMPLE_CSV_XLS_EXPORTER_PROCESS', SIMPLE_CSV_XLS_EXPORTER_PLUGIN_PATH . 'process/');
	}

	/** Define extra file name */
	if(!defined('SIMPLE_CSV_XLS_EXPORTER_EXTRA_FILE_NAME')) {
		define('SIMPLE_CSV_XLS_EXPORTER_EXTRA_FILE_NAME', '');
	}

	$include_directories = array(
		"classes",
		"includes"
	);

	foreach($include_directories as $include_directory) {
		$include_directory = SIMPLE_CSV_XLS_EXPORTER_PLUGIN_PATH . $include_directory;
		$files             = glob("$include_directory/*.php");

		foreach($files as $file) {
			if(is_file($file)) {
				require_once $file;
			}
		}
	}

	register_activation_hook(__FILE__, array('Simple_CSV_Exporter', 'activate'));
	register_deactivation_hook(__FILE__, array('Simple_CSV_Exporter', 'deactivate'));

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'simple_csv_exporter_plugin_settings_link');

	$SIMPLE_CSV_EXPORTER = new Simple_CSV_Exporter();