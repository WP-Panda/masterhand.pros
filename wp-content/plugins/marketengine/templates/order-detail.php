<?php
/**
 *  The Template for displaying details of a order.
 *  This template can be overridden by copying it to yourtheme/marketengine/order-detail.php.
 *
 * @author      EngineThemes
 * @package     MarketEngine/Templates
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$transaction = marketengine_get_order();
get_header();
?>

<?php do_action('marketengine_before_main_content'); ?>

<div id="marketengine-page">

    <div class="me-container">

    <?php do_action('marketengine_before_transaction_details', $transaction); ?>

        <div class="marketengine-content-wrap">

            <?php do_action('marketengine_transaction_details_start', $transaction); ?>

            <?php marketengine_get_template('purchases/order-heading', array('transaction' => $transaction)); ?>

            <div class="marketengine-content">
            <?php 
                if ($transaction->post_author == get_current_user_id() && !empty($_GET['action']) ) {
                    do_action('marketengine_order_details_action', esc_attr( $_GET['action'] ), $transaction );
                } else {
                    do_action('marketengine_transaction_details', $transaction);
                }
            ?>

            </div>

            <?php do_action('marketengine_transaction_details_end', $transaction); ?>

        </div>

    <?php do_action('marketengine_after_transaction_details', $transaction); ?>

    </div>

</div>

<?php do_action('marketengine_after_main_content');?>

<?php get_footer();