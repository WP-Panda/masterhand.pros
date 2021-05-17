<?php

/**
 * Fired during plugin activation
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Add_Pro_Status
 * @subpackage Add_Pro_Status/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Add_Pro_Status
 * @subpackage Add_Pro_Status/includes
 * @author     lazkris <a@a.a>
 */
class Add_Pro_Status_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        // Код активации ...
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

        $table = $wpdb->get_blog_prefix() . 'pro_status';
        $sql[] = "CREATE TABLE {$table} (
            id  INT(20) unsigned NOT NULL auto_increment,
            status_name varchar(255) NOT NULL,
            status_position INT(20) unsigned NOT NULL,
            user_role varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        )
        {$charset_collate};";

        $table = $wpdb->get_blog_prefix() . 'pro_properties';
        $sql[] = "CREATE TABLE {$table} (
            id  INT(20) unsigned NOT NULL auto_increment,
            property_name varchar(255) NOT NULL,
            property_position INT(20) unsigned NOT NULL,
            property_display tinyint(1) unsigned NOT NULL,
            property_type tinyint(1) unsigned NOT NULL,
            property_published tinyint(1) unsigned NOT NULL,
            user_role varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        )
        {$charset_collate};";

        $table = $wpdb->get_blog_prefix() . 'pro_values';
        $sql[] = "CREATE TABLE {$table} (
            id  INT(20) unsigned NOT NULL auto_increment,
            status_id INT(20) unsigned NOT NULL,
            property_id INT(20) unsigned NOT NULL,
            property_value varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        )
	    {$charset_collate};";

        $table = $wpdb->get_blog_prefix() . 'pro_options';
        $sql[] = "CREATE TABLE {$table} (
            id  INT(20) unsigned NOT NULL auto_increment,
            option_key varchar(255) NOT NULL,
            option_value varchar(255) NOT NULL,
            property_id INT(20) unsigned NOT NULL,
            PRIMARY KEY  (id)
        )
        {$charset_collate};";


        /**
         * The table with paid users
         */
        $table = $wpdb->get_blog_prefix() . 'pro_paid_users';
        $sql[] = "CREATE TABLE {$table} (
            id INT(10) unsigned NOT NULL auto_increment,
            txn_id  varchar(20),       --  номер транзакции
            user_id INT(10) unsigned NOT NULL, 
            fist_name varchar (100),
            last_name varchar (100),
            payer_email varchar (100),
            status_id INT(10) unsigned NOT NULL,
            order_duration INT(10) unsigned NOT NULL,
            price VARCHAR (10),
            activation_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
            expired_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY (id)
        )
        {$charset_collate};";

        foreach ($sql as $item) {
            dbDelta($item);
        }

        $table_options = $wpdb->get_blog_prefix() . 'pro_options';
        $wpdb->query("INSERT INTO {$table_options} (`option_key`, `option_value`) VALUES ('currency', 'y.e.')");
    }

}
