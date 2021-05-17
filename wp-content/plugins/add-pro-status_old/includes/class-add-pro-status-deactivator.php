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

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Add_Pro_Status
 * @subpackage Add_Pro_Status/includes
 * @author     lazkris <a@a.a>
 */
class Add_Pro_Status_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $table = $wpdb->get_blog_prefix() . 'pro_properties';
        $sql[] = "DROP TABLE IF EXISTS $table";

        $table = $wpdb->get_blog_prefix() . 'pro_status';
        $sql[] = "DROP TABLE IF EXISTS $table";

        $table = $wpdb->get_blog_prefix() . 'pro_values';
        $sql[] = "DROP TABLE IF EXISTS $table";

        $table = $wpdb->get_blog_prefix() . 'pro_options';
        $sql[] = "DROP TABLE IF EXISTS $table";

        foreach ($sql as $item) {
            $wpdb->query($item);
        }
	}

}
