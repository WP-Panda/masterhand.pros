<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/form-login.php.
 *
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
do_action('marketengine_before_user_login_form');

$register_link = marketengine_get_endpoint_url('register');
$notices = marketengine_get_notices();
?>
<?php if( isset($notices['success'])) : ?>
	<?php marketengine_print_notices();?>
<?php endif; ?>

<div class="me-authen-wrap me-authen-login">
	<form id="login-form" action="" method="post">
		<h3><?php _e("Login", "enginethemes");?></h3>

		<?php marketengine_print_notices();?>
		<?php do_action('marketengine_user_login_form_start');?>

		<div class="marketengine-group-field">
			<div class="marketengine-input-field">
			    <label class="me-field-title" for="username"><?php _e("Email/Username", "enginethemes");?></label>
			    <input type="text" name="user_login" class="required" id="username" value="<?php if (!empty($_POST['user_login'])) {echo esc_attr($_POST['user_login']);}?>">
			</div>
		</div>
		<div class="marketengine-group-field">
			<div class="marketengine-input-field">
			    <label class="me-field-title" for="password"><?php _e("Password", "enginethemes");?></label>
			    <input type="password" class="required" name="user_password" id="password">
			</div>
		</div>

		<?php do_action('marketengine_user_login_form');?>

		<div class="marketengine-group-field me-submit-sigin">
			<input type="submit" class="marketengine-btn" name="login" value="<?php _e("LOGIN", "enginethemes");?>">
		</div>
		<div class="marketengine-group-field forgot-sigin">
			<a href="<?php echo marketengine_get_endpoint_url('forgot-password'); ?>" class="forgot-pass"><?php _e("Forgot password? &nbsp;", "enginethemes");?></a>
			<span class="me-account-register"><?php _e("Need an account?", "enginethemes");?><a href="<?php echo marketengine_get_auth_url('register'); ?>"><?php _e("Register", "enginethemes");?></a></span>
		</div>
		<a href="<?php echo home_url(); ?>" class="back-home-sigin"><?php _e("&lt;  Back to Home", "enginethemes");?></a>

		<?php wp_nonce_field('me-login', "_wpnonce");?>

		<?php if (wp_get_referer() || !empty($_REQUEST['redirect'])): ?>
			<input type="hidden" name="redirect" value="<?php echo !empty($_REQUEST['redirect']) ? $_REQUEST['redirect'] : wp_get_referer(); ?>" />
		<?php endif;?>

		<?php do_action('marketengine_user_login_form_end');?>
	</form>
</div>
<?php
do_action('marketengine_after_user_login_form');