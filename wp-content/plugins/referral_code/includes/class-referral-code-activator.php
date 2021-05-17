<?php

class Referral_Code_Activator
{

    public static function activate()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

        $table = $wpdb->get_blog_prefix() . 'referral_code';
        $sql[] = "CREATE TABLE {$table} (
            user_id INT(20) unsigned NOT NULL,
            referral_code INT(20) unsigned NOT NULL,
            user_id_referral INT(20) unsigned,
            PRIMARY KEY  (referral_code)
        )
        {$charset_collate};";

        foreach ($sql as $item) {
            dbDelta($item);
        }

        $table_user = $wpdb->get_blog_prefix() . 'users';
        $table_referral_code = $wpdb->get_blog_prefix() . 'referral_code';
        $users = $wpdb -> get_results("SELECT id FROM {$table_user} WHERE id NOT IN (SELECT user_id FROM {$table_referral_code})", ARRAY_A);

        foreach ($users as $user_id) {
            set_referral_code_by_old_user($user_id['id']);
        }
    }
}