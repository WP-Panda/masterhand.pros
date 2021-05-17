<?php
/**
 * Admin Manage orders
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage orders in WP post screen
 */

/**
 * Remove edit post row action
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_order_row_actions($actions, $post)
{
    if ($post && 'me_order' == $post->post_type) {
        return array();
    }
    return $actions;
}
add_filter('post_row_actions', 'marketengine_order_row_actions', 10, 2);

/**
 * Hook to change order list table primary column
 * 
 * @param string $column The primary column
 * @param string $screen_id Current screen
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_change_order_primary_column($column, $screen_id) {
    if($screen_id == 'edit-me_order') {
        return 'listing';
    }
    return $column;
}
add_filter('list_table_primary_column', 'marketengine_change_order_primary_column',10, 2);

/**
 * Hook to action manage_me_order_posts_columns
 * Add order columns: ID, Listing Items, Tota, Commission, Date
 *
 * @package Admin/Manage
 * @category Hook Function
 *
 * @param array $existing_columns WP default post column
 * @since 1.0
 */
function marketengine_me_order_columns($existing_columns)
{
    if (empty($existing_columns) && !is_array($existing_columns)) {
        $existing_columns = array();
    }

    unset($existing_columns['comments'], $existing_columns['title'], $existing_columns['date'], $existing_columns['author']);

    $columns = array();

    $columns['status']   = 'Status';
    $columns['order_id']   = 'ID';
    $columns['listing']    = 'Listing';
    $columns['total']      = 'Total';
    $columns['commission'] = 'Commission';
    $columns['date']       = 'Date';

    return array_merge($existing_columns, $columns);
}
add_filter('manage_me_order_posts_columns', 'marketengine_me_order_columns');

/**
 * Hook to manage_me_order_posts_custom_column render order column data
 *
 * @package Admin/Manage
 * @category Hook Function
 *
 * @param string $column
 * @since 1.0
 */
function marketengine_render_me_order_columns($column)
{
    global $post, $wpdb;
    $order = marketengine_get_order($post);

    switch ($column) {
        case 'status':
            $status = get_post_status_object($post->post_status);
            echo $status->label;
            break;

        case 'order_id':
            $edit_post_link = edit_post_link("#" . $post->ID);
            $edit_user_link = '<a href="' . get_edit_user_link($post->post_author) . '">' . get_the_author_meta('display_name', $post->post_author) . '</a>';

            printf(__("%s by %s", "enginethemes"), $edit_post_link, $edit_user_link);
            break;
        case 'listing':
            $listing_items = $order->get_listing_items();
            foreach ($listing_items as $key => $listing) {
                $listing = get_post($listing['ID']);
                if ($listing) {
                    echo '<a href="' . get_permalink($listing->ID) . '" target="_blank" >' . esc_html(get_the_title($listing->ID)) . '</a>';
                } else {
                    echo $listing['title'];
                }
            }
            break;
        case 'commission':
            $currency         = get_post_meta($post->ID, '_order_currency', true);
            $commission_items = marketengine_get_order_items($post->ID, 'commission_item');
            if (!empty($commission_items)) {
                $item_id = $commission_items[0]->order_item_id;
                echo marketengine_price_html(marketengine_get_order_item_meta($item_id, '_amount', true), $currency);
            }else{
                echo '0';
            }
            break;

        case 'total':
            $currency = get_post_meta($post->ID, '_order_currency', true);
            echo marketengine_price_html(get_post_meta($post->ID, '_order_total', true), $currency);
            break;
    }
}
add_action('manage_me_order_posts_custom_column', 'marketengine_render_me_order_columns', 2);

/**
 * Load metabox details
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_order_payment_details() {
    marketengine_get_template('admin/order-metabox');
}

/**
 * Add order metabox, remove submitdiv
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_order_meta_box()
{
    add_meta_box('order_meta', __('Order Payment Info'), 'marketengine_order_payment_details', 'me_order', 'normal', 'high');
    remove_meta_box('submitdiv', 'me_order', 'side');
    remove_meta_box('postcustom', 'me_order', 'normal');
}
add_action('add_meta_boxes', 'marketengine_order_meta_box');


/**
 * Hook to remove filter mine in order list
 * 
 * @package Admin/Manage
 * @category Hook Function
 * 
 * @since 1.0
 */
function marketengine_remove_filter_order_mine($views) {
    unset($views['mine']);
    return $views;
}
add_filter( 'views_edit-me_order', 'marketengine_remove_filter_order_mine' );
