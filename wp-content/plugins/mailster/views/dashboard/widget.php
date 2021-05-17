<div class="mailster-dashboard">
<?php
	require MAILSTER_DIR . 'views/dashboard/mb-campaigns.php';
	require MAILSTER_DIR . 'views/dashboard/mb-subscribers.php';
?>
	<div class="versions">
		<span class="textleft">Mailster <?php echo esc_html( MAILSTER_VERSION ); ?></span>

		<?php
		if ( current_user_can( 'update_plugins' ) && ! is_plugin_active_for_network( MAILSTER_SLUG ) ) :
			$plugin_info = mailster()->plugin_info();
			$plugins     = get_site_transient( 'update_plugins' );
			if ( isset( $plugin_info->update ) && $plugin_info->update ) {
				?>
				<a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( MAILSTER_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . MAILSTER_SLUG ); ?>" class="button button-primary alignright"><?php printf( esc_html__( 'Update to %s', 'mailster' ), $plugin_info->new_version ); ?></a>
				<?php
			}
		endif;
		?>
		<br class="clear">
	</div>
	<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
</div>
