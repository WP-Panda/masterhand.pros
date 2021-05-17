<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/reset-pass.php.
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

do_action('marketengine_before_reset_password_form');
?>
<div class="me-authen-wrap me-authen-reset">

	<form id="reset-pass-form" action="" method="post">

		<h3><?php _e("RESET PASSWORD", "enginethemes"); ?></h3>

		<?php marketengine_print_notices(); ?>
		<?php do_action('marketengine_reset_password_form_start'); ?>

		<div class="marketengine-group-field">
			<div class="marketengine-input-field">
			    <label class="me-field-title" for="me-new-pass"><?php _e("Enter new password", "enginethemes"); ?></label>
			    <input id="me-new-pass" type="password" name="new_pass">
			</div>
		</div>
		<div class="marketengine-group-field">
			<div class="marketengine-input-field">
			    <label class="me-field-title" for="me-confirm-pass"><?php _e("Confirm password", "enginethemes"); ?></label>
			    <input id="me-confirm-pass" type="password" name="confirm_pass">
			</div>
		</div>

		<input type="hidden" name="key" value="<?php echo esc_attr( $_GET['key'] ); ?>" />
		<input type="hidden" name="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" />
		<?php wp_nonce_field('me-reset_password', "_wpnonce");?>

		<div class="marketengine-group-field me-submit-reset">
			<input type="submit" class="marketengine-btn" name="reset_password" value="<?php _e("SET NEW PASSWORD", "enginethemes"); ?>">
		</div>
		<a href="<?php echo marketengine_get_page_permalink('user-profile'); ?>" class="back-home-sigin"><?php _e("&lt; Cancel", "enginethemes"); ?></a>

		<?php do_action('marketengine_reset_password_form_end'); ?>

	</form>

</div>
<?php do_action('marketengine_after_reset_password_form'); ?>