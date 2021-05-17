<?php
/**
 * This is an email template
 * Email content send to seller when buyer order a listing successfull
 *
 * You can modify the email by copy it to {your theme}/templates/emails
 * @author EngineThemes
 * @package MarketEngine/Templates
 * @since 1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>
<p><?php printf(__("Dear %s", "enginethemes"), esc_html( $display_name )); ?>,</p>
<p><?php printf(__("You've got a new order for the listing %s on %s", "enginethemes"), $listing_link, esc_html( get_bloginfo('blogname') ) ); ?></p>
<p><?php _e("Here are the order details:", "enginethemes"); ?></p>
<ol>
	<li><?php printf(__("Buyer: %s", "enginethemes"), esc_html( $buyer_name )) ?></li>
	<li><?php printf(__("Price: %s", "enginethemes"), $listing_price) ?></li>
	<li><?php printf(__("Unit: %s", "enginethemes"), $unit) ?></li>
	<li><?php printf(__("Total: %s", "enginethemes"), $total) ?></li>
	<li><?php printf(__("Earnings (commission deducted): %s", "enginethemes"), $earning) ?></li>
</ol>
<p>
<?php printf(__("For this order, %s commission fee has been deducted from your total earnings of %s. <br/>View your order details here: %s.", "enginethemes"), $commission, $total, $order_link) ?>
</p>
<p><?php _e("Sincerely", "enginethemes"); ?>,
<br><?php echo esc_html( get_bloginfo('blogname') ); ?></p>