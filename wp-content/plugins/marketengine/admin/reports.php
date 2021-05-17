<?php
/**
 * Backend Report functions
 *
 * Functions for rendering backend report.
 *
 * @author EngineThemes
 * @package Admin/Reports
 * @category Function
 *
 * @since 1.0.0
 *
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prints report heading html.
 *
 * @param string $name
 * @param string $label
 *
 * @since 1.0.0
 */
function marketengine_report_heading($name, $label)
{
    $class = '';
    $link  = add_query_arg('orderby', $name);
    if ($name == 'quant' && empty($_REQUEST['orderby'])) {
        $class = 'me-sort-asc';
        $link  = add_query_arg('order', 'desc', $link);
    }
    if (!empty($_REQUEST['orderby']) && $_REQUEST['orderby'] == $name) {
        if (!empty($_REQUEST['order']) && $_REQUEST['order'] == 'desc') {
            $class = 'me-sort-desc';
            $link  = add_query_arg('order', 'asc', $link);
        } else {
            $class = 'me-sort-asc';
            $link  = add_query_arg('order', 'desc', $link);
        }
    }
    ?>
    <div class="me-table-col">
        <a href="<?php echo $link; ?>" class="<?php echo $class; ?>"><?php echo $label; ?></a>
    </div>
<?php
}

/**
 * Gets quantity report
 *
 * @param string $col_name
 * @param string $quant
 * @param string $name
 *
 * @return string $time
 *
 * @since 1.0.0
 */
function marketengine_get_quantity_report($col_name, $quant, $name = 'quant')
{
    switch ($quant) {
        case 'week':
            $time = "WEEK({$col_name}) as `{$name}`, YEAR({$col_name}) as `year`";
            break;
        case 'quarter':
            $time = "QUARTER({$col_name}) as `{$name}` , YEAR({$col_name}) as `year`";
            break;
        case 'month':
            $time = "MONTH({$col_name}) as `{$name}` , YEAR({$col_name}) as `year`";
            break;
        case 'year':
            $time = "YEAR({$col_name}) as `{$name}` , YEAR({$col_name}) as `year`";
            break;
        default:
            $time = "date({$col_name}) as `{$name}` , YEAR({$col_name}) as `year`";
            break;
    }

    return $time;
}

/**
 * Gets date range
 *
 * @param string $quant
 * @param string $week
 * @param string $year
 * @param string $date_format
 *
 * @return string start date and end date
 *
 * @since 1.0.0
 */
function marketengine_get_start_and_end_date($quant, $week, $year, $date_format = '')
{
    if (!$date_format) {
        $date_format = get_option('date_format');
    }

    if ($quant == 'week') {
        $time = strtotime("1 January $year", time());
        $day  = date('w', $time);
        $time += ((7 * $week) + 1 - $day) * 24 * 3600;
        $return[0] = date_i18n($date_format, $time);
        $time += 6 * 24 * 3600;
        $return[1] = date_i18n($date_format, $time);
        return $return[0] . ' - ' . $return[1];
    }

    if ($quant == 'day') {
        return date_i18n($date_format, strtotime($week));
    }

    if ($quant == 'year') {
        return $week;
    }

    if ($quant == 'month') {
        $start_date = date_i18n($date_format, strtotime("01-{$week}-{$year}"));
        $ts         = strtotime("20-{$week}-{$year}");
        $ts         = date('t', $ts);
        $end_date   = date_i18n($date_format, strtotime("{$ts}-{$week}-{$year}"));
        return $start_date . ' - ' . $end_date;
    }

    if ($quant == 'quarter') {
        $week       = (($week - 1) * 3) + 1;
        $start_date = date_i18n($date_format, strtotime("01-{$week}-{$year}"));

        $week = $week + 2;

        $ts       = strtotime("20-{$week}-{$year}");
        $ts       = date('t', $ts);
        $end_date = date_i18n($date_format, strtotime("{$ts}-{$week}-{$year}"));
        return $start_date . ' - ' . $end_date;
    }
}

/**
 * Gets data of listing report
 *
 * @param string $args
 *
 * @return array data of listing report
 *
 * @since 1.0.0
 */
