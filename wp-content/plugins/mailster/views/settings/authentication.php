<?php
	$spf        = mailster_option( 'spf' );
	$dkim       = mailster_option( 'dkim' );
	$spf_domain = mailster_option( 'spf_domain' );
?>
<p class="description"><?php esc_html_e( 'You need to change the namespace records of your domain if you would like to use one of these methods. Ask your provider how to add "TXT namespace records". Changes take some time to get published on all DNS worldwide.', 'mailster' ); ?></p>

<table class="form-table no-margin">
	<tr valign="top" class="settings-row settings-row-spf-domain">
		<th scope="row">SPF Domain</th>
		<td><input type="text" name="mailster_options[spf_domain]" id="spf-domain" value="<?php echo esc_attr( $spf_domain ); ?>" class="regular-text dkim">
		<span class="description"><?php esc_html_e( 'The domain you would like to add a SPF record', 'mailster' ); ?></span>
		</td>
	</tr>
<?php if ( $spf_domain ) : ?>
	<tr valign="top" class="settings-row settings-row-loading-spf-data">
		<th scope="row">&nbsp;</th>
		<td>
		<div class="spf-result spinner"><?php esc_html_e( 'Loading SPF data', 'mailster' ); ?>&hellip;</div>

		<p class="description"><?php printf( esc_html__( 'SPF doesn\'t require any configuration on this settings page. This should give you some help to set it up correctly. If this SPF configuration doesn\'t work or your mails returned as spam you should ask your provider for help or change your delivery method or try %s', 'mailster' ), '<a href="http://www.openspf.org/FAQ/Common_mistakes" class="external">' . esc_html__( 'to get help here', 'mailster' ) . '</a>' ); ?></p>
		</td>
	</tr>
	<?php endif; ?>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-dkim">
		<th scope="row"><h4>DKIM</h4></th>
		<td><label><input type="hidden" name="mailster_options[dkim]" value=""><input type="checkbox" name="mailster_options[dkim]" id="mailster_dkim" value="1" <?php checked( $dkim ); ?>> <?php esc_html_e( 'Use DomainKeys Identified Mail', 'mailster' ); ?>. <a href="https://en.wikipedia.org/wiki/DomainKeys_Identified_Mail" class="external"><?php esc_html_e( 'read more', 'mailster' ); ?></a></label> </td>
	</tr>
</table>
<div class="dkim-info"<?php echo ! $dkim ? ' style="display:none"' : ''; ?>>
<table class="form-table no-margin">
<?php if ( $dkim && mailster_option( 'dkim_private_key' ) && mailster_option( 'dkim_public_key' ) ) : ?>
	<tr valign="top" class="settings-row settings-row-loading-dkim-data">
		<th scope="row">&nbsp;</th>
		<td>
		<div class="dkim-result spinner"><?php esc_html_e( 'Loading DKIM data', 'mailster' ); ?>&hellip;</div>
		</td>
	</tr>
