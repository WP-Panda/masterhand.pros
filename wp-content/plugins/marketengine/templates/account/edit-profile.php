<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/edit-profile.php.
 *
 * @package     MarketEngine/Templates
 * @version     1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$user = ME()->get_current_user();
$first_name = isset($_POST['first_name']) ? esc_attr( $_POST['first_name'] ) : $user->first_name;
$last_name = isset($_POST['last_name']) ? esc_attr( $_POST['last_name'] ) : $user->last_name;
$location = isset($_POST['location']) ? esc_attr( $_POST['location'] ) : $user->location;
$paypal_email = isset($_POST['paypal_email']) ? esc_attr( $_POST['paypal_email'] ) : $user->paypal_email;
$about_user = isset($_POST['description']) ? esc_attr( $_POST['description'] ) : $user->description;
$user_avatar = $user->get_user_avatar_id();
?>

<?php do_action('marketengine_before_edit_user_profile_form', $user); ?>

<form id="edit-profile-form" action="" method="post" >

	<?php do_action('marketengine_edit_user_profile_form_start', $user); ?>

	<div class="me-authen-wrap me-authen-profile">
		<div class="me-profile-info">
			<?php do_action('marketengine_before_edit_user_avatar', $user); ?>
			<div class="me-avatar-user">
				<?php
		        marketengine_get_template('upload-file/avatar-form', array(
		            'id' => 'upload_user_avatar',
		            'name' => 'user_avatar',
		            'source' => $user_avatar,
		            'button' => 'change-avatar-user',
		            'extension' => 'jpg,jpeg,gif,png',
		            'multi' => false,
		            'maxsize' => esc_html( '2mb' ),
		            'maxcount' => 1,
		            'close' => false
		        ));
		    ?>
			</div>

			<?php do_action('marketengine_after_edit_user_avatar', $user); ?>

			<?php marketengine_print_notices(); ?>

			<?php do_action('marketengine_before_edit_user_profile', $user); ?>

			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-group-field">
						<div class="marketengine-input-field">
							<label class="me-field-title" for="me-first-name"><?php _e("First Name", "enginethemes");?></label>
							<input id="me-first-name" type="text" value="<?php echo $first_name; ?>" name="first_name" id="first_name" />
						</div>
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-group-field">
						<div class="marketengine-input-field">
							<label class="me-field-title" for="me-last-name"><?php _e("Last Name", "enginethemes");?></label>
							<input id="me-last-name" type="text" value="<?php echo $last_name; ?>" name="last_name" id="last_name">
						</div>
					</div>
				</div>
			</div>
			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title"><?php _e("Display Name", "enginethemes");?></label>
					<select name="display_name" id="display_name" class="me-chosen-select">
						<?php
						$public_display = array();
						$public_display['display_nickname'] = $user->nickname;
						$public_display['display_username'] = $user->user_login;

						if ($user->first_name) {
						    $public_display['display_firstname'] = $user->first_name;
						}

						if ($user->last_name) {
						    $public_display['display_lastname'] = $user->last_name;
						}

						if ($user->first_name && $user->last_name) {
						    $public_display['display_firstlast'] = $user->first_name . ' ' . $user->last_name;
						    $public_display['display_lastfirst'] = $user->last_name . ' ' . $user->first_name;
						}

						if (!in_array($user->display_name, $public_display)) // Only add this if it isn't duplicated elsewhere
						{
						    $public_display = array('display_displayname' => $user->display_name) + $public_display;
						}

						$public_display = array_map('trim', $public_display);
						$public_display = array_unique($public_display);
						foreach ($public_display as $id => $item) {
    					?>
							<option <?php selected($user->display_name, $item);?>><?php echo $item; ?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>

			<?php do_action('marketengine_edit_user_profile', $user); ?>

			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-user-name"><?php _e("Username", "enginethemes");?></label>
					<input id="me-user-name" disabled="disabled" type="text" value="<?php echo $user->user_login; ?>" id="username">
				</div>
			</div>

			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-email"><?php _e("Email", "enginethemes");?></label>
					<input id="me-email" disabled="disabled" type="text" value="<?php echo $user->email; ?>" id="email">
				</div>
			</div>

			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-paypal-email"><?php _e("Paypal Email (this email will be used for Paypal payment)", "enginethemes"); ?></label>
					<input id="me-paypal-email" type="text" value="<?php echo $paypal_email; ?>" name="paypal_email" id="paypal_email">
				</div>
			</div>

			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
					<label class="me-field-title" for="me-location"><?php _e("Location", "enginethemes");?></label>
					<input id="me-location" type="text" value="<?php echo $location; ?>" name="location" id="location">
				</div>
			</div>

			<div class="marketengine-group-field me-no-margin-bottom">
				<div class="marketengine-textarea-field">
					<label class="me-field-title" for="me-description"><?php _e("About Me", "enginethemes");?></label>
					<textarea id="me-description" name="description" id="description"><?php echo $about_user; ?></textarea>
				</div>
			</div>

			<?php wp_nonce_field('me-update_profile'); ?>
			<?php wp_nonce_field('marketengine', 'me-user-avatar'); ?>
			<?php do_action('marketengine_after_edit_user_profile', $user); ?>
		</div>
		<div class="marketengine-text-field edit-profile">
			<input type="submit" class="marketengine-btn" name="update_profile" value="<?php _e("UPDATE PROFILE", "enginethemes");?>" />
		</div>
		<a href="<?php echo marketengine_get_page_permalink('user_account'); ?>" class="back-home-sigin me-backlink"><?php _e("Cancel", "enginethemes");?></a>
	</div>

	<?php do_action('marketengine_edit_user_profile_form_end', $user); ?>

</form>
<?php do_action('marketengine_after_edit_user_profile_form', $user); ?>