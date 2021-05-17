<?php
$listing_image = !empty($_POST['listing_image']) ? absint( $_POST['listing_image'] ) : $listing_image;
?>
<div class="marketengine-group-field">

    <?php do_action('marketengine_before_post_listing_image_form');?>

    <div class="marketengine-upload-field">

        <label class="me-field-title" for="upload_company_gallery"><?php _e('Your listing image', 'enginethemes'); ?></label>
        <?php
        marketengine_get_template('upload-file/upload-form', array(
            'id' => 'upload_listing_image',
            'name' => 'listing_image',
            'source' => $listing_image,
            'button' => 'btn-listing-image',
            'multi' => false,
            'maxsize' => esc_html( '2mb' ),
            'maxcount' => 1,
            'close' => true
        ));
        ?>
    </div>

    <?php do_action('marketengine_after_post_listing_image_form');?>

</div>