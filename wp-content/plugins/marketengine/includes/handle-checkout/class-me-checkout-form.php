<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Checkout_Form {
    public static function init_hook() {
        add_action('wp_loaded', array(__CLASS__, 'add_to_cart'));
        add_action('wp_loaded', array(__CLASS__, 'process_checkout'));
        // parse_request
        add_action('wp_loaded', array(__CLASS__, 'confirm_payment'));
    }

    public static function confirm_payment() {
        if (!empty($_GET['me-payment'])) {
            $request = sanitize_text_field(strtolower($_GET['me-payment']));
            do_action('marketegine_' . $request, $_REQUEST);
        }
    }

    public static function add_to_cart() {
        if (isset($_POST['add_to_cart']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-add-to-cart')) {
            
            $current_user_id = get_current_user_id();
            // kiem tra san pham co con duoc ban ko
            $listing_id = absint( $_POST['add_to_cart'] );
            $listing    = marketengine_get_listing($listing_id);            
            // kiem tra san pham co ton tai hay ko
            if(!$listing || !$listing->is_available() || $current_user_id == $listing->post_author) {
                return false;
            }
            // neu co the mua thi dieu huong nguoi dung den trang thanh toa
            marketengine_add_to_cart($listing_id, absint( $_POST['qty'] ));
            wp_redirect(marketengine_get_page_permalink('checkout'));
            exit;
        }
    }

    public static function process_checkout() {
        if (isset($_POST['checkout']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-checkout')) {
            $order = ME_Checkout_Handle::checkout($_POST);
            if (!$order || is_wp_error($order)) {
                marketengine_wp_error_to_notices($order);
            } else {
                marketengine_empty_cart();
                // redirect to payment gateway or confirm payment
                self::process_pay($order);

            }
        }
        // TODO: update order function
        if (isset($_POST['order_id']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-pay')) {
            $order = new ME_Order(absint( $_POST['order_id'] ));
            self::process_pay($order);
        }
    }

    public static function process_pay($order) {
        $result = ME_Checkout_Handle::pay($order);
        if (!$result || is_wp_error($result)) {
            marketengine_wp_error_to_notices($result);
            wp_redirect($order->get_order_detail_url());
            exit;
        } else {
            wp_redirect($result->transaction_url);
            exit;
        }
    }
}
ME_Checkout_Form::init_hook();