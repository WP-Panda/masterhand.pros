<?php
/**
 * This is data of a sample listing.
 *
 * @author      EngineThemes
 * @package     MarketEngine/SampleData
 * @since       1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

return array(
    'post_author'      => array(
        'user_login' => 'henrywilson',
        'first_name' => 'Henry',
        'last_name'  => 'Wilson',
        'user_email' => 'henrywilson@mailinator.com',
        'location'   => 'UK',
        'user_pass'  => '123',
        'avatar'     => 'avatar-1.jpg',
        'paypal_email' => 'dinhle1987-buyer@yahoo.com',
    ),
    'meta_input'       => array
    (
        'listing_price'    => 1279,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name' => 'marketengine-sample-listing-1',
    'post_title'       => 'Apple MacBook MLH72LL/A 12-Inch Laptop with Retina Display (Space Gray, 256 GB)',
    'post_content'     =>
    	"<p>- 1.1 GHz Dual-Core Intel Core M3 Processor (Turbo Boost up to 2.2 GHz) with 4 MB shared L3 cache. OS Mac OS X 10.11 El Capitan</p>
		<p>- 8 GB of 1866 MHz LPDDR3 RAM; 256 GB PCIe-based onboard flash storage</p>
		<p>- 12-Inch IPS LED-backlit Display; 2304-by-1440 Resolution</p>
		<p>- USB-C port with support for: Charging,USB 3.1 Gen 1 (up to 5 Gbps), Native DisplayPort 1.2 video output, VGA output using USB-C VGA Multiport Adapter (sold separately), HDMI video output using USB-C Digital AV Multiport Adapter (sold separately)
		Intel HD Graphics 515</p>",

    'listing_gallery'  => array
    (
        '0' => 'dell.jpg',
        '1' => 'macbook.jpg',
    ),

    'listing_category' => array(
        'Computers & Tablets', 'Laptops',
    ),

    'listing_tag'      => 'selling tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish',

    'order'            => array(
        array(
            'user_login'   => 'karlabertha',
            'first_name'   => 'Karla',
            'last_name'    => 'Bertha',
            'user_email'   => 'karlabertha@mailinator.com',
            'location'     => 'German',
            'user_pass'    => '123',
            'avatar'       => 'avatar-2.png',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'Great machine but bright and loud.',
                'rate'    => 4,
            )
        ),
        array(
            'user_login'   => 'malenesara',
            'first_name'   => 'Malene',
            'last_name'    => 'Sara',
            'user_email'   => 'malenesara@mailinator.com',
            'location'     => 'Danish',
            'user_pass'    => '123',
            'avatar'       => 'avatar-3.png',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'I really enjoy this laptop and everything about it is very good quality, especially for gaming, except for the fact of the very small crack in the case this computer is awesome.',
                'rate'    => 5,
            ),
        )
    ),
);
