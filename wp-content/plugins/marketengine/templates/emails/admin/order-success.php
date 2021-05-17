<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * This is an email template
 * Email content send to admin when buyer order a listing successfull
 * 
 * You can modify the email by copy it to {your theme}/templates/emails/admin
 * @since 1.0
 */
?>
<p><?php printf(__("Dear %s", "enginethemes"), esc_html( $display_name )); ?>,</p>
<p><?php printf(__("There's a new order for the listing %s on %s.", "enginethemes"), $listing_link, esc_html( get_bloginfo('blogname') ) ); ?></p>
<p><?php _e("Order details:", "enginethemes"); ?></p>
<ol>
	<li><?php printf(__("Listing: %s", "enginethemes"), $listing_link) ?></li>
	<li><?php printf(__("Seller: %s", "enginethemes"), esc_html( $seller_name )) ?></li>
	<li><?php printf(__("Buyer: %s", "enginethemes"), esc_html( $buyer_name )) ?></li>
	<li><?php printf(__("Total payment: %s", "enginethemes"), $total) ?></li>
	<li><?php printf(__("Commission: %s", "enginethemes"), $commission) ?></li>
</ol>
<p><?php _e("Sincerely", "enginethemes"); ?>,
<br><?php echo esc_html( get_bloginfo('blogname') ); ?></p>