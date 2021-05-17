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
        'listing_price'    => 538,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name'        => 'marketengine-sample-listing-5',
    'post_title'       => 'iPhone 7 Plus Screen Protector, Yootech [2-Pack] iPhone 7 Plus Tempered Glass Screen Protector Only for Apple iphone 7 Plus',
    'post_content'     =>
    "
<p>
   Ultra-clear with 99.9% transparency to allow an optimal, natural viewing experience
</p>
<p>
    Ultra thin-0.3mm thickness is reliable and resiliant, and promises full compatibility with touchscreen sensitivity
</p>
<p>
    Lifetime no-hassle warranty provides easy lifetime protection for your tempered glass screen protector.
</p>
<p>
    Includes: 2x iPhone 7 Plus Tempered Glass Screen Protector, Wet/Dry Wipes, Dust Removal Stickers, Installation manual. 100% Risk-free, Lifetime Replacement Warranty.
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
        'Computers & Tablets', 'Accessories',
    ),

    'listing_tag'      => 'selling tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish',
    'order'            => array(
        array(
            'user_login'   => 'shindaiki',
            'first_name'   => 'Shin',
            'last_name'    => 'Daiki',
            'user_email'   => 'shindaiki@mailinator.com',
            'location'     => 'Japan',
            'user_pass'    => '123',
            'avatar'       => 'avatar-2.jpg',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'It is a great laptop but the back light bleed kills it. It is only a small portion in the upper left-hand panel but that is unacceptable for a laptop that costs $2000. I am thinking of returning it for this reason',
                'rate'    => 3,
            ),
        ),
    ),
);
