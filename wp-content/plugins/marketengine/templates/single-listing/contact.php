<?php
/**
 * The template for displaying contact action button.
 *
 * This template can be override by copying it to yourtheme/marketengine/single-listing/contact.php
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

$listing_status = $listing->post_status;
?>

<?php do_action('marketengine_before_single_listing_contact_button'); ?>

<?php if('publish' == $listing_status) : ?>

<form method="post">
	<?php do_action('marketengine_single_listing_send_inquiry_form_start'); ?>

	<div class="me-contact">
		<input <?php disabled( !marketengine_is_activated_user() ); ?> type="submit" class="me-buy-now-btn <?php echo !marketengine_is_activated_user() ? 'me-disable-btn' : ''; ?>" value="<?php echo marketengine_option('contact-action') ?  marketengine_option('contact-action') : __("CONTACT NOW", "enginethemes") ; ?>">
	</div>

	<?php wp_nonce_field('me-send-inquiry'); ?>
	<input type="hidden" name="send_inquiry" value="<?php the_ID(); ?>" />

	<?php do_action('marketengine_single_listing_send_inquiry_form_end'); ?>
</form>

<?php else : ?>

	<div class="me-contact">
		<p class="me-contact-archive"><?php echo marketengine_option('contact-title') ?  marketengine_option('contact-title') : __("OFFERING", "enginethemes") ; ?></p>
	</div>

<?php endif; ?>

<?php do_action('marketengine_after_single_listing_contact_button'); ?>