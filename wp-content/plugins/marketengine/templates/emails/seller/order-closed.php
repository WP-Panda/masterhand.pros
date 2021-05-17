<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * This is an email template
 * Email content send to seller when the order closed
 * 
 * You can modify the email by copy it to {your theme}/templates/emails
 * @since 1.0
 */
?>
<p><?php printf(__("Dear %s", "enginethemes"), esc_html( $display_name )); ?>,</p>
<p><?php printf(__("Your order for the listing  %s on %s has been closed.", "enginethemes"), $listing_link, esc_html( get_bloginfo('blogname') ) ); ?></p>
<p><?php _e("Order details:", "enginethemes"); ?></p>
<ol>
	<li><?php printf(__("Listing: %s", "enginethemes"), $listing_link) ?></li>
	<li><?php printf(__("Buyer: %s", "enginethemes"), esc_html( $buyer_name )) ?></li>
	<li><?php printf(__("Date of order: %s", "enginethemes"), $order_date) ?></li>
	<li><?php printf(__("Total earnings (commission deducted): %s", "enginethemes"), $earning) ?></li>
</ol>
<p>
<?php printf(__("View your order details here: %s.", "enginethemes"), $order_link); ?>
</p>
<p><?php _e("Sincerely", "enginethemes"); ?>,
<br><?php echo esc_html( get_bloginfo('blogname') ); ?></p>