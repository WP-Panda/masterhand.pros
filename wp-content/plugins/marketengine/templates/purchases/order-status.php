<div class="me-order-detail-block">
	<div class="me-orderstatus-info">
		<h5><?php echo __('Order status:', 'enginethemes'); ?></h5>
		<div class="me-orderstatus">
			<?php
				marketengine_print_order_status( $order_status );
				$status_info = marketengine_get_order_status_info( $order_status, 'text' );
			?>

			<?php if($status_info) : ?>
			<p class="me-orderstatus-notifi"><i class="icon-me-info-circle"></i><?php echo $status_info; ?></p>
			<?php endif; ?>
		</div>
		<?php
			$process_index = marketengine_get_order_status_info( $order_status );
		?>
		<div class="me-line-process-order">
			<div class="me-line-step-order <?php echo $process_index >= 1 ? 'active' : '' ?>">
				<span><?php _e('Check payment', 'enginethemes'); ?></span>
			</div>
			<div class="me-line-step-order <?php echo $process_index >= 2 ? 'active' : '' ?>">
				<span><?php _e('Order completed', 'enginethemes'); ?></span>
			</div>
			<div class="me-line-step-order <?php echo $process_index >= 5 ? 'active' : '' ?>">
				<span><?php _e('Closed order', 'enginethemes'); ?></span>
			</div>
		</div>
	</div>
</div>