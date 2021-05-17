<?php
/**
 * 	The Template for displaying file uploader.
 * 	This template can be overridden by copying it to yourtheme/marketengine/upload-file/multi-file.php.
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
$attached_file = get_attached_file($image_id);
$file_name     = basename($attached_file);
$image_url 	   = wp_get_attachment_image_url( $image_id, 'thumbnail' );
$file_type     = wp_check_filetype($file_name);
$file_icon = '';

switch ($file_type['ext']) {
	case 'jpeg':
	case 'png':
	case 'jpeg':
	case 'jpg' :
	case 'gif' :
		break;
	case 'pdf' :
		$file_icon  = '<i class="icon-me-file-pdf"></i>';
		break;
	case 'docx' :
	case 'doc' :
		$file_icon = '<i class="icon-me-file-doc"></i>';
		break;
	case 'xls':
	case 'xlsx' :
		$file_icon = '<i class="icon-me-file-excel"></i>';
		break;
	default:
		$file_icon = '<i class="icon-me-file-code" ></i>';
		break;
}
?>

<li class="me-item-img" title="<?php _e("Drag to sort", "enginethemes"); ?>">
	<span class="me-gallery-img">
	    <input type="hidden" name="<?php echo esc_attr($filename); ?>[]" value="<?php echo esc_attr($image_id) ?>">
	    <?php if(isset($image_url) && !empty($image_url)) : ?>
	    	<img src="<?php echo $image_url; ?>" alt="">
		<?php else : ?>
			<?php echo $file_icon; ?>
			<p><?php echo $file_name; ?></p>
		<?php endif; ?>
		<a class="me-delete-img remove"></a>
	</span>
	<span class="me-gallery-file">
		<?php echo $file_name; ?>
		<a class="me-delete-img remove"></a>
	</span>
</li>