<?php
/**
 *  The Template for displaying heading details of a order.
 *  This template can be overridden by copying it to yourtheme/marketengine/transaction-heading.php.
 *
 * @author      EngineThemes
 * @package     MarketEngine/Templates
 * @version     1.0.0
 */

$is_buyer = ($transaction->post_author == get_current_user_id());

$title = $is_buyer ? __('MY TRANSACTIONS', 'enginethemes') : __('MY ORDERS', 'enginethemes');
$url = $is_buyer ? marketengine_get_auth_url('purchases') : marketengine_get_auth_url('orders');

?>
<div class="marketengine-page-title me-have-breadcrumb">
    <h2><?php echo apply_filters( 'marketengine_transaction_title', $title ); ?></h2>
    <ol class="me-breadcrumb">
    	
    	<?php do_action('marketengine_order_breadcrumb_start'); ?>
        
        <li><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
        <li><a href="<?php echo $transaction->get_order_detail_url(); ?>"><?php printf( '#%s', $transaction->id ); ?></a></li>
        
        <?php do_action('marketengine_order_breadcrumb_end'); ?>

    </ol>
</div>