<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Order
{
    /**
     * Order ID.
     *
     * @var int
     */
    public $id;
    /**
     * Order ID.
     *
     * @var int
     */
    public $ID;

    /**
     * ID of sender.
     *
     * A numeric string, for compatibility reasons.
     *
     * @var string
     */
    public $post_author = 0;

    /**
     * The Order's local publication time.
     *
     * @var string
     */
    public $post_date = '0000-00-00 00:00:00';

    /**
     * The Order's GMT publication time.
     *
     * @var string
     */
    public $post_date_gmt = '0000-00-00 00:00:00';

    /**
     * The Order's content.
     *
     * @var string
     */
    public $post_content = '';

    /**
     * The Order's title.
     *
     * @var string
     */
    public $post_title = '';

    /**
     * The Order's excerpt.
     *
     * @var string
     */
    public $post_excerpt = '';

    /**
     * The Order's status.
     *
     * @var string
     */
    public $post_status = 'sent';

    /**
     * The Order's password in plain text.
     *
     * @var string
     */
    public $post_password = '';

    /**
     * The Order's slug.
     *
     * @var string
     */
    public $post_name = '';

    /**
     * The Order's local modified time.
     *
     * @var string
     */
    public $post_modified = '0000-00-00 00:00:00';

    /**
     * The Order's GMT modified time.
     *
     * @var string
     */
    public $post_modified_gmt = '0000-00-00 00:00:00';

    /**
     * A utility DB field for Order content.
     *
     *
     * @var string
     */
    public $post_content_filtered = '';

    /**
     * ID of a Order's parent Order.
     *
     * @var int
     */
    public $post_parent = 0;

    /**
     * The unique identifier for a Order, not necessarily a URL, used as the feed GUID.
     *
     * @var string
     */
    public $guid = '';

    /**
     * The Order's type, like post or page.
     *
     * @var string
     */
    public $post_type = 'post';

    /**
     * Stores the Order object's sanitization level.
     *
     * Does not correspond to a DB field.
     *
     * @var string
     */
    public $filter;
    /**
     *
     */
    public function __construct($order_id = 0)
    {

        if (is_numeric($order_id)) {
            $order_id = (int) $order_id;
            $post     = get_post($order_id);
        } else {
            $post = $order_id;
        }

        if (!$post || $post->post_type != 'me_order') {
            return false;
        }


        foreach (get_object_vars($post) as $key => $value) {
            $this->$key = $value;
        }

        $this->id    = $this->ID;
        $this->order = $post;

        $this->caculate_subtotal();
        $this->caculate_total();
    }

    public function __get($name)
    {
        if (strrpos($name, 'billing') !== false) {
            $billing_address = $this->get_address('billing');
            $name            = str_replace('billing_', '', $name);
            if (isset($billing_address[$name])) {
                return $billing_address[$name];
            } else {
                return '';
            }
        }

        if (strrpos($name, 'shipping') !== false) {
            $shipping = $this->get_address('shipping');
            $name     = str_replace('shipping_', '', $name);
            if (isset($shipping[$name])) {
                return $shipping[$name];
            } else {
                return '';
            }
        }

        return '';
    }

    public function has_status($status)
    {
        if (is_array($status)) {
            return in_array($this->post_status, $status);
        }
        return $this->post_status === $status;
    }

    public function get_confirm_url()
    {
        return marketengine_get_order_url('confirm_order', 'order-id', $this->id);
    }

    public function get_order_detail_url()
    {
        return get_the_permalink($this->id);
    }

    public function get_cancel_url()
    {
        return marketengine_get_order_url('cancel_order', 'order-id', $this->id);
    }

    /**
     * Retrieve Order Currency Code
     * @return String
     * @since 1.0
     */
    public function get_currency_code()
    {
        return get_post_meta($this->ID, '_order_currency_code', true);
    }

    public function get_currency()
    {
        return get_post_meta($this->ID, '_order_currency', true);
    }

    /**
     * Get the seller info
     *
     * @since 1.1
     * @return WP_User | WP_Error
     */
    public function get_seller()
    {
        $receiver_item = marketengine_get_order_items($this->id, 'receiver_item');
        $user_name     = $receiver_item[0]->order_item_name;
        $user          = get_user_by('login', $user_name);
        return $user;
    }

    /**
     * Add listing item to order details
     *
     * @param ME_Listing $listing The listing object
     * @param int $qty
     *
     * @since 1.0
     * @return int|bool Return order item id if sucess, if not success return false
     */
    public function add_listing($listing, $qty = 1)
    {
        if (!is_object($listing)) {
            return false;
        }

        $order_item_id = marketengine_add_order_item($this->id, get_the_title($listing->ID), 'listing_item');
        if ($order_item_id) {
            marketengine_add_order_item_meta($order_item_id, '_listing_id', $listing->ID);
            marketengine_add_order_item_meta($order_item_id, '_listing_description', $listing->post_content);

            marketengine_add_order_item_meta($order_item_id, '_qty', $qty);
            marketengine_add_order_item_meta($order_item_id, '_listing_price', $listing->get_price());
        }

        $seller = get_userdata($listing->post_author);

        $this->caculate_subtotal();
        $this->caculate_total();

        // TODO: neu add nhieu listing thi de o day khong on
        $receiver_0 = (object) array(
            'user_name'  => $seller->user_login,
            'email'      => get_user_meta($seller->ID, 'paypal_email', true),
            'amount'     => $this->get_total(),
            'is_primary' => false,
        );

        $this->add_receiver($receiver_0);

        return $order_item_id;
    }

    /**
     * Update listing item
     *
     * @param int $item_id The order item id
     * @param ME_Listing $listing The listing object
     * @param array $args
     *
     * @since 1.0
     */
    public function update_listing($item_id, $args)
    {
        $item_id = absint($item_id);

        if (!$item_id) {
            return false;
        }

        if (isset($args['qty'])) {
            marketengine_update_order_item_meta($item_id, '_qty', $args['qty']);
        }

        if (isset($args['price'])) {
            marketengine_update_order_item_meta($item_id, '_listing_price', $args['price']);
        }

        $this->caculate_subtotal();
        $this->caculate_total();

        return $item_id;
    }
    /**
     * Update order listing when it is pending
     */
    public function update_listings()
    {
        if ($this->has_status('me-pending')) {
            $listing_items = $this->get_listing_items();
            foreach ($listing_items as $key => $item) {
                // update listing item price
                $listing = marketengine_get_listing($item['ID']);
                if ($listing && $listing->is_available()) {
                    $this->update_listing($item['order_item_id'], array('price' => $listing->get_price()));
                }
                // listing da bi xoa hoac ko ban nua
            }
        }
    }

    /**
     * Retrieve ordered listing items
     * @return array
     * @since 1.0
     */
    public function get_listing_items()
    {
        $order_listing_item = marketengine_get_order_items($this->id, 'listing_item');
        $listing_items      = array();
        if (!empty($order_listing_item)) {
            foreach ($order_listing_item as $key => $item) {
                $id                 = marketengine_get_order_item_meta($item->order_item_id, '_listing_id', true);
                $listing_items[$id] = array(
                    'ID'            => $id,
                    'title'         => $item->order_item_name,
                    'qty'           => marketengine_get_order_item_meta($item->order_item_id, '_qty', true),
                    'price'         => marketengine_get_order_item_meta($item->order_item_id, '_listing_price', true),
                    'description'   => marketengine_get_order_item_meta($item->order_item_id, '_listing_description', true),
                    'order_item_id' => $item->order_item_id,
                );
            }
        }
        return $listing_items;
    }

    /**
     * Get listing item
     *
     * @param string $type Order id
     *
     * @since 1.0
     *
     * @return listing item
     */
    public function get_listing()
    {
        $order_listing_item = marketengine_get_order_items($this->id, 'listing_item');
        $listing_item       = marketengine_get_order_item_meta($order_listing_item[0]->order_item_id);
        return $listing_item;
    }

    /**
     * Add receiver to order details
     *
     * @param ME_User $receiver The ME user object
     *
     * @since 1.0
     * @return int|bool Return order item id if sucess, if not success return false
     */
    public function add_receiver($receiver)
    {
        if (!is_object($receiver)) {
            return false;
        }

        $order_item_id = marketengine_add_order_item($this->id, $receiver->user_name, 'receiver_item');
        if ($order_item_id) {
            marketengine_add_order_item_meta($order_item_id, '_receive_email', $receiver->email);
            marketengine_add_order_item_meta($order_item_id, '_is_primary', $receiver->is_primary);
            marketengine_add_order_item_meta($order_item_id, '_amount', $receiver->amount);
        }
        return $order_item_id;
    }

    /**
     * Update receiver item
     *
     * @param int $item_id The order item id
     * @param ME_User $receiver The ME user object
     *
     * @since 1.0
     * @return int|bool Return order item id if sucess, if not success return false
     */
    public function update_receiver($item_id, $receiver)
    {
        $order_item_id = absint($item_id);

        if (!$order_item_id) {
            return false;
        }

        marketengine_update_order_item_meta($order_item_id, '_receive_email', $receiver->email);
        marketengine_update_order_item_meta($order_item_id, '_is_primary', $receiver->is_primary);
        marketengine_update_order_item_meta($order_item_id, '_amount', $receiver->amount);

        return $order_item_id;
    }

    public function add_commission($receiver)
    {
        if (!is_object($receiver)) {
            return false;
        }

        $order_item_id = marketengine_add_order_item($this->id, $receiver->user_name, 'commission_item');
        if ($order_item_id) {
            marketengine_add_order_item_meta($order_item_id, '_receive_email', $receiver->email);
            marketengine_add_order_item_meta($order_item_id, '_is_primary', $receiver->is_primary);
            marketengine_add_order_item_meta($order_item_id, '_amount', $receiver->amount);
        }
        return $order_item_id;
    }

    public function update_commission($item_id, $receiver)
    {
        $order_item_id = absint($item_id);

        if (!$order_item_id) {
            return false;
        }

        marketengine_update_order_item_meta($order_item_id, '_receive_email', $receiver->email);
        marketengine_update_order_item_meta($order_item_id, '_is_primary', $receiver->is_primary);
        marketengine_update_order_item_meta($order_item_id, '_amount', $receiver->amount);

        return $order_item_id;
    }

    /**
     * Get order address
     *
     * @param string $type The address type
     *
     * @since 1.0
     *
     * @return array Array of address details
     */
    public function get_address($type)
    {
        $address_fields = array('first_name', 'last_name', 'phone', 'email', 'address', 'city', 'country', 'postcode');
        $address        = array();
        foreach ($address_fields as $field) {
            $address[$field] = get_post_meta($this->id, '_me_' . $type . '_' . $field, true);
        }
        return $address;
    }

    /**
     * Set order address
     *
     * @param array $address The address details
     * @param string $type The address type
     *
     * @since 1.0
     *
     * @return array Array of address details
     */
    public function set_address($address, $type = 'billing')
    {
        $address_fields = array('first_name', 'last_name', 'phone', 'email', 'postcode', 'address', 'city', 'country');
        foreach ($address_fields as $field) {
            if (isset($address[$field])) {
                update_post_meta($this->id, '_me_' . $type . '_' . $field, sanitize_text_field( $address[$field] ));
            }
        }
    }

    public function add_shipping($shipping_name)
    {
        update_post_meta($this->id, '_shipping_method', $shipping_name);
        $this->shipping_info['name'] = $shipping_name;
        $this->caculate_total();
    }

    public function update_shipping($item_id, $args)
    {

    }

    /**
     * Add order fee
     * @param array $fee
     *  - name The fee name
     *  - title The fee title
     *  - amount The amount of fee
     *
     * @since 1.0
     * @return int
     */
    public function add_fee($fee)
    {
        $item_id = marketengine_add_order_item($this->id, $fee['name'], '_order_fee');
        if ($item_id) {
            marketengine_add_order_item_meta($item_id, '_fee_amount', $fee['amount']);
            marketengine_add_order_item_meta($item_id, '_fee_title', $fee['title']);
        }
        $this->caculate_total();
        return $item_id;
    }

    /**
     * Add order fee
     *
     * @param int $item_id The fee item id want to update
     * @param array $fee
     *  - name The fee name
     *  - title The fee title
     *  - amount The amount of fee
     *
     * @since 1.0
     * @return int
     */
    public function update_fee($item_id, $args)
    {
        if (!empty($args['name'])) {
            marketengine_update_order_item($item_id, array('order_item_name' => $args['name']));
        }

        $fee_attrs = array('title', 'amount');
        foreach ($fee_attrs as $fee_attr) {
            if (!empty($args[$fee_attr])) {
                marketengine_update_order_item_meta($item_id, '_fee_' . $fee_attr, $args[$fee_attr]);
            }
        }

        $this->caculate_total();
        return $item_id;
    }

    /**
     * Calculate the total amount of product in the order
     *
     * @since 1.0
     * @return int
     */
    public function caculate_subtotal()
    {
        $listing_items = marketengine_get_order_items($this->id, 'listing_item');
        $subtotal      = 0;

        foreach ($listing_items as $key => $item) {
            $price = marketengine_get_order_item_meta($item->order_item_id, '_listing_price', true);
            $qty   = marketengine_get_order_item_meta($item->order_item_id, '_qty', true);
            $subtotal += $price * $qty;
        }

        $this->subtotal = $subtotal;
        update_post_meta($this->id, '_order_subtotal', $subtotal);
        return $this->subtotal;
    }

    public function caculate_fee()
    {
        return 0;
    }

    /**
     * Calculate the total shipping cost of order
     *
     * @since 1.0
     * @return double
     */
    public function caculate_shipping()
    {
        if (!empty($this->shipping_info['name'])) {
            $shipping_class = marketengine_get_shipping_class($this->shipping_info['name'], $this);
            return $shipping_class->caculate_fee();
        }
        return 0;
    }

    /**
     * Caculate order total: subtotal, shipping fee, order fee
     * @since 1.0
     * @return double
     */
    public function caculate_total()
    {
        $this->shipping_fee = $this->caculate_shipping();
        $this->payment_fee  = $this->caculate_fee();
        $this->total        = $this->subtotal + $this->shipping_fee + $this->payment_fee;

        update_post_meta($this->id, '_order_total', $this->total);
        return $this->total;
    }

    public function get_total()
    {
        return get_post_meta($this->id, '_order_total', true);
    }

    /**
     * Retrieve the order transaction id
     * @since 1.0
     * @return string
     */
    public function get_transaction_id()
    {
        return get_post_meta($this->id, '_me_transation_id', true);
    }

    /**
     * Retrieve the payment key associated with the payment in payment gateway
     * @since 1.0
     * @return string
     */
    public function get_payment_key()
    {
        return get_post_meta($this->id, '_me_payment_key', true);
    }

    public function get_order_details()
    {

    }

    public function get_order_number()
    {
        return $this->id;
    }

    public function get_buyer()
    {

    }

    public function get_payment_info()
    {

    }

    public function set_payment_method($payment)
    {
        if (is_object($payment)) {
            update_post_meta($this->id, '_me_payment_gateway', $payment->name);
            update_post_meta($this->id, '_me_gateway_title', $payment->title);
        } else {
            update_post_meta($this->id, '_me_payment_gateway', $payment);
            update_post_meta($this->id, '_me_gateway_title', $payment);
        }
    }

    public function get_payment_method()
    {
        return get_post_meta($this->id, '_me_payment_gateway', true);
    }

    public function get_dispute_time_limit()
    {
        $remaining = 0;
        if ($this->has_status('me-complete')) {

            $order_close_date = strtotime(get_post_meta($this->id, '_me_order_closed_time', true));
            $now              = time();

            $remaining = $order_close_date - $now;
            if ($remaining < 0) {
                return false;
            }

            return human_time_diff($now, $order_close_date);

        }

        return false;
    }

}
