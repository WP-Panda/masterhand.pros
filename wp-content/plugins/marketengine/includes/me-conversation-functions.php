<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * MarketEngine create a message
 *
 * @param array $message_arr The message data
 * @param bool $wp_error Return WP_Error when error occur
 *
 * @return WP_Error | int
 * @since 1.0
 *
 * @author EngineTeam
 */
function marketengine_insert_message($message_arr, $wp_error = false) {
    global $wpdb;

    $user_id = empty($message_arr['sender']) ? get_current_user_id() : $message_arr['sender'];

    if (empty($user_id)) {
        if ($wp_error) {
            return new WP_Error('empty_receiver', __('Sender is empty.'));
        } else {
            return 0;
        }
    }

    $defaults = array(
        'sender'                => '',
        'receiver'              => '',
        'post_content'          => '',
        'post_content_filtered' => '',
        'post_title'            => '',
        'post_excerpt'          => '',
        'post_status'           => 'read',
        'post_type'             => 'inquiry',
        'post_password'         => '',
        'post_parent'           => 0,
        'guid'                  => '',
    );

    $message_arr = wp_parse_args($message_arr, $defaults);

    $message_arr = sanitize_post($message_arr, 'db');
    // Are we updating or creating?
    $message_ID = 0;
    $update     = false;
    $guid       = $message_arr['guid'];

    if (!empty($message_arr['ID'])) {
        $update = true;

        // Get the post ID and GUID.
        $message_ID     = $message_arr['ID'];
        $message_before = marketengine_get_message($message_ID);
        if (is_null($message_before)) {
            if ($wp_error) {
                return new WP_Error('invalid_post', __('Invalid message ID.'));
            }
            return 0;
        }

        $guid            = marketengine_get_message_field('guid', $message_ID);
        $previous_status = marketengine_get_message_field('message_status', $message_ID); // get_post_field
    } else {
        $previous_status = 'new';
    }

    $post_type = empty($message_arr['post_type']) ? 'message' : $message_arr['post_type'];

    $post_title   = $message_arr['post_title'];
    $post_content = $message_arr['post_content'];
    $post_excerpt = $message_arr['post_excerpt'];
    if (isset($message_arr['post_name'])) {
        $post_name = $message_arr['post_name'];
    } elseif ($update) {
        // For an update, don't modify the post_name if it wasn't supplied as an argument.
        $post_name = $message_before->post_name;
    }

    $maybe_empty = 'attachment' !== $post_type && !$post_content;

    /**
     * Filters whether the message should be considered "empty".
     *
     * Returning a truthy value to the filter will effectively short-circuit
     * the new post being inserted, returning 0. If $wp_error is true, a WP_Error
     * will be returned instead.
     *
     * @since 1.0
     *
     * @param bool  $maybe_empty Whether the post should be considered "empty".
     * @param array $message_arr     Array of post data.
     */
    if (apply_filters('marketengine_insert_message_empty_content', $maybe_empty, $message_arr)) {
        if ($wp_error) {
            return new WP_Error('empty_content', __('Content are empty.', 'enginethemes'));
        } else {
            return 0;
        }
    }

    if (empty($message_arr['receiver'])) {
        if ($wp_error) {
            return new WP_Error('empty_receiver', __('Receiver is empty.', 'enginethemes'));
        } else {
            return 0;
        }
    }

    if ($message_arr['receiver'] == $message_arr['sender']) {
        if ($wp_error) {
            return new WP_Error('send_to_yourself', __('You can not send message to your self.', 'enginethemes'));
        } else {
            return 0;
        }
    }

    $post_status = empty($message_arr['post_status']) ? 'sent' : $message_arr['post_status'];

    /*
     * If the post date is empty (due to having been new or a draft) and status
     * is not 'draft' or 'pending', set date to now.
     */
    if (empty($message_arr['post_date_gmt']) || '0000-00-00 00:00:00' == $message_arr['post_date_gmt']) {
        $post_date = current_time('mysql');
    } else {
        $post_date = get_date_from_gmt($message_arr['post_date_gmt']);
    }

    if (!in_array($post_status, array('draft', 'pending', 'auto-draft'))) {
        $post_date_gmt = get_gmt_from_date($post_date);
    } else {
        $post_date_gmt = '0000-00-00 00:00:00';
    }

    if ($update || '0000-00-00 00:00:00' == $post_date) {
        $post_modified     = current_time('mysql');
        $post_modified_gmt = current_time('mysql', 1);
    } else {
        $post_modified     = $post_date;
        $post_modified_gmt = $post_date_gmt;
    }

    // These variables are needed by compact() later.
    $post_content_filtered = $message_arr['post_content_filtered'];
    if(!$update) {
        $sender                = $user_id;
        $receiver              = $message_arr['receiver'];    
    }    

    if (isset($message_arr['post_parent'])) {
        $post_parent = (int) $message_arr['post_parent'];
    } else {
        $post_parent = 0;
    }

    // Expected_slashed (everything!).
    $data  = compact('sender', 'receiver', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'post_password', 'post_name', 'post_modified', 'post_modified_gmt', 'post_parent', 'guid');
    $data  = wp_unslash($data);
    $where = array('ID' => $message_ID);

    $message_table = $wpdb->prefix . 'marketengine_message_item';
    if ($update) {
        /**
         * Fires immediately before an existing post is updated in the database.
         *
         * @since 1.0.0
         *
         * @param int   $message_ID Message ID.
         * @param array $data    Array of unslashed post data.
         */
        do_action('pre_message_update', $message_ID, $data);
        if (false === $wpdb->update($message_table, $data, $where)) {
            if ($wp_error) {
                return new WP_Error('db_update_error', __('Could not update post in the database', 'enginethemes'), $wpdb->last_error);
            } else {
                return 0;
            }
        }
    } else {
        // If there is a suggested ID, use it if not already present.
        if (!empty($import_id)) {
            $import_id = (int) $import_id;
            if (!$wpdb->get_var($wpdb->prepare("SELECT ID FROM $message_table WHERE ID = %d", $import_id))) {
                $data['ID'] = $import_id;
            }
        }
        if (false === $wpdb->insert($message_table, $data)) {
            if ($wp_error) {
                return new WP_Error('db_insert_error', __('Could not insert post into the database', 'enginethemes'), $wpdb->last_error);
            } else {
                return 0;
            }
        }
        $message_ID = (int) $wpdb->insert_id;

        // Use the newly generated $message_ID.
        $where = array('ID' => $message_ID);
    }

    // TODO: message meta
    // if (!empty($message_arr['meta_input'])) {
    //     foreach ($message_arr['meta_input'] as $field => $value) {
    //         update_post_meta($message_ID, $field, $value);
    //     }
    // }

    $message = marketengine_get_message($message_ID);
    if ($update) {
        /**
         * Fires once an existing message has been updated.
         *
         * @since 1.0
         *
         * @param int     $message_ID   Message ID.
         * @param ME_Message $post         Message object.
         */
        do_action('edit_message', $message_ID, $message);
        $message_after = marketengine_get_message($message_ID);

        /**
         * Fires once an existing message has been updated.
         *
         * @since 1.0
         *
         * @param int     $message_ID      Message ID.
         * @param ME_Message $message_after   Message object following the update.
         * @param ME_Message $message_before  Message object before the update.
         */
        do_action('message_updated', $message_ID, $message_after, $message_before);
    }

    /**
     * Fires once a message has been saved.
     *
     * The dynamic portion of the hook name, `$message->post_type`, refers to
     * the message type slug.
     *
     * @since 1.0
     *
     * @param int     $message_ID message ID.
     * @param ME_Message $message    message object.
     * @param bool    $update  Whether this is an existing message being updated or not.
     */
    do_action("save_message_{$message->post_type}", $message_ID, $message, $update);

    /**
     * Fires once a message has been saved.
     *
     * @since 1.0
     *
     * @param int     $message_ID message ID.
     * @param ME_Message $message    message object.
     * @param bool    $update  Whether this is an existing message being updated or not.
     */
    do_action('save_message', $message_ID, $message, $update);

    /**
     * Fires once a message has been saved.
     *
     * @since 1.0
     *
     * @param int     $message_ID message ID.
     * @param ME_Message $message    message object.
     * @param bool    $update  Whether this is an existing message being updated or not.
     */
    do_action('marketengine_insert_message', $message_ID, $message, $update);

    return $message_ID;
}

