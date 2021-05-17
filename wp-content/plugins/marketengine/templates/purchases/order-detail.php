<?php
/**
 * The template for displaying order items details
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/order-details.php.
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
<?php do_action('marketengine_before_transaction_items_details', $transaction); ?>

<div class="me-order-detail">

<?php do_action('marketengine_transaction_items_details', $transaction); ?>

</div>

<?php do_action('marketengine_after_transaction_items_details', $transaction); ?>
