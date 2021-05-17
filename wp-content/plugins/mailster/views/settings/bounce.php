
<table class="form-table">

	<tr valign="top" class="settings-row settings-row-bounce-address">
		<th scope="row"><?php esc_html_e( 'Bounce Address', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[bounce]" value="<?php echo esc_attr( mailster_option( 'bounce' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'Undeliverable emails will return to this address', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-enable-automatic-bounce-handling">
		<th scope="row">&nbsp;</th>
		<td><label><input type="hidden" name="mailster_options[bounce_active]" value=""><input type="checkbox" name="mailster_options[bounce_active]" id="bounce_active" value="1" <?php checked( mailster_option( 'bounce_active' ) ); ?>> <?php esc_html_e( 'Enable automatic bounce handling', 'mailster' ); ?></label>
		</td>
	</tr>

</table>
<div id="bounce-options"<?php echo ! mailster_option( 'bounce_active' ) ? ' style="display:none"' : ''; ?>>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-if-you-would-like-to-enable-bouncing-you-have-to-setup-a-separate-mail-account">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php esc_html_e( 'If you would like to enable bouncing you have to setup a separate mail account', 'mailster' ); ?></p></td>
		</tr>
	<?php if ( function_exists( 'imap_open' ) ) : ?>
		<tr valign="top" class="settings-row settings-row-service">
			<th scope="row"><?php esc_html_e( 'Service', 'mailster' ); ?></th>
			<td>
			<label><input type="radio" name="mailster_options[bounce_service]" value="pop3" <?php checked( mailster_option( 'bounce_service' ), 'pop3' ); ?>> POP3 </label>&nbsp;
			<label><input type="radio" name="mailster_options[bounce_service]" value="imap" <?php checked( mailster_option( 'bounce_service' ), 'imap' ); ?>> IMAP </label>&nbsp;
			<label><input type="radio" name="mailster_options[bounce_service]" value="nntp" <?php checked( mailster_option( 'bounce_service' ), 'nntp' ); ?>> NNTP </label>&nbsp;
			<label><input type="radio" name="mailster_options[bounce_service]" value="" <?php checked( ! mailster_option( 'bounce_service' ) ); ?>> POP3 (legacy)</label>
			</td>
		</tr>
	<?php endif; ?>
		<tr valign="top" class="settings-row settings-row-server-address-port">
			<th scope="row"><?php esc_html_e( 'Server Address : Port', 'mailster' ); ?></th>
			<td><input type="text" name="mailster_options[bounce_server]" value="<?php echo esc_attr( mailster_option( 'bounce_server' ) ); ?>" class="regular-text">:<input type="text" name="mailster_options[bounce_port]" id="bounce_port" value="<?php echo mailster_option( 'bounce_port' ); ?>" class="small-text"></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-secure">
			<th scope="row"><?php esc_html_e( 'Secure', 'mailster' ); ?></th>
			<td>
			<label><input type="radio" name="mailster_options[bounce_secure]" value="" <?php checked( ! mailster_option( 'bounce_secure' ) ); ?>> <?php esc_html_e( 'none', 'mailster' ); ?></label>
			<label><input type="radio" name="mailster_options[bounce_secure]" value="ssl" <?php checked( mailster_option( 'bounce_secure' ), 'ssl' ); ?>> SSL </label>&nbsp;
			<label><input type="radio" name="mailster_options[bounce_secure]" value="tls" <?php checked( mailster_option( 'bounce_secure' ), 'tls' ); ?>> TLS </label>&nbsp;
			</td>
		</tr>
		<tr valign="top" class="settings-row settings-row-username">
			<th scope="row"><?php esc_html_e( 'Username', 'mailster' ); ?></th>
			<td><input type="text" name="mailster_options[bounce_user]" value="<?php echo esc_attr( mailster_option( 'bounce_user' ) ); ?>" class="regular-text"></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-password">
			<th scope="row"><?php esc_html_e( 'Password', 'mailster' ); ?></th>
			<td><input type="password" name="mailster_options[bounce_pwd]" value="<?php echo esc_attr( mailster_option( 'bounce_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
		<tr valign="top" class="settings-check-bounce-server wp_cron">
			<th scope="row"></th>
			<td><p><?php printf( esc_html__( 'Check bounce server every %s minutes for new messages', 'mailster' ), '<input type="text" name="mailster_options[bounce_check]" value="' . mailster_option( 'bounce_check' ) . '" class="small-text">' ); ?></p></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-delete-message">
			<th scope="row"><?php esc_html_e( 'Delete messages', 'mailster' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[bounce_delete]" value=""><input type="checkbox" name="mailster_options[bounce_delete]" value="1" <?php checked( mailster_option( 'bounce_delete' ) ); ?>> <?php esc_html_e( 'Delete messages without tracking code to keep postbox clear (recommended)', 'mailster' ); ?></label>
			</td>
		</tr>
		<tr valign="top" class="settings-soft-bounces wp_cron">
			<th scope="row"><?php esc_html_e( 'Soft Bounces', 'mailster' ); ?></th>
			<td><p><?php printf( esc_html__( 'Resend soft bounced mails after %s minutes', 'mailster' ), '<input type="text" name="mailster_options[bounce_delay]" value="' . mailster_option( 'bounce_delay' ) . '" class="small-text">' ); ?></p>
			<p>
			<?php
				$dropdown = '<select name="mailster_options[bounce_attempts]" class="postform">';
				$value    = mailster_option( 'bounce_attempts' );
			?>
			<?php
			for ( $i = 1; $i <= 10; $i++ ) {
				$selected  = ( $value == $i ) ? ' selected' : '';
				$dropdown .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
			}
			$dropdown .= '</select>';

			printf( esc_html__( '%s attempts to deliver message until hardbounce', 'mailster' ), $dropdown );
			?>
			</p>
			</td>
		</tr>
	</table>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-test-bounce-settings">
			<th scope="row"></th>
			<td>
			<input type="button" value="<?php esc_attr_e( 'Test bounce settings', 'mailster' ); ?>" class="button mailster_bouncetest">
			<div class="loading bounce-ajax-loading"></div>
			<span class="bouncetest_status"></span>
			</td>
		</tr>
	</table>
</div>
