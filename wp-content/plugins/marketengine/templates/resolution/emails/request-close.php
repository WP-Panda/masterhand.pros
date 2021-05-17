<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<p><?php printf(__("Hi %s,", "enginethemes"), $display_name);?></p>
<p><?php printf(__("Seller %s has requested to close the dispute for your order. Please review it <a href='%s' >here</a> and consider to close the dispute.", "enginethemes"), $seller_name, $dispute_link); ?></p>
<p><?php _e("Thank you for your cooperation.", "enginethemes"); ?></p>
<p><?php printf(__("Regards, <br/> %s", "enginethemes"), $blogname) ?></p>
