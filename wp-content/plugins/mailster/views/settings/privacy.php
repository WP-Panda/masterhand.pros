<table class="form-table">
	<tr valign="top" class="settings-row settings-row-tracking">
		<th scope="row"><?php esc_html_e( 'Tracking', 'mailster' ); ?></th>
		<td>
		<p><label><input type="hidden" name="mailster_options[track_opens]" value=""><input type="checkbox" name="mailster_options[track_opens]" value="1" <?php checked( mailster_option( 'track_opens' ) ); ?>> <?php esc_html_e( 'Track opens in your campaigns', 'mailster' ); ?></label></p>
		<p><label><input type="hidden" name="mailster_options[track_clicks]" value=""><input type="checkbox" name="mailster_options[track_clicks]" value="1" <?php checked( mailster_option( 'track_clicks' ) ); ?>> <?php esc_html_e( 'Track clicks in your campaigns', 'mailster' ); ?></label></p>

		<?php
			$geoip                 = isset( $_GET['nogeo'] ) ? false : mailster_option( 'track_location' );
			$geo_db_file_countries = mailster( 'geo' )->get_file_path( 'country' );
			$geo_db_file_cities    = mailster( 'geo' )->get_file_path( 'city' );
		?>

		<p><label><input type="hidden" name="mailster_options[track_location]" value=""><input type="checkbox" id="mailster_geoip" name="mailster_options[track_location]" value="1" <?php checked( $geoip ); ?>> <?php esc_html_e( 'Track location in campaigns', 'mailster' ); ?>*</label>
			<br>&nbsp;&#x2514;&nbsp;<label><input type="hidden" name="mailster_options[track_location_update]" value=""><input type="checkbox" name="mailster_options[track_location_update]" value="1" <?php checked( mailster_option( 'track_location_update' ) ); ?>> <?php esc_html_e( 'Update location database automatically', 'mailster' ); ?></label>
		</p>

	<?php if ( ! mailster()->is( 'setup' ) && $geoip && is_file( $geo_db_file_cities ) ) : ?>
		<p class="description"><?php esc_html_e( 'If you don\'t find your country down below the geo database is missing or corrupt', 'mailster' ); ?></p>
		<p>
	<strong><?php esc_html_e( 'Your IP', 'mailster' ); ?>:</strong> <?php echo mailster_get_ip(); ?><?php if ( mailster_is_local() ) : ?>
	<strong><?php esc_html_e( 'Geolocation is not available on localhost!', 'mailster' ); ?></strong>
	<?php endif; ?><br>
		<strong><?php esc_html_e( 'Your country', 'mailster' ); ?>:</strong> <?php echo mailster_ip2Country( '', 'name' ); ?><br>
		<?php if ( is_file( $geo_db_file_cities ) ) : ?>
		<strong><?php esc_html_e( 'Your city', 'mailster' ); ?>:</strong> <?php echo mailster_ip2City( '', 'city' ); ?>
	<?php endif; ?>
		</p>
		<p><button id="load_location_db" class="button-primary" <?php disabled( ! $geoip ); ?>><?php esc_html_e( 'Update Location Database', 'mailster' ); ?></button>&nbsp;<span class="loading geo-ajax-loading"></span>
			<em id="location_last_update"><?php esc_html_e( 'Last update', 'mailster' ); ?>: <?php printf( esc_html__( '%s ago', 'mailster' ), human_time_diff( filemtime( $geo_db_file_cities ) ) ); ?></em>
		</p>
	<?php elseif ( $geoip ) : ?>
		<div class="error inline"><p><?php esc_html_e( 'Looks like the location database hasn\'t been loaded yet!', 'mailster' ); ?></p></div>
		<p><button id="load_location_db" class="button-primary"><?php esc_html_e( 'Load Location Database manually', 'mailster' ); ?></button>&nbsp;<span class="loading geo-ajax-loading"></span>
			<em id="location_last_update"></em>
		</p>
	<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-save-subscribers-ip">
		<th scope="row"><?php esc_html_e( 'Save Subscriber IP', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[track_users]" value=""><input type="checkbox" name="mailster_options[track_users]" value="1" <?php checked( mailster_option( 'track_users' ) ); ?>> <?php esc_html_e( 'Save IP address and time of new subscribers', 'mailster' ); ?></label>
		<p class="description"><?php esc_html_e( 'In some countries it\'s required to save the IP address and the sign up time for legal reasons. Please add a note in your privacy policy if you save users data', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-do-not-track">
		<th scope="row">Do Not Track</th>
		<td><label><input type="hidden" name="mailster_options[do_not_track]" value=""><input type="checkbox" name="mailster_options[do_not_track]" value="1" <?php checked( mailster_option( 'do_not_track' ) ); ?>> <?php esc_html_e( 'Respect users "Do Not Track" option', 'mailster' ); ?></label>
		<p class="description"><?php printf( esc_html__( 'If enabled Mailster will respect users option for not getting tracked. Read more on the %s', 'mailster' ), '<a href="http://donottrack.us/" class="external">' . esc_html__( 'official website', 'mailster' ) . '</a>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-custom-tags-in-web-version">
		<th scope="row"><?php esc_html_e( 'Custom Tags in web version', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[tags_webversion]" value=""><input type="checkbox" name="mailster_options[tags_webversion]" value="1" <?php checked( mailster_option( 'tags_webversion' ) ); ?>> <?php esc_html_e( 'Show subscribers tags in web version.', 'mailster' ); ?></label>
		<p class="description"><?php esc_html_e( 'Mailster can display custom tags from subscribers on the web version of your campaigns. They will only get displayed if they click a link in the newsletter.', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-gdpr-compliance-forms">
		<th scope="row"><?php esc_html_e( 'GDPR Compliance Forms', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[gdpr_forms]" value=""><input type="checkbox" name="mailster_options[gdpr_forms]" value="1" <?php checked( mailster_option( 'gdpr_forms' ) ); ?>> <?php esc_html_e( 'Add a checkbox on your forms for user consent.', 'mailster' ); ?></label>
		<p class="description"><?php esc_html_e( 'Users must check this checkbox to submit the form.', 'mailster' ); ?></p>
		<p class="description"><?php printf( esc_html__( 'You can define Texts on the %s settings tab.', 'mailster' ), '<strong>' . esc_html__( 'Text Strings', 'mailster' ) . '</strong>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-link-to-privacy-page">
		<th scope="row"></th>
		<td>
		<p><?php esc_html_e( 'Link to your privacy policy page.', 'mailster' ); ?>
			<input type="text" name="mailster_options[gdpr_link]" value="<?php echo esc_attr( mailster_option( 'gdpr_link' ) ); ?>" class="large-text">
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-maxmind-copy">
		<th scope="row"></th>
		<td><p class="description">* This product includes GeoLite data created by MaxMind, available from <a href="https://www.maxmind.com" class="external">maxmind.com</a></p>
		</td>
	</tr>
</table>

