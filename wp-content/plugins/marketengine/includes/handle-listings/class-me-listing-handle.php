<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME_Listing_Handle
 *
 * Handling user post listing behavior
 *
 * @class       ME_Listing_Handle
 * @version     1.0
 * @package     Includes/Post-Listings
 * @author      EngineThemesTeam
 * @category    Class
 */
class ME_Listing_Handle
{
    /**
     * Insert Listing
     *
     * Insert Listing to database
     *
     * @since 1.0
     *
     * @see wp_insert_post()
     * @param array $listing_data
     *
     * @return WP_Error| WP_Post
     */
    public static function insert($listing_data, $is_update = false)
    {

        $user_ID = get_current_user_id();
        // validate data
        $is_valid = self::validate($listing_data, $is_update);
        if (is_wp_error($is_valid)) {
            return $is_valid;
        }

        $listing_data = self::filter($listing_data);

        if (isset($listing_data['listing_gallery'])) {
            $maximum_files_allowed = get_option('marketengine_listing_maximum_images_allowed', 5);
            $number_of_files       = count($listing_data['listing_gallery']);
            if ($number_of_files > $maximum_files_allowed) {
                return new WP_Error('over_maximum_files_allowed', sprintf(__("You can only add %d image(s) to listing gallery.", "enginethemes"), $maximum_files_allowed));
            }

            foreach ($listing_data['listing_gallery'] as $key => $value) {
                $listing_data['listing_gallery'][$key] = esc_sql($value);
            }
        }

        if (isset($listing_data['listing_image']) && !empty($listing_data['listing_image'])) {
            // process upload featured image
            $listing_data['meta_input']['_thumbnail_id'] = absint(esc_sql($listing_data['listing_image']));
        } else {
            if (!empty($listing_data['listing_gallery'])) {
                $listing_data['meta_input']['_thumbnail_id'] = $listing_data['listing_gallery'][0];
            } else {
                $listing_data['meta_input']['_thumbnail_id'] = '';
            }
        }

        if (isset($listing_data['ID'])) {
            if (($listing_data['post_author'] != $user_ID) && !current_user_can('edit_others_posts')) {
                return new WP_Error('edit_others_posts', __("You are not allowed to edit listing as this user.", "enginethemes"));
            }
            $post = wp_update_post($listing_data);
            /**
             * Do action after update listing
             *
             * @param object|WP_Error $post
             * @param array $listing_data
             * @since 1.0
             */
            do_action('marketengine_after_update_listing', $post, $listing_data);
        } else {
            if (!self::current_user_can_create_listing()) {
                return new WP_Error('create_posts', __("You are not allowed to create posts as this user.", "enginethemes"));
            }
            $post = wp_insert_post($listing_data);
            /**
             * Do action after insert listing
             *
             * @param object|WP_Error $post
             * @param array $listing_data
             * @since 1.0
             */
            do_action('marketengine_after_insert_listing', $post, $listing_data);
        }

        if (isset($listing_data['listing_gallery'])) {
            $listing_gallery = array_map('absint', $listing_data['listing_gallery']);
            //process upload image gallery
            update_post_meta($post, '_me_listing_gallery', $listing_gallery);
        } else {
            update_post_meta($post, '_me_listing_gallery', array());
        }
        return $post;
    }

    /**
     * Update Listing
     *
     * Update Listing Data to database
     *
     * @since 1.0
     *
     * @see insert()
     * @param array $listing_data
     *
     * @return WP_Error| WP_Post
     */
    public static function update($listing_data)
    {
        $current_user_id    = get_current_user_id();
        $listing_data['ID'] = absint( $listing_data['edit'] );

        $listing                     = marketengine_get_listing($listing_data['ID']);
        $listing_data['post_author'] = $listing->post_author;

        $listing_type = $listing->get_listing_type();
        if ($listing_type !== $listing_data['listing_type']) {
            return new WP_Error('permission_denied', __("You can not change the listing type.", "enginethemes"));
        }

        $listing_parent_category = wp_get_post_terms($listing->ID, 'listing_category', array('fields' => 'ids', 'parent' => 0));

        if (!in_array($listing_data['parent_cat'], $listing_parent_category)) {
            return new WP_Error('permission_denied', __("You can not change the listing category.", "enginethemes"));
        }

        $listing_sub_category = wp_get_post_terms($listing->ID, 'listing_category', array('fields' => 'ids', 'parent' => $listing_parent_category[0]));
        if (!empty($listing_sub_category) && !in_array($listing_data['sub_cat'], $listing_sub_category)) {
            return new WP_Error('permission_denied', __("You can not change the listing category.", "enginethemes"));
        }

        return self::insert($listing_data, true);
    }

