<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Simple Checks' , 'mailster-email-verify' ) ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[sev_check_mx]" value=""><input type="checkbox" name="mailster_options[sev_check_mx]" value="1" <?php checked( mailster_option( 'sev_check_mx' ) ); ?>><?php esc_html_e( 'Check MX record', 'mailster' );?></label><br><span class="description"><?php esc_html_e( 'Check the domain for an existing MX record.', 'mailster-email-verify' );?></span>
		</p>
		<p><label><input type="hidden" name="mailster_options[sev_check_smtp]" value=""><input type="checkbox" name="mailster_options[sev_check_smtp]" value="1" <?php checked( mailster_option( 'sev_check_smtp' ) ); ?>><?php esc_html_e( 'Validate via SMTP', 'mailster' );?></label><br><span class="description"><?php esc_html_e( 'Connects the domain\'s SMTP server to check if the address really exists.', 'mailster-email-verify' );?></span></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'mailster-email-verify' ) ?>:</strong>
		<input type="text" name="mailster_options[sev_check_error]" value="<?php echo esc_attr( mailster_option( 'sev_check_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Disposable Email Provider' , 'mailster-email-verify' ) ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[sev_dep]" value=""><input type="checkbox" name="mailster_options[sev_dep]" value="1" <?php checked( mailster_option( 'sev_dep' ) ); ?>><?php esc_html_e( 'reject email addresses from disposable email providers (DEP).', 'mailster' );?></label></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'mailster-email-verify' ) ?>:</strong>
		<input type="text" name="mailster_options[sev_dep_error]" value="<?php echo esc_attr( mailster_option( 'sev_dep_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Blacklisted Email Addresses' , 'mailster-email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted email addresses. One email each line.' , 'mailster-email-verify' ) ?><br>
		<textarea name="mailster_options[sev_emails]" placeholder="<?php echo "john@blacklisted.com\njane@blacklisted.co.uk\nhans@blacklisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'sev_emails' ) ) ?></textarea></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'mailster-email-verify' ) ?>:</strong>
		<input type="text" name="mailster_options[sev_emails_error]" value="<?php echo esc_attr( mailster_option( 'sev_emails_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Reject Domains' , 'mailster-email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted domains. One domain each line.' , 'mailster-email-verify' ) ?><br>
		<textarea name="mailster_options[sev_domains]" placeholder="<?php echo "blacklisted.com\nblacklisted.co.uk\nblacklisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'sev_domains' ) ) ?></textarea></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'mailster-email-verify' ) ?>:</strong>
		<input type="text" name="mailster_options[sev_domains_error]" value="<?php echo esc_attr( mailster_option( 'sev_domains_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'White listed Domains' , 'mailster-email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List domains which bypass the above rules. One domain each line.' , 'mailster-email-verify' ) ?><br>
		<textarea name="mailster_options[sev_whitelist]" placeholder="<?php echo "whitelisted.com\nwhitelisted.co.uk\nwhitelisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( mailster_option( 'sev_whitelist' ) ) ?></textarea></p>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Import' , 'mailster-email-verify' ) ?></th>
		<td><p><label><input type="hidden" name="mailster_options[sev_import]" value=""><input type="checkbox" name="mailster_options[sev_import]" value="1" <?php checked( mailster_option( 'sev_import' ) ) ?>> <?php esc_html_e( 'use for import' , 'mailster-email-verify' ) ?></label></p>
		<p class="description"><?php esc_html_e( 'This will significantly increase import time because for every subscriber WordPress needs to verify the email address on the given domain. It\'s better to import a cleaned list.' , 'mailster-email-verify' ) ?></p>
		</td>
	</tr>
</table>
