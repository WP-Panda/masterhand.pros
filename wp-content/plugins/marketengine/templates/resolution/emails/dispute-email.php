<?php
/**
 * This is an email template send to seller when buyer send dispute
 *
 * You can modify the email by copy it to {your theme}/templates/resolution/email
 * @since 1.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<p><?php printf(__("Hi %s,", "enginethemes"), $display_name);?></p>
<p><?php printf(__("Buyer %s has sent a dispute for your order <a href='%s' >#%d</a>.  Here’re the case details:", "enginethemes"), $buyer_name, $order_link, $order_id);?></p>
<p>
<?php _e("You should work directly with the buyer to resolve the problem to avoid the dispute ending in a chargeback. Please the link below:", "enginethemes");?>
<br/><a href='<?php echo $dispute_link; ?>' ><?php echo $dispute_link; ?></a>
</p>
<p><?php printf(__("Regards, <br/> %s", "enginethemes"), $blogname);?></p>
