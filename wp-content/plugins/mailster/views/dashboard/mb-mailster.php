<?php
	$plugin_info = mailster()->plugin_info();
	$dateformat  = mailster( 'helper' )->dateformat();

	$license_email = '';
	$license_user  = '';

if ( mailster()->is_verified() ) {
	$license_user  = mailster()->username( '' );
	$license_email = mailster()->email( '' );
}

?>
<div class="locked">
	<h2><span class="not-valid"><?php esc_html_e( 'Please Validate', 'mailster' ); ?></span><span class="valid"><?php esc_html_e( 'Validated!', 'mailster' ); ?></span>
	</h2>
</div>
<dl class="mailster-icon mailster-icon-finished valid">
	<dt><?php esc_html_e( 'Verified License', 'mailster' ); ?></dt>
	<dd><?php printf( esc_html__( 'User: %1$s - %2$s', 'mailster' ), '<span class="mailster-username">' . esc_html( $license_user ) . '</span>', '<span class="mailster-email lighter">' . esc_html( $license_email ) . '</span>' ); ?></dd>
	<?php if ( ! mailster()->is_email_verified() ) : ?>
		<dd style="color:#D54E21"><?php esc_html_e( 'Please verify your Mailster account!', 'mailster' ); ?></dd>
	<?php endif; ?>
	<dd>
		<?php if ( current_user_can( 'mailster_manage_licenses' ) ) : ?>
		<a href="https://mailster.co/manage-licenses/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Manage Licenses', 'mailster' ); ?></a> |
		<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard&reset_license=' . wp_create_nonce( 'mailster_reset_license' ) ); ?>" class="reset-license"><?php esc_html_e( 'Reset License', 'mailster' ); ?></a> |
		<?php endif; ?>
		<a href="https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Buy new License', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-delete not-valid">
	<dt><?php esc_html_e( 'Not Verified', 'mailster' ); ?></dt>
	<dd><?php esc_html_e( 'Your license has not been verified', 'mailster' ); ?></dd>
	<dd>
		<?php if ( current_user_can( 'mailster_manage_licenses' ) ) : ?>
		<a href="https://mailster.co/manage-licenses/" class="external"><?php esc_html_e( 'Manage Licenses', 'mailster' ); ?></a> |
		<?php endif; ?>
		<a href="https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Buy new License', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-reload update-not-available">
	<dt><?php printf( esc_html__( 'Installed Version %s', 'mailster' ), MAILSTER_VERSION ); ?></dt>
	<dd><?php esc_html_e( 'You have the latest version', 'mailster' ); ?></dd>
	<dd><span class="lighter"><?php echo isset( $plugin_info->last_update ) ? sprintf( esc_html__( 'checked %s ago', 'mailster' ), '<span class="update-last-check">' . human_time_diff( $plugin_info->last_update ) . '</span>' ) . ' &ndash; ' : ''; ?></span> <span class="lighter"><a href="" class="check-for-update"><?php esc_html_e( 'Check Again', 'mailster' ); ?></a></span>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-reload update-available">
	<dt><?php printf( esc_html__( 'Installed Version %s', 'mailster' ), MAILSTER_VERSION ); ?></dt>
	<dd><?php esc_html_e( 'A new Version is available', 'mailster' ); ?></dd>
	<dd><a class="thickbox" href="<?php echo network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=mailster&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=745' ); ?>"><?php esc_html_e( 'view changelog', 'mailster' ); ?></a> <?php esc_html_e( 'or', 'mailster' ); ?> <a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( MAILSTER_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . MAILSTER_SLUG ); ?>" class="update-button"><?php printf( esc_html__( 'update to %s now', 'mailster' ), '<span class="update-version">' . $plugin_info->new_version . '</span>' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-support">
	<dt><?php esc_html_e( 'Support', 'mailster' ); ?></dt>
	<?php if ( mailster()->support() ) : ?>
		<?php if ( mailster()->has_support() ) : ?>
		<dd><span class="lighter"><?php printf( esc_html__( 'Your support expires on %s.', 'mailster' ), '<span class="">' . esc_html( date( $dateformat, mailster()->support() ) ) . '</span>' ); ?></span></dd>
		<?php else : ?>
		<dd><strong><?php printf( esc_html__( 'Your support expired %s ago!', 'mailster' ), '<span class="mailster-username">' . esc_html( human_time_diff( mailster()->support() ) ) . '</span>' ); ?></strong> &ndash; <a href="https://mailster.co/go/buy-support?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Renew Support', 'mailster' ); ?></a></dd>
		<?php endif; ?>
	<?php endif; ?>
	<dd>
		<a href="https://docs.mailster.co/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Documentation', 'mailster' ); ?></a> |
		<a href="https://kb.mailster.co/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Knowledge Base', 'mailster' ); ?></a> |
	<?php if ( mailster()->has_support() || ! mailster()->support() ) : ?>
		<a href="https://mailster.co/support/?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Support', 'mailster' ); ?></a> |
	<?php else : ?>
		<a href="https://mailster.co/go/buy-support?utm_campaign=plugin&utm_medium=dashboard&utm_source=mailster_plugin" class="external"><?php esc_html_e( 'Renew Support', 'mailster' ); ?></a> |
	<?php endif; ?>
		<a href="<?php echo admin_url( 'admin.php?page=mailster_tests' ); ?>"><?php esc_html_e( 'Self Test', 'mailster' ); ?></a>
	</dd>
</dl>
<?php if ( current_user_can( 'install_languages' ) && $set = mailster( 'translations' )->get_translation_set() ) : ?>
<dl class="mailster-icon mailster-dash mailster-icon-translate">
	<dt><?php esc_html_e( 'Translation', 'mailster' ); ?> </dt>
	<?php if ( mailster( 'translations' )->translation_installed() ) : ?>
		<?php $name = ( esc_html_x( 'Thanks for using Mailster in %s!', 'Your language', 'mailster' ) == 'Thanks for using Mailster in %s!' ) ? $set->name : $set->native_name; ?>
	<dd><?php printf( esc_html_x( 'Thanks for using Mailster in %s!', 'Your language', 'mailster' ), '<strong>' . esc_html( $name ) . '</strong>' ); ?></dd>
		<?php if ( mailster( 'translations' )->translation_available() ) : ?>
	<dd><a href="" class="load-language"><strong><?php esc_html_e( 'Update Translation', 'mailster' ); ?></strong></a></dd>
		<?php endif; ?>
	<?php elseif ( mailster( 'translations' )->translation_available() ) : ?>
	<dd><?php printf( esc_html__( 'Mailster is available in %s!', 'mailster' ), '<strong>' . esc_html( $set->name ) . '</strong>' ); ?></dd>
	<dd><a href="" class="load-language"><strong><?php esc_html_e( 'Download Translation', 'mailster' ); ?></strong></a></dd>
	<?php endif; ?>
	<dd><span class="lighter"><?php printf( esc_html__( 'Currently %s translated.', 'mailster' ), '<strong>' . $set->percent_translated . '%</strong>' ); ?></span></dd>
</dl>
<?php endif; ?>
