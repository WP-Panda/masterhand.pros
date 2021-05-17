<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$file_size =  '192 KB';
$file_icon = '';

switch ($file_type['ext']) {
	case 'jpeg':
	case 'png':
	case 'jpeg':
	case 'jpg' :
	case 'gif' :
		?>
		<a href="<?php echo $url; ?>" class="me-message-fancybox mess-file-item" title="<?php echo $name; ?>">
			<?php echo wp_get_attachment_image( $file_id, 'thumbnail'); ?>
		</a>
		<?php
		$file_icon = '<i class="icon-me-file-image" ></i>';
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
	case 'image' :
		$file_icon = '<i class="icon-me-file-image" ></i>';
		break;
	default:
		$file_icon = '<i class="icon-me-file-code" ></i>';
		break;
}
?>
<?php echo '<a href="'. $url .'" class="me-mess-file-item" download="'.$name.'">'. $file_icon .'<span class="me-mess-name">'. $name .'</span><span class="me-mess-size">'. marketengine_format_size_units($size) .'</span><span class="me-mess-download"><i class="icon-me-download"></i></span></a>'; ?>