<?php
/**
 * 	The Template for displaying tags of the listing.
 * 	This template can be overridden by copying it to yourtheme/marketengine/post-listing/listing-tags.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php do_action('marketengine_before_post_listing_tags_form'); ?>
<div class="marketengine-group-field">

	<?php do_action('marketengine_post_listing_tags_form_start'); ?>

	<div class="marketengine-input-field">
	    <?php
	    	$listing_tag = !empty($_POST['listing_tag']) ? esc_attr($_POST['listing_tag']) : $default;
	    	marketengine_post_tags_meta_box($listing_tag, 'listing_tag');
	    ?>
	</div>

	<?php do_action('marketengine_post_listing_tags_form_end'); ?>

</div>
<?php do_action('marketengine_after_post_listing_tags_form'); ?>