/**
 * MarketEngine update a message
 *
 * @param array $message_arr The message data
 * @param bool $wp_error Return WP_Error when error occur
 *
 * @return WP_Error | int
 * @since 1.0
 *
 * @author EngineTeam
 */
function marketengine_update_message($message_arr = array(), $wp_error = false) {
    if (is_object($message_arr)) {
        // Non-escaped post was passed.
        $message_arr = get_object_vars($message_arr);
        $message_arr = wp_slash($message_arr);
    }

    // First, get all of the original fields.
    $post = marketengine_get_message($message_arr['ID'], ARRAY_A);

    if (is_null($post)) {
        if ($wp_error) {
            return new WP_Error('invalid_post', __('Invalid post ID.'));
        }

        return 0;
    }

    // Escape data pulled from DB.
    $post = wp_slash($post);

    // Drafts shouldn't be assigned a date unless explicitly done so by the user.
    if (isset($post['post_status']) && in_array($post['post_status'], array('draft', 'pending', 'auto-draft')) && empty($message_arr['edit_date']) &&
        ('0000-00-00 00:00:00' == $post['post_date_gmt'])) {
        $clear_date = true;
    } else {
        $clear_date = false;
    }

    // Merge old and new fields with new fields overwriting old ones.
    $message_arr = array_merge($post, $message_arr);
    if ($clear_date) {
        $message_arr['post_date']     = current_time('mysql');
        $message_arr['post_date_gmt'] = '';
    }

    return marketengine_insert_message($message_arr, $wp_error);
}

