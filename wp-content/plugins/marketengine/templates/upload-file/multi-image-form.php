<?php
/**
 * 	The Template for displaying file uploader.
 * 	This template can be overridden by copying it to yourtheme/marketengine/upload-file/multi-image-form.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
?>
<li class="me-item-img" title="<?php _e("Drag to sort", "enginethemes"); ?>">
	<span class="me-gallery-img">
	    <input type="hidden" name="<?php echo esc_attr($filename); ?>[]" value="<?php echo esc_attr($image_id) ?>">
	    <img src="<?php echo $image_url; ?>" alt="">
		<a class="me-delete-img remove"></a>
	</span>
</li>