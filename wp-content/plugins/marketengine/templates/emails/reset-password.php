<?php
/**
 * The email template for sending to user when they request reset pass
 *
 * This template can be overridden by copying it to yourtheme/marketengine/emails/reset-password-success.php.
 *
 * @author         EngineThemes
 * @package     MarketEngine/Templates
 *
 * @since         1.0.0
 *
 * @version     1.0.0
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<p><?php printf(__("Hello %s,", "enginethemes"), esc_html( $display_name ));?></p>
<p>
<?php 
	printf(__("You have just sent a request to recover the password associated with your account in %s. ", "enginethemes"), esc_html( $blogname ));
	echo '</br>';
	printf(__("If you did not make this request, please ignore this email; otherwise, click the link below to create your new password: <br/> %s", "enginethemes"), $recover_url);
?>
</p>
<p><?php printf(__("Regards, <br/> %s", "enginethemes"), esc_html( $blogname )); ?></p>