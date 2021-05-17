<?php
/**
 * This is an email template
 * Email content send to buyer when he order a listing successfull
 *
 * You can modify the email by copy it to {your theme}/templates/emails/buyer/order-success.php
 *
 * @author         EngineThemes
 * @package     MarketEngine/Templates
 *
 * @since         1.0.0
 *
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<p><?php printf(__("Dear %s", "enginethemes"), esc_html($display_name));?>,</p>
<p><?php printf(__("Your payment for the listing %s on %s has been accepted. ", "enginethemes"), $listing_link, esc_html(get_bloginfo('blogname')));?></p>
<p><?php _e("Here are the payment details:", "enginethemes");?></p>
<ol>
	<li><?php printf(__("Listing: %s", "enginethemes"), $listing_link)?></li>
	<li><?php printf(__("Price: %s", "enginethemes"), $listing_price)?></li>
	<li><?php printf(__("Unit: %s", "enginethemes"), $unit)?></li>
	<li><?php printf(__("Total: %s", "enginethemes"), $total)?></li>
</ol>
<p>
<?php printf(__("View your order details here: %s.", "enginethemes"), $order_link);?>
</p>
<p><?php _e("Sincerely", "enginethemes");?>,
<br><?php echo esc_html(get_bloginfo('blogname')); ?></p>