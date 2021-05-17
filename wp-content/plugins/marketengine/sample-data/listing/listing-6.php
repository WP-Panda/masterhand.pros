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
        'listing_price'    => 538,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name'        => 'marketengine-sample-listing-6',
    'post_title'       => '7 TheiaPro App Enabled EyeGlasses Camera(Black)',
    'post_content'     =>
    "
<p>
    TheiaPro is equipped with a state of the art HD Camera. No more pulling out a device, opening app and focusing on camera to capture an image. Just look and capture with a press of a button.
</p>
<p>
    Comes with a built-in flashlight, letting you make the most of your travel expeditions. Be it hiking or just a backyard night-out you can now focus on it, and not worry about searching flashlight or your smartphone app
</p>
<p>
    Theia Pro comes with 3 MP Camera, 1080p FULL HD Video Recording, Wi-Fi Remote Control (10m), Expandable Memory (up to 32 GB)
</p>
<p>
    Ergonomic Design and easy DIY installation with your existing glasses or shades
</p>
"
    ,

    'listing_gallery'  => array
    (
        '0' => 'listing-2.jpg',
        '1' => 'listing-1.jpg',
    ),

    'listing_category' => array(
        'Wearable Technology', 'Smart Glasses',
    ),

    'listing_tag'      => 'selling tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish',
    'order' => array(
        array(
            'user_login'   => 'alanroger',
            'first_name'   => 'Alan',
            'last_name'    => 'Roger',
            'user_email'   => 'alanroger@mailinator.com',
            'location'     => 'France',
            'user_pass'    => '123',
            'avatar'       => 'avatar-2.jpg',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
            'review'       => array(
                'content' => 'Received the wrong color.',
                'rate'    => 3,
            ),
        ),
    )
);
