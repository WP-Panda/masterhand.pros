<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * @project Simple CSV Exporter
 */

function generate_post_meta_keys($post_type) {
		global $wpdb;
		$query = "
            SELECT DISTINCT($wpdb->postmeta.meta_key)
            FROM $wpdb->posts
            LEFT JOIN $wpdb->postmeta
            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
            WHERE $wpdb->posts.post_type = '%s'
            AND $wpdb->postmeta.meta_key != ''
            AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
          ";
		// Removed this
		// AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
		// as second AND, to show also fields starting with _
		$meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

		/*set_transient($store_meta_keys, $meta_keys, 6 * HOUR_IN_SECONDS); // 24h
  }
  $meta_keys = get_transient($store_meta_keys);*/

		/*echo '<pre>';
		var_dump($meta_keys);
		echo '</pre>';*/

		return $meta_keys;
	}

	/**
	 * @param $post_type
	 * @param $post_status
	 *
	 * @return array
	 */
	function generate_std_fields($post_type, $post_status) {
		if($post_status == '' || is_null($post_status)) {
			$post_status = 'any';
		}
		//var_dump($post_status);
		$fields = array('permalink', 'post_thumbnail');
		$q      = new WP_Query(array('post_type' => $post_type, 'post_status' => $post_status, 'posts_per_page' => 1));
		$p      = $q->posts[0];
		foreach($p as $f => $v) {
			$fields[] = $f;
		}

		return $fields;
	}

	function ccsve_checkboxes_fix($input, $post_type) {
		$options  = get_option('ccsve_custom_fields');
		$merged   = $options;
		$merged[] = $input;
	}
