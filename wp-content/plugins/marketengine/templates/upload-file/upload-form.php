<div id="<?php echo esc_attr($id); ?>" class="me-upload-wrapper <?php if(!$multi) { echo 'me-single-image';} ?>">
    <div class="upload_preview_container">
        <ul class="marketengine-gallery-img">
            <?php
            if($source) {
                if(!$multi) {
                    marketengine_get_template('upload-file/single-image-form', array(
                        'image_id' => $source,
                        'filename' => $name,
                        'close' => $close
                    ));
                } else {
                    echo $source;
                }
            }
            ?>

        </ul>
    </div>
    <div class="upload-container">
        <span id="<?php echo esc_attr($button); ?>" class="<?php echo esc_attr($button); ?> me-gallery-add-img">
            <?php if(isset($button_text)) :  echo $button_text; else : _e("Choose image", "enginethemes");  endif; ?>
        </span>
    </div>
</div>