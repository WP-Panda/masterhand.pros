<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if('me-resolved' === $transaction->post_status) : ?>

	<div class="me-transaction-dispute">
		<p><?php echo __('Dispute has already been resolved. You can view the history of dispute.', 'enginethemes'); ?></p>
		<a href="<?php echo marketengine_rc_dispute_link($case); ?>"><?php _e('View', 'enginethemes'); ?></a>
	</div>

<?php else : ?>

	<div class="me-orderwarning-info">
		<p><?php echo __('This order has been disputed. In order to resolve problems, move to resolution center.', 'enginethemes'); ?></p>
		<a href="<?php echo marketengine_rc_dispute_link($case); ?>" class="me-resolution-center"><?php _e('TO THE DISPUTED CASE', 'enginethemes'); ?></a>
	</div>

<?php endif; ?>