function marketengine_listing_report($args)
{
    global $wpdb;
    $defaults = array(
        'quant'     => 'day',
        'from_date' => '',
        'to_date'   => '',
        'orderby'   => 'quant',
        'order'     => 'ASC',
        'paged'     => 1,
        'showposts' => get_option('posts_per_page'),
    );
    $args = wp_parse_args($args, $defaults);
    extract($args);

    if (empty($from_date)) {
        $from_date = '1970-1-1';
    } else {
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    }

    if (empty($to_date)) {
        $to_date = date('Y-m-d  H:i:s', time());
    } else {
        $to_date = date('Y-m-d 12:00:00 PM', strtotime($to_date));
    }

    $pgstrt = absint(($paged - 1) * $showposts) . ', ';

    $field = $wpdb->posts . '.post_date';
    $time  = marketengine_get_quantity_report($field, $quant);

    $select          = "SELECT SQL_CALC_FOUND_ROWS {$time} ,count( {$wpdb->posts}.ID) as count";
    $select_contact  = ", count(A.meta_value) as contact_type ";
    $select_purchase = ", count(B.meta_value) as purchase_type ";

    $from = " FROM {$wpdb->posts}";

    $join          = '';
    $join_contact  = " LEFT JOIN  $wpdb->postmeta as A ON  A .post_id = {$wpdb->posts}.ID AND A.meta_key = '_me_listing_type' AND A.meta_value = 'contact' ";
    $join_purchase = " LEFT JOIN  $wpdb->postmeta as B ON  B.post_id = {$wpdb->posts}.ID AND B.meta_key = '_me_listing_type' AND B.meta_value = 'purchasion' ";

    $where   = " WHERE post_type = 'listing' AND post_date BETWEEN '{$from_date}' AND '{$to_date}'";
    $groupby = " GROUP BY `quant` ,`year`";
    $orderby = " ORDER BY {$orderby} {$order}";

    $limits = ' LIMIT ' . $pgstrt . $showposts;

    if (!isset($section) || empty($section)) {
        $select = $select . $select_contact . $select_purchase;
        $join   = $join . $join_contact . $join_purchase;
    } else {
        if ($section == 'contact') {
            $select = $select . $select_contact;
            $join   = $join . $join_contact;
            $where .= "AND A.meta_key = '_me_listing_type'";
        } else {
            $select = $select . $select_purchase;
            $join   = $join . $join_purchase;
            $where .= "AND B.meta_key = '_me_listing_type'";
        }
    }

    $sql = $select . $from . $join . $where . $groupby . $orderby . $limits;

    $result = $wpdb->get_results($sql);

    $found_rows     = $wpdb->get_var('SELECT FOUND_ROWS() as row');
    $max_numb_pages = ceil($found_rows / $showposts);
    return array(
        'found_posts'    => $found_rows,
        'max_numb_pages' => $max_numb_pages,
        'posts'          => $result,
    );
}

/**
 * Gets data of members report
 *
 * @param string $args
 *
 * @return array data of members report
 *
 * @since 1.0.0
 */
function marketengine_members_report($args)
{
    global $wpdb;
    $defaults = array(
        'quant'     => 'day',
        'from_date' => '',
        'to_date'   => '',
        'orderby'   => 'quant',
        'order'     => 'ASC',
        'paged'     => 1,
        'showposts' => get_option('posts_per_page'),
    );
    $args = wp_parse_args($args, $defaults);

    extract($args);

    if (empty($from_date)) {
        $from_date = '1970-1-1';
    } else {
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    }

    if (empty($to_date)) {
        $to_date = date('Y-m-d  H:i:s', time());
    } else {
        $to_date = date('Y-m-d 12:00:00 PM', strtotime($to_date));
    }

    $pgstrt = absint(($paged - 1) * $showposts) . ', ';

    $field = $wpdb->users . '.user_registered';
    $time  = marketengine_get_quantity_report($field, $quant);

    $select  = "SELECT SQL_CALC_FOUND_ROWS {$time} , count({$wpdb->users}.ID) as count FROM {$wpdb->users}";
    $where   = " WHERE user_registered BETWEEN '{$from_date}' AND '{$to_date}' ";
    $groupby = " GROUP BY `quant` ,`year` ";
    $orderby = " ORDER BY {$orderby} {$order}";
    $limits  = ' LIMIT ' . $pgstrt . $showposts;

    $join = '';
    if(is_multisite()) {
        $blog_id = $GLOBALS['blog_id'];
        $key = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';
        $compare = 'EXISTS';
        $join = " LEFT JOIN {$wpdb->usermeta} as M ON M.user_id = ID AND meta_key = '{$key}' " ;
        $where .= " AND M.meta_value != '' ";
    }

    $sql = $select . $join .  $where . $groupby . $orderby . $limits;
    $result = $wpdb->get_results($sql);

    $found_rows     = $wpdb->get_var('SELECT FOUND_ROWS() as row');
    $max_numb_pages = ceil($found_rows / $showposts);
    return array(
        'found_posts'    => $found_rows,
        'max_numb_pages' => $max_numb_pages,
        'posts'          => $result,
    );
}

