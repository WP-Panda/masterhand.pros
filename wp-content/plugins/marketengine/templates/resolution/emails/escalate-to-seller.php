<?php
/**
 * This is an email template send to seller when buyer escalate dispute
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
<p><?php printf(__("Buyer %s has escalated into the dispute for the order <a href='%s' >#%d</a> to admin. ", "enginethemes"), $buyer_name, $order_link, $order_id); ?></p>
<p><?php _e("Please review and provide admin with detailed information and materials involved in this transaction in the conversation area. Please click the link below for further detail:", "enginethemes"); ?>
<br/><a href='<?php echo $dispute_link; ?>' ><?php echo $dispute_link; ?></a>
</p>
<p><?php printf(__("Regards, <br/> %s", "enginethemes"), $blogname);?></p>