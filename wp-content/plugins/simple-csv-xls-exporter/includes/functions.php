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

	/**
	 * Generates a randomized file name which
	 * includes the selected export post type.
	 *
	 * @param string $selected_post_type
	 *
	 * @since 1.4.7
	 *
	 * @return string
	 */
	//function simple_csv_xls_exporter_generate_file_name(string $selected_post_type) {
	function simple_csv_xls_exporter_generate_file_name($selected_post_type) {
		if(!$selected_post_type) {
			return "";
		}

		$selected_post_type = trim($selected_post_type);
		$now                = time();
		$md5                = md5($now);
		$md5                = substr($md5, 1, 12);
		$date               = date("Y-m-d");
		$file_name          = $date . "_" . $selected_post_type . "_" . $md5;
		$file_name          = SIMPLE_CSV_XLS_EXPORTER_EXTRA_FILE_NAME . $file_name;
		$file_name          = apply_filters("simple_csv_xls_exporter_export_file_name", $file_name);

		return (string)$file_name;
	}