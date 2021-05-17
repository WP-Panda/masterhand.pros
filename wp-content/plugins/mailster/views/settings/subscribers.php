<table class="form-table">
	<tr valign="top" class="settings-row settings-row-notification">
		<th scope="row"><?php esc_html_e( 'Notification', 'mailster' ); ?></th>
		<td>
		<p>
			<label><input type="hidden" name="mailster_options[subscriber_notification]" value=""><input type="checkbox" name="mailster_options[subscriber_notification]" value="1" <?php checked( mailster_option( 'subscriber_notification' ) ); ?>> <?php esc_html_e( 'Send a notification of new subscribers to following receivers (comma separated)', 'mailster' ); ?> <input type="text" name="mailster_options[subscriber_notification_receviers]" value="<?php echo esc_attr( mailster_option( 'subscriber_notification_receviers' ) ); ?>" class="regular-text"></label>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'use', 'mailster' ); ?>
			<?php mailster( 'helper' )->notifcation_template_dropdown( mailster_option( 'subscriber_notification_template', 'notification.html' ), 'mailster_options[subscriber_notification_template]' ); ?>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'send', 'mailster' ); ?>
			<select name="mailster_options[subscriber_notification_delay]">
			<?php $selected = mailster_option( 'subscriber_notification_delay' ); ?>
				<option value="0"<?php selected( ! $selected ); ?>><?php esc_html_e( 'immediately', 'mailster' ); ?></option>
				<option value="day"<?php selected( 'day' == $selected ); ?>><?php esc_html_e( 'daily', 'mailster' ); ?></option>
				<option value="week"<?php selected( 'week' == $selected ); ?>><?php esc_html_e( 'weekly', 'mailster' ); ?></option>
				<option value="month"<?php selected( 'month' == $selected ); ?>><?php esc_html_e( 'monthly', 'mailster' ); ?></option>
			</select>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-notification">
		<th scope="row">&nbsp;</th>
		<td>
		<p>
			<label><input type="hidden" name="mailster_options[unsubscribe_notification]" value=""><input type="checkbox" name="mailster_options[unsubscribe_notification]" value="1" <?php checked( mailster_option( 'unsubscribe_notification' ) ); ?>> <?php esc_html_e( 'Send a notification if subscribers cancel their subscription to following receivers (comma separated)', 'mailster' ); ?> <input type="text" name="mailster_options[unsubscribe_notification_receviers]" value="<?php echo esc_attr( mailster_option( 'unsubscribe_notification_receviers' ) ); ?>" class="regular-text"></label>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'use', 'mailster' ); ?>
			<?php mailster( 'helper' )->notifcation_template_dropdown( mailster_option( 'unsubscribe_notification_template', 'notification.html' ), 'mailster_options[unsubscribe_notification_template]' ); ?>

			<br>&nbsp;&nbsp;<?php esc_html_e( 'send', 'mailster' ); ?>
			<select name="mailster_options[unsubscribe_notification_delay]">
			<?php $selected = mailster_option( 'unsubscribe_notification_delay' ); ?>
				<option value="0"<?php selected( ! $selected ); ?>><?php esc_html_e( 'immediately', 'mailster' ); ?></option>
				<option value="day"<?php selected( 'day' == $selected ); ?>><?php esc_html_e( 'daily', 'mailster' ); ?></option>
				<option value="week"<?php selected( 'week' == $selected ); ?>><?php esc_html_e( 'weekly', 'mailster' ); ?></option>
				<option value="month"<?php selected( 'month' == $selected ); ?>><?php esc_html_e( 'monthly', 'mailster' ); ?></option>
			</select>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-list-based-subscription">
		<th scope="row"><?php esc_html_e( 'List Based Subscription', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[list_based_opt_in]" value=""><input type="checkbox" name="mailster_options[list_based_opt_in]" value="1" <?php checked( mailster_option( 'list_based_opt_in' ) ); ?>> <?php esc_html_e( 'Subscribers sign up on a per list basis instead of globally.', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-single-opt-out">
		<th scope="row"><?php esc_html_e( 'Single-Opt-Out', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[single_opt_out]" value=""><input type="checkbox" name="mailster_options[single_opt_out]" value="1" <?php checked( mailster_option( 'single_opt_out' ) ); ?>> <?php esc_html_e( 'Subscribers instantly signed out after clicking the unsubscribe link in mails', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-mail-app-unsubscribe">
		<th scope="row"><?php esc_html_e( 'Mail App Unsubscribe', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[mail_opt_out]" value=""><input type="checkbox" name="mailster_options[mail_opt_out]" value="1" <?php checked( mailster_option( 'mail_opt_out' ) ); ?>> <?php esc_html_e( 'Allow Subscribers to opt out from their mail application if applicable.', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-name-order">
		<th scope="row"><?php esc_html_e( 'Name Order', 'mailster' ); ?></th>
		<td>
		<select name="mailster_options[name_order]">
			<option value="0"<?php selected( ! mailster_option( 'name_order' ) ); ?>><?php esc_html_e( 'Firstname', 'mailster' ); ?> <?php esc_html_e( 'Lastname', 'mailster' ); ?></option>
			<option value="1"<?php selected( mailster_option( 'name_order' ) ); ?>><?php esc_html_e( 'Lastname', 'mailster' ); ?> <?php esc_html_e( 'Firstname', 'mailster' ); ?></option>
		</select>
		<p class="description"><?php printf( esc_html__( 'Define in which order names appear in your language or country. This is used for the %s tag.', 'mailster' ), '<code>{fullname}</code>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-custom-fields">
		<th scope="row"><?php esc_html_e( 'Custom Fields', 'mailster' ); ?>:
			<p class="description"><?php esc_html_e( 'Custom field tags are individual tags for each subscriber. You can ask for them on subscription and/or make it a required field.', 'mailster' ); ?></p>
			<p class="description"><?php esc_html_e( 'You have to enable Custom fields for each form:', 'mailster' ); ?><br><a href="edit.php?post_type=newsletter&page=mailster_forms"><?php esc_html_e( 'Forms', 'mailster' ); ?></a></p>
		</th>
		<td>
		<input type="hidden" name="mailster_options[custom_field][0]" value="empty">
			<div class="customfields">
		<?php if ( $customfields ) : ?>
			<?php
			$types = array(
				'textfield' => esc_html__( 'Textfield', 'mailster' ),
				'textarea'  => esc_html__( 'Textarea', 'mailster' ),
				'dropdown'  => esc_html__( 'Dropdown Menu', 'mailster' ),
				'radio'     => esc_html__( 'Radio Buttons', 'mailster' ),
				'checkbox'  => esc_html__( 'Checkbox', 'mailster' ),
				'date'      => esc_html__( 'Date', 'mailster' ),
			);
			?>
			<?php foreach ( $customfields as $id => $data ) : ?>
				<div class="customfield">
					<a class="customfield-move-up" title="<?php esc_attr_e( 'move up', 'mailster' ); ?>">&#9650;</a>
					<a class="customfield-move-down" title="<?php esc_attr_e( 'move down', 'mailster' ); ?>">&#9660;</a>
					<div><span class="label"><?php esc_html_e( 'Field Name', 'mailster' ); ?>:</span><label><input type="text" name="mailster_options[custom_field][<?php echo $id; ?>][name]" value="<?php echo esc_attr( $data['name'] ); ?>" class="regular-text customfield-name"></label></div>
					<div><span class="label"><?php esc_html_e( 'Tag', 'mailster' ); ?>:</span><span><code>{</code><input type="text" name="mailster_options[custom_field][<?php echo $id; ?>][id]" value="<?php echo sanitize_key( $id ); ?>" class="code"><code>}</code></span></div>
					<div><span class="label"><?php esc_html_e( 'Type', 'mailster' ); ?>:</span><select class="customfield-type" name="mailster_options[custom_field][<?php echo $id; ?>][type]">
					<?php
					foreach ( $types as $value => $name ) {
						echo '<option value="' . $value . '" ' . selected( $data['type'], $value, false ) . '>' . esc_attr( $name ) . '</option>';
					}
					?>
					</select>
				</div>
				<ul class="customfield-additional customfield-dropdown customfield-radio"<?php echo in_array( $data['type'], array( 'dropdown', 'radio' ) ) ? ' style="display:block"' : ''; ?>>
					<li>
					<ul class="customfield-values">
						<?php $values = ! empty( $data['values'] ) ? $data['values'] : array( '' ); ?>
						<?php foreach ( $values as $value ) : ?>
						<li>
							<span>&nbsp;</span>
							<span class="customfield-value-box"><input type="text" name="mailster_options[custom_field][<?php echo $id; ?>][values][]" class="regular-text customfield-value" value="<?php echo $value; ?>">
								<label><input type="radio" name="mailster_options[custom_field][<?php echo $id; ?>][default]" value="<?php echo $value; ?>" title="<?php esc_attr_e( 'this field is selected by default', 'mailster' ); ?>" <?php checked( isset( $data['default'] ) && $data['default'], true ); ?><?php disabled( ! in_array( $data['type'], array( 'dropdown', 'radio' ) ) ); ?>>
										<?php esc_html_e( 'default', 'mailster' ); ?>
								</label> &nbsp; <a class="customfield-value-remove" title="<?php esc_attr_e( 'remove field', 'mailster' ); ?>">&#10005;</a>
							</span>
						</li>
						<?php endforeach; ?>
					</ul>
					<span>&nbsp;</span> <a class="customfield-value-add"><?php esc_html_e( 'add field', 'mailster' ); ?></a>
					</li>
				</ul>
				<?php if ( 'checkbox' == $data['type'] ) : ?>
					<div class="customfield-additional customfield-checkbox" style="display:block">
						<span>&nbsp;</span>
						<label><input type="hidden" name="mailster_options[custom_field][<?php echo $id; ?>][default]" value=""><input type="checkbox" name="mailster_options[custom_field][<?php echo $id; ?>][default]" value="1" title="<?php esc_attr_e( 'this field is selected by default', 'mailster' ); ?>"<?php checked( isset( $data['default'] ) && $data['default'], true ); ?>>
						<?php esc_html_e( 'checked by default', 'mailster' ); ?>
						</label>
					</div>
				<?php endif; ?>
					<a class="customfield-remove"><?php esc_html_e( 'remove field', 'mailster' ); ?></a>
					<br>
				</div>
		<?php endforeach; ?>
	<?php endif; ?>
			</div>
			<input type="button" value="<?php esc_attr_e( 'add', 'mailster' ); ?>" class="button" id="mailster_add_field">
		</td>
	</tr>
</table>
