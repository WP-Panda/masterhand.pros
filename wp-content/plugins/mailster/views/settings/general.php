<table class="form-table">
	<tr valign="top" class="settings-row settings-row-from-name">
		<th scope="row"><?php esc_html_e( 'From Name', 'mailster' ); ?> *</th>
		<td><input type="text" name="mailster_options[from_name]" value="<?php echo esc_attr( mailster_option( 'from_name' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-from-email">
		<th scope="row"><?php esc_html_e( 'From Email', 'mailster' ); ?> *</th>
		<td><input type="text" name="mailster_options[from]" value="<?php echo esc_attr( mailster_option( 'from' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-reply-to-email">
		<th scope="row"><?php esc_html_e( 'Reply-to Email', 'mailster' ); ?> *</th>
		<td><input type="text" name="mailster_options[reply_to]" value="<?php echo esc_attr( mailster_option( 'reply_to' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The address users can reply to', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-send-delay">
		<th scope="row"><?php esc_html_e( 'Send delay', 'mailster' ); ?> *</th>
		<td><input type="text" name="mailster_options[send_offset]" value="<?php echo esc_attr( mailster_option( 'send_offset' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'The default delay in minutes for sending campaigns.', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-delivery-by-time-zone">
		<th scope="row"><?php esc_html_e( 'Delivery by Time Zone', 'mailster' ); ?> *</th>
		<td><label><input type="hidden" name="mailster_options[timezone]" value=""><input type="checkbox" name="mailster_options[timezone]" value="1" <?php checked( mailster_option( 'timezone' ) ); ?>> <?php esc_html_e( 'Send Campaigns based on the subscribers timezone if known', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-embed-images">
		<th scope="row"><?php esc_html_e( 'Embed Images', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[embed_images]" value=""><input type="checkbox" name="mailster_options[embed_images]" value="1" <?php checked( mailster_option( 'embed_images' ) ); ?>> <?php esc_html_e( 'Embed images in the mail', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-module-thumbnails">
		<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[module_thumbnails]" value=""><input type="checkbox" name="mailster_options[module_thumbnails]" value="1" <?php checked( mailster_option( 'module_thumbnails' ) ); ?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-post-list-count">
		<th scope="row"><?php esc_html_e( 'Post List Count', 'mailster' ); ?></th>
		<td><input type="text" name="mailster_options[post_count]" value="<?php echo esc_attr( mailster_option( 'post_count' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Number of posts or images displayed at once in the editbar.', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-auto-update">
		<th scope="row"><?php esc_html_e( 'Auto Update', 'mailster' ); ?></th>
		<td>
		<?php
		$is    = mailster_option( 'autoupdate', 'minor' );
		$types = array(
			'1'     => esc_html__( 'enabled', 'mailster' ),
			'0'     => esc_html__( 'disabled', 'mailster' ),
			'minor' => esc_html__( 'only minor updates', 'mailster' ),
		);
		?>
		<select name="mailster_options[autoupdate]">
			<?php foreach ( $types as $value => $name ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $is == $value ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'auto updates are recommended for important fixes.', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-system-mails">
		<th scope="row"><?php esc_html_e( 'System Mails', 'mailster' ); ?><a class="infolink external" href="https://kb.mailster.co/how-can-i-use-mailster-with-the-wp_mail-function/"></a>
		<p class="description"><?php esc_html_e( 'Decide how Mailster uses the wp_mail function.', 'mailster' ); ?></p>
		</th>
		<td>
		<p><label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="0" <?php checked( ! mailster_option( 'system_mail' ) ); ?>> <?php esc_html_e( 'Do not use Mailster for outgoing WordPress mails', 'mailster' ); ?></label></p>
		<p><label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="1" <?php checked( mailster_option( 'system_mail' ) == 1 ); ?>> <?php esc_html_e( 'Use Mailster for all outgoing WordPress mails', 'mailster' ); ?></label><br>
			<label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="template" <?php checked( mailster_option( 'system_mail' ) == 'template' ); ?>> <?php esc_html_e( 'Use only the template for all outgoing WordPress mails', 'mailster' ); ?></label></p>
		<p>&nbsp;&nbsp;<?php esc_html_e( 'use', 'mailster' ); ?>
		<?php
		mailster( 'helper' )->notifcation_template_dropdown( mailster_option( 'system_mail_template', 'notification.html' ), 'mailster_options[system_mail_template]', ! mailster_option( 'system_mail' ) );
		esc_html_e( 'and', 'mailster' );
		?>
			<select name="mailster_options[respect_content_type]"<?php echo ! mailster_option( 'system_mail' ) ? ' disabled' : ''; ?>>
				<option value="0" <?php selected( ! mailster_option( 'respect_content_type' ) ); ?>><?php esc_html_e( 'ignore', 'mailster' ); ?></option>
				<option value="1" <?php selected( mailster_option( 'respect_content_type' ) ); ?>><?php esc_html_e( 'respect', 'mailster' ); ?></option>
			</select>
			<?php esc_html_e( 'third party content type settings.', 'mailster' ); ?>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-charset-encoding">
		<th scope="row"><?php esc_html_e( 'CharSet', 'mailster' ); ?> / <?php esc_html_e( 'Encoding', 'mailster' ); ?></th>
		<td>
		<?php
		$is       = mailster_option( 'charset', 'UTF-8' );
		$charsets = array(
			'UTF-8'       => 'Unicode 8',
			'ISO-8859-1'  => 'Western European',
			'ISO-8859-2'  => 'Central European',
			'ISO-8859-3'  => 'South European',
			'ISO-8859-4'  => 'North European',
			'ISO-8859-5'  => 'Latin/Cyrillic',
			'ISO-8859-6'  => 'Latin/Arabic',
			'ISO-8859-7'  => 'Latin/Greek',
			'ISO-8859-8'  => 'Latin/Hebrew',
			'ISO-8859-9'  => 'Turkish',
			'ISO-8859-10' => 'Nordic',
			'ISO-8859-11' => 'Latin/Thai',
			'ISO-8859-13' => 'Baltic Rim',
			'ISO-8859-14' => 'Celtic',
			'ISO-8859-15' => 'Western European revision',
			'ISO-8859-16' => 'South-Eastern European',
		)
		?>
		<select name="mailster_options[charset]">
			<?php foreach ( $charsets as $code => $region ) : ?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ); ?>><?php echo $code; ?> - <?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		$is       = mailster_option( 'encoding', '8bit' );
		$encoding = array(
			'8bit'             => '8bit',
			'7bit'             => '7bit',
			'binary'           => 'binary',
			'base64'           => 'base64',
			'quoted-printable' => 'quoted-printable',
		)
		?>
		 /
		<select name="mailster_options[encoding]">
			<?php foreach ( $encoding as $code ) : ?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ); ?>><?php echo $code; ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'change Charset and encoding of your mails if you have problems with some characters', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-google-api-key">
		<th scope="row"><?php esc_html_e( 'Google API Key', 'mailster' ); ?>
		</th>
		<td><input type="password" name="mailster_options[google_api_key]" value="<?php echo esc_attr( mailster_option( 'google_api_key' ) ); ?>" class="regular-text" autocomplete="new-password">
		<p class="description">
		<?php esc_html_e( 'The Google API key is used to display Maps for Mailster on the back end.', 'mailster' ); ?><br>
		<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" class="external"><?php esc_html_e( 'Get your Google API Key.', 'mailster' ); ?></a></p>
		</td>
	</tr>
</table>
<p class="description">* <?php esc_html_e( 'can be changed in each campaign', 'mailster' ); ?></p>
