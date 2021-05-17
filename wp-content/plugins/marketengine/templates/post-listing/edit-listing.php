<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/post-listing/post-listing.php.
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php if($listing) : ?>

<?php

if( !isset($_POST['referer']) ) {
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
} else {
	$referer = $_POST['referer'];
}

$selected_cat = '';
$selected_sub_cat = '';
$terms = wp_get_post_terms( $listing->ID, 'listing_category');
foreach ($terms as $key => $term) {
	if(!$term->parent) {
		$selected_cat = $term->term_id;
	}else {
		$selected_sub_cat = $term->term_id;
	}
}

$selected_listing_type = $listing->get_listing_type();
if($selected_listing_type == 'purchasion') {
	$listing_types =  array('selected_listing_type' => $selected_listing_type, 'price' => $listing->get_price(), 'unit' => $listing->get_unit(), 'contact_email' => '');
}else {
	$listing_types =  array('selected_listing_type' => $selected_listing_type, 'contact_email' => $listing->get_contact_info(), 'price' => '', 'unit' => '');
}
$listing_types['editing'] = true;

$listing_tag = wp_get_post_terms($listing->ID, 'listing_tag', array('fields' => 'names'));
$listing_content = apply_filters('the_content', $listing->post_content);
?>

<?php do_action('marketengine_before_edit_listing_form', $listing); ?>

<div id="marketengine-wrapper" class="marketengine">
	<div class="marketengine-post-listing-wrap">
		<form  id="edit-listing-form" class="post-listing-form me-edit-listing" method="post" accept-charset="utf-8" enctype="multipart/form-data">
			<h3><?php _e('Edit the Listing', 'enginethemes'); ?></h3>
			<?php marketengine_print_notices(); ?>

			<?php do_action('marketengine_edit_listing_form_start', $listing); ?>

			<?php marketengine_get_template('post-listing/category', array('selected_cat' => $selected_cat, 'selected_sub_cat' => $selected_sub_cat, 'editing' => true) ); ?>

			<?php marketengine_get_template('post-listing/type', $listing_types); ?>

			<?php marketengine_get_template('post-listing/information', array('listing_content' => $listing_content,  'listing_title' => $listing->post_title)); ?>

			<?php do_action('marketengine_edit_listing_information_form_fields', $listing); ?>

			<?php marketengine_get_template('post-listing/gallery', array('listing_gallery' => $listing->get_gallery(), 'listing_image' => $listing->get_featured_image())); ?>

			<?php marketengine_get_template('post-listing/tags', array('default' => join(',', $listing_tag))); ?>

			<?php do_action('marketengine_edit_listing_form_fields', $listing); ?>

			<?php wp_nonce_field('me-update_listing'); ?>
			<?php wp_nonce_field('marketengine', 'me-post-listing-gallery'); ?>

			<input type="hidden" name="edit" value="<?php echo $listing->ID; ?>" />
			
			<div class="marketengine-group-field me-text-center submit-post">
				<input class="marketengine-post-submit-btn" type="submit" name="update_lisiting" value="<?php _e("SUBMIT", "enginethemes"); ?>">
			</div>
			<a href="<?php echo $referer; ?>" class="back-link-page" data-active="2"><?php _e("Cancel", "enginethemes"); ?></a>

			<?php do_action('marketengine_edit_listing_form_end', $listing); ?>

			<input type="hidden" name="referer" value="<?php echo $referer; ?>" />

		</form>
	</div>
</div>

<?php do_action('marketengine_after_edit_listing_form', $listing); ?>
<?php endif; ?>