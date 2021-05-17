<?php
/**
 * The template for displaying information of listing ordered.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/order-listing.php.
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
<div class="me-orderlisting-info">

	<?php if ($listing) : ?>

		<?php marketengine_get_template('purchases/order-listing-image', array('listing' => $listing)); ?>

		<div class="me-listing-info">
			<div class="me-row">
				<div class="me-col-md-8 me-col-sm-8">

					<h2>
						<a href="<?php echo $listing->get_permalink(false, 'javascript:void(0)'); ?>">
							<?php echo esc_html($cart_listing['title']); ?>
						</a>
					</h2>
					<div class="me-rating">
						<div class="result-rating" data-score="<?php echo $listing->get_review_score(); ?>"></div>
					</div>
					<div class="me-count-purchases-review">
						<span><?php printf(_n('%d Purchase', '%d Purchases', $listing->get_order_count(), 'enginethemes'), $listing->get_order_count()); ?></span>
						<span><?php printf(_n('%d Review', '%d Reviews', $listing->get_review_count(), 'enginethemes'), $listing->get_review_count()); ?></span>
					</div>
				</div>
				<div class="me-col-md-4 me-col-sm-4">
					<?php
						$seller = $listing->get_author();
						$can_rate = $seller != get_current_user_id() && $listing->is_available();
					?>

					<?php if( $can_rate  && !marketengine_get_user_rate_listing_score($listing->ID, $transaction->post_author) && !$transaction->has_status('me-pending') ) : ?>
						<a class="me-orderlisting-review" href="<?php echo add_query_arg(array('id' => $listing->ID, 'action' => 'review')); ?>">
							<?php _e('RATE &amp; REVIEW NOW', 'enginethemes'); ?>
						</a>
					<?php endif; ?>

					<?php marketengine_get_template('purchases/listing-archived', array('listing' => $listing)) ?>
				</div>
			</div>
		</div>

	<?php else : ?>

		<?php marketengine_get_template('purchases/listing-deleted'); ?>

	<?php endif; ?>
</div>