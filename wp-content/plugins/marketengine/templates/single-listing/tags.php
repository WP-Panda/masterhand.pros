<?php
/**
 * 	The Template for displaying tags of the listing.
 * 	This template can be overridden by copying it to yourtheme/marketengine/single-listing/tags.php.
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
?>

<?php do_action('marketengine_before_single_listing_tags'); ?>

<div class="me-tags">
	<?php if( get_the_terms( '', 'listing_tag', '&nbsp') ) : ?>
	<span><?php _e("Tags:", "enginethemes"); the_terms('', 'listing_tag', '&nbsp;'); ?> </span>
	<?php endif; ?>
</div>

<?php do_action('marketengine_after_single_listing_tags'); ?>