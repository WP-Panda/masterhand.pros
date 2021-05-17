<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME Template Loader
 *
 * @version     1.0
 * @package     MarketEngine/Includes
 * @author      Dakachi
 * @category    Class
 */
class ME_Template_Loader
{

    public static function init_hooks()
    {
        add_filter('template_include', array(__CLASS__, 'template_include'));
    }

    public static function template_include($template)
    {
        $find = array();
        $file = '';
        if (is_embed()) {
            return $template;
        }

        if (is_author()) {

            $file   = 'seller-profile/seller-profile.php';
            $find[] = $file;
            $find[] = ME()->template_path() . $file;
        } elseif (is_single() && get_post_type() == 'listing') {
            $file   = 'single-listing.php';
            $find[] = $file;
            $find[] = ME()->template_path() . $file;

        } elseif (is_single() && get_post_type() == 'me_order') {

            global $current_user;
            $order_id = get_the_ID();
            $order    = marketengine_get_order($order_id);

            $is_buyer    = ($order->post_author == $current_user->ID);
            $seller_name = marketengine_get_order_items($order_id, 'receiver_item')[0]->order_item_name;

            if (!$is_buyer && $seller_name != $current_user->user_login && !current_user_can('manage_options')) {
                $file   = '404.php';
                $find[] = $file;
                $find[] = get_404_template();
            } else {
                $file   = 'order-detail.php';
                $find[] = $file;
                $find[] = ME()->template_path() . $file;
            }

        } elseif (is_tax(get_object_taxonomies('listing_category'))) {

            $term = get_queried_object();

            if (is_tax('listing_cat') || is_tax('listing_tag')) {
                $file = 'taxonomy-' . $term->taxonomy . '.php';
            } else {
                $file = 'archive-listing.php';
            }

            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = ME()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = ME()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = $file;
            $find[] = ME()->template_path() . $file;

        } elseif (is_post_type_archive('listing')) {

            $file   = 'archive-listing.php';
            $find[] = $file;
            $find[] = ME()->template_path() . $file;

        }

        if ($file) {
            $template = locate_template(array_unique($find));
            if (!$template) {
                $template = ME()->plugin_path() . '/templates/' . $file;
            }
        }

        return $template;
    }

}
ME_Template_Loader::init_hooks();
