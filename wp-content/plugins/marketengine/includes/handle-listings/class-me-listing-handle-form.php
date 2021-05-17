<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Listing Handle Form
 *
 * Class control listing data submit by user fromt post listing form
 *
 * @version     1.0
 * @package     Includes/Post-Listings
 * @author      Dakachi
 * @category    Class
 */
class ME_Listing_Handle_Form extends ME_Form
{
    public static function init_hook()
    {

        add_action('wp_loaded', array(__CLASS__, 'process_insert'));
        add_action('wp_loaded', array(__CLASS__, 'process_update'));

        add_action('wp_loaded', array(__CLASS__, 'process_review_listing'));
        add_action('transition_comment_status', array(__CLASS__, 'approve_review_callback'), 10, 3);
        add_action('marketengine_insert_review', array(__CLASS__, 'insert_review_callback'), 10, 2);

        // ajax action
        add_action('wp_ajax_me-load-sub-category', array(__CLASS__, 'load_sub_category'));
        add_action('wp_ajax_nopriv_me-load-sub-category', array(__CLASS__, 'load_sub_category'));

        add_action('wp_ajax_me_load_more_reviews', array(__CLASS__, 'load_more_review'));
        add_action('wp_ajax_nopriv_me_load_more_reviews', array(__CLASS__, 'load_more_review'));

    }
    
    /**
     * Handling listing data to create new listing
     * @since 1.0
     */
    public static function process_insert($data)
    {
        if (!empty($_POST['insert_lisiting']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-insert_listing')) {
            $new_listing = ME_Listing_Handle::insert($_POST);
            if (is_wp_error($new_listing)) {
                marketengine_wp_error_to_notices($new_listing);
            } else {
                // set the redirect link after submit new listing
                if (isset($_POST['redirect'])) {
                    $redirect = esc_url($_POST['redirect']);
                } else {
                    $redirect = get_permalink($new_listing);
                }
                /**
                 * action filter redirect link after user submit a new listing
                 * @param String $redirect
                 * @param Object $new_listing Listing object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_insert_listing_redirect', $redirect, $new_listing);
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }
    /**
     * Handling listing data to update
     * @since 1.0
     */
    public static function process_update($data)
    {
        if (!empty($_POST['update_lisiting']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-update_listing')) {
            $listing = ME_Listing_Handle::update($_POST);
            if (is_wp_error($listing)) {
                marketengine_wp_error_to_notices($listing);
            } else {
                // set the redirect link after update listing
                if (isset($_POST['redirect'])) {
                    $redirect = esc_url($_POST['redirect']);
                } else {
                    $redirect = get_permalink($listing);
                }
                /**
                 * action filter redirect link after user update listing
                 * @param String $redirect
                 * @param Object $listing Listing object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_update_listing_redirect', $redirect, $listing);
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }

    /**
     * Handle review listing
     * @since 1.0
     */
    public static function process_review_listing()
    {
        if (!empty($_POST['listing_id']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-review-listing')) {
            $review = ME_Listing_Handle::insert_review($_POST);
            if (is_wp_error($review)) {
                marketengine_wp_error_to_notices($review);
            } else {
                $redirect = get_permalink(absint($_POST['listing_id']));
                wp_safe_redirect($redirect);
                exit;
            }
        }
    }

    /**
     * @param $new_status
     * @param $old_status
     * @param $comment
     * @since 1.0
     */
    public static function approve_review_callback($new_status, $old_status, $comment)
    {
        if ($old_status != $new_status) {
            ME_Listing_Handle::update_post_rating($comment->comment_ID, $comment);
        }
    }

    /**
     * catch hook wp_insert_comment to update rating
     * @param int $comment_id
     * @param $comment
     * @since 1.0
     */
    public static function insert_review_callback($comment_id, $comment)
    {
        ME_Listing_Handle::update_post_rating($comment_id, $comment);
    }

    /**
     * Retrieve sub category select template
     * @since 1.0
     */
    public static function load_sub_category()
    {
        if (isset($_REQUEST['parent-cat'])) {
            $child_categories = get_terms(array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => absint($_REQUEST['parent-cat'])));
            ob_start();
            marketengine_get_template('post-listing/sub-cat', array('child_categories' => $child_categories));
            $content = ob_get_clean();

            $purchase_cats = marketengine_option('purchasion-available');
            $contact_cats  = marketengine_option('contact-available');

            wp_send_json_success(array(
                'content'          => $content,
                'has_child'        => !empty($child_categories),
                'support_purchase' => in_array($_REQUEST['parent-cat'], $purchase_cats),
                'support_contact'  => in_array($_REQUEST['parent-cat'], $contact_cats),
            ));
        }
    }

    public static function load_more_review()
    {
        if (!empty($_GET['post_id']) && !empty($_GET['page'])) {
            $offset = 2 * (absint($_GET['page']) - 1);
            $number = get_option('comments_per_page');

            $comments = get_comments(array('offset' => $offset, 'type' => 'review', 'post_id' => absint($_GET['post_id']), 'status' => 'approve', 'number' => $number));
            ob_start();
            wp_list_comments(wp_list_comments(array('callback' => 'marketengine_comments'), $comments));
            $content = ob_get_clean();

            wp_send_json(array('success' => true, 'data' => $content));
        }
    }

    public static function update_order_count($order_id)
    {
        $listing    = marketengine_get_order_items($order_id, 'listing_item');
        $listing_id = marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_listing_id', true);
        ME_Listing_Handle::update_order_count($listing_id);
    }

}
ME_Listing_Handle_Form::init_hook();
