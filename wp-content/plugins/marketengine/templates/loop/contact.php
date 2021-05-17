<div class="me-item-contact">
	<span class="post-price"><?php _e("Contact", "enginethemes"); ?></span>
</div>
<?php if(get_current_user_id() != $listing->post_author) : ?>
<div class="me-contact-now">
	<form method="post">
		<?php do_action('marketengine_single_listing_send_inquiry_form_start'); ?>
		<div class="me-contact">

			<?php if(!marketengine_is_activated_user()) : ?>
			<input type="submit" class="me-buy-now-btn" value="<?php echo marketengine_option('contact-action') ?  marketengine_option('contact-action') : __("CONTACT NOW", "enginethemes");?>">
			<?php else : ?>
			<a href="<?php the_permalink(); ?>" class="me-buy-now-btn">
				<?php echo marketengine_option('contact-action') ?  marketengine_option('contact-action') : __("CONTACT NOW", "enginethemes"); ?>
			</a>
			<?php endif; ?>
		</div>

		<?php wp_nonce_field('me-send-inquiry'); ?>

		<input type="hidden" name="send_inquiry" value="<?php the_ID(); ?>" />
		<?php do_action('marketengine_single_listing_send_inquiry_form_end'); ?>
	</form>
</div>
<?php endif; ?>