// TODO: archive message
function marketengine_archive_message() {

}

// TODO: delete message
function marketengine_delete_message($message_id) {
    global $wpdb;
    $message_table = $wpdb->prefix . 'marketengine_message_item';
    $wpdb->delete( $message_table, array('ID' => $message_id) );
}

/**
 * Retrieve messages statuses list
 * @return Array
 * @author EngineThemes
 */
function marketengine_get_message_status_list() {
    return apply_filters('marketengine_message_status_list', array(
        'sent'    => __("Sent", "enginethemes"),
        'read'    => __("Seen", "enginethemes"),
        'archive' => __("Archived", "enginethemes"),
    ));
}

/**
 * Retrieve messages types list
 * @return Array
 * @author EngineThemes
 */
function marketengine_get_message_types() {
    return apply_filters('marketengine_message_status_list', array(
        'inquiry' => __("Inquiry", "enginethemes"),
        'inbox'   => __("Inbox", "enginethemes"),
    ));
}

/**
 * Retrieve list of latest messages or messages matching criteria.
 *
 * The defaults are as follows:
 *
 * @since 1.0
 *
 * @see ME_Message_Query::parse_query()
 *
 * @param array $args {
 *     Optional. Arguments to retrieve messages. See ME_Message_Query::parse_query() for all
 *     available arguments.
 *
 *     @type int        $numberposts      Total number of posts to retrieve. Is an alias of $posts_per_page
 *                                        in ME_Message_Query. Accepts -1 for all. Default 5.
 *     @type array      $include          An array of post IDs to retrieve, sticky posts will be included.
 *                                        Is an alias of $post__in in ME_Message_Query. Default empty array.
 *     @type array      $exclude          An array of post IDs not to retrieve. Default empty array.
 *     @type bool       $suppress_filters Whether to suppress filters. Default true.
 * }
 * @return array List of messages.
 */
