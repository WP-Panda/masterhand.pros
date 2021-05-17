<?php
/**
 * Create functional pages supported by MarketEngine
 *
 * @package Admin/Setupwizard
 * @category Function
 *
 * @since 1.0
 */
function marketengine_create_functional_pages()
{
    global $wpdb;
    $default_pages = marketengine_get_functional_pages();

    foreach ($default_pages as $key => $page) {

        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'page',
        );

        $pages = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='page' AND post_content='" . $page['post_content'] . "'");
        if (!empty($pages)) {
            $page_id = array_pop($pages);
        } else {
            $args    = wp_parse_args($args, $page);
            $page_id = wp_insert_post($args);
        }

        if ($page_id) {
            marketengine_update_option('marketengine_' . $key . '_page_id', $page_id);
        }

        flush_rewrite_rules();
    }
}

/**
 * Retrieve list of name and content of functional pages supported by MarketEngine
 *
 * @package Admin/Setupwizard
 * @category Function
 *
 * @return array
 * @since 1.0
 */
function marketengine_get_functional_pages()
{
    return array(
        'user_account'  => array(
            'post_title'   => __("My Account", "enginethemes"),
            'post_content' => '[me_user_account]',
        ),
        'post_listing'  => array(
            'post_title'   => __("Post a Listing", "enginethemes"),
            'post_content' => '[me_post_listing_form]',
        ),
        'edit_listing'  => array(
            'post_title'   => __("Edit the Listing", "enginethemes"),
            'post_content' => '[me_edit_listing_form]',
        ),
        'checkout'      => array(
            'post_title'   => __("Checkout", "enginethemes"),
            'post_content' => '[me_checkout_form]',
        ),
        'confirm_order' => array(
            'post_title'   => __("Thank you for payment", "enginethemes"),
            'post_content' => '[me_confirm_order]',
        ),
        'cancel_order'  => array(
            'post_title'   => __("Order Cancellation", "enginethemes"),
            'post_content' => '[me_cancel_payment]',
        ),
        'inquiry'       => array(
            'post_title'   => __("Inquiry", "enginethemes"),
            'post_content' => '[me_inquiry_form]',
        ),
    );
}
/**
 * Filter order status when import sample data
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_sample_filter_order_status($status)
{
    return 'me-complete';
}

/**
 * Add sample order
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_add_sample_order($orders, $listing_id)
{
    foreach ($orders as $key => $order_data) {
        $order_data['post_author'] = marketengine_add_sample_user($order_data);
        $order_data['customer_note'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.';
        
        add_filter('marketengine_create_order_status', 'marketengine_sample_filter_order_status');
        $order = marketengine_insert_order($order_data);
        update_post_meta($order, 'is_sample_data', 'sample-data');
        remove_filter('marketengine_create_order_status', 'marketengine_sample_filter_order_status');

        $billing = $order_data;
        $billing['phone'] = '13132132130';
        $billing['email'] = $order_data['user_email'];
        $billing['address'] = $billing['country']  = $billing['city'] = $order_data['location'];

        $me_order = new ME_Order($order);
        $listing  = marketengine_get_listing($listing_id);

        $me_order->set_address($billing);

        $me_order->add_listing($listing);

        $receiver_1 = (object) array(
            'user_name'  => 'admin',
            'email'      => 'dinhle1987-per2@yahoo.com',
            'amount'     => 5,
            'is_primary' => false,
        );
        $me_order->add_commission($receiver_1);

        $commentdata = array(
            'comment_post_ID'      => $listing_id,
            'comment_author'       => get_the_author_meta('display_name', $order_data['post_author']),
            'comment_author_email' => $order_data['user_email'],
            'comment_content'      => $order_data['review']['content'],
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => $order_data['post_author'],
            'comment_author_IP'    => $_SERVER['REMOTE_ADDR'],
            'comment_approved'     => 1,
        );

        $comment_id = wp_insert_comment($commentdata);
        if (!is_wp_error($comment_id)) {

            update_comment_meta($comment_id, '_me_rating_score', $order_data['review']['rate']);
            update_comment_meta($comment_id, 'is_sample_data', 'sample-data');

            $comment = get_comment($comment_id);
            do_action('marketengine_insert_review', $comment_id, $comment);
        }
    }
}

/**
 * Add sample inquiry
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_add_sample_inquiry($inquiries, $listing_id)
{
    foreach ($inquiries as $key => $inquiry_data) {
        $inquiry_data['post_author'] = marketengine_add_sample_user($inquiry_data);
        $receiver                    = get_post_field('post_author', $listing_id);
        $inquiry_id                  = marketengine_insert_message(
            array(
                'sender'       => $inquiry_data['post_author'],
                'post_content' => 'Inquiry listing #' . $listing_id,
                'post_title'   => 'Inquiry listing #' . $listing_id,
                'post_type'    => 'inquiry',
                'receiver'     => $receiver,
                'post_parent'  => $listing_id,
            )
        );

        marketengine_update_message_meta($inquiry_id, 'is_sample_data', 'sample-data');

        foreach ($inquiry_data['messages'] as $key => $message) {
            $message_data = array(
                'post_content' => $message,
                'post_title'   => 'Message listing #' . $listing_id,
                'post_type'    => 'message',
                'receiver'     => $receiver,
                'post_parent'  => $inquiry_id,
            );
            if (($key % 2) == 0) {
                $message_data['receiver'] = $receiver;
                $message_data['sender']   = $inquiry_data['post_author'];
            } else {
                $message_data['receiver'] = $inquiry_data['post_author'];
                $message_data['sender']   = $receiver;
            }
            $message_id = marketengine_insert_message($message_data);
            marketengine_update_message_meta($message_id, 'is_sample_data', 'sample-data');
        }

    }
}

/**
 * Add sample user
 * @param array $user_data The user data
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_add_sample_user($user_data)
{
    // add user
    $defaults = array(
        'user_login'   => 'henrywilson',
        'first_name'   => 'Henry',
        'last_name'    => 'Wilson',
        'user_email'   => 'henrywilson@mailinator.com',
        'location'     => 'UK',
        'user_pass'    => '123',
        'avatar'       => 'http://lorempixel.com/150/150/business/',
        'paypal_email' => 'dinhle1987-buyer@yahoo.com',
        'role'         => 'author',
    );
    $user_data = wp_parse_args($user_data, $defaults);
    $user      = get_user_by('login', $user_data['user_login']);
    if (!$user) {
        $user_id = wp_insert_user($user_data);
        update_user_meta($user_id, 'paypal_email', $user_data['paypal_email']);
        update_user_meta($user_id, 'location', $user_data['location']);
        update_user_meta($user_id, 'is_sample_data', 'sample-data');

        $number = rand(1, 5);
        $img_1  = marketengine_handle_sample_image(MARKETENGINE_URL . 'sample-data/images/' . $user_data['avatar'], $user_data['user_login']);

        update_user_meta($user_id, 'user_avatar', $img_1);

        return $user_id;
    }

    if(is_multisite()) {
        $blog_id = get_current_blog_id();
        add_user_to_blog( $blog_id, $user->ID, 'author' );
    }

    return $user->ID;
}

/**
 * Filter post where when add listing, order
 * 
 * @param string $where The post query where clause
 * @param WP_Query $wp_query WP_Query object
 * 
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_setup_sample_data_post_where($where, &$wp_query)
{
    global $wpdb;
    if ($search_keyword = $wp_query->get('s')) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql($wpdb->esc_like($search_keyword)) . '%\'';
    }
    return $where;
}

/**
 * Add sample image
 * @param string $image_url The image url
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_handle_sample_image($image_url)
{
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);

    $filename = basename($image_url);
    $title    = preg_replace('/\.[^.]+$/', '', basename($filename));

    add_filter('posts_where', 'marketengine_setup_sample_data_post_where', 10, 2);
    $query = new WP_Query(array(
        'post_status' => array('inherit', 'publish'),
        'post_type'   => 'attachment',
        's'           => $title,
    ));

    if ($query->have_posts()) {
        return $query->post->ID;
    }

    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    file_put_contents($file, $image_data);
    $wp_filetype = wp_check_filetype($filename, null);
    $attachment  = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_status'    => 'auto',
        'post_content'   => '',
        'post_status'    => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $file);
    require_once ABSPATH . '/wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    return $attach_id;
}
/**
 * Add sample listing
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_add_sample_listing()
{

    $listing_number = $_POST['number'];

    $listing = include MARKETENGINE_PATH . '/sample-data/listing/listing-' . $listing_number . '.php';

    add_filter('posts_where', 'marketengine_setup_sample_data_post_where', 10, 2);
    $query = new WP_Query(array(
        'post_status' => array('inherit', 'publish'),
        'post_type'   => 'listing',
        's'           => $listing['post_title'],
    ));

    if ($query->have_posts()) {
        return array('id' => $query->post->ID);
    }

    $user_id                = marketengine_add_sample_user($listing['post_author']);
    $listing['post_author'] = $user_id;

    $cats = $listing['listing_category'];

    $cat_1 = wp_insert_term($cats[0], 'listing_category');
    if (!is_wp_error($cat_1)) {
        $cat_id_1 = $cat_1['term_id'];
    } else {
        $cat_id_1 = $cat_1->error_data['term_exists'];
    }

    $cat_2 = wp_insert_term($cats[1], 'listing_category', array('parent' => $cat_id_1));
    if (!is_wp_error($cat_2)) {
        $cat_id_2 = $cat_2['term_id'];
    } else {
        $cat_id_2 = $cat_2->error_data['term_exists'];
    }

    $listing['tax_input'] = array(
        'listing_tag'      => $listing['listing_tag'],
        'listing_category' => array($cat_id_1, $cat_id_2),
    );

    $img_1 = marketengine_handle_sample_image(MARKETENGINE_URL . 'sample-data/images/dell.jpg', $listing['post_name'] . '-1');
    $img_2 = marketengine_handle_sample_image(MARKETENGINE_URL . 'sample-data/images/macbook.jpg', $listing['post_name'] . '-2');
    $img_3 = marketengine_handle_sample_image(MARKETENGINE_URL . 'sample-data/images/samsung.jpg', $listing['post_name'] . '-3');

    $listing_gallery = array(
        $img_1,
        $img_2,
        $img_3,
    );

    shuffle($listing_gallery);
    $listing['listing_gallery'] = $listing_gallery;

    $listing['meta_input']['_thumbnail_id'] = $listing['listing_gallery'][0];

    $result = wp_insert_post($listing);

    update_post_meta($result, '_me_listing_gallery', $listing['listing_gallery']);
    update_post_meta($result, 'is_sample_data', 'sample-data');

    if (!empty($listing['order'])) {
        marketengine_add_sample_order($listing['order'], $result);
    }

    if (!empty($listing['inquiry'])) {
        marketengine_add_sample_inquiry($listing['inquiry'], $result);
    }
    echo 1;
    exit;
}

/**
 * Ajax Delete sample data
 * @package Admin/Setupwizard
 * @category Function
 * @since 1.0
 */