/**
 * Gets data of orders report
 *
 * @param string $args
 *
 * @return array data of orders report
 *
 * @since 1.0.0
 */
function marketengine_orders_report($args)
{
    global $wpdb;
    $defaults = array(
        'quant'     => 'day',
        'from_date' => '',
        'to_date'   => '',
        'orderby'   => 'quant',
        'order'     => 'ASC',
        'paged'     => 1,
        'showposts' => get_option('posts_per_page'),
    );
    $args = wp_parse_args($args, $defaults);

    extract($args);

    if (empty($from_date)) {
        $from_date = '1970-1-1';
    } else {
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    }

    if (empty($to_date)) {
        $to_date = date('Y-m-d  H:i:s', time());
    } else {
        $to_date = date('Y-m-d 12:00:00 PM', strtotime($to_date));
    }

    $pgstrt = absint(($paged - 1) * $showposts) . ', ';

    $field = $wpdb->posts . '.post_date';
    $time  = marketengine_get_quantity_report($field, $quant);

    $select = "SELECT SQL_CALC_FOUND_ROWS ({$wpdb->posts}.ID), {$time} ,
                count( DISTINCT  {$wpdb->posts}.ID) as count,
                SUM(  {$wpdb->postmeta}.meta_value) as total
            ";

    $from = " FROM {$wpdb->posts}";

    $join = " LEFT JOIN  $wpdb->postmeta ON  {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID AND {$wpdb->postmeta}.meta_key = '_order_subtotal' ";

    $where   = " WHERE post_type = 'me_order' AND post_status != 'me-pending' AND post_date BETWEEN '{$from_date}' AND '{$to_date}'";
    $groupby = " GROUP BY `quant` ,`year` ";
    $orderby = " ORDER BY {$orderby} {$order} ";
    $limits  = " LIMIT " . $pgstrt . $showposts;

    $sql = $select . $from . $join . $where . $groupby . $orderby . $limits;

    $result = $wpdb->get_results($sql);

    $found_rows     = $wpdb->get_var('SELECT FOUND_ROWS() as row');
    $max_numb_pages = ceil($found_rows / $showposts);
    return array(
        'found_posts'    => $found_rows,
        'max_numb_pages' => $max_numb_pages,
        'posts'          => $result,
    );
}

/**
 * Gets data of inquiries report
 *
 * @param string $args
 *
 * @return array data of inquiries report
 *
 * @since 1.0.0
 */
function marketengine_inquiries_report($args)
{
    global $wpdb;
    $defaults = array(
        'quant'     => 'day',
        'from_date' => '',
        'to_date'   => '',
        'orderby'   => 'quant',
        'order'     => 'ASC',
        'paged'     => 1,
        'showposts' => get_option('posts_per_page'),
    );
    $args = wp_parse_args($args, $defaults);
    extract($args);

    if (empty($from_date)) {
        $from_date = '1970-1-1';
    } else {
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    }

    if (empty($to_date)) {
        $to_date = date('Y-m-d  H:i:s', time());
    } else {
        $to_date = date('Y-m-d 12:00:00 PM', strtotime($to_date));
    }

    $pgstrt = absint(($paged - 1) * $showposts) . ', ';

    $field = $wpdb->marketengine_message_item . '.post_date';
    $time  = marketengine_get_quantity_report($field, $quant);

    $select  = "SELECT SQL_CALC_FOUND_ROWS {$time}, count({$wpdb->marketengine_message_item}.ID) as count FROM {$wpdb->marketengine_message_item}";
    $where   = " WHERE post_type = 'inquiry'  AND post_date BETWEEN '{$from_date}' AND '{$to_date}'";
    $groupby = " GROUP BY `quant` ,`year` ";
    $orderby = " ORDER BY {$orderby} {$order}";
    $limits  = ' LIMIT ' . $pgstrt . $showposts;

    $sql = $select . $where . $groupby . $orderby . $limits;

    $result = $wpdb->get_results($sql);

    $found_rows     = $wpdb->get_var('SELECT FOUND_ROWS() as row');
    $max_numb_pages = ceil($found_rows / $showposts);
    return array(
        'found_posts'    => $found_rows,
        'max_numb_pages' => $max_numb_pages,
        'posts'          => $result,
    );
}

// tinh toan start date va end date