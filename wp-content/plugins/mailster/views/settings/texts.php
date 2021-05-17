<table class="form-table">
	<tr valign="top" class="settings-row settings-row-subscription-form">
		<th scope="row"><?php esc_html_e( 'Subscription Form', 'mailster' ); ?>
		<p class="description"><?php esc_html_e( 'Define messages for the subscription form', 'mailster' ); ?>.<br>
		<?php if ( mailster_option( 'homepage' ) ) : ?>
			<?php printf( esc_html__( 'Some text can get defined on the %s as well', 'mailster' ), '<a href="post.php?post=' . mailster_option( 'homepage' ) . '&action=edit">Newsletter Homepage</a>' ); ?>
		<?php endif; ?>
		</p>
		</th>
		<td>
		<div class="mailster_text"><label><?php esc_html_e( 'Confirmation', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[confirmation]" value="<?php echo esc_attr( mailster_text( 'confirmation' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Successful', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[success]" value="<?php echo esc_attr( mailster_text( 'success' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Error Message', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[error]" value="<?php echo esc_attr( mailster_text( 'error' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Unsubscribe', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[unsubscribe]" value="<?php echo esc_attr( mailster_text( 'unsubscribe' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Unsubscribe Error', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[unsubscribeerror]" value="<?php echo esc_attr( mailster_text( 'unsubscribeerror' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Profile Update', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[profile_update]" value="<?php echo esc_attr( mailster_text( 'profile_update' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Newsletter Sign up', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[newsletter_signup]" value="<?php echo esc_attr( mailster_text( 'newsletter_signup' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-field-labels">
		<th scope="row"><?php esc_html_e( 'Field Labels', 'mailster' ); ?><p class="description"><?php esc_html_e( 'Define texts for the labels of forms. Custom field labels can be defined on the Subscribers tab', 'mailster' ); ?></p></th>
		<td>
		<div class="mailster_text"><label><?php esc_html_e( 'Email', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[email]" value="<?php echo esc_attr( mailster_text( 'email' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'First Name', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[firstname]" value="<?php echo esc_attr( mailster_text( 'firstname' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Last Name', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[lastname]" value="<?php echo esc_attr( mailster_text( 'lastname' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Lists', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[lists]" value="<?php echo esc_attr( mailster_text( 'lists' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Submit Button', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[submitbutton]" value="<?php echo esc_attr( mailster_text( 'submitbutton' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Profile Button', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[profilebutton]" value="<?php echo esc_attr( mailster_text( 'profilebutton' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Unsubscribe Button', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[unsubscribebutton]" value="<?php echo esc_attr( mailster_text( 'unsubscribebutton' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-mail">
		<th scope="row"><?php esc_html_e( 'Mail', 'mailster' ); ?><p class="description"><?php esc_html_e( 'Define texts for the mails', 'mailster' ); ?></p></th>
		<td>
		<div class="mailster_text"><label><?php esc_html_e( 'Unsubscribe Link', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[unsubscribelink]" value="<?php echo esc_attr( mailster_text( 'unsubscribelink' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Webversion Link', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[webversion]" value="<?php echo esc_attr( mailster_text( 'webversion' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Forward Link', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[forward]" value="<?php echo esc_attr( mailster_text( 'forward' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Profile Link', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[profile]" value="<?php echo esc_attr( mailster_text( 'profile' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-order">
		<th scope="row"><?php esc_html_e( 'Other', 'mailster' ); ?></th>
		<td>
		<div class="mailster_text"><label><?php esc_html_e( 'Already registered', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[already_registered]" value="<?php echo esc_attr( mailster_text( 'already_registered' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'New confirmation message', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[new_confirmation_sent]" value="<?php echo esc_attr( mailster_text( 'new_confirmation_sent' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Enter your email', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[enter_email]" value="<?php echo esc_attr( mailster_text( 'enter_email' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-gdpr">
		<th scope="row"><?php esc_html_e( 'GDPR', 'mailster' ); ?></th>
		<td>
		<div class="mailster_text"><label><?php esc_html_e( 'Terms confirmation text', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[gdpr_text]" value="<?php echo esc_attr( mailster_text( 'gdpr_text' ) ); ?>" class="regular-text"></div>
		<div class="mailster_text"><label><?php esc_html_e( 'Error text', 'mailster' ); ?>:</label> <input type="text" name="mailster_texts[gdpr_error]" value="<?php echo esc_attr( mailster_text( 'gdpr_error' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<?php

$dir    = defined( 'WP_LANG_DIR' ) ? WP_LANG_DIR : MAILSTER_DIR . '/languages/';
$files  = array();
$locale = get_locale();

if ( is_dir( $dir ) ) {
	$files = list_files( $dir );
	$files = preg_grep( '/mailster-(.*)\.po$/', $files );
}
?>
<?php if ( ! empty( $files ) ) : ?>
<table class="form-table language-switcher-field">
	<tr valign="top" class="settings-row settings-row-change-language">
		<th scope="row"><?php esc_html_e( 'Change Language', 'mailster' ); ?></th>
		<td>
			<p class="description">
			<?php esc_html_e( 'change language of texts if available to', 'mailster' ); ?>
			<select name="language-file">
				<option<?php selected( preg_match( '#^en_#', $locale ) ); ?> value="en_US"><?php esc_html_e( 'English', 'mailster' ); ?> (en_US)</option>
				<?php
				foreach ( $files as $file ) {
					$lang = str_replace( array( '.po', 'mailster-' ), '', basename( $file ) );
					?>
				<option<?php selected( $lang == $locale ); ?> value="<?php echo $lang; ?>"><?php echo $lang; ?></option>
				<?php } ?>
			</select>
			<button name="change-language" class="button"><?php esc_html_e( 'change language', 'mailster' ); ?></button>
			<br class="clearfix">
			</p>
		</td>
	</tr>
</table>
<?php endif; ?>
