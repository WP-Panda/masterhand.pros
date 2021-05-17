<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * This is an email template
 * Email content send to buyer when the order closed
 * 
 * You can modify the email by copy it to {your theme}/templates/emails/buyer
 * @since 1.0
 */
?>
<p><?php printf(__("Dear %s", "enginethemes"), esc_html( $display_name )); ?>,</p>
<p><?php printf(__("Your transaction for the listing %s on %s has been closed. ", "enginethemes"), $listing_link, esc_html( get_bloginfo('blogname') ) ); ?></p>
<p><?php _e("Transaction details:", "enginethemes"); ?></p>
<ol>
	<li><?php printf(__("Listing: %s", "enginethemes"), $listing_link) ?></li>
	<li><?php printf(__("Seller: %s", "enginethemes"), esc_html( $seller_name )) ?></li>
	<li><?php printf(__("Date of purchase: %s", "enginethemes"), $order_date) ?></li>
	<li><?php printf(__("Total payment: %s", "enginethemes"), $total) ?></li>
</ol>
<p>
<?php printf(__("View your order details here: %s.", "enginethemes"), $order_link); ?>
</p>
<p><?php _e("Sincerely", "enginethemes"); ?>,
<br><?php echo esc_html( get_bloginfo('blogname') ); ?></p>