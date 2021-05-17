<?php
/**
 * This is an email template send to seller when buyer close dispute
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
<p>
<?php 
printf(__("Buyer %s has closed the dispute for your order.  You can review it <a href='%s' >here</a> for further details. ", "enginethemes"), 
	$buyer_name, $dispute_link); 
?>
</p>
<p><?php _e("Thank you for your cooperation.", "enginethemes"); ?><br/>
<?php printf(__("Regards, <br/> %s", "enginethemes"), $blogname); ?>
</p>
