<?php
/**
 * The template for displaying order extra information.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/order-extra.php.
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

<?php  do_action('marketengine_before_order_extra', $transaction);  ?>

<div class="me-row">

	<?php  do_action('marketengine_order_extra_start', $transaction);  ?>	

	<div class="me-col-md-9">

		<?php  do_action('marketengine_order_extra_content', $transaction);  ?>

	</div>

	<div class="me-col-md-3">

		<?php  do_action('marketengine_order_extra_sidebar', $transaction);  ?>

	</div>

	<?php  do_action('marketengine_order_extra_end', $transaction);  ?>

</div>

<?php  do_action('marketengine_after_order_extra', $transaction);  ?>