function marketengine_get_messages($args = null) {
    $defaults = array(
        'numberposts' => 10,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'post_type'   => 'message',
    );

    $r = wp_parse_args($args, $defaults);

    if (!empty($r['numberposts']) && empty($r['posts_per_page'])) {
        $r['posts_per_page'] = $r['numberposts'];
    }

    if (!empty($r['include'])) {
        $incposts            = wp_parse_id_list($r['include']);
        $r['posts_per_page'] = count($incposts); // only the number of posts included
        $r['post__in']       = $incposts;
    } elseif (!empty($r['exclude'])) {
        $r['post__not_in'] = wp_parse_id_list($r['exclude']);
    }

    $r['ignore_sticky_posts'] = true;
    $r['no_found_rows']       = true;

    $get_posts = new ME_Message_Query;
    return $get_posts->query($r);
}

/**
 * Retrieves message data given a message ID or message object.
 *
 * See sanitize_post() for optional $filter values. Also, the parameter
 * `$message`, must be given as a variable, since it is passed by reference.
 *
 * @since 1.5.1
 *
 * @global ME_Message $message
 *
 * @param int|ME_Message|null $message   Optional. Post ID or message object. Defaults to global $message.
 * @param string           $output Optional, default is Object. Accepts OBJECT, ARRAY_A, or ARRAY_N.
 *                                 Default OBJECT.
 * @param string           $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
 *                                 or 'display'. Default 'raw'.
 * @return ME_Message|array|null Type corresponding to $output on success or null on failure.
 *                            When $output is OBJECT, a `ME_Message` instance is returned.
 */
function marketengine_get_message($message = null, $output = OBJECT, $filter = 'raw') {
    if (empty($message) && isset($GLOBALS['message'])) {
        $message = $GLOBALS['message'];
    }

    if ($message instanceof ME_Message) {
        $_message = $message;
    } elseif (is_object($message)) {
        if (empty($message->filter)) {
            $_message = sanitize_post($message, 'raw');
            $_message = new ME_Message($_message);
        } elseif ('raw' == $message->filter) {
            $_message = new ME_Message($message);
        } else {
            $_message = ME_Message::get_instance($message->ID);
        }
    } else {
        $_message = ME_Message::get_instance($message);
    }

    if (!$_message) {
        return null;
    }

    $_message = $_message->filter($filter);

    if ($output == ARRAY_A) {
        return $_message->to_array();
    } elseif ($output == ARRAY_N) {
        return array_values($_message->to_array());
    }

    return $_message;
}

/**
 * Retrieve data from a message field based on message ID.
 *
 * Examples of the message field will be, 'post_type', 'post_status', 'post_content',
 * etc and based off of the post object property or key names.
 *
 * The context values are based off of the filter functions and
 * supported values are found within those functions.
 *
 * @since 1.0
 *
 * @see sanitize_post_field()
 *
 * @param string      $field   Message field name.
 * @param int|ME_Message $post    Optional. Message ID or Message object
 * @param string      $context Optional. How to filter the field. Accepts 'raw', 'edit', 'db',
 *                             or 'display'. Default 'display'.
 * @return string The value of the message field on success, empty string on failure.
 */
function marketengine_get_message_field($field, $message, $context = 'display') {
    $message = marketengine_get_message($message);

    if (!$message) {
        return '';
    }

    if (!isset($message->$field)) {
        return '';
    }
    return sanitize_post_field($field, $message->$field, $message->ID, $context);
}

