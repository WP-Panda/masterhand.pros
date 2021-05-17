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
    'post_author'      =>
array(
        'user_login'   => 'henrywilson',
        'first_name'   => 'Henry',
        'last_name'    => 'Wilson',
        'user_email'   => 'henrywilson@mailinator.com',
        'location'     => 'UK',
        'user_pass'    => '123',
        'avatar'       => 'avatar-1.jpg',
        'paypal_email' => 'dinhle1987-buyer@yahoo.com',
    ),
    'meta_input'       => array
    (
        'listing_price'    => 8,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name'        => 'marketengine-sample-listing-3',
    'post_title'       => 'AmazonBasics Apple Certified Lightning to USB Cable - 6 Feet (1.8 Meters) - White',
    'post_content'     =>
        "
<p>
    Apple MFi certified charging and syncing cable for your Apple devices
</p>
<p>
    Apple MFi certification ensures complete charge and sync compatibility with iPhone 7 Plus / 7 / 6s Plus / 6s / 6 Plus / 6 / 5s / 5c / 5 / iPad Pro / iPad Air / Air 2 / iPad mini / mini 2 / mini 4 / iPad 4th gen / iPod Touch 5th gen / iPod nano 7th gen and Beats Pill+
</p>
<p>
    Connects to your iPhone, iPad, or iPod with Lightning Connector and charges/syncs by connecting the USB connector into your wall charger or computer
    <br/>
    Compact Lightning Connector head works with nearly all cases
</p>
<p>
    With fast charging, get up to 10.5 hours (1) on a single charge
</p>
<p>
    An additional layer of protection has been added to the Lightning and USB ends to improve durability and reduce fraying; Cables have been tested to bend 95-degrees 4,000 times
</p>
<p>
    Backed by an AmazonBasics 1-Year Limited Warranty
</p>
",

    'listing_gallery'  => array
    (
        '0' => 'listing-2.jpg',
        '1' => 'listing-1.jpg',
    ),

    'listing_category' => array(
        'Computers & Tablets', 'Computer Accessoriess',
    ),

    'listing_tag'      => 'selling tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish',

    'order'            => array(
        array(
            'user_login'   => 'katelinharper',
            'first_name'   => 'Katelin',
            'last_name'    => 'Harper',
            'user_email'   => 'katelinharper@mailinator.com',
            'location'     => 'UK',
            'user_pass'    => '123',
            'avatar'       => 'avatar-2.jpg',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'Cracked already and leaves so many hand prints on it. The whole frosting on the case is disappearing slowly. Poor product.',
                'rate'    => 1,
            ),
        ),
    ),
);
