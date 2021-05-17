<?php
/**
 * The template for displaying order item.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/account/confirm-email.php.
 *
 * @package     MarketEngine/Templates
 * @since 		1.0.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$total = 0;
$unit = ($order_item['qty']) ? $order_item['qty'][0] : 1;
?>

<?php do_action( 'marketengine_before_checkout_form' ); ?>

<div class="me-shopping-cart">
	<h5><?php _e("Order Item", "enginethemes"); ?></h5>
	<div class="me-table me-cart-table">
		<div class="me-table-rhead">
			<div class="me-table-col me-cart-name"><?php _e("Listing", "enginethemes"); ?></div>
			<div class="me-table-col me-cart-price"><?php _e("Price", "enginethemes"); ?></div>
			<div class="me-table-col me-cart-units"><?php _e("Units", "enginethemes"); ?></div>
			<div class="me-table-col me-cart-units-total"><?php _e("Total price", "enginethemes"); ?></div>
		</div>

		<?php do_action( 'marketengine_before_cart_item_list' ); ?>

		<div class="me-table-row me-cart-item">

			<div class="me-table-col me-cart-name">
				<div class="me-cart-listing">
				
					<?php marketengine_get_template('purchases/order-listing-image', array('listing' => $listing) ); ?>

					<?php if(!$listing) : ?> 

						<span><?php echo esc_html($order_item['title']); ?></span>
						<?php marketengine_get_template('purchases/listing-deleted', array('listing' => $listing) ); ?>

					<?php else : ?>

						<a href="<?php echo $listing->get_permalink(false, 'javascript:void(0)'); ?>">
							<span><?php echo esc_html($order_item['title']); ?></span>
						</a>

						<?php marketengine_get_template('purchases/listing-archived', array('listing' => $listing) ); ?>

					<?php endif; ?>

				</div>
			</div>
			<div class="me-table-col me-cart-price">
				<?php echo marketengine_price_html( $order_item['price'] ); ?>
				<span class="me-cart-price-mobile"><?php _e("Price", "enginethemes"); ?></span>
			</div>
			<div class="me-table-col me-cart-units">
				<?php echo $unit ?>
				<span class="me-cart-units-mobile"><?php _e("Units", "enginethemes"); ?></span>
			</div>
			<div class="me-table-col me-cart-units-total">
				<?php echo marketengine_price_html($order_item['price'] * $unit); ?>
			</div>

		</div>

		<?php do_action( 'marketengine_after_cart_item_list' ); ?>

		<div class="me-table-row me-cart-rtotals">
			<div class="me-table-col me-table-col-empty"></div>
			<div class="me-table-col me-table-col-empty"></div>
			<div class="me-table-col me-cart-amount"><?php _e("Total amount:", "enginethemes"); ?></div>
			<div class="me-table-col me-cart-totals"><?php echo marketengine_price_html($order_item['price'] * $unit); ?></div>
		</div>
	</div>
	<div class="me-checkout-submit">
	<?php if( $listing && $transaction->post_status === 'me-pending' && $listing->is_available() ) : ?>
		<form method="post">
			<?php wp_nonce_field('me-pay'); ?>
			<input type="hidden" name="order_id" value="<?php echo $transaction->id; ?>" />
			<input type="hidden" name="payment_method" value="ppadaptive" />
			<input class="me-checkout-submit-btn" type="submit" name="checkout" value="<?php _e("COMPLETE PAYMENT", "enginethemes"); ?>">
		</form>
	<?php endif; ?>
	</div>
</div>

<?php do_action( 'marketengine_after_checkout_form' ); ?>

<?php wp_reset_postdata(); ?>