/**
 * Retrieve message item meta field for a message item.
 *
 * @since 1.0
 *
 * @param int    $message_id    message ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value. Default false.
 *                           Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function marketengine_get_message_meta($message_id, $key = '', $single = false) {
    return get_metadata('marketengine_message_item', $message_id, $key, $single);
}

/**
 * Add meta data field to a message item
 *
 * @since 1.0
 *
 * @param int    $message_id Message ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function marketengine_add_message_meta($message_id, $meta_key, $meta_value, $unique = true) {
    return add_metadata('marketengine_message_item', $message_id, $meta_key, $meta_value, $unique);
}

/**
 *  Update Message meta field based on mesage_id.
 *
 * @since 1.0
 *
 * @param int    $message_id Message ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 * @return int|false Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function marketengine_update_message_meta($message_id, $meta_key, $meta_value, $prev_value = '') {
    return update_metadata('marketengine_message_item', $message_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Remove metadata matching criteria from a message item.
 *
 * @since 1.0
 *
 * @param int    $mesage_id  Message ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 *
 * @return bool True on success, false on failure.
 */
function marketengine_delete_message_meta($message_id, $meta_key, $meta_value = '') {
    return delete_metadata('marketengine_message_item', $message_id, $meta_key, $meta_value);
}

/**
 * Get current user inquiry for listing
 * @param int $listing_id
 * @return int $inquiry_id
 */
function marketengine_get_current_inquiry($listing_id, $sender = '') {
    global $wpdb;
    if(!$sender) {
        $sender = get_current_user_id();
    }

    if(!$sender) return false;

    $message_table = $wpdb->prefix . 'marketengine_message_item';
    $sql = "SELECT ID FROM $message_table WHERE sender = $sender AND post_parent = $listing_id AND post_type='inquiry'";

    $inquiry_id = $wpdb->get_var($sql);

    return $inquiry_id;
}

/**
 * Retrieves current user sent inquiry
 * @return array
 */
function marketengine_my_inquiries($args = array()) {
    $user_id = get_current_user_id();
    $args = array_merge($args, array('sender' => $user_id));
    return marketengine_get_inquiries($args);
    // SELECT count(message.post_status) as count_status, post_status, message.post_parent FROM `me_marketengine_message_item` as message WHERE message.post_status = 'sent' GROUP by message.post_status, message.post_parent
}

/**
 * Retrieves current user accepted request
 * @return array
 */
function marketengine_my_request($args) {
    $user_id = get_current_user_id();
    $args = array_merge($args, array('receiver' => $user_id));
    return marketengine_get_inquiries($args);
}


function marketengine_get_inquiries($args) {
    global $wpdb;
    $user_id = get_current_user_id();
    $where         = "WHERE message.sender = $user_id  ";

    if (!empty($args['receiver'])) {
        $user_id = $args['receiver'];
        $where         = "WHERE message.receiver = $user_id  ";
    }

    $message_table = $wpdb->prefix . 'marketengine_message_item';
    $select        = "SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->posts.*, max(message.post_date) as message_date ";
    $from          = "FROM $wpdb->posts JOIN $message_table as message ON $wpdb->posts.ID = message.post_parent ";

    $group_by      = "GROUP By $wpdb->posts.ID ";

    if (!empty($args['s'])) {
        $search = marketengine_parse_search($args);
        $where .= $search;

        $join_users = " JOIN $wpdb->users ON $wpdb->users.ID = message.receiver ";
        if (!empty($args['receiver'])) {
            $join_users = " JOIN $wpdb->users ON $wpdb->users.ID = message.sender ";
        }

        $from .= $join_users;
    }

    // Handle date queries
    if ( ! empty( $args['date_query'] ) ) {
        $date_query = new WP_Date_Query( $args['date_query'] );
        $date_query = str_replace($wpdb->posts, $message_table, $date_query->get_sql());
        $where .= $date_query;
    }

    $order_by = 'ORDER BY message_date DESC ';

    $args['paged'] = 1;
    $args['posts_per_page'] = 10;
    // Paging
    if ( empty($args['nopaging']) ) {
        $page = absint($args['paged']);
        if ( !$page )
            $page = 1;
        $pgstrt = absint( ( $page - 1 ) * $args['posts_per_page'] ) . ', ';

        $limits = 'LIMIT ' . $pgstrt . $args['posts_per_page'];
    }

    $request = $select . $from . $where . $group_by . $order_by . $limits;

    $posts = $wpdb->get_results($request);
    $found_rows = $wpdb->get_var('SELECT FOUND_ROWS()');

    $results = array(
        'posts' => $posts,
        'found_rows' => $found_rows,
        'max_num_pages' => ceil($found_rows/$args['posts_per_page'])
    );

    return $results;
}

