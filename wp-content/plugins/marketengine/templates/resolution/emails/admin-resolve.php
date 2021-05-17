<?php
/**
 * This is an email template send to user when admin resolve dispute
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

<?php printf(__("The dispute for your transaction <a href='%s' >#%d</a> has been resolved. Admin has decided %s is right. ", "enginethemes"), $order_link, $order_id, $winner) ?>
<?php printf(__("You can review it <a href='%s'>here</a> for further details.", "enginethemes"), $dispute_link); ?>

<p>
<?php _e("Thank you for your cooperation.", "enginethemes"); ?> </br>
<?php printf(__("Regards, <br/> %s", "enginethemes"), $blogname); ?>
</p>
