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
            'user_login'   => 'karlabertha',
            'first_name'   => 'Karla',
            'last_name'    => 'Bertha',
            'user_email'   => 'karlabertha@mailinator.com',
            'location'     => 'German',
            'user_pass'    => '123',
            'avatar'       => 'avatar-1.png',
            'paypal_email' => 'dinhle1987-per@yahoo.com',
        ),
    'meta_input'       => array
    (
        '_me_listing_type' => 'contact',
    ),

    'post_name'        => 'marketengine-sample-listing-11',
    'post_title'       => 'TV Connect with TV Mounting Service and Basic Audio Setup For TVs 50" and Smaller',
    'post_content'     =>
        "
<h6>
    Transferring your data
</h6>
<p>
    We'll back up or transfer your data to one medium or device (including DVDs, NAS, hard drive, etc.). You will need to supply the backup device unless you opt for a cloud-based backup service.
</p>
<h6>
    Keeping it organized
</h6>
<p>
    We will make sure the data maintains the same folder structure as the previous location. (For example, documents will go into the My Documents folder.)
</p>
<h6>
    Finishing touches
</h6>
<p>
    We'll install and configure included or built-in software for any external hard drive, and we'll help you set up a data backup schedule that fits your needs.
</p>
*This service does not include network device setup or managing file names or tags*.
"
,

    'listing_gallery'  => array
    (
        '0' => 'listing-2.jpg',
        '1' => 'listing-1.jpg',
    ),

    'listing_category' => array(
        'Computers & Tablets', 'Laptops',
    ),

    'listing_tag'      => 'offering tag',
    'post_type'        => 'listing',

    'post_status'      => 'publish'
);
