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

	if(!class_exists('Simple_CSV_Exporter')) {
		/**
		 * Class Simple_CSV_Exporter
		 */
		class Simple_CSV_Exporter {
			/**
			 * Simple_CSV_Exporter constructor.
			 */
			public function __construct() {
				if(isset($_GET['export']) && ($_GET['export'] === 'csv' || $_GET['export'] === 'xls')) {
					add_action('wp_loaded', 'ccsve_export');
				}

				new Simple_CSV_Exporter_Settings();
			}

			public static function activate() {
			}

			public static function deactivate() {
				unregister_setting('wp_ccsve-group', 'ccsve_post_type');
				unregister_setting('wp_ccsve-group', 'ccsve_post_status');
				unregister_setting('wp_ccsve-group', 'ccsve_std_fields');
				unregister_setting('wp_ccsve-group', 'ccsve_tax_terms');
				unregister_setting('wp_ccsve-group', 'ccsve_custom_fields');
				unregister_setting('wp_ccsve-group', 'ccsve_woocommerce_fields');
				//since 1.5.5 - July 11, 2020
				unregister_setting('wp_ccsve-group', 'ccsve_date_min');

				delete_option('wp_ccsve-group');
			}
		}
	}