<?php
/**
 * Admin Manage listings
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage listings in WP post screen
 */
/**
 * Remove post row action to prevent admin edit listing
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_post_row_actions($actions, $post)
{
    if ($post && 'listing' == $post->post_type) {
        return array();
    }
    return $actions;
}
add_filter('post_row_actions', 'marketengine_post_row_actions', 10, 2);

/**
 * Hook to change listing list table primary column
 * 
 * @param string $column The primary column
 * @param string $screen_id Current screen
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_change_listing_primary_column($column, $screen_id) {
    if($screen_id == 'edit-listing') {
        return 'post_title';
    }
    return $column;
}
add_filter('list_table_primary_column', 'marketengine_change_listing_primary_column',10, 2);

/**
 * Add and modify listing post column
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_listing_columns($existing_columns)
{
    if (empty($existing_columns) && !is_array($existing_columns)) {
        $existing_columns = array();
    }

    unset($existing_columns['comments'], $existing_columns['date'], $existing_columns['title']);

    $columns = array();

    $columns['post_title']       = 'Title';
    $columns['type']             = 'Type';
    $columns['listing_category'] = 'Categories';
    $columns['price']            = 'Price';
    $columns['author']           = 'Author';
    $columns['date']             = 'Posted';

    return array_merge($existing_columns, $columns);
}
add_filter('manage_listing_posts_columns', 'marketengine_listing_columns');

/**
 * Render listing column value
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_render_listing_columns($column)
{
    global $post, $wpdb;

    switch ($column) {
        case 'post_title':
            echo '<a href="' . get_permalink($post->ID) . '" target="_blank" >' . esc_html(get_the_title($post->ID)) . '</a>';
            break;

        case 'author_profile':
            printf(
                '<a target="_blank" href="%1$s" title="%2$s" rel="author">%3$s</a>',
                esc_url(get_author_posts_url($post->post_author, get_the_author_meta( 'display_name', '$post->post_author' ))),
                esc_attr(sprintf(__('Posts by %s'), get_the_author())),
                get_the_author()
            );
            break;

        case 'type':
            $listing_type = get_post_meta($post->ID, '_me_listing_type', true);
            if (!empty($listing_type)) {
                echo esc_html(marketengine_get_listing_type_label($listing_type));
            }
            break;

        case 'listing_category':
            $categoryarray = array();
            $categories    = get_the_terms($post->ID, 'listing_category');
            if ($categories) {
                foreach ($categories as $category) {
                    $categoryarray[] = edit_term_link($category->name, '', '', $category, false);
                }
                echo implode(', ', $categoryarray);
            } else {
                echo '&ndash;';
            }
            break;

        case 'price':
            $price = get_post_meta($post->ID, 'listing_price', true);
            if ($price) {
                echo marketengine_price_html($price);
            }
            break;
    }
}
add_action('manage_listing_posts_custom_column', 'marketengine_render_listing_columns', 2);

/**
 * Add listing metabox, remove authordiv
 * @package Admin/Manage
 * @category Hook Function
 * @since 1.0
 */
function marketengine_listing_meta_box()
{
    remove_meta_box('authordiv', 'listing', 'normal');
}
add_action('add_meta_boxes', 'marketengine_listing_meta_box');


/**
 * Hook to remove filter mine in listing list
 * 
 * @package Admin/Manage
 * @category Hook Function
 * 
 * @since 1.0
 */
function marketengine_remove_filter_listing_mine($views) {
    unset($views['mine']);
    return $views;
}
add_filter( 'views_edit-listing', 'marketengine_remove_filter_listing_mine' );