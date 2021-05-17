<?php
$attached_file = get_attached_file($image_id);
$file_name     = basename($attached_file);
?>
<li class="me-item-img">
	<input type="hidden" name="<?php echo esc_attr($filename); ?>[]" value="<?php echo esc_attr($image_id) ?>">
	 <?php echo $file_name; ?><a class="me-delete-img remove"></a>
</li>