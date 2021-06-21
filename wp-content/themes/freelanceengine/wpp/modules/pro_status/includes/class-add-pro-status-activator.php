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

class Add_Pro_Status_Activator
{

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
            property_nickname varchar(255) NOT NULL,
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
            first_name varchar (100),
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

        $table_status = $wpdb->get_blog_prefix() . 'pro_status';
        $statuses = array(
            array(1, 'Basic', 1, 'freelance'),
            array(2, 'Business PRO', 2, 'freelance'),
            array(3, 'Premium PRO', 3, 'freelance'),
            array(4, 'Basic plan', 1, 'employer'),
            array(5, 'PRO plan', 2, 'employer'),
        );
        foreach ($statuses as $item) {
            if (empty($wpdb->get_var("select count(*) from {$table_status} s where s.status_name='{$item['0']}' and s.user_role='{$item['2']}'"))) {
                $wpdb->query("INSERT INTO {$table_status} (`id`,`status_name`,`status_position`,`user_role`)
                    VALUES ('{$item['0']}','{$item['1']}','{$item['2']}','{$item['3']}')");
            }
        }

        // !!!!!!! сделать переводы !!!!!!!!

        $properties_fre = array(
            array('status_next_to_name', 'Status "PRO" next to your name', 1, 1, 1, 1, 'freelance'),
            array('activity_rating_increase', 'Activity rating increase (for a period of status)', 2, 1, 1, 1, 'freelance'),
            array('work_in_portfolio', 'Number of works in Portfolio', 3, 1, 1, 1, 'freelance'),
            array('access_to_pro_projects', '"Only for Business Accounts" projects bidding', 4, 1, 0, 1, 'freelance'),            
            array('bid_in_project', 'Bids on Projects per day', 5, 1, 1, 1, 'freelance'),
            array('max_portfolio', 'Extended Portfolio ', 6, 1, 0, 1, 'freelance'),
            array('sort_skill', 'Priority of Specializations in Portfolio', 7, 1, 0, 1, 'freelance'),
            array('subsribe_to_new_proj', 'New Projects notification', 8, 1, 0, 1, 'freelance'),
            array('access_to_contacts', 'Contacts visible', 9, 1, 0, 1, 'freelance'),
            array('priority_in_list_freelancer', 'Priority in Professionals Catalog', 10, 1, 0, 1, 'freelance'),
            array('show_max_bid', 'Extended form for bidding projects', 11, 1, 0, 1, 'freelance'),
            array('access_advert', 'My Special Offers (per month)', 12, 1, 1, 1, 'freelance'),
            array('no_ads', 'No third-party advertising in profile', 13, 1, 0, 0, 'freelance'),
            array('visual_flag', 'Master/Creator/Expert title in profile', 14, 1, 0, 1, 'freelance'),
            array('personal_cover', 'Custom background in Portfolio', 15, 1, 0, 1, 'freelance'),
            array('private_bid', 'Private Bidding in Projects', 16, 1, 0, 1, 'freelance'),
            array('examples_jobs_in_advert', '"My Works Photos" in My Special Offers', 17, 1, 0, 1, 'freelance'),
            array('price_fre_1', 'Price', 18, 1, 2, 1, 'freelance'),
            array('price_fre_2', 'Price', 19, 0, 2, 1, 'freelance'),
            array('price_fre_3', 'Price', 20, 0, 2, 1, 'freelance'),
        );
        $properties_em = array(
            array('price_for_extra_options', 'Prices for Extra Options', 1, 1, 1, 1, 'employer'),
            array('posts_per_day', 'Posts per day', 2, 1, 1, 1, 'employer'),
            array('number_of_specializations', 'Number of Specializations in a post', 3, 1, 1, 1, 'employer'),
            array('your_rating', 'Your rating', 3, 1, 1, 1, 'employer'),
            array('you_can_see', 'You can see contacts', 4, 1, 1, 1, 'employer'),
            array('your_contacts_visible', 'Your contacts are visible', 5, 1, 1, 1, 'employer'),
            
            array('create_project_for_all', 'For all types of accounts (non-PRO and PRO). Unchecked - for PRO accounts only', 6, 1, 3, 1, 'employer'),
            array('priority_in_list_project', 'Keep at the top of the list', 7, 1, 3, 1, 'employer'),
            array('highlight_project', 'Highlight the project in colour', 8, 1, 3, 1, 'employer'),
            array('urgent_project', 'Mark as "Urgent"', 9, 1, 3, 1, 'employer'),
            array('hidden_project', 'Mark as "Hidden"', 10, 1, 3, 1, 'employer'),
            array('price_em_1', 'Price', 11, 1, 2, 1, 'employer'),
            array('price_em_2', 'Price', 12, 0, 2, 1, 'employer'),
            array('price_em_3', 'Price', 13, 0, 2, 1, 'employer'),
        );
        $properties = array_merge($properties_fre, $properties_em);
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
        foreach ($properties as $item) {
            if (empty($wpdb->get_var("select count(*) from {$table_properties} p where p.property_nickname='{$item['0']}'"))) {
                $wpdb->query("INSERT INTO {$table_properties} (`property_nickname`,`property_name`,`property_position`,`property_display`,`property_type`, `property_published`,`user_role`)
                    VALUES ('{$item['0']}','{$item['1']}','{$item['2']}','{$item['3']}','{$item['4']}','{$item['5']}','{$item['6']}')");
            }
        }

        $values_fre = array(
            array(0, 0, 5, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'free', 'free','free'),
            array(1, 'Your rating x 1.5', 25, 1, 100, 1, 1, 1, 1, 1, 1, 3, 1, 0, 0, 0, 0, 10, 20, 30),
            array(1, 'Your rating x 2', 100, 1, 100, 1, 1, 1, 1, 1, 1, 25, 1, 1, 1, 1, 1, 20, 30, 50),
        );
        foreach ($values_fre as $key1 => $item) {
            foreach ($item as $key2 => $value) {
                $values[] = array($key1 + 1, $key2 + 1, $value);
            }
        }

        $values_em = array(
            array('Regular','Three','One','As it is','Only "PRO" Professionals','Only for "PRO" Professionals',10, 25, 15, 15, 15, 'free', 'free', 'free'),
            array('Discounted','Unlimited','Three','Your rating x 2','All Professionals','For all Professionals','free', 5, 3, 3, 3, 10, 20, 30),
        );
        foreach ($values_em as $key1 => $item) {
            foreach ($item as $key2 => $value) {
                $values[] = array(count($values_fre) + $key1 + 1, count($properties_fre) + $key2 + 1, $value);
            }
        }

        $table_values = $wpdb->get_blog_prefix() . 'pro_values';
        foreach ($values as $item) {
            if (empty($wpdb->get_var("select count(*) from {$table_values} v where v.status_id='{$item['0']}' and v.property_id='{$item['1']}'"))) {
                $wpdb->query("INSERT INTO {$table_values} (`status_id`,`property_id`,`property_value`)
                    VALUES ('{$item['0']}','{$item['1']}','{$item['2']}')");
            }
        }

        $table_options = $wpdb->get_blog_prefix() . 'pro_options';

        $options = array(
            array('currency', 'y.e.', 0),
            array('time', 1, count($properties_fre) - 2),
            array('time', 6, count($properties_fre) - 1),
            array('time', 12, count($properties_fre)),
            array('time', 1, count($properties_fre) + count($properties_em) - 2),
            array('time', 6, count($properties_fre) + count($properties_em) - 1),
            array('time', 12, count($properties_fre) + count($properties_em)),
        );
        foreach ($options as $item) {
            if (empty($wpdb->get_var("select count(*) from {$table_options} p where o.option_key='{$item['0']}' and o.option_value='{$item['1']}'"))) {
                $wpdb->query("INSERT INTO {$table_options} (`option_key`, `option_value`, `property_id`)
                    VALUES ('{$item['0']}','{$item['1']}','{$item['2']}')");
            }
        }


        // Adds once three hours to the existing schedules.
//        add_filter('cron_schedules', 'cron_add_one_three_hours');
//        function cron_add_one_three_hours($schedules)
//        {
//            $schedules['one_three_hours'] = array(
//                'interval' => 10800,
//                'display' => __('Once Three Hours')
//            );
//            return $schedules;
//        }
//
//        // The cron task to remove pro user
//        if (!wp_next_scheduled('remove_pro_users_task_hook')) {
//            wp_schedule_event(time(), 'one_three_hours', 'remove_pro_users_task_hook');
//        }


    }

}
