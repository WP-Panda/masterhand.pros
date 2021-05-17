<?php
/**
 * The template for displaying thumbnail of listing ordered.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/order-listing-image.php.
 *
 * @package     MarketEngine/Templates
 * @since 		1.0.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php if($listing) : ?>
<a class="me-orderlisting-thumbs" href="<?php echo $listing->get_permalink(false, 'javascript:void(0)'); ?>">
	<?php 
		if( $listing->get_listing_thumbnail() ) :
			echo $listing->get_listing_thumbnail();
		else : 
			echo '<i class="icon-me-image"></i>';
		endif;
	?>
</a>
<?php endif; ?>