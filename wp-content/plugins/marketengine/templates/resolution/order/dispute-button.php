<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="me-transaction-dispute">
	<p>
		<?php printf( __('You have %s to dispute this order.', 'enginethemes'), $dispute_time_limit); ?>
	</p>
	<a href="<?php echo add_query_arg('action', 'dispute'); ?>" >
		<?php _e('DISPUTE', 'enginethemes'); ?>
	</a>

</div>