function marketengine_parse_search($args) {
    global $wpdb;

    $search = '';

    // added slashes screw with quote grouping when done early, so done later
    $args['s'] = stripslashes($args['s']);
    // there are no line breaks in <input /> fields
    $args['s']                  = str_replace(array("\r", "\n"), '', $args['s']);
    $args['search_terms_count'] = 1;
    if (!empty($args['sentence'])) {
        $args['search_terms'] = array($args['s']);
    } else {
        if (preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $args['s'], $matches)) {
            $args['search_terms_count'] = count($matches[0]);
            $args['search_terms']       = marketengine_parse_search_terms($matches[0]);
            // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
            if (empty($args['search_terms']) || count($args['search_terms']) > 9) {
                $args['search_terms'] = array($args['s']);
            }

        } else {
            $args['search_terms'] = array($args['s']);
        }
    }

    $n                         = !empty($args['exact']) ? '' : '%';
    $searchand                 = '';
    $args['search_orderby_title'] = array();
    foreach ($args['search_terms'] as $term) {
        // Terms prefixed with '-' should be excluded.
        $include = '-' !== substr($term, 0, 1);
        if ($include) {
            $like_op  = 'LIKE';
            $andor_op = 'OR';
        } else {
            $like_op  = 'NOT LIKE';
            $andor_op = 'AND';
            $term     = substr($term, 1);
        }

        if ($n && $include) {
            $like                        = '%' . $wpdb->esc_like($term) . '%';
            $args['search_orderby_title'][] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $like);
        }

        $like = $n . $wpdb->esc_like($term) . $n;
        $search .= $wpdb->prepare("{$searchand}(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_excerpt $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s))", $like, $like, $like);
        $searchand = ' AND ';
    }

    $searchand   = '';
    $search_user = '';
    foreach ($args['search_terms'] as $term) {
        // Terms prefixed with '-' should be excluded.
        $include = '-' !== substr($term, 0, 1);
        if ($include) {
            $like_op  = 'LIKE';
            $andor_op = 'OR';
        } else {
            $like_op  = 'NOT LIKE';
            $andor_op = 'AND';
            $term     = substr($term, 1);
        }

        if ($n && $include) {
            $like                        = '%' . $wpdb->esc_like($term) . '%';
            $args['search_orderby_title'][] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $like);
        }

        $like = $n . $wpdb->esc_like($term) . $n;
        $search_user .= $wpdb->prepare("{$searchand}(($wpdb->users.display_name $like_op %s))", $like, $like, $like);
        $searchand = ' AND ';
    }

    if (!empty($search)) {
        $search = " AND ({$search} OR {$search_user}) ";
    }

    return $search;
}

function marketengine_parse_search_terms($terms) {
    $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
    $checked    = array();

    $stopwords = marketengine_get_search_stopwords();

    foreach ($terms as $term) {
        // keep before/after spaces when term is for exact match
        if (preg_match('/^".+"$/', $term)) {
            $term = trim($term, "\"'");
        } else {
            $term = trim($term, "\"' ");
        }

        // Avoid single A-Z and single dashes.
        if (!$term || (1 === strlen($term) && preg_match('/^[a-z\-]$/i', $term))) {
            continue;
        }

        if (in_array(call_user_func($strtolower, $term), $stopwords, true)) {
            continue;
        }

        $checked[] = $term;
    }

    return $checked;
}