<?php endif; ?>

	<tr valign="top" class="settings-row settings-row-dkim-domain">
		<th scope="row">DKIM Domain</th>
		<td><input type="text" name="mailster_options[dkim_domain]" value="<?php echo esc_attr( mailster_option( 'dkim_domain' ) ); ?>" class="regular-text dkim">
		<span class="description"><?php esc_html_e( 'The domain you have set the TXT namespace records', 'mailster' ); ?></span>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-dkim-selector">
		<th scope="row">DKIM Selector</th>
		<td><input type="text" name="mailster_options[dkim_selector]" value="<?php echo esc_attr( mailster_option( 'dkim_selector' ) ); ?>" class="regular-text dkim">
		<span class="description"><?php esc_html_e( 'The selector is used to identify the keys used to attach a token to the email', 'mailster' ); ?></span>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-dkim-identify">
		<th scope="row">DKIM Identity</th>
		<td><input type="text" name="mailster_options[dkim_identity]" value="<?php echo esc_attr( mailster_option( 'dkim_identity' ) ); ?>" class="regular-text dkim">
		<span class="description"><?php esc_html_e( 'You can leave this field blank unless you know what you do', 'mailster' ); ?></span>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-dkim-pass-phrase">
		<th scope="row">DKIM Pass Phrase</th>
		<td><input type="text" name="mailster_options[dkim_passphrase]" value="<?php echo esc_attr( mailster_option( 'dkim_passphrase' ) ); ?>" class="regular-text dkim">
		<span class="description"><?php esc_html_e( 'You can leave this field blank unless you know what you do', 'mailster' ); ?></span>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-keys">
		<th scope="row"><h4><?php esc_html_e( 'Keys', 'mailster' ); ?></h4></th>
		<td>
		<p class="description">
		<?php esc_html_e( 'If you have defined the domain and a selector you have to generate a public and a private key. Once created you have to add some TXT namespace records at your mail provider', 'mailster' ); ?>.
		<?php esc_html_e( 'DKIM often doesn\'t work out of the box. You may have to contact your email provider to get more information', 'mailster' ); ?>.
		<?php esc_html_e( 'Changing namespace entries can take up to 48 hours to take affect around the world.', 'mailster' ); ?>.
		<?php esc_html_e( 'It\'s recommend to change the keys occasionally', 'mailster' ); ?>.
		<?php esc_html_e( 'If you change one of the settings above new keys are required', 'mailster' ); ?>.
		<?php esc_html_e( 'Some providers don\'t allow TXT records with a specific size. Choose less bits in this case', 'mailster' ); ?>.
		</p>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-new-keys">
		<th scope="row"><?php esc_html_e( 'new Keys', 'mailster' ); ?></th>
		<td>
		<p class="dkim-create-keys">
			<?php $bitsize = mailster_option( 'dkim_bitsize', 1024 ); ?>
			<?php esc_html_e( 'Bit Size', 'mailster' ); ?>:
			<label> <input type="radio" name="mailster_options[dkim_bitsize]" value="512" <?php checked( $bitsize, 512 ); ?>> 512</label>&nbsp;
			<label> <input type="radio" name="mailster_options[dkim_bitsize]" value="768" <?php checked( $bitsize, 768 ); ?>> 768</label>&nbsp;
			<label> <input type="radio" name="mailster_options[dkim_bitsize]" value="1024" <?php checked( $bitsize, 1024 ); ?>> 1024</label>&nbsp;
			<label> <input type="radio" name="mailster_options[dkim_bitsize]" value="2048" <?php checked( $bitsize, 2048 ); ?>> 2048</label>&nbsp;
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'generate Keys', 'mailster' ); ?>" name="mailster_generate_dkim_keys" id="mailster_generate_dkim_keys" />
			<?php esc_html_e( 'or', 'mailster' ); ?>
			<a href="#" class="dkim-enter-keys"><?php esc_html_e( 'enter keys manually', 'mailster' ); ?></a>
		</p>
		</td>
	</tr>
</table>
<div class="dkim-keys"<?php echo ! mailster_option( 'dkim_private_key' ) || ! mailster_option( 'dkim_public_key' ) ? ' style="display:none"' : ''; ?>>
<table class="form-table" id="dkim_keys_active">
	<tr valign="top" class="settings-row settings-row-dkim-public-key">
		<th scope="row">DKIM Public Key</th>
		<td><textarea name="mailster_options[dkim_public_key]" id="dkim-public-key" rows="10" cols="40" class="large-text code" placeholder="-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----"><?php echo esc_attr( mailster_option( 'dkim_public_key' ) ); ?></textarea>
		<a class="clipboard" data-clipboard-target="#dkim-public-key"><?php esc_html_e( 'copy', 'mailster' ); ?></a>
	</tr>
	<tr valign="top" class="settings-row settings-row-dkim-private-key">
		<th scope="row">DKIM Private Key
			<p class="description">
		<?php esc_html_e( 'Private keys should be kept private. Don\'t share them or post it somewhere', 'mailster' ); ?>
		</p>
		</th>
		<td><textarea name="mailster_options[dkim_private_key]" id="dkim-private-key" rows="10" cols="40" class="large-text code" placeholder="-----BEGIN PRIVATE KEY-----
...
-----END PRIVATE KEY-----"><?php echo esc_attr( mailster_option( 'dkim_private_key' ) ); ?></textarea>
		<a class="clipboard" data-clipboard-target="#dkim-private-key"><?php esc_html_e( 'copy', 'mailster' ); ?></a>
		<input type="hidden" name="mailster_options[dkim_private_hash]" value="<?php echo esc_attr( mailster_option( 'dkim_private_hash' ) ); ?>" class="regular-text dkim"></td>
	</tr>
</table>
</div>

</div>
