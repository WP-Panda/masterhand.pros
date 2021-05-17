<table class="form-table">
	<tr valign="top" class="settings-row settings-row-default-template">
		<th scope="row"><?php esc_html_e( 'Default Template', 'mailster' ); ?></th>
		<td><p><select name="mailster_options[default_template]" class="postform">
		<?php
		$templates = mailster( 'templates' )->get_templates();
		$selected  = mailster_option( 'default_template' );
		?>
		<?php foreach ( $templates as $slug => $data ) : ?>
			<option value="<?php echo $slug; ?>"<?php selected( $selected, $slug ); ?>><?php echo esc_html( $data['name'] ); ?></option>
		<?php endforeach; ?>
		</select> <a href="edit.php?post_type=newsletter&page=mailster_templates"><?php esc_html_e( 'show Templates', 'mailster' ); ?></a> | <a href="edit.php?post_type=newsletter&page=mailster_templates&more"><?php esc_html_e( 'get more', 'mailster' ); ?></a>
		</p></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-logo">
		<th scope="row"><?php esc_html_e( 'Logo', 'mailster' ); ?> *
		<p class="description"><?php esc_html_e( 'Use a logo for new created campaigns', 'mailster' ); ?></p>
		</th>
		<td>
		<?php mailster( 'helper' )->media_editor_link( mailster_option( 'logo', get_theme_mod( 'custom_logo' ) ), 'mailster_options[logo]', 'full' ); ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-logo-link">
		<th scope="row"><?php esc_html_e( 'Logo Link', 'mailster' ); ?> *</th>
		<td><input type="text" name="mailster_options[logo_link]" value="<?php echo esc_attr( mailster_option( 'logo_link' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'A link for your logo.', 'mailster' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-social-services">
		<th scope="row"><?php esc_html_e( 'Social Services', 'mailster' ); ?> *
		<p class="description"><?php esc_html_e( 'Use links to your social account in your campaigns', 'mailster' ); ?></p>
		</th>
		<td>
		<?php
			$social_links = mailster( 'helper' )->get_social_links( '%s', true );
			$services     = mailster_option( 'services', array() );
			$services     = array( '0' => '' ) + $services;
		?>
			<ul id="social-services">
		<?php foreach ( $services as $service => $username ) : ?>
				<li>
					<a href="" class="social-service-remove" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
					<select class="social-service-dropdown">
						<option value="0"><?php esc_html_e( 'choose', 'mailster' ); ?></option>
					<?php foreach ( $social_links as $social_link_service => $link ) : ?>
						<option value="<?php echo esc_attr( $social_link_service ); ?>" data-url="<?php echo esc_attr( $link ); ?>" <?php selected( $service, $social_link_service ); ?>><?php echo esc_html( $social_link_service ); ?></option>
					<?php endforeach; ?>
					</select>
					<span class="social-service-url-field">
					<?php if ( $service ) : ?>
						<label><span class="description"><?php echo str_replace( '%s', '<input type="text" name="mailster_options[services][' . esc_attr( $service ) . ']" value="' . esc_attr( $username ) . '" class="regular-text">', $social_links[ $service ] ); ?></span></label>
					<?php endif; ?>
					</span>
				</li>
		<?php endforeach; ?>
			</ul>
			<a class="button social-service-add"><?php esc_html_e( 'add', 'mailster' ); ?></a>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-high-dpi">
		<th scope="row"><?php esc_html_e( 'High DPI', 'mailster' ); ?> *
		</th>
		<td>
			<p class="description"><label><input type="hidden" name="mailster_options[high_dpi]" value=""><input type="checkbox" name="mailster_options[high_dpi]" value="1" <?php checked( mailster_option( 'high_dpi' ) ); ?>> <?php esc_html_e( 'Use High DPI or retina ready images if available.', 'mailster' ); ?></label></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-template-notice">
		<th scope="row">&nbsp;</th>
		<td>
			<p class="description">* <?php esc_html_e( 'Depending on your used template these features may not work!', 'mailster' ); ?></p>
		</td>
	</tr>
</table>
