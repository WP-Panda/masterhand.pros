<div class="me-cancel-payment">
	<h3><?php _e("Cancelling Payment", "enginethemes"); ?></h3>
	<p class="me-cancel-payment-1"><?php _e("There was something went wrong with your payment!", "enginethemes"); ?></p>
	<p class="me-cancel-payment-2"><?php _e("Your payment can be completed in the order detail", "enginethemes"); ?></p>
	<a href="<?php echo $order->get_order_detail_url() ?>" class="marketengine-btn"><?php _e('TO ORDER DETAIL', 'enginethemes') ; ?></a>
</div>
