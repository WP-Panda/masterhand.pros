<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/user-profile.php.
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$user = ME()->get_current_user();
?>

<?php do_action('marketengine_before_user_profile', $user); ?>

<?php marketengine_print_notices(); ?>

<div class="me-authen-wrap me-authen-profile">
	<?php do_action('marketengine_user_profile_start', $user); ?>

		<div class="me-profile-info">

			<?php do_action('marketengine_before_user_profile_avatar', $user); ?>

			<div class="me-avatar-user">
				<a class="avatar-user">
					<?php echo $user->get_avatar(); ?>
				</a>
			</div>

			<?php do_action('marketengine_after_user_profile_avatar', $user); ?>

			<?php do_action('marketengine_before_user_profile_information', $user); ?>

			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-text-field">
						<label class="me-field-title"><?php _e("First Name", "enginethemes");?></label>
						<p><?php echo $user->first_name; ?></p>
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-text-field">
						<label class="me-field-title"><?php _e("Last Name", "enginethemes");?></label>
						<p><?php echo $user->last_name; ?></p>
					</div>
				</div>
			</div>
			<div class="marketengine-text-field">
				<label class="me-field-title"><?php _e("Display Name", "enginethemes");?></label>
				<p><?php echo $user->display_name; ?></p>
			</div>

			<?php do_action('marketengine_user_profile_information', $user); ?>

			<div class="marketengine-text-field">
				<label class="me-field-title"><?php _e("Username", "enginethemes");?></label>
				<p><?php echo $user->user_login; ?></p>
			</div>
			<div class="marketengine-text-field">
				<label class="me-field-title"><?php _e("Email", "enginethemes");?></label>
				<p><?php echo $user->user_email; ?></p>
			</div>

			<div class="marketengine-text-field">
				<label class="me-field-title">
					<?php _e("Paypal Email <i>(this email will be used for Paypal payment)</i>", "enginethemes"); ?>
				</label>
				<p>
					<?php
						if($user->paypal_email){
							echo $user->paypal_email;
						}else{
							echo '<span class="me-not-yet-info">';
							_e('Not yet received info', 'enginethemes');
							echo '</span>';
						}
					?>
				</p>
			</div>

			<div class="marketengine-text-field">
				<label class="me-field-title"><?php _e("Location", "enginethemes");?></label>
				<p>
					<?php
						if($user->location){
							echo $user->location;
						}else{
							echo '<span class="me-not-yet-info">';
							_e('Not yet received info', 'enginethemes');
							echo '</span>';
						}
					?>
				</p>
			</div>

			<div class="marketengine-text-field me-no-margin-bottom">
				<label class="me-field-title"><?php _e("About Me", "enginethemes");?></label>
				<p>
					<?php
						if($user->description){
							echo nl2br($user->description);
						}else{
							echo '<span class="me-not-yet-info">';
							_e('Not yet received info', 'enginethemes');
							echo '</span>';
						}
					?>
				</p>
			</div>

			<?php do_action('marketengine_after_user_profile_information', $user); ?>

		</div>
		<div class="marketengine-text-field edit-profile">
			<a href="<?php echo $user->is_activated() ? marketengine_get_endpoint_url('edit-profile') : 'javascript:void(0)'; ?>" class="marketengine-btn <?php echo $user->is_activated() ? '' : 'me-disable-btn'; ?>"><?php _e("EDIT PROFILE", "enginethemes");?></a>
		</div>

		<?php if($user->is_activated()): ?>
		<a href="<?php echo marketengine_get_endpoint_url('change-password'); ?>" class="back-home-sigin me-backlink"><?php _e("Change Password", "enginethemes");?></a>
		<?php else : ?>
		<a href="<?php echo add_query_arg(array( 'resend-confirmation-email' => true, '_wpnonce' => wp_create_nonce('me-resend_confirmation_email') )); ?>" id="resend-confirmation-email" class="back-home-sigin me-backlink"><?php _e("Resend activation email", "enginethemes");?></a>
		<?php endif; ?>

	<?php do_action('marketengine_user_profile_end', $user); ?>
</div>
<?php do_action('marketengine_after_user_profile', $user); ?>