function marketengine_get_search_stopwords() {
    /* translators: This is a comma-separated list of very common words that should be excluded from a search,
     * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
     * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
     */
    $words = explode(',', _x('about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
        'Comma-separated list of search stopwords in your language', 'enginethemes'));

    $stopwords = array();
    foreach ($words as $word) {
        $word = trim($word, "\r\n\t ");
        if ($word) {
            $stopwords[] = $word;
        }

    }

    /**
     * Filters stopwords used when parsing search terms.
     *
     * @since 3.7.0
     *
     * @param array $stopwords Stopwords.
     */
    $stopwords = apply_filters('wp_search_stopwords', $stopwords);
    return $stopwords;
}

function marketengine_inquiry_permalink( $inquiry_id ) {
    $link = marketengine_get_page_permalink('inquiry');
    $link = add_query_arg(array('inquiry_id' => $inquiry_id), $link);
    return $link;
}

function marketengine_inquiry_ids_by_listing( $value ) {
    global $wpdb;
    $query = "SELECT $wpdb->posts.ID
        FROM $wpdb->marketengine_message_item
        LEFT JOIN $wpdb->posts
        ON $wpdb->marketengine_message_item.post_parent = $wpdb->posts.ID
        WHERE $wpdb->marketengine_message_item.post_type = 'inquiry'
        AND $wpdb->posts.post_title LIKE '%{$value}%'";

    $results = $wpdb->get_col($query);

    return $results;
}

function marketengine_inquiry_ids_by_user( $user, $role ) {
    global $wpdb;
    $query = "SELECT $wpdb->marketengine_message_item.post_parent
        FROM $wpdb->marketengine_message_item
        LEFT JOIN $wpdb->users
        ON $wpdb->marketengine_message_item.{$role} = $wpdb->users.ID
        WHERE $wpdb->marketengine_message_item.post_type = 'inquiry'
        AND $wpdb->users.display_name LIKE '%{$user}%'";

    $results = $wpdb->get_col($query);

    return $results;
}

/**
 *  Returns inquiry query args
 *  @param: $query
 *  @return: $args - query args
 */
function marketengine_filter_inquiry_query( $query, $role ) {
    $args = array();
    
    if( isset($query['from_date']) || isset($query['to_date']) ){
        $before = $after = '';
        if( isset($query['from_date']) && !empty($query['from_date']) ){
            if( preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/", $query['from_date']) ){
                $after = $query['from_date'];
            } else {
                $args['post__in'][] = -1;
                return $args;
            }
        }

        if( isset($query['to_date']) && !empty($query['to_date']) ){
            if( preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/", $query['to_date']) ){
                $before = $query['to_date'] . ' 23:59:59' ;
            } else {
                $args['post__in'][] = -1;
                return $args;
            }
        }

        $args['date_query'] = array(
            array(
                'column' => 'post_modified',
                'after'     => $after,
                'before'    => $before,
            ),
        );
    }

    if( isset($query['keyword']) && $query['keyword'] != '' ) {
        $keyword = esc_sql( $query['keyword'] );
        $ids_by_listing = $ids_by_user = array();

        $ids_by_listing = marketengine_inquiry_ids_by_listing( $keyword );
        if( $role == 'sender' ) {
            $ids_by_user = marketengine_inquiry_ids_by_user( $keyword, 'receiver' );
        } else {
            $ids_by_user = marketengine_inquiry_ids_by_user( $keyword, 'sender' );
        }

        $post_parent = array_merge($ids_by_listing, $ids_by_user);

        if( $post_parent ) {
            if( sizeof($post_parent) === 1 ) {
                $post_parent = (int) $post_parent[0];
                $args['post_parent'] = $post_parent;
            } else {
                $args['post_parent__in'] = $post_parent;
            }
        } else {
            $args['post_parent'] = -1;
        }

    }
    return $args;
}
add_filter( 'marketengine_filter_inquiry', 'marketengine_filter_inquiry_query', 1, 2 );