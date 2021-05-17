<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              /
 * @since             1.0.0
 * @package           Add_Pro_Status
 *
 * @wordpress-plugin
 * Plugin Name:       activate PRO
 * Plugin URI:        /
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            lazkris
 * Author URI:        /
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       add-pro-status
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');
//define('ET_DOMAIN', 'enginetheme');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-add-pro-status-activator.php
 */
function activate_pro_status()
{
    require_once 'includes/class-add-pro-status-activator.php';
    Add_Pro_Status_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-add-pro-status-deactivator.php
 */
function deactivate_pro_status()
{
    require_once 'includes/class-add-pro-status-deactivator.php';
    Add_Pro_Status_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_pro_status');
register_deactivation_hook(__FILE__, 'deactivate_pro_status');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
//require plugin_dir_path(__FILE__) . 'includes/class-add-pro-status.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
//function run_add_pro_status()
//{
//    $plugin = new Add_Pro_Status();
//    $plugin->run();
//}
//
//run_add_pro_status();

add_action('admin_menu', 'menu');
function menu()
{
    add_menu_page('User PRO status', 'PRO status', 'manage_options', 'pro-status', 'show_status_users', '', '200');
    $page_freelance = add_submenu_page('pro-status', 'Freelance status', 'Freelance status', 'manage_options', 'freelance', 'show_settings_freelance');
    add_action('admin_print_styles-' . $page_freelance, 'my_plugin_admin_styles');
    $page_employer = add_submenu_page('pro-status', 'Employer status', 'Employer status', 'manage_options', 'employer', 'show_settings_employer');
    add_action('admin_print_styles-' . $page_employer, 'my_plugin_admin_styles');
}

function my_plugin_admin_styles()
{
    wp_enqueue_style('myPluginStylesheet', plugins_url('css/add-pro-status-public.css', __FILE__));
}

// Include table with pro users
require_once (__DIR__ . '/includes/class-pro-paid-users-table.php');

// отображение в админке
// список пользователей с активным статусом
// настройка статусов
function show_status_users()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // список пользователей с активным статусом
    $t_usermeta = $wpdb->get_blog_prefix() . 'usermeta';
    $t_postmeta = $wpdb->get_blog_prefix() . 'postmeta';
    $t_pro_status = $wpdb->get_blog_prefix() . 'pro_status';
//    $sql = "select pm.post_id, p.post_title, s.status_name from wp_postmeta pm left join wp_posts p on p.ID=pm.post_id
//left join {$table} s on pm.meta_value=s.id where pm.meta_key='pro_status' && meta_value>0";
    $sql = "SELECT wp_users.id, wp_users.display_name, pro_status.meta_value as pro_status, status_name.status_name, data_active.meta_value as data_active, data_deactivate.meta_value as data_deactivate from wp_users
left join $t_usermeta  on wp_usermeta.user_id=wp_users.ID and wp_usermeta.meta_key='user_profile_id'
left join $t_postmeta pro_status on pro_status.post_id=wp_usermeta.meta_value and pro_status.meta_key='pro_status'
left join $t_pro_status status_name on status_name.id=pro_status.meta_value
left join $t_postmeta data_active on data_active.post_id=wp_usermeta.meta_value and data_active.meta_key='pro_status_data_active'
left join $t_postmeta data_deactivate on data_deactivate.post_id=wp_usermeta.meta_value and data_deactivate.meta_key='pro_status_data_deactivate'
ORDER BY wp_users.id";
    $users_pro = $wpdb->get_results($sql, ARRAY_A);

    $base_status = get_base_status();

    include 'page-pro-status.php';
}

function get_base_status($user_role='')
{
    global $wpdb;
    if(!empty($user_role)){
        $where_and = " AND user_role='$user_role'";
    } else $where_and='';
    $table = $wpdb->get_blog_prefix() . 'pro_status';
    $base_status = $wpdb->get_results("select id, status_name, user_role from {$table} where status_position=1 $where_and", ARRAY_A);
    if (empty($base_status)) {
        return null;
    } else return $base_status[0];
}

function show_settings_freelance()
{
    show_settings('freelance');
}

function show_settings_employer()
{
    show_settings('employer');
}

function show_settings($type)
{
    global $wpdb;
    $type_user=$type;
    $where = '';//"WHERE p.user_role='$type_user' AND s.user_role='$type_user'";
    $result = table_properties($type_user, $where, 1);
//    var_dump($result);
    $table = $wpdb->get_blog_prefix() . 'pro_status';
    $statuses = $wpdb->get_results("SELECT id as status_id, status_position, status_name FROM $table WHERE user_role='$type_user' ORDER BY status_position", ARRAY_A);
    $table = $wpdb->get_blog_prefix() . 'pro_properties';
    $properties = $wpdb->get_results("
SELECT p.id as property_id, p.property_position, p.property_name, p.property_display, p.property_type, p.property_published, o.option_value 
FROM $table p
left join wp_pro_options o ON o.property_id=p.id AND o.option_key='time'
WHERE user_role='$type_user'
ORDER BY property_position", ARRAY_A);
    $table = $wpdb->get_blog_prefix() . 'pro_options';
    $options = $wpdb->get_results("SELECT id as option_id, option_value, option_key FROM $table WHERE option_key!='time'", ARRAY_A);
    include 'page-pro-properties.php';
}


// добавляет ссылку на pro в профиле
/*function add_pro_str()
{
    global $current_user, $wpdb;
    $profile_id = get_user_meta($current_user->ID, 'user_profile_id', true);
    if ($profile_id) {
//        if (ae_user_role($current_user->ID) == FREELANCER) {
        $pro_status = get_post_meta($profile_id, 'pro_status', true);
        if (!empty($pro_status) && $pro_status != 1) {
            $table = $wpdb->get_blog_prefix() . 'pro_status';
            $res = $wpdb->get_var("select status_name from {$table} where id={$pro_status}");
            $pro_data_deactivate = get_post_meta($profile_id, 'pro_status_data_deactivate', true);
            $data = implode('.', $pro_data_deactivate);
            echo "<span class='fre-view-as-others fre-pro-status'>Your account - " . $res . "</span>";
            echo "<span class='fre-view-as-others fre-pro-date'>Active by - " . $data . "</span>";
            echo "<a href='/pro' class='fre-view-as-others fre-pro'>";
            echo "Change Business Account Pro";
            echo "</a>";
//            'action' 'deactivate'
        } else {
            echo "<a href='/pro' class='fre-view-as-others fre-pro'>";
            echo "Activate Business Account Pro";
            echo "</a>";
        }
//        } else {
//            echo "<a href='' class='fre-view-as-others fre-pro'>";
//            echo "employer";
//            echo "</a>";
//        }
    }
}*/

function table_properties($type, $where_and = '', $options = 0) // проверить при одинаковых названиях статусов берет только первый
{
    global $wpdb;
    $where="WHERE p.user_role='$type' AND s.user_role='$type' ".$where_and;
    $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
    $table_status = $wpdb->get_blog_prefix() . 'pro_status';
    $table_values = $wpdb->get_blog_prefix() . 'pro_values';
    $table_options = $wpdb->get_blog_prefix() . 'pro_options';
    $sql = "
SELECT s.id as status_id, s.status_name, p.id as property_id, p.property_name, p.property_type, p.property_display, p.property_published, v.property_value, o.option_value FROM $table_values v 
left JOIN $table_properties p on p.id=v.property_id 
left JOIN $table_status s on s.id=v.status_id 
left join $table_options o ON o.property_id=p.id AND o.option_key='time'
$where
ORDER BY s.status_position, p.property_position";
    $result = $wpdb->get_results($sql, ARRAY_A);
//var_dump($sql);
    // массив названий статусов со сброшнными индексами, без повторений
    $statuses = array_values(array_unique(array_column($result, 'status_name')));
    // вставляеться в начало массива
    array_unshift($statuses, '');
    $arr[0] = $statuses;
//var_dump($result);
    $i = 1;
    foreach ($result as $item) {
        $key_status = array_search($item['status_name'], $statuses);
        $key_property = array_search($item['property_name'], array_column($arr, 0));
        if ($key_property === false) {  // новое свойство по имени
            $arr[$i][0] = $item['property_name'];
            $key_property = $i;
            $arr[$key_property]['property_id'] = $item['property_id'];
            $arr[$key_property]['property_type'] = $item['property_type'];
            $i++;
        } else { // свойство с таким именем уже есть
            $id = array_search($item['property_id'], array_column($arr, 'property_id'));
            $key_property = $id + 1;
            if ($id === false) { // новое свойство по id
                $arr[$i][0] = $item['property_name'];
                $key_property = $i;
                $arr[$key_property]['property_id'] = $item['property_id'];
                $arr[$key_property]['property_type'] = $item['property_type'];
                $i++;
            }
        }

        $arr[$key_property][$key_status] = $item['property_value'];
        $arr[$key_property]['property_published'] = $item['property_published'];

        if ($options) {
            $arr[$key_property]['property_display'] = $item['property_display'];
            $arr[$key_property]['option_value'] = $item['option_value'];
        }
    }

    $id_statuses = array_values(array_unique(array_column($result, 'status_id')));
    array_unshift($id_statuses, 'id');
    $arr[] = $id_statuses;
    return $arr;
}

// сборка заказа                    !!!!!!!!!!!!!!!!!!!!!!!!!!
function сheckout_order()
{
    $res = $_POST;
    if (!empty($res)) {


        $where_and = "AND s.status_position<>1 AND p.property_published=1";
        $result = table_properties($res['role'], $where_and, 1);
        $res['statuses'] = $result;

//        global $wpdb;
//        $table_status = $wpdb->get_blog_prefix() . 'pro_status';
//        $res['status_name'] = $wpdb->get_var("SELECT status_name FROM {$table_status} WHERE id='{$res['status']}'");
        return $res;
    } else {
        wp_redirect(home_url('/404/'));
        exit();
    }
}

//функция включить про
/*function add_status()
{
    global $wpdb;
    $res = $_POST;
    if ($res) {
        $user_ID = $res["userID"];
        $status = $res['status'];
        $time_id = $res['time'];
        $price = $res['price'];

        $table_options = $wpdb->get_blog_prefix() . 'pro_options';
        $time = $wpdb->get_var("SELECT option_value FROM $table_options WHERE property_id = $time_id");
        $data = getdate();
        $data_d = $data['mday'];
        $data_m = $data['mon'];
        $data_y = $data['year'];
        $data_active = [
            'day' => $data_d,
            'mon' => $data_m,
            'year' => $data_y
        ];

        $data_m_new = $data_m + $time;
        if ($data_m_new > 12) {
            $data_deactivate = [
                'day' => $data_d,
                'mon' => $data_m_new - 12,
                'year' => $data_y + 1
            ];
        } else {
            $data_deactivate = [
                'day' => $data_d,
                'mon' => $data_m_new,
                'year' => $data_y
            ];
        }

        $profile_id = get_user_meta($user_ID, 'user_profile_id', true);
        if ($profile_id) {
            update_post_meta($profile_id, 'pro_status', $status);
            update_post_meta($profile_id, 'pro_status_data_active', $data_active);
            update_post_meta($profile_id, 'pro_status_data_deactivate', $data_deactivate);

            // email
            $user_data = $wpdb->get_results("SELECT id, user_email, display_name FROM wp_users WHERE id = $user_ID", ARRAY_A);
            $table_status = $wpdb->get_blog_prefix() . 'pro_status';
            $status_list['name'] = $wpdb->get_var("SELECT status_name FROM $table_status WHERE id = $status");
//            $status_list['data_active'] = implode('-', $data_active);
            $status_list['data_deactivate'] = implode('-', $data_deactivate);
            send_email_pro($user_data[0], 'activate', $status_list);
//            header("location:" . $_SERVER['PHP_SELF']);
//            include 'profile.php';
//            header("location:" . home_url('/profile/'));
//            exit();

        }
    }
}*/

function send_email_pro($user_data, $type, $status)
{
    $profile_id=get_user_meta($user_data['id'], 'user_profile_id', true);
    $pro_status=get_post_meta($profile_id, 'pro_status', true);
    $register_status=get_user_meta($user_data['id'], 'register_status', true);

    if(empty($pro_status) || empty($register_status)){
//        $mes='No email';
        return;
    }
//    elseif ($register_status!='confirm'){
//        $mes='No verify email';
//        return $mes;
//    }

//    if( !defined( 'ET_DOMAIN' ) ) {
//        define( 'ET_DOMAIN', 'enginetheme' );
//    }
    require_once ABSPATH . WPINC . '/pluggable.php';

    $email_text = __('Hi ###USERNAME###,
    On ###SITENAME### your profile has been changed to "###NAMEPRO###".
    Profile active to ###DATA###.
    Regards, ###SITENAME###
    ###SITEURL###', 'enginetheme');

    $content = str_replace('###USERNAME###', $user_data['display_name'], $email_text);
    $content = str_replace('###SITENAME###', get_site_option('blogname'), $content);
    $content = str_replace('###NAMEPRO###', $status['name'], $content);
    $content = str_replace('###DATA###', $status['data_deactivate'], $content);
    $content = str_replace('###SITEURL###', network_home_url(), $content);

    wp_mail($user_data['user_email'],
        sprintf(__('[%s]Profile status Change', 'enginetheme'), wp_specialchars_decode(get_option('blogname'))),
        $content,'','');
}

//функция выключить про
function off_status($profile_id)
{
    $pro_status = get_post_meta($profile_id, 'pro_status', true);

    if ($profile_id && $pro_status) {
        update_post_meta($profile_id, 'pro_status', 1);
        delete_post_meta($profile_id, 'pro_status_data_active');
        delete_post_meta($profile_id, 'pro_status_data_deactivate');
        return 'ok';
    }
    return 'error';
}

$action = !empty($_POST['action']) ? $_POST['action'] : '';
//var_dump($_POST);
//echo'post '.$action;
switch ($action) {
//    case "order":
//        echo 'order ';
//        сheckout_order();
//        break;
//    case "buy":
//        add_status();
//        break;
    case "deactivate":
        off_status();
        break;
    case "create_property":
        create_property();
        break;
    case "get_property":
        print_r(json_encode(get_property()));
        exit;
        break;
    case "edit_property":
        edit_property();
        break;
    case "delete_property":
        delete_property_with_value();
        exit;
        break;
    case "create_additional":
        create_additional();
        break;
    case "get_additional":
        print_r(json_encode(get_additional()));
        exit;
        break;
    case "edit_additional":
        edit_additional();
        break;
    case "delete_additional":
        delete_additional();
        exit;
        break;
    case "create_status":
        echo create_status_with_prop();
        break;
    case "get_status":
        print_r(json_encode(get_status()));
        exit;
        break;
    case "edit_status":
        edit_status();
        break;
    case "delete_status":
        delete_status_with_value();
        exit;
        break;
    case "edit_options":
        edit_options();
        break;
}

/** Админка **/
function create_property()
{
    global $wpdb;
    $res = $_POST;
//    var_dump($res);
    if ($res && $res['position'] !== 'no') {
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

        $prop = array(
            'property_name' => $res['name'],
            'property_position' => $res['position'] == 0 ? 1 : $res['position'] + 1,
            'property_display' => (string)$res['display'],
            'property_published' => (string)$res['published'],
            'property_type' => (string)$res['type'],
            'user_role' => $res['type_user']
        );
        $where = $res['position'] != 0 ? "AND property_position>{$res['position']}" : '';
        $wpdb->query("UPDATE $table_properties SET property_position = property_position+1 WHERE user_role='{$res['type_user']}' $where");
        $wpdb->insert($table_properties, $prop, array('%s', '%d', '%d', '%d', '%d', '%s'));
        $id_new_property = $wpdb->insert_id;

//        $id_new_property = $wpdb->get_var("SELECT id FROM {$table_properties} WHERE property_name='{$res['name']}'");

        if ($id_new_property) {
            $table_status = $wpdb->get_blog_prefix() . 'pro_status';
            $arr_status_id = $wpdb->get_col("SELECT id FROM {$table_status} WHERE user_role='{$res['type_user']}'");

            $table_values = $wpdb->get_blog_prefix() . 'pro_values';
            foreach ($arr_status_id as $item) {
//                $wpdb->insert($table_values, array('status_id' => $item, 'property_id' => $id_new_property, 'property_value' => 0), array('%d', '%d', '%d'));
                $wpdb->query("INSERT INTO {$table_values} (`status_id`, `property_id`, `property_value`) VALUES ($item, $id_new_property, 0)");
            }
        }

        if (!empty($res['time']) && $res['type'] == 2) {
            $table_options = $wpdb->get_blog_prefix() . 'pro_options';
            $wpdb->insert($table_options, array('option_key' => 'time', 'option_value' => $res['time'], 'property_id' => $id_new_property), array('%s', '%s', '%d'));
        }
    }
}

function get_property()
{
    global $wpdb;
    $res = $_POST;
    if ($res && $res['property_id']) {
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
        $property = $wpdb->get_results("
SELECT p.id, p.property_name, p.property_position, p.property_display, p.property_type, o.option_value 
FROM $table_properties p
left join wp_pro_options o ON o.property_id={$res['property_id']} && o.option_key='time'
WHERE p.id={$res['property_id']}", ARRAY_A); //, p.property_published
    } else $property = '';
    return $property;
}

function edit_property()
{
    global $wpdb;
    $res = $_POST;
    if ($res) {
        $table_property = $wpdb->get_blog_prefix() . 'pro_properties';
        if ($res['position'] != $res['position_old_property']) {
            if ($res['position_old_property'] > $res['position']) {
                $wpdb->query("UPDATE $table_property SET property_position = property_position+1 WHERE property_position<{$res['position_old_property']} AND property_position>{$res['position']} AND user_role='{$res['type_user']}'");
                if ($res['position'] == 0)
                    $wpdb->query("UPDATE $table_property SET property_position = 1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'");
                else
                    $wpdb->query("UPDATE $table_property SET property_position = {$res['position']}+1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'");
            } else {
                $wpdb->query("UPDATE $table_property SET property_position = property_position-1 WHERE property_position>{$res['position_old_property']} AND property_position<={$res['position']} AND user_role='{$res['type_user']}'");
                $wpdb->query("UPDATE $table_property SET property_position = {$res['position']} WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'");
            }
        }

        $prop = array(
            'property_name' => $res['name'],
            'property_display' => (string)$res['display'],
            'property_published' => (string)$res['published'],
            'property_type' => (string)$res['type']
        );
        $wpdb->update($table_property, $prop, array('id' => $res['property_id']));

        if (!empty($res['time']) && $res['type'] == 2) {
            $table_options = $wpdb->get_blog_prefix() . 'pro_options';
            $time_var = $wpdb->get_var("SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'");
            if (!empty($time_var))
                $wpdb->update($table_options, array('option_value' => $res['time']), array('property_id' => $res['property_id']));
            else
                $wpdb->insert($table_options, array('option_key' => 'time', 'option_value' => $res['time'], 'property_id' => $res['property_id']), array('%s', '%d', '%d'));
        } else {
            $table_options = $wpdb->get_blog_prefix() . 'pro_options';
            $time_var = $wpdb->get_var("SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'");
            if (!empty($time_var)) {
                $wpdb->delete($table_options, array('option_key' => 'time', 'property_id' => $res['property_id']), array('%d'));
            }
        }
    }
}

function delete_property_with_value()
{
    global $wpdb;
    echo time() . '-' . $_SERVER['REQUEST_TIME'] . '-' . (time() - $_SERVER['REQUEST_TIME']);
    $res = $_POST;
    if ($res && $res['property_id']) {
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
//        $wpdb->delete($table_properties, array('id' => $res['property_id'], 'user_role'=>$res['type_user']), array('%d','%s'));
//        $sql="DELETE FROM $table_properties WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'";
//        var_dump($wpdb->get_results($sql,ARRAY_A));
        $wpdb->query("DELETE FROM $table_properties WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'");
        $wpdb->query("UPDATE $table_properties SET property_position = property_position-1 WHERE property_position>{$res['position']} AND user_role='{$res['type_user']}'");

        $table_values = $wpdb->get_blog_prefix() . 'pro_values';
//        $wpdb->delete($table_values, array('property_id' => $res['property_id']), array('%d'));
        $wpdb->query("DELETE FROM $table_values WHERE property_id={$res['property_id']}");


        $table_options = $wpdb->get_blog_prefix() . 'pro_options';
//        $wpdb->delete($table_options, array('property_id' => $res['property_id']), array('%d'));
        $wpdb->query("DELETE FROM $table_options WHERE property_id={$res['property_id']}");
    }
}

function create_additional()
{
    global $wpdb;
    $res = $_POST;
    var_dump($res);
    if ($res && $res['position'] !== 'no') {
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

        $prop = array(
            'property_name' => $res['name'],
            'property_position' => $res['position'] == 0 ? 1 : $res['position'] + 1,
            'property_display' => (string)$res['display'],
            'property_published' => (string)$res['published'],
            'property_type' => (string)$res['type'],
            'user_role' => $res['type_user']
        );
        $where = $res['position'] != 0 ? "AND property_position>{$res['position']}" : '';
        $wpdb->query("UPDATE $table_properties SET property_position = property_position+1 WHERE user_role='{$res['type_user']}' AND property_type=3 $where");
        $wpdb->insert($table_properties, $prop, array('%s', '%d', '%d', '%d', '%d', '%s'));
        $id_new_property = $wpdb->insert_id;

//        $id_new_property = $wpdb->get_var("SELECT id FROM {$table_properties} WHERE property_name='{$res['name']}'");

        if ($id_new_property) {
            $table_status = $wpdb->get_blog_prefix() . 'pro_status';
            $arr_status_id = $wpdb->get_col("SELECT id FROM {$table_status} WHERE user_role='{$res['type_user']}'");

            $table_values = $wpdb->get_blog_prefix() . 'pro_values';
            foreach ($arr_status_id as $item) {
                $wpdb->insert($table_values, array('status_id' => $item, 'property_id' => $id_new_property, 'property_value' => 0), array('%d', '%d', '%d'));
            }
        }
    }
}

function get_additional()
{
    global $wpdb;
    $res = $_POST;
    if ($res && $res['additional_id']) {
        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
        $additional = $wpdb->get_results("
SELECT id, property_name, property_position, property_display, property_type
FROM $table_properties 
WHERE id={$res['additional_id']}", ARRAY_A); //, p.property_published
    } else $additional = '';
    return $additional;
}

function edit_additional()
{
    global $wpdb;
    $res = $_POST;
    if ($res) {
//        var_dump($res);
        $table_property = $wpdb->get_blog_prefix() . 'pro_properties';
        if ($res['position'] != $res['position_old_additional']) {
            if ($res['position_old_additional'] > $res['position']) {
                $wpdb->query("UPDATE $table_property SET property_position = property_position+1 WHERE property_position<{$res['position_old_additional']} AND property_position>{$res['position']} AND user_role='{$res['type_user']}' AND property_type=3");
                if ($res['position'] == 0)
                    $wpdb->query("UPDATE $table_property SET property_position = 1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3");
                else
                    $wpdb->query("UPDATE $table_property SET property_position = {$res['position']}+1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3");
            } else {
                $wpdb->query("UPDATE $table_property SET property_position = property_position-1 WHERE property_position>{$res['position_old_additional']} AND property_position<={$res['position']} AND user_role='{$res['type_user']}' AND property_type=3");
                $wpdb->query("UPDATE $table_property SET property_position = {$res['position']} WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3");
            }
        }

        $prop = array(
            'property_name' => $res['name'],
            'property_display' => (string)$res['display'],
            'property_published' => (string)$res['published'],
            'property_type' => (string)$res['type']
        );
        $wpdb->update($table_property, $prop, array('id' => $res['property_id']));

        if (!empty($res['time']) && $res['type'] == 2) {
            $table_options = $wpdb->get_blog_prefix() . 'pro_options';
            $time_var = $wpdb->get_var("SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'");
            if (!empty($time_var))
                $wpdb->update($table_options, array('option_value' => $res['time']), array('property_id' => $res['property_id']));
            else
                $wpdb->insert($table_options, array('option_key' => 'time', 'option_value' => $res['time'], 'property_id' => $res['property_id']), array('%s', '%d', '%d'));
        } else {
            $table_options = $wpdb->get_blog_prefix() . 'pro_options';
            $time_var = $wpdb->get_var("SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'");
            if (!empty($time_var)) {
                $wpdb->delete($table_options, array('option_key' => 'time', 'property_id' => $res['property_id']), array('%d'));
            }
        }
    }
}

function create_status_with_prop()
{
    global $wpdb;
    $res = $_POST;
    if ($res && $res['position'] !== 'no') {
        $table_status = $wpdb->get_blog_prefix() . 'pro_status';
        if ($res['position'] == 0) {
            $wpdb->query("UPDATE $table_status SET status_position = status_position+1 WHERE user_role='{$res['type_user']}'");
            $wpdb->insert($table_status, array('status_name' => $res['name'], 'status_position' => 1, 'user_role' => $res['type_user']), array('%s', '%d', '%s'));
        } else {
            $wpdb->query("UPDATE $table_status SET status_position = status_position+1 WHERE status_position>{$res['position']} AND user_role='{$res['type_user']}'");
            $wpdb->insert($table_status, array('status_name' => $res['name'], 'status_position' => $res['position'] + 1, 'user_role' => $res['type_user']), array('%s', '%d', '%s'));
        }
        $id_new_status = $wpdb->insert_id;
//        $id_new_status = $wpdb->get_var("SELECT id FROM {$table_status} WHERE status_name='{$res['name']}'");

        if ($id_new_status) {
            $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
            $properties = $wpdb->get_col("SELECT id FROM $table_properties WHERE user_role='{$res['type_user']}'");
            $table_values = $wpdb->get_blog_prefix() . 'pro_values';
            foreach ($properties as $key) {
                if (empty($res[$key]))
                    $value = '0';
                else {
                    if ($res[$key] == 'on') $value = '1';
                    else $value = $res[$key];
                }
                $wpdb->insert($table_values, array('status_id' => $id_new_status, 'property_id' => $key, 'property_value' => $value));
            }
        }
    }
}

function get_status()
{
    $res = $_POST;
    if ($res && $res['status_id']) {
        $type = $res['type_user'];
        $where_and = "AND s.id={$res['status_id']} AND p.property_published=1";
        $property_for_status = table_properties($type, $where_and, 1);
    } else $property_for_status = '';
    return $property_for_status;
}

function edit_status()
{
    global $wpdb;
    $res = $_POST;
    if ($res) {
        $table_status = $wpdb->get_blog_prefix() . 'pro_status';
        if ($res['position'] != $res['position_old_status']) {
            if ($res['position_old_status'] > $res['position']) {
                $wpdb->query("UPDATE $table_status SET status_position = status_position+1 WHERE status_position<{$res['position_old_status']} AND status_position>{$res['position']}");
                if ($res['position'] == 0)
                    $wpdb->query("UPDATE $table_status SET status_position = 1 WHERE id={$res['status_id']}");
                else
                    $wpdb->query("UPDATE $table_status SET status_position = {$res['position']}+1 WHERE id={$res['status_id']}");
            } else {
                $wpdb->query("UPDATE $table_status SET status_position = status_position-1 WHERE status_position>{$res['position_old_status']} AND status_position<={$res['position']}");
                $wpdb->query("UPDATE $table_status SET status_position = {$res['position']} WHERE id={$res['status_id']}");
            }
        }

        $wpdb->query("UPDATE $table_status SET status_name = '{$res['name']}' WHERE id={$res['status_id']}");

        $table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
        $properties = $wpdb->get_col("SELECT id FROM $table_properties");

        $table_values = $wpdb->get_blog_prefix() . 'pro_values';
        foreach ($properties as $key) {
            if (empty($res[$key]))
                $value = '0';
            else {
                if ($res[$key] == 'on') $value = '1';
                else $value = $res[$key];
            }
            $wpdb->update($table_values, array('property_value' => $value), array('status_id' => $res['status_id'], 'property_id' => $key));
        }
    }
}

function delete_status_with_value()
{
    global $wpdb;
    $res = $_POST;
    if ($res && $res['status_id']) {
        $table_status = $wpdb->get_blog_prefix() . 'pro_status';
        $wpdb->delete($table_status, array('id' => $res['status_id'], 'user_role' => $res['type_user']), array('%d', '%s'));
        $wpdb->query("UPDATE $table_status SET status_position = status_position-1 WHERE status_position>{$res['position']} AND user_role='{$res['type_user']}'");

        $table_values = $wpdb->get_blog_prefix() . 'pro_values';
        $wpdb->delete($table_values, array('status_id' => $res['status_id']), array('%d'));
    }
}

function edit_options()
{
    global $wpdb;
    $res = $_POST;
    if ($res) {
        $table_options = $wpdb->get_blog_prefix() . 'pro_options';
        $wpdb->update($table_options, array('option_value' => $res['currency']), array('option_key' => 'currency'));
    }
}

function get_pro_option($name)
{
    global $wpdb;
    if ($name) {
        $table_options = $wpdb->get_blog_prefix() . 'pro_options';
        return $wpdb->get_var("SELECT option_value FROM {$table_options} WHERE option_key = '{$name}'");
    }
    return null;
}