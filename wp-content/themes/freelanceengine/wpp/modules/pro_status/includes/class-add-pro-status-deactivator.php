<?php

/**
 * Fired during plugin deactivation
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Add_Pro_Status
 * @subpackage Add_Pro_Status/includes
 */

class Add_Pro_Status_Deactivator {

	public static function deactivate() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table = $wpdb->get_blog_prefix() . 'pro_properties';
		$sql[] = "DROP TABLE IF EXISTS $table";

		$table = $wpdb->get_blog_prefix() . 'pro_status';
		$sql[] = "DROP TABLE IF EXISTS $table";

		$table = $wpdb->get_blog_prefix() . 'pro_values';
		$sql[] = "DROP TABLE IF EXISTS $table";

		$table = $wpdb->get_blog_prefix() . 'pro_options';
		$sql[] = "DROP TABLE IF EXISTS $table";
		//
		//        $table = $wpdb->get_blog_prefix() . 'pro_paid_users';
		//        $sql[] = "DROP TABLE IF EXISTS $table";

		foreach ( $sql as $item ) {
			$wpdb->query( $item );
		}

		// Remove task cron
		wp_clear_scheduled_hook( 'remove_pro_users_task_hook' );
	}

}
