<?php
/**
 * The Template for displaying billing information.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/order-bill-info.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.0
 *
 * @version     1.0.0
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$shipping_address = $transaction->get_address( 'shipping' );
$billing_address = $transaction->get_address( 'billing' );

$note = esc_html( $transaction->post_excerpt );
?>

<div class="me-order-detail-block">
	<div class="me-row">
		<div class="me-col-md-5">
			<div class="me-row">
				<div class="me-col-md-12 me-col-sm-12">
					<div class="me-orderbill-info">
						<h5><?php echo __( 'Billed to:', 'enginethemes' ); ?></h5>
						<p><?php marketengine_print_buyer_information( $billing_address ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="me-col-md-7">
			<div class="me-ordernotes-info">
				<h5><?php echo __( 'Order Notes:', 'enginethemes' ); ?></h5>
				<p class=""><?php echo nl2br(esc_attr($note)); ?></p>
			</div>
		</div>
	</div>
</div>