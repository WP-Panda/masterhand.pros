<?php
/**
 * The Template for displaying details of a transaction.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/transaction.php.
 *
 * @package     MarketEngine/Templates
 * @version       1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php marketengine_print_notices(); ?>
<?php marketengine_get_template( 'purchases/order-detail', array('transaction' => $transaction) ); ?>
<?php marketengine_get_template( 'purchases/order-extra', array('transaction' => $transaction ) ); ?>