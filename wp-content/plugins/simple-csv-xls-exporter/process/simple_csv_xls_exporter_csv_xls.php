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
		wp_die();
	}

	function simple_csv_xls_exporter_csv_xls() {
		/** Error display will output csv content and script will fail */
		if(!ini_get('display_errors') || ini_get('display_errors') === '1') {
			ini_set('display_errors', '0');
		}

		global $ccsve_export_check, $export_only;

		// Get the custom post type that is being exported
		$post_type_var = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
		if(empty($post_type_var)) {
			$ccsve_generate_post_type = get_option('ccsve_post_type');
		}
		else {
			$ccsve_generate_post_type = $post_type_var;
		}

		// Get the custom post status that is being exported
		$post_status_var = isset($_REQUEST['post_status']) ? $_REQUEST['post_status'] : '';
		if(empty($post_status_var)) {
			$ccsve_get_option_post_status = get_option('ccsve_post_status');
			$ccsve_generate_post_status   = $ccsve_get_option_post_status['selectinput'][0];
		}
		else {
			$ccsve_generate_post_status = $post_status_var;
		}
		//var_dump($ccsve_generate_post_status); echo $ccsve_generate_post_status; exit;

		// Get only the content from specific user
		if(isset($_REQUEST['user'])) {
			if(!$_REQUEST['user']) {
				$user_id = (int)get_current_user_id();
			}
			else {
				$user_id = (int)$_REQUEST['user'];
			}
		}
		//echo $user_id;
		//echo get_the_author_meta( 'display_name', $user_id );exit;

		//Get only the specific posts by ID
		$specific_posts = isset($_REQUEST['specific_posts']) ? $_REQUEST['specific_posts'] : get_option('ccsve_specified_posts');
		if($specific_posts == '') {
			$specific_posts = array();
		}
		else {
			$specific_posts = explode(",", $specific_posts);
		}

		// Get the custom fields (for the custom post type) that are being exported
		$ccsve_generate_custom_fields      = get_option('ccsve_custom_fields');
		$ccsve_generate_std_fields         = get_option('ccsve_std_fields');
		$ccsve_generate_tax_terms          = get_option('ccsve_tax_terms');
		$ccsve_generate_woocommerce_fields = get_option('ccsve_woocommerce_fields');

		//since 1.5.5 - July 11, 2020
		$date_query = false;
		//$ccsve_date_min = isset($_REQUEST['date_min']) && $_REQUEST['date_min'] ? wp_kses($_REQUEST['date_min'], '') : get_option('ccsve_date_min');

		if(isset($_REQUEST['date_min']) && $_REQUEST['date_min']) {
			$ccsve_date_min = isset($_REQUEST['date_min']) && $_REQUEST['date_min'] ? wp_kses($_REQUEST['date_min'], '') : get_option('ccsve_date_min');
			$date_format = 'Y-d-m';
		} else {
			$ccsve_date_min = get_option('ccsve_date_min');
			$date_format = 'Y-m-d';
		}

		if($ccsve_date_min){
			if(wp_checkdate( (int)date('m', strtotime($ccsve_date_min)), (int)date('d', strtotime($ccsve_date_min)), (int)date('Y', strtotime($ccsve_date_min)), date('Y-m-d', strtotime($ccsve_date_min)))) {
				$date_query = array(
								array(
									'after'     => date($date_format, strtotime($ccsve_date_min)),
									'inclusive' => true,
								),
							);
			}
		}

		// Debug
		/*if(current_user_can('administrator')) {
			echo '<pre>';
			//var_dump($ccsve_date_min);
			var_dump($date_query);
			echo '</pre>';
			exit;
		}*/

		// Are we getting only parents or children?
		if($export_only == 'parents') {

			// Query the DB for all instances of the custom post type
			$ccsve_generate_query = new WP_Query(
				array(
					'ignore_sticky_posts' => true,
					'post_type'      => $ccsve_generate_post_type,
					'post_parent'    => 0,
					'post_status'    => $ccsve_generate_post_status,
					'posts_per_page' => -1,
					'author'         => $user_id,
					'order'          => 'ASC',
					'post_in'        => $specific_posts,
					//since 1.5.4.2 - July 11, 2020
					'date_query'     => $date_query,
					//'orderby' => 'name'
				)
			);
		}
		elseif($export_only == 'children') {

			// Query the DB for all instances of the custom post type
			$csv_parent_export = new WP_Query(
				array(
					'post_type'      => $ccsve_generate_post_type,
					'post_parent'    => 0,
					'post_status'    => $ccsve_generate_post_status,
					'posts_per_page' => -1,
					'author'         => $user_id
				)
			);

			$parents_ids_array = array();
			foreach($csv_parent_export->posts as $post): setup_postdata($post);
				//if($post->post_parent) != 0) {
				$parents_ids_array[] = $post->ID;
				//}
			endforeach;

			$ccsve_generate_query = new WP_Query(
				array(
					'ignore_sticky_posts' => true,
					'post_type'      => $ccsve_generate_post_type,
					'post_status'    => $ccsve_generate_post_status,
					'exclude'        => $parents_ids_array,
					'posts_per_page' => -1,
					'author'         => $user_id,
					'order'          => 'ASC',
					'post_in'        => $specific_posts,
					//since 1.5.4.2 - July 11, 2020
					'date_query'     => $date_query,
					//'orderby' => 'name'
				)
			);
		}
		else {

			// Query the DB for all instances of the custom post type
			$ccsve_generate_query = new WP_Query(
				array(
					'ignore_sticky_posts' => true,
					'post_type'      => $ccsve_generate_post_type,
					'post_status'    => $ccsve_generate_post_status,
					'posts_per_page' => -1,
					'author'         => $user_id,
					'order'          => 'ASC',
					'post__in'       => $specific_posts,
					//since 1.5.4.2 - July 11, 2020
					'date_query'     => $date_query,
					//'orderby' => 'name'
				)
			);
		}

		//echo '<pre>';    var_dump($ccsve_generate_query);    echo '</pre>'; exit;
		wp_reset_query();
		wp_reset_postdata();

		// Count the number of instances of the custom post type
		//$ccsve_count_posts = count($ccsve_generate_query);
		$ccsve_count_posts = $ccsve_generate_query->found_posts;

		// Build an array of the custom field values
		$ccsve_generate_value_arr = array();
		$i                        = 0;

		foreach($ccsve_generate_query->posts as $post): setup_postdata($post);

			$post->permalink      = get_permalink($post->ID);
			$post->post_thumbnail = wp_get_attachment_url(get_post_thumbnail_id($post->ID));

			// get the standard wordpress fields for each instance of the custom post type
			if(!empty($ccsve_generate_std_fields['selectinput'])) {
				foreach($post as $key => $value) {
					if(in_array($key, $ccsve_generate_std_fields['selectinput'])) {
						// Prevent SYLK format issue
						if($key === 'ID') {
							// add an apostrophe before ID
							//$ccsve_generate_value_arr["'".$key][$i] = $post->$key;
							// or make it lower-case
							//$low_id = strtolower($key);
							$low_id                                = 'id';
							$ccsve_generate_value_arr[$low_id][$i] = $post->$key;
						}
						else {
							$ccsve_generate_value_arr[$key][$i] = $post->$key;
						}
					}
				}
			}

			// get custom taxonomy information
			if(!empty($ccsve_generate_tax_terms['selectinput'])) {
				foreach($ccsve_generate_tax_terms['selectinput'] as $tax) {
					$names = array();
					$terms = wp_get_object_terms($post->ID, $tax);

					if(!empty($terms)) {
						if(!is_wp_error($terms)) {
							foreach($terms as $t) {
								//echo $t->name;
								$names[] = htmlspecialchars_decode($t->name);
							}
						}
						else {
							$names[] = '- error -';
						}
					}
					else {
						$names[] = '';
					}

					$ccsve_generate_value_arr[$tax][$i] = implode(',', $names);
					//echo implode(',', $names);
				}
			}

			// get the custom field values for each instance of the custom post type
			if(!empty($ccsve_generate_custom_fields['selectinput'])) {
				$ccsve_generate_post_values = get_post_custom($post->ID);
				foreach($ccsve_generate_custom_fields['selectinput'] as $key) {
					// check if each custom field value matches a custom field that is being exported
					if(array_key_exists($key, $ccsve_generate_post_values)) {
						// if the the custom fields match, save them to the array of custom field values
						$ccsve_generate_value_arr[$key][$i] = $ccsve_generate_post_values[$key]['0'];
					}
				}
			}

			$i++;

		endforeach;

		//exit;

		// create a new array of values that reorganizes them in a new multidimensional array where each sub-array contains all of the values for one custom post instance
		$ccsve_generate_value_arr_new = array();

		foreach($ccsve_generate_value_arr as $value) {
			$i = 0;
			while($i <= ($ccsve_count_posts - 1)) {
				$ccsve_generate_value_arr_new[$i][] = $value[$i];
				$i++;
			}
		}

		if($ccsve_export_check === 'csv') {
			$csv_delimiter               = get_option('ccsve_delimiter');
			$ccsve_generate_csv_filename = simple_csv_xls_exporter_generate_file_name($ccsve_generate_post_type) . ".csv";

			/** Output the headers for the CSV file */
			header('Content-Encoding: UTF-8');
			header("Content-type: text/csv; charset=utf-8");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header('Content-Description: File Transfer');
			header("Content-Disposition: attachment; filename={$ccsve_generate_csv_filename}");
			header("Expires: 0");
			header("Pragma: public");

			echo "\xEF\xBB\xBF"; // UTF-8 BOM

			/** Open the file stream */
			$fh = fopen('php://output', 'wb');

			$headerDisplayed = false;

			foreach($ccsve_generate_value_arr_new as $data) {
				// Add a header row if it hasn't been added yet -- using custom field keys from first array
				if(!$headerDisplayed) {
					fputcsv($fh, array_keys($ccsve_generate_value_arr));
					$headerDisplayed = true;
				}

				// Put the data from the new multi-dimensional array into the stream
				fputcsv($fh, $data, $csv_delimiter);
			}

			// Close the file stream
			fclose($fh);
			// Make sure nothing else is sent, our file is done
			exit;
		}

		// PHP

		if($ccsve_export_check === 'xls') {
			/**
			 * @param $str
			 */
			function cleanData(&$str) {
				$str = preg_replace("/\t/", "\\t", $str);
				$str = preg_replace("/\r?\n/", "\\n", $str);
				if(false !== strpos($str, '"')) {
					$str = '"' . str_replace('"', '""', $str) . '"';
				}
			}

			$filename = simple_csv_xls_exporter_generate_file_name($ccsve_generate_post_type) . ".xls";

			//output the headers for the XLS file
			header('Content-Encoding: UTF-8');
			header("Content-Type: Application/vnd.ms-excel; charset=utf-8");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header('Content-Description: File Transfer');
			header("Content-Disposition: Attachment; Filename=\"$filename\"");
			header("Expires: 0");
			header("Pragma: public");

			$flag = false;
			foreach($ccsve_generate_value_arr_new as $data) {
				if(!$flag) {
					echo implode("\t", array_keys($ccsve_generate_value_arr)) . "\r\n";
					$flag = true;
				}
				array_walk($data, 'cleanData');

				$data_string = implode("\t", array_map('utf8_decode', array_values($data)));

				echo $data_string . "\r\n";
			}
			exit;
		}
	}
