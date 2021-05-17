<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php do_action('marketengine_before_single_listing_description');?>

<div itemprop="description" class="me-description listing-description me-desc-box">

	<?php do_action('marketengine_single_listing_description_start');?>

	<h3><?php _e("Explore this listing", "enginethemes");?></h3>
	<?php the_content();?>

	<?php do_action('marketengine_single_listing_description_end');?>

</div>
<?php do_action('marketengine_after_single_listing_description');?>