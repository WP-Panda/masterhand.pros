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
        'listing_price'    => 380,
        'pricing_unit'     => 'per_unit',
        '_me_listing_type' => 'purchasion',
    ),

    'post_name'        => 'marketengine-sample-listing-7',
    'post_title'       => 'Canon EOS Rebel T5 Digital SLR Camera Kit with EF-S 18-55mm IS II Lens',
    'post_content'     =>
    "
<p>
   18 megapixel CMOS (APS-C) sensor with DIGIC 4 image processor
</p>
<p>
    EF-S 18-55mm IS II standard zoom lens expands picture-taking possibilities
</p>
<p>
    3-inch LCD TFT color, liquid-crystal monitor for easy viewing and sharing
</p>
<p>
    Features include continuous shooting up to 3fps, Scene Intelligent Auto mode, creative filers, built-in flash and feature guide
</p>
"
    ,

    'listing_gallery'  => array
    (
        '0' => 'listing-2.jpg',
        '1' => 'listing-1.jpg',
    ),

    'listing_category' => array(
        'Camera, Photo & Video', 'Cameras',
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
