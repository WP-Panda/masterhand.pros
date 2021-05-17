<?php

class Referral_Code_Deactivator
{

    public static function deactivate()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table = $wpdb->get_blog_prefix() . 'referral_code';
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}