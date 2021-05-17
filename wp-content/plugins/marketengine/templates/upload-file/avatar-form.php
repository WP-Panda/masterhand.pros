<div id="<?php echo esc_attr($id); ?>" class="me-upload-wrapper <?php if(!$multi) { echo 'me-single-image';} ?>">
    <div class="upload_preview_container">
        <ul class="marketengine-gallery-img">
            <?php
            if($source) {
                marketengine_get_template('upload-file/single-image-form', array(
                    'image_id' => $source,
                    'filename' => $name,
                    'close' => $close
                ));
            } else { ?>
                <li class="me-item-img">
                    <span class="me-gallery-img">
                        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0">
                        <?php echo marketengine_get_avatar(get_current_user_id()); ?>
                        <?php if($close): ?>
                            <a class="me-delete-img remove"></a>
                        <?php endif; ?>
                    </span>
                </li>
            <?php }
            ?>
        </ul>
    </div>

    <span id="<?php echo esc_attr($button); ?>" class="<?php echo esc_attr($button); ?> me-gallery-add-img">
        <?php _e("Choose image", "enginethemes"); ?>
    </span>

</div>