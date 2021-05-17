<?php
$image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
?>
<li class="me-item-img">
	<span class="me-gallery-img">
	    <input type="hidden" name="<?php echo esc_attr($filename); ?>" value="<?php echo esc_attr($image_id) ?>">
	    <img src="<?php echo $image_url; ?>" alt="">
		<?php if($close): ?>
			<a class="me-delete-img remove"></a>
		<?php endif; ?>
	</span>
</li>