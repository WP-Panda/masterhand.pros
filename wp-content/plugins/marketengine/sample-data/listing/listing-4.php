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
        'avatar'     => 'avatar-1.png',
        'paypal_email' => 'dinhle1987-buyer@yahoo.com',
    ),
    'meta_input'       => array
    (
        'listing_price'    => 538,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name'        => 'marketengine-sample-listing-4',
    'post_title'       => 'Nextbit Robin Factory Unlocked Phone - Midnight (U.S. Warranty)',
    'post_content'     =>
    "
<p>
    GSM Version (for AT&T; and TMobile networks in the US; best choice for all international backers including Canada)
</p>
<p>
    GSM 850/900/1800/1900 HSPA 850/900/1700/1800/1900/2100 LTE Bands 1/2/3/4/5/7/8/12/17/20/28
</p>
<p>
    Snapdragon 808 Memory: 3GB RAM / 32 GB onboard
</p>
<p>
    100 GB online Screen: 5.2” IPS LCD 1080p, Gorilla Glass 4
</p>
<p>
    3MP with phase detection autofocus, dual tone flash Front camera: 5MP Battery: 2680 mAh Dual front facing stereo speakers Fingerprint
sensor NFC Quick charging Bluetooth 4.0 LE WiFi A/B/G/N/AC
18 new from $169.99 3 used from $180.00
</p>
",

    'listing_gallery'  => array
    (
        '0' => 'listing-2.jpg',
        '1' => 'listing-1.jpg',
    ),

    'listing_category' => array(
        'Computers & Tablets', 'Unlocked Cell Phones',
    ),

    'listing_tag'      => 'selling tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish',
    'order' => array(
        array(
            'user_login'   => 'karlabertha',
            'first_name'   => 'Karla',
            'last_name'    => 'Bertha',
            'user_email'   => 'karlabertha@mailinator.com',
            'location'     => 'German',
            'user_pass'    => '123',
            'avatar'       => 'avatar-2.jpg',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'Great machine but bright and loud.',
                'rate'    => 4,
            ),
        ),
    )
);
