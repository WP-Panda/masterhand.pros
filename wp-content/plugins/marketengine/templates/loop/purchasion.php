<?php
$price = $listing->get_price();
$pricing_unit = $listing->get_pricing_unit();
?>
<div class="me-item-price">
	<span itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="me-price pull-left">
		<?php echo marketengine_price_html( $price, $pricing_unit ); ?>
	</span>
	<div class="me-rating pull-right">
		<div class="result-rating" data-score="<?php echo $listing->get_review_score(); ?>"></div>
	</div>
</div>
<?php if(get_current_user_id() != $listing->post_author) : ?>
<div class="me-buy-now">
	<form method="post">
		<input type="hidden" required min="1" value="1" name="qty" />
		<?php wp_nonce_field('me-add-to-cart'); ?>

		<?php do_action('marketengine_single_listing_add_to_cart_form_field'); ?>

		<input type="hidden" name="add_to_cart" value="<?php echo $listing->ID; ?>" />

		<?php if(marketengine_is_activated_user()) : ?>
		<input type="submit" class="me-buy-now-btn" value="<?php echo marketengine_option('purchasion-action') ?  marketengine_option('purchasion-action') : __("BUY NOW", "enginethemes");?>">
		<?php else : ?>
		<a href="<?php the_permalink(); ?>" class="me-buy-now-btn"><?php echo marketengine_option('purchasion-action') ?  marketengine_option('purchasion-action') : __("BUY NOW", "enginethemes"); ?></a>
		<?php endif; ?>

	</form>
</div>
<?php endif; ?>