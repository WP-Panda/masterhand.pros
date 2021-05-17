<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function marketengine_add_to_cart($item, $qty) {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    marketengine_empty_cart();
    $me_cart = ME()->session->get('marketengine_carts', array());
    /**
     * marketengine_add_to_cart
     *
     * @param int $item
     * @since 1.0
     */
    $item            = apply_filters('marketengine_add_to_cart', $item);
    $me_cart['item'][$item] = array('id' => $item, 'qty' => $qty);
    ME()->session->set('marketengine_carts', $me_cart);
}

function marketengine_get_cart_items() {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    $me_cart = ME()->session->get('marketengine_carts', array());

    if (empty($me_cart)) {
        return false;
    }
    return $me_cart['item'];
}

function marketengine_empty_cart() {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    $me_cart = ME()->session->get('marketengine_carts', array());
    // empty cart
    ME()->session->set('marketengine_carts', array());
}