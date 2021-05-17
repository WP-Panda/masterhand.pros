<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

add_filter('wp_mail_content_type', 'marketengine_set_html_content_type');
function marketengine_set_html_content_type() {
    return 'text/html';
}

/**
 * Send complete order email to seller
 * @param int $order_id
 */
function marketengine_complete_order_email($order_id) {
    if (!$order_id) {
        return false;
    }

    $order      = new ME_Order($order_id);
    $commission = 0;

    $receiver_item  = marketengine_get_order_items($order_id, 'receiver_item');
    $commision_item = marketengine_get_order_items($order_id, 'commission_item');

    if (empty($receiver_item)) {
        return false;
    }

    if (!empty($commision_item)) {
        $commission = marketengine_get_order_item_meta($commision_item[0]->order_item_id, '_amount', true);
    }

    $user_name = $receiver_item[0]->order_item_name;

    $seller = get_user_by('login', $user_name);
    if (!$seller) {
        return false;
    }

    $listing_item  = marketengine_get_order_items($order_id, 'listing_item');
    $listing_id    = marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_listing_id', true);
    $listing_price = marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_listing_price', true);

    $subject  = sprintf(__("You have a new order on %s.", "enginethemes"), get_bloginfo('blogname'));
    $currency = $order->get_currency();
    $total    = $order->get_total();

    $order_details = array(
        'listing_link'  => '<a href="' . get_permalink($listing_id) . '" >' . get_the_title($listing_id) . '</a>',
        'listing_price' => marketengine_price_format($listing_price, $currency),
        'unit'          => marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_qty', true),
        'total'         => marketengine_price_format($total, $currency),
        'commission'    => marketengine_price_format($commission, $currency),
        'earning'       => marketengine_price_format(($total - $commission), $currency),
        'order_link'    => '<a href="' . $order->get_order_detail_url() . '" >' . $order->ID . '</a>',
        'currency'      => $currency,
    );

    ob_start();
    marketengine_get_template('emails/seller/order-success',
        array_merge(array(
            'display_name' => get_the_author_meta('display_name', $seller->ID),
            'buyer_name'   => get_the_author_meta('display_name', $order->post_author),
        ), $order_details)
    );
    $seller_message = ob_get_clean();
    wp_mail($seller->user_email, $subject, $seller_message);

    $subject = sprintf(__("Your payment on %s has been accepted", "enginethemes"), get_bloginfo('blogname'));
    $buyer   = get_userdata($order->post_author);
    ob_start();
    marketengine_get_template('emails/buyer/order-success',
        array_merge(array(
            'display_name' => get_the_author_meta('display_name', $order->post_author),
        ), $order_details)
    );
    $buyer_message = ob_get_clean();

    wp_mail($buyer->user_email, $subject, $buyer_message);

    /**
     * mail to admin
     */
    $subject = sprintf(__("New order and commission earning on %s", "enginethemes"), get_bloginfo('blogname'));
    ob_start();
    marketengine_get_template('emails/admin/order-success',
        array_merge(array(
            'display_name' => 'Admin',
            'seller_name'  => get_the_author_meta('display_name', $seller->ID),
            'buyer_name'   => get_the_author_meta('display_name', $order->post_author),
        ), $order_details)
    );
    $admin_message = ob_get_clean();

    wp_mail(get_option('admin_email'), $subject, $admin_message);
}
add_action('marketengine_complete_order', 'marketengine_complete_order_email');

/**
 * Send email to buyer and seller when close order
 * @param int $order_id
 */
function marketengine_close_order_email($order_id) {
    if (!$order_id) {
        return false;
    }

    $order      = new ME_Order($order_id);
    $commission = 0;

    $receiver_item  = marketengine_get_order_items($order_id, 'receiver_item');
    $commision_item = marketengine_get_order_items($order_id, 'commission_item');

    if (empty($receiver_item)) {
        return false;
    }

    if (!empty($commision_item)) {
        $commission = marketengine_get_order_item_meta($commision_item[0]->order_item_id, '_amount', true);
    }

    $user_name = $receiver_item[0]->order_item_name;

    $seller = get_user_by('login', $user_name);
    if (!$seller) {
        return false;
    }

    $listing_item  = marketengine_get_order_items($order_id, 'listing_item');
    $listing_id    = marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_listing_id', true);
    $listing_price = marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_listing_price', true);

    $subject  = sprintf(__("You have a new order on %s.", "enginethemes"), get_bloginfo('blogname'));
    $currency = $order->get_currency();
    $total    = $order->get_total();

    $order_details = array(
        'listing_link'  => '<a href="' . get_permalink($listing_id) . '" >' . get_the_title($listing_id) . '</a>',
        'listing_price' => marketengine_price_format($listing_price, $currency),
        'unit'          => marketengine_get_order_item_meta($listing_item[0]->order_item_id, '_qty', true),
        'total'         => marketengine_price_format($total, $currency),
        'commission'    => marketengine_price_format($commission, $currency),
        'earning'       => marketengine_price_format(($total - $commission), $currency),
        'order_link'    => '<a href="' . $order->get_order_detail_url() . '" >' . $order->ID . '</a>',
        'currency'      => $currency,
        'order_date' => date_i18n( get_option('date_format'), strtotime($order->post_date) )
    );

    $subject = sprintf(__("Your order on %s has been closed", "enginethemes"), get_bloginfo('blogname'));
    ob_start();
    marketengine_get_template('emails/seller/order-closed',
        array_merge(array(
            'display_name' => get_the_author_meta('display_name', $seller->ID),
            'buyer_name'   => get_the_author_meta('display_name', $order->post_author),
        ), $order_details)
    );
    $seller_message = ob_get_clean();
    wp_mail($seller->user_email, $subject, $seller_message);

    $subject = sprintf(__("Your transaction on %s has been closed", "enginethemes"), get_bloginfo('blogname'));
    $buyer   = get_userdata($order->post_author);
    ob_start();
    marketengine_get_template('emails/buyer/order-closed',
        array_merge(array(
            'display_name' => get_the_author_meta('display_name', $order->post_author),
            'seller_name' => get_the_author_meta('display_name', $seller->ID),
        ), $order_details)
    );
    $buyer_message = ob_get_clean();

    wp_mail($buyer->user_email, $subject, $buyer_message);

}
add_action('marketengine_close_order', 'marketengine_close_order_email');

/**
 * Override default email header of WP
 * @param int $message_headers, $comment_ID
 */
function marketengine_filter_receive_comment_email_header( $message_headers, $comment_ID ) {
    ob_start();
    marketengine_get_template('emails/email-header');
    $header = ob_get_clean();
    return $header;
}
// add_filter('comment_notification_headers', 'marketengine_filter_receive_comment_email_header');

/**
 * Override default email content of WP
 * @param int $notify_message, $comment_ID
 */
function marketengine_filter_receive_comment_email_content( $notify_message, $comment_ID ) {
    $comment = get_comment($comment_ID);

    if( empty($comment->comment_type) ) {
        ob_start();
        marketengine_get_template('emails/receive-comment', array( 'notify_message' => $notify_message, 'comment' => $comment) );
        $notify_message = ob_get_clean();
    }

    return $notify_message;
}
add_filter( 'comment_moderation_text', 'marketengine_filter_receive_comment_email_content', 1, 2 );
add_filter( 'comment_notification_text', 'marketengine_filter_receive_comment_email_content', 1, 2 );
