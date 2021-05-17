<?php
/**
 * The template for displaying a notice when listing has been archived.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/account/archived-listing-notice.php.
 *
 * @package     MarketEngine/Templates
 * @since 		1.0.1
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php if($listing_obj ) : ?>
	<?php if ( !$listing_obj->is_available() ) : ?>
<div class="me-table-col me-order-status">
	<p class="me-order-item-archive"><i class="icon-me-info-circle"></i><?php _e('Archived', 'enginethemes'); ?></p>
</div>
	<?php endif; ?>
<?php else: ?>
<div class="me-table-col me-order-status">
	<p class="me-order-item-archive"><i class="icon-me-info-circle"></i><?php _e('Deleted', 'enginethemes'); ?></p>
</div>
<?php endif; ?>
