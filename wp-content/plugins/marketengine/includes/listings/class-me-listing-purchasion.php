<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ME_Listing_Purchasion extends ME_Listing{
    /**
     * @var int $stock
     * the number of good regularly available for sale.
     */
    public $stock;
    /**
     * @var double $price
     */
    public $price;
    /**
     * @var array $shipping
     * shipping fee info
     */
    public $shipping;
	/**
     * The single instance of the class.
     *
     * @var ME_Listing
     * @since 1.0
     */

    /**
     * Constructor.
     *
     * @param ME_Listing|object $post Post object.
     */
    public function __construct($post) {
        foreach (get_object_vars($post) as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get_price() {
        return get_post_meta($this->ID, 'listing_price', true);
    }

    public function get_unit() {
        return get_post_meta($this->ID, 'pricing_unit', true);
    }

    public function get_pricing_unit() {
        $pricing_text = array(
            '0' => '',
            'none' => '',
            'per_unit' => __("/Unit", "enginethemes"),
            'per_hour' => __("/Hour", "enginethemes")
        );
        return isset($pricing_text[get_post_meta($this->ID, 'pricing_unit', true)]) ? $pricing_text[get_post_meta($this->ID, 'pricing_unit', true)] : '';   
    }

    public function is_downloadable() {
        return get_post_meta($this->ID, 'marketengine_is_downloadable', true);
    }

    public function is_in_stock() {
        return get_post_meta($this->ID, 'marketengine_is_in_stock', true);
    }

    public function get_shipping_fee() {
        return 10;
    }
}