function marketengine_delete_sample_data()
{
    if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'marketengine-setup')) {
        global $wpdb;

        // delete message
        $message_table = $wpdb->prefix . 'marketengine_message_item';
        $message_meta_table = $wpdb->prefix . 'marketengine_message_itemmeta';
        $wpdb->query("DELETE from $message_table WHERE ID IN ( SElECT marketengine_message_item_id FROM $message_meta_table WHERE meta_key = 'is_sample_data' )");

        $post_ids  = $wpdb->get_results("SElECT marketengine_message_item_id FROM $message_meta_table WHERE meta_key = 'is_sample_data'", ARRAY_A);
        $post_list = '0';
        foreach ($post_ids as $key => $value) {
            $post_list .= ', ' . $value['marketengine_message_item_id'];
        }
        $wpdb->query("DELETE from $message_meta_table  WHERE marketengine_message_item_id IN ( " . $post_list . " )");

        // delte sample listing
        $wpdb->query("DELETE from $wpdb->posts WHERE ID IN ( SElECT post_id FROM $wpdb->postmeta WHERE meta_key = 'is_sample_data' )");
        $post_ids  = $wpdb->get_results("SElECT post_id FROM $wpdb->postmeta WHERE meta_key = 'is_sample_data'", ARRAY_A);
        $post_list = '0';
        foreach ($post_ids as $key => $value) {
            $post_list .= ', ' . $value['post_id'];
        }
        $wpdb->query("DELETE from $wpdb->postmeta  WHERE post_id IN ( " . $post_list . " )");

        // delete order item
        $order_item = $wpdb->prefix . 'marketengine_order_items';
        $order_item_meta = $wpdb->prefix . 'marketengine_order_itemmeta';
        $order_item_ids  = $wpdb->get_results("SElECT order_item_id FROM $order_item WHERE order_id IN ( " . $post_list . " )", ARRAY_A);
        $order_item_list = '0';
        foreach ($order_item_ids as $key => $value) {
            $order_item_list .= ', ' . $value['order_item_id'];
        }
        // delete order itemmeta
        $wpdb->query("DELETE from $order_item_meta  WHERE marketengine_order_item_id IN ( " . $order_item_list . " )");
        // delete order item
        $wpdb->query("DELETE from $order_item  WHERE order_id IN ( " . $post_list . " )");

        // delete sample review
        $wpdb->query("DELETE from $wpdb->comments WHERE comment_ID IN ( SElECT comment_id FROM $wpdb->commentmeta as B WHERE B.meta_key = 'is_sample_data' )");
        $comment_ids  = $wpdb->get_results("SElECT comment_id FROM $wpdb->commentmeta as B WHERE B.meta_key = 'is_sample_data'", ARRAY_A);
        $comment_list = '0';
        foreach ($comment_ids as $key => $value) {
            $comment_list .= ', ' . $value['comment_id'];
        }
        $wpdb->query("DELETE from $wpdb->commentmeta  WHERE comment_id IN ( " . $comment_list . " )");


        // delete sample user
        $wpdb->query("DELETE from $wpdb->users WHERE ID IN ( SElECT user_id FROM $wpdb->usermeta WHERE meta_key = 'is_sample_data')");

        $user_id   = $wpdb->get_results("SElECT user_id FROM $wpdb->usermeta as B WHERE B.meta_key = 'is_sample_data'", ARRAY_A);
        $user_list = '0';
        foreach ($user_id as $key => $value) {
            $user_list .= ', ' . $value['user_id'];
        }
        $wpdb->query("DELETE from $wpdb->usermeta  WHERE user_id IN ( " . $user_list . " )");

        delete_option('me-added-sample-data');
        wp_delete_comment( 1 );
        echo 1;
        exit;
    }
}
add_action('wp_ajax_me-remove-sample-data', 'marketengine_delete_sample_data');
