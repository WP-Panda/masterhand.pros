<?php
/**
 * The email template for sending to user when they create an account.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/emails/registration-success.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.0
 *
 * @version     1.0.0
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>
<p><?php printf(__("Hello %s,", "enginethemes"), esc_html( $display_name )); ?></p>
<p><?php printf(__("You have successfully registered an account with %s.Here is your account information:", "enginethemes"), esc_html( $blogname )); ?></p>
<ol>
	<li><?php printf(__("Username: %s", "enginethemes"), $user_login); ?></li>
	<li><?php printf(__("Email: %s", "enginethemes"), $user_email); ?></li>
</ol>
<p><?php printf(__("Thank you and welcome to %s.", "enginethemes"), esc_html( $blogname )); ?></p>