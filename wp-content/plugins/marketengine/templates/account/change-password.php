<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/change-password.php.
 *
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php do_action('marketengine_before_change_password_form', $user); ?>

<form id="edit-profile-form" action="" method="post" >


	<?php do_action('marketengine_change_password_form_start'); ?>

	<div class="me-authen-wrap me-authen-change">

		<?php marketengine_print_notices(); ?>
		
		<div class="me-profile-info">
			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-current-pass"><?php _e("Your current password", "enginethemes");?></label>
					<input id="me-current-pass" class="required" type="password" value="" name="current_password" id="current_password">
				</div>
			</div>
		</div>
		<div class="me-profile-info">
			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-new-pass"><?php _e("New password", "enginethemes");?></label>
					<input id="me-new-pass" class="required" type="password" value="" name="new_password" id="new_password">
				</div>
			</div>
		</div>
		<div class="me-profile-info">
			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-confirm-pass"><?php _e("Confirm password", "enginethemes");?></label>
					<input id="me-confirm-pass" class="required" type="password" value="" name="confirm_password" id="confirm_password">
				</div>
			</div>
		</div>

		<?php do_action('marketengine_change_password_form'); ?>

		<?php wp_nonce_field('marketengine_change-password'); ?>

		<div class="marketengine-text-field edit-profile">
			<input type="submit" class="marketengine-btn" name="change_password" value="<?php _e("CHANGE", "enginethemes");?>" />
		</div>
		<a href="<?php echo marketengine_get_page_permalink('user_account'); ?>" class="back-home-sigin me-backlink"><?php _e("&lt; My profile", "enginethemes");?></a>
	</div>

	<?php do_action('marketengine_change_password_form_end'); ?>

</form>
<?php do_action('marketengine_after_change_password_form', $user); ?>
