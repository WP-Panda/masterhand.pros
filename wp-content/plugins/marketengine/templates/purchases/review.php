<div class="marketengine-content">

<form method="post" >
	<div class="me-review-listing">
		<?php marketengine_print_notices(); ?>
		<h3><?php echo esc_html( get_the_title( $listing_id ) ); ?></h3>
		<div class="me-rating">
			<div class="do-rating" data-score=""></div>
		</div>
		<p><?php _e("Share your review about this listing", "enginethemes"); ?></p>
		<textarea name="content"></textarea>
		<div class="me-review-submit">
			<input class="me-review-btn" type="submit" value="<?php _e("SUBMIT", "enginethemes"); ?>">
		</div>
		
		<input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
		<input type="hidden" name="order_id" value="<?php the_ID(); ?>">

		<a href="<?php echo remove_query_arg( array('action', 'id') ); ?>" class="me-backlink">&lt; <?php _e("Back to transaction detail", "enginethemes"); ?></a>
	</div>
	<?php wp_nonce_field( 'me-review-listing' ); ?>

</form>
</div>