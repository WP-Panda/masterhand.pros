<?php
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

	function simple_csv_exporter_assets() {
		wp_enqueue_style(
			"simple-csv-exporter-admin",
			SIMPLE_CSV_EXPORTER_PLUGIN_URL . "/styles/admin.css",
			array(),
			SIMPLE_CSV_EXPORTER_VERSION
		);

		//since 1.5.5 - July 15, 2020
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_enqueue_style('jquery-ui');

	}

	add_action("admin_enqueue_scripts", "simple_csv_exporter_assets");

	/**
	 * Adds a link to the settings page onto the plugin page.
	 *
	 * @param $links
	 *
	 * @return array
	 */
	function simple_csv_exporter_plugin_settings_link($links) {
		$link_text = __("Export", TEXTDOMAIN);
		$links[]   = '<a href="tools.php?page=Simple_CSV_Exporter_Settings">' . $link_text . '</a>';

		return $links;
	}