    /**
     * Filter Listing Data
     *
     * Convert the listing data to compatible with WordPress post data
     *
     * @since 1.0
     *
     * @param array $listing_data
     *
     * @return array The listing data filtered
     */
    public static function filter($listing_data)
    {
        $listing_data['post_type'] = 'listing';

        $listing_data['post_title']   = $listing_data['listing_title'];
        $listing_data['post_content'] = $listing_data['listing_description'];
        // filter taxonomy
        $listing_data['tax_input']['listing_category'] = array($listing_data['parent_cat'], $listing_data['sub_cat']);
        if (!empty($listing_data['listing_tag'])) {
            $listing_data['tax_input']['listing_tag'] = $listing_data['listing_tag'];
        } else {
            $listing_data['tax_input']['listing_tag'] = '';
        }
        // set listing status
        if (self::current_user_can_publish_listing()) {
            $listing_data['post_status'] = 'publish';
        } else {
            $listing_data['post_status'] = 'draft';
        }

        $listing_data['meta_input']['_me_listing_type'] = $listing_data['listing_type'];

        /**
         * Filter listing data
         *
         * @param array $listing_data
         * @since 1.0
         */
        return apply_filters('marketengine_filter_listing_data', $listing_data);
    }

    /**
     * Process upload listing featured image
     *
     * @param array $files The submit file from client
     *      - string name
     *      - int  size
     *      - string type
     *      - string  tmp_name
     *
     * @since 1.0
     *
     * @return int The attachment id
     */
    public static function process_feature_image($file)
    {
        global $user_ID;
        $mimes = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif'          => 'image/gif',
            'png'          => 'image/png',
            'bmp'          => 'image/bmp',
            'tif|tiff'     => 'image/tiff',
            'ico'          => 'image/x-icon',
        );
        //return self::process_file_upload($file, 0, $user_ID, $mimes);
    }

    /**
     * Check current user create post capability
     *
     * @since 1.0
     *
     * @return bool
     */
    public static function current_user_can_create_listing()
    {
        global $user_ID;
        if ($user_ID) {
            return apply_filters('marketengine_user_can_create_listing', true, $user_ID);
        }
        return false;
    }

    /**
     * Check current user create publish post capability
     *
     * @since 1.0
     *
     * @return bool
     */
    public static function current_user_can_publish_listing()
    {
        global $user_ID;
        return apply_filters('marketengine_user_can_publish_listing', true, $user_ID);
    }

    /**
     * Check current user create new term taxonomy
     *
     * @since 1.0
     *
     * @return bool
     */
    public static function current_user_can_create_taxonomy($taxonomy)
    {
        global $user_ID;
        if (is_taxonomy_hierarchical($taxonomy)) {
            return apply_filters('marketengine_user_can_create_$taxonomy', false, $taxonomy, $user_ID);
        }
        return apply_filters('marketengine_user_can_create_$taxonomy', true, $taxonomy, $user_ID);
    }
    /**
     * Validate listing data
     *
     * Validate listing data, listing meta data, listing taxonomy data
     *
     * @since 1.0
     *
     * @see marketengine_validate
     * @param array $data Listing data
     *
     * @return True|WP_Error True if success, WP_Error if false
     *
     */
    public static function validate($listing_data, $is_update = false)
    {
        $current_user_id = get_current_user_id();
        $invalid_data    = array();
        // validate post data
        $rules = array(
            'listing_title'       => 'required|string|max:150',
            'listing_description' => 'required',
            'listing_type'        => 'required|in:contact,purchasion',
        );
        /**
         * Filter listing data validate rule
         *
         * @param array $rules
         * @param array $listing_data
         * @since 1.0
         */
        $custom_attributes = array(
            'listing_title'       => __("listing title", "enginethemes"),
            'listing_description' => __("listing description", "enginethemes"),
            'listing_type'        => __("listing type", "enginethemes"),
        );

        $rules    = apply_filters('marketengine_insert_listing_rules', $rules, $listing_data);
        $is_valid = marketengine_validate($listing_data, $rules, $custom_attributes);
        if (!$is_valid) {
            $invalid_data = marketengine_get_invalid_message($listing_data, $rules, $custom_attributes);
        }

        if (!empty($listing_data['listing_type'])) {
            // user must add paypal email to start selling
            if ($listing_data['listing_type'] == 'purchasion') {
                $user_paypal_email = get_user_meta($current_user_id, 'paypal_email', true);
                if (!is_email($user_paypal_email)) {
                    $invalid_data['empty_paypal_email'] = __("You must input paypal email in your profile to start selling.", "enginethemes");
                }
            }
            /**
             * Filter listing meta data validate rule
             *
             * @param array $listing_meta_data_rules
             *
             * @since 1.0
             */
            $listing_meta_data_rules = self::get_listing_type_fields_rule($listing_data['listing_type']);

            // validate post meta data
            $is_valid = marketengine_validate($listing_data['meta_input'], $listing_meta_data_rules['rules'], $listing_meta_data_rules['custom_attributes']);
            if (!$is_valid) {
                $invalid_data = array_merge($invalid_data, marketengine_get_invalid_message($listing_data['meta_input'], $listing_meta_data_rules['rules'], $listing_meta_data_rules['custom_attributes']));
            }

            // validate listing category
            $invalid_data = array_merge($invalid_data, self::validate_category($listing_data, $is_update));
        }

         /**
         * Filter listing data  eror message
         *
         * @param array $invalid_data The listing data invalid message
         * @param array $listing_data The listing data
         *
         * @since 1.0.1
         */
        $invalid_data = apply_filters('marketengine_post_listing_error_messages', $invalid_data, $listing_data);
        if (!empty($invalid_data)) {
            $errors = new WP_Error();
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        $listing_data['listing_title'] = sanitize_title( $listing_data['listing_title'] );
        $listing_data['listing_description'] = wp_kses($listing_data['listing_description'], '<p><a><ul><ol><li><h6><span><b><em><strong><br>');
        $listing_data['listing_description'] = wp_rel_nofollow($listing_data['listing_description']);
        /**
         * Filter validate listing data result
         *
         * @param bool TRUE
         * @param array $listing_data
         *
         * @since 1.0
         */
        return apply_filters('marketengine_validate_listing_data', true, $listing_data);
    }

    /**
     * Validate Listing Category
     *
     * @param array $listing_data The listing data user input
     * @param bool $is_update Is ser editing listing or not
     *
     * @since 1.0.1
     *
     * @return array Array of messages
     */
    public static function validate_category($listing_data, $is_update = false)
    {
        $current_user_id = get_current_user_id();
        $invalid_data    = array();

        if (empty($listing_data['parent_cat'])) {
            return array('listing_category' => __("The listing category field is required.", "enginethemes"));
        } elseif (!term_exists(intval($listing_data['parent_cat']), 'listing_category')) {
            return array('invalid_listing_category' => __("The selected listing category is invalid.", "enginethemes"));
        }
        
        $listing_type_categories = marketengine_get_listing_type_categories();
        if(!$is_update && !in_array($listing_data['parent_cat'], $listing_type_categories['all'])) {
            return array('no_support_category' => __("The selected listing category is not support in any listing type.", "enginethemes"));   
        }

        // check the parent cat sub is empty or not
        $child_cats          = get_terms('listing_category', array('hide_empty' => false, 'parent' => $listing_data['parent_cat']));
        $is_child_cats_empty = empty($child_cats);
        // validate sub cat
        if (!$is_child_cats_empty && empty($listing_data['sub_cat'])) {
            $invalid_data['sub_listing_category'] = __("The sub listing category field is required.", "enginethemes");
        } elseif (!$is_child_cats_empty && !term_exists(intval($listing_data['sub_cat']))) {
            $invalid_data['invalid_sub_listing_category'] = __("The selected sub listing category is invalid.", "enginethemes");
        }

        // check supported listing type
        $parent_cat = $listing_data['parent_cat'];
        $listing_type = $listing_data['listing_type'];

        if (!$is_update && isset($listing_type_categories[$listing_type])) {
            
            $available_cats = $listing_type_categories[$listing_type];
            
            if (!in_array($listing_data['parent_cat'], $available_cats)) {
                $term = get_term($listing_data['parent_cat']);

                $invalid_data['unsupported_type'] = sprintf(
                    __("The listing type %s is not supported in category %s.", "enginethemes"),
                    marketengine_get_listing_type_label($listing_type),
                    $term->name
                );
            }
        }
        return $invalid_data;
    }

    /**
     * Get Listing Type Fields Rules
     *
     * Return the data rules base on listing type
     *
     * @param string $listing_type The listing type
     *
     * @return array
     *
     * @since 1.0
     */
    public static function get_listing_type_fields_rule($listing_type)
    {
        switch ($listing_type) {
            case 'contact':
                $rules      = array('contact_email' => 'email');
                $attributes = array('contact_email' => __("contact email", "enginethemes"));
                break;
            case 'rental':

            default:
                $rules      = array('listing_price' => 'required|numeric|greaterThan:0');
                $attributes = array('listing_price' => __("listing price", "enginethemes"));
                break;
        }

        $the_rules = array(
            'rules'             => $rules,
            'custom_attributes' => $attributes,
        );

        return apply_filters('marketengine_insert_listing_meta_rules', $the_rules, $listing_type);
    }

    /**
     * Insert Listing Review
     *
     * @param array $data The review data
     *
     * @since 1.0
     *
     * @return WP_Error | ME_Review
     */
    public static function insert_review($data)
    {
        // validate current user
        $current_user_id = get_current_user_id();
        $rules           = array('content' => 'required', 'score' => 'required|numeric|greaterThan:0');

        $custom_attributes = array(
            'content' => __("review content", "enginethemes"),
            'score'   => __("rating", "enginethemes"),
        );
        /**
         * Filter review data validate rule
         *
         * @param array $rules
         * @param array $data
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_insert_review_rules', $rules, $data);
        $is_valid = marketengine_validate($data, $rules, $custom_attributes);
        if (!$is_valid) {
            $invalid_data = marketengine_get_invalid_message($data, $rules, $custom_attributes);
        }

        if (!empty($invalid_data)) {
            $errors = new WP_Error();
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        if (empty($data['listing_id'])) {
            return new WP_Error('invalid_listing', __("The reviewed listing is invalid.", "enginethemes"));
        }

        if (empty($data['order_id'])) {
            return new WP_Error('invalid_order', __("Invalid order id.", "enginethemes"));
        }

        $listing_id = absint( $data['listing_id'] );
        $listing    = marketengine_get_listing($listing_id);
        if (!$listing || is_wp_error($listing)) {
            return new WP_Error('invalid_listing', __("The reviewed listing is invalid.", "enginethemes"));
        }

        $order = new ME_Order($data['order_id']);
        if ($order->post_author != $current_user_id) {
            return new WP_Error('permission_denied', __("You cannot review the listing base on this order.", "enginethemes"));
        }

        if (!$order->has_status(array('me-complete', 'me-closed', 'me-resolved'))) {
            return new WP_Error('order_onhold', __("You must complete the order to send review.", "enginethemes"));
        }

        $listing_items = $order->get_listing_items();
        if (!array_key_exists($listing_id, $listing_items)) {
            return new WP_Error('listing_not_in_order', sprintf(__("You are trying to review listing is not belong to order %d", "enginethemes"), $order->ID));
        }

        $current_user = wp_get_current_user();
        $comments     = get_comments(array(
            'post_id'        => $listing_id,
            'type'           => 'review',
            'author_email'   => $current_user->user_email,
            'number'         => 1,
            'comment_parent' => 0,
        ));

        if (!empty($comments)) {
            return new WP_Error('duplicationde', sprintf(__("You have already review on %s.", 'enginethemes'), esc_html(get_the_title($listing_id))));
        }

        $review_item = marketengine_get_order_items($order->ID, 'review_item');
        if (empty($review_item)) {
            $order_item_id = marketengine_add_order_item($order->ID, esc_html(get_the_title($listing_id)), 'review_item');
            marketengine_add_order_item_meta($order_item_id, '_listing_id', $listing_id);
            marketengine_add_order_item_meta($order_item_id, '_review_score', absint( $data['score'] ));
            marketengine_add_order_item_meta($order_item_id, '_review_content', sanitize_textarea_field( $data['content'] ));
        }

        $commentdata = array(
            'comment_post_ID'      => $listing_id,
            'comment_author'       => $current_user->display_name,
            'comment_author_email' => $current_user->user_email,
            // 'comment_author_url'   => 'http://',
            'comment_content'      => sanitize_textarea_field( $data['content'] ),
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => $current_user_id,
            'comment_author_IP'    => $_SERVER['REMOTE_ADDR'],
            // 'comment_agent'        => $browser['userAgent'],
            'comment_approved'     => 1,
        );

        $comment_id = wp_insert_comment($commentdata);
        if (!is_wp_error($comment_id)) {
            update_comment_meta($comment_id, '_me_rating_score', absint($data['score']));

            $comment = get_comment($comment_id);
            do_action('marketengine_insert_review', $comment_id, $comment);
        }

        return $comment_id;
    }

    /**
     * catch hook wp_insert_comment to update rating
     * @param int $comment_id
     * @param $comment
     * @author Dakachi
     */
    public static function update_post_rating($comment_id, $comment)
    {

        $post_id = $comment->comment_post_ID;
        $post    = get_post($post_id);
        if ($post->post_type == 'listing') {
            // update post rating score
            self::update_post_rating_score($post_id);
            self::update_post_review_count($post_id);
        }
    }

    public static function update_post_rating_score($post_id)
    {
        global $wpdb;
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                    FROM    $wpdb->comments as C
                        JOIN $wpdb->commentmeta as M
                                on C.comment_ID = M.comment_id
                    WHERE   M.meta_key = '_me_rating_score'
                            AND C.comment_post_ID = $post_id
                            AND C.comment_approved = 1";

        $results = $wpdb->get_results($sql);
        // update post rating score
        update_post_meta($post_id, '_rating_score', round($results[0]->rate_point, 1));
        update_post_meta($post_id, '_me_reviews_count', $results[0]->count);
    }

    public static function update_post_review_count($post_id)
    {
        global $wpdb;
        $sql = "SELECT COUNT(C.comment_ID) as count, M.meta_value
                    FROM    $wpdb->comments as C
                        JOIN $wpdb->commentmeta as M
                                on C.comment_ID = M.comment_id
                    WHERE   M.meta_key = '_me_rating_score'
                            AND C.comment_post_ID = $post_id
                            AND C.comment_approved = 1
                            GROUP BY M.meta_value";

        $results = $wpdb->get_results($sql);
        $count   = array();
        foreach ($results as $key => $value) {
            $count[$value->meta_value . '_star'] = $value->count;
        }
        update_post_meta($post_id, '_me_review_count_details', $count);
    }

    /**
     * @param int $listing_id
     * @author KyNguyen
     */
    public static function update_order_count($listing_id)
    {
        global $wpdb;

        $listing      = marketengine_get_listing($listing_id);
        $listing_name = $listing->get_title();

        $sql = "SELECT COUNT(OI.order_id) as count, O.post_status as status
                FROM    $wpdb->marketengine_order_items as OI
                LEFT JOIN $wpdb->posts as O
                    ON OI.order_id = O.ID
                WHERE   OI.order_item_type = 'listing_item'
                        AND OI.order_item_name = '$listing_name'
                GROUP BY O.post_status";

        $results = $wpdb->get_results($sql);
        $results = marketengine_filter_order_count_result($results);

        if (isset($results['me-complete'])) {
            update_post_meta($listing_id, '_me_order_count', $results['me-complete']);
        }
    }

    public static function update_inquiry_count($listing_id)
    {
        global $wpdb;
        $sql = "SELECT COUNT(M.ID) as count
            FROM $wpdb->marketengine_message_item as M
            LEFT JOIN $wpdb->posts as P
            ON M.post_parent = P.ID
            WHERE M.post_type = 'inquiry'
            AND P.ID LIKE '$listing_id'";

        $results = $wpdb->get_results($sql);

        update_post_meta($listing_id, '_me_inquiry_count', $results[0]->count);
    }
}
