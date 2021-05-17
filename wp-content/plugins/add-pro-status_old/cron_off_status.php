<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.12.2018
 * Time: 11:38
 */
ini_set('display_errors', 1);
// Include files required for initialization.
require(dirname(dirname(dirname(__DIR__))) . '/wp-blog-header.php');
//запрос в базу за списком кому отключить статус
global $wpdb;

$data = getdate();
$data_d = $data['mday'];
$data_m = $data['mon'];
$data_y = $data['year'];
$data_now = [
    'day' => $data_d,
    'mon' => $data_m,
    'year' => $data_y
];
$table = $wpdb->get_blog_prefix() . 'postmeta';
$sql = "SELECT p.post_id, w.meta_value as data_deactivate FROM wp_postmeta p 
LEFT JOIN wp_postmeta w ON w.meta_key='pro_status_data_deactivate' 
WHERE p.meta_key='pro_status' AND p.meta_value<>1";
$result = $wpdb->get_results($sql, ARRAY_A); // id
foreach ($result as $item) {
	$data_deactivate=unserialize($item['data_deactivate']);
    if (is_array($data_deactivate)) {
        if ($data_deactivate['year'] == $data_now['year'] && 
        	$data_deactivate['mon'] == $data_now['mon'] && 
        	$data_deactivate['day'] == $data_now['day']) {
            echo off_status($item['post_id']);
        }
    }
}