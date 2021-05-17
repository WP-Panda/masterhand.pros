<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$listing_gallery = !empty($_POST['listing_gallery']) ? array_map('absint', $_POST['listing_gallery']) : $listing_gallery;
?>

<?php do_action('marketengine_before_post_listing_picture_form');?>

<div class="marketengine-group-field">

    <?php do_action('marketengine_before_post_listing_gallery_form');?>

    <div class="marketengine-upload-field">
        <label class="me-field-title" for="upload_company_gallery">
            <?php _e('Gallery', 'enginethemes'); ?>
            <span><?php _e("(optional, max 5 files)", "enginethemes"); ?></span>
        </label>
        <?php

        ob_start();
        if($listing_gallery) {
            foreach($listing_gallery as $gallery) {
                marketengine_get_template('upload-file/multi-image-form', array(
                    'image_id' => $gallery,
                    'filename' => 'listing_gallery',
                    'close' => true
                ));
            }
        }
        $listing_gallery = ob_get_clean();

        marketengine_get_template('upload-file/upload-form', array(
            'id' => 'upload_listing_gallery',
            'name' => 'listing_gallery',
            'source' => $listing_gallery,
            'button' => 'me-btn-upload',
            'multi' => true,
            'maxsize' => esc_html( '2mb' ),
            'maxcount' => 5,
            'close' => true
        ));
        ?>
    </div>

    <?php do_action('marketengine_after_post_listing_gallery_form');?>

</div>

<?php do_action('marketengine_after_post_listing_picture_form');?>

