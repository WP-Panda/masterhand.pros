<?php
/**
 * MarketEngine Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package MarketEngine/Includes
 * @category Function
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

include_once ('widgets/class-me-widget-listing-categories.php');
include_once ('widgets/class-me-widget-listing-types.php');
include_once ('widgets/class-me-widget-price-filter.php');
include_once ('widgets/class-me-widget-listing-search.php');

/**
 * Registers MarketEngine Widgets
 *
 * @since 1.0.0
 */
function marketengine_register_widgets() {
    register_widget('ME_Widget_Listing_Categories');
    register_widget('ME_Widget_Listing_Types');
    register_widget('ME_Widget_Price_Filter');
    register_widget('ME_Widget_Search');
}
add_action('widgets_init', 'marketengine_register_widgets');