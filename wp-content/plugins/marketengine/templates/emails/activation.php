<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * This is an email template
 * Require activation email content send to user when he register
 * 
 * You can modify the email by copy it to {your theme}/templates/emails/
 * @since 1.0
 */
?>
<p><?php printf(__("Hello %s,", "enginethemes"), esc_html( $display_name )); ?></p>
<p>
	<?php printf(__("You have successfully registered an account with %s.", "enginethemes"), esc_html( $blogname ) ); ?>
	&nbsp;<?php _e("Here is your account information:", "enginethemes"); ?>
</p>
<ol>
	<li><?php printf(__("Username: %s", "enginethemes"), $user_login); ?></li>
	<li><?php printf(__("Email: %s", "enginethemes"), $user_email); ?></li>
</ol>
<p><?php _e("Please click the link below to confirm your email address.", "enginethemes"); ?></p>
<p><?php echo $activate_email_link; ?></p>
<p><?php printf(__("Thank you and welcome to %s.", "enginethemes"), esc_html( $blogname )); ?></p>