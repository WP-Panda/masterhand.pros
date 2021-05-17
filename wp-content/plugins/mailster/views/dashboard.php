<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php


$classes = array( 'wrap', 'mailster-dashboard' );
if ( $this->update ) {
	$classes[] = 'has-update';
}

?>
<div class="<?php echo implode( ' ', $classes ); ?>">
<h1><?php esc_html_e( 'Dashboard', 'mailster' ); ?></h1>

<?php if ( ! $this->verified && current_user_can( 'mailster_manage_licenses' ) ) : ?>
	<div id="mailster-register-panel" class="welcome-panel" style="display:block !important">
		<div class="welcome-panel-content">
			<p class="about-description"></p>
			<div class="welcome-panel-column-container">

			<h2 class="welcome-header"><?php esc_html_e( 'Register for News, Support and Updates related to Mailster.', 'mailster' ); ?></h2>

				<?php mailster( 'register' )->form(); ?>

			</div>

		</div>
	</div>
<?php elseif ( ! mailster_option( 'usage_tracking' ) && mailster_option( 'ask_usage_tracking' ) && ( time() - get_option( 'mailster_updated' ) ) > HOUR_IN_SECONDS && current_user_can( 'manage_options' ) ) : ?>
	<div class="info notice">
		<h2><?php esc_html_e( 'Help us improve Mailster automatically.', 'mailster' ); ?></h2>
		<p style="max-width: 800px;"><?php esc_html_e( 'If you enable this option we are able to track the usage of Mailster on your site. We don\'t record any sensitive data but only information regarding the WordPress environment and plugin settings, which we use to make improvements to the plugin. Tracking is completely optional and can be disabled anytime.', 'mailster' ); ?><br><a href="https://kb.mailster.co/usage-tracking/" class="external"><?php esc_html_e( 'Read more about what we collect if you enable this option.', 'mailster' ); ?></a>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo wp_nonce_url( add_query_arg( 'mailster_allow_usage_tracking', 1 ), 'mailster_allow_usage_tracking', '_wpnonce' ); ?>"><?php esc_html_e( 'Yes, let me help you by enabling this option!', 'mailster' ); ?></a>
			<a class="button" href="<?php echo wp_nonce_url( add_query_arg( 'mailster_allow_usage_tracking', 0 ), 'mailster_allow_usage_tracking', '_wpnonce' ); ?>"><?php esc_html_e( 'No, I\'m not interested.', 'mailster' ); ?></a>
		</p>
	</div>
<?php endif; ?>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" data-id="normal">
				<?php do_meta_boxes( $this->screen->id, 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container" data-id="side">
				<?php do_meta_boxes( $this->screen->id, 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container" data-id="column3">
				<?php do_meta_boxes( $this->screen->id, 'column3', '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container" data-id="column4">
				<?php do_meta_boxes( $this->screen->id, 'column4', '' ); ?>
			</div>
		</div>
	</div>

<?php $addons = mailster( 'helper' )->get_addons(); ?>
<?php if ( $addons && ! is_wp_error( $addons ) ) : ?>
	<div id="addons-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<p class="about-description"></p>
			<div class="welcome-panel-column-container">

					<h2><?php esc_html_e( 'Supercharge Mailster!', 'mailster' ); ?></h2>
					<h3><?php printf( esc_html__( 'Mailster comes with %1$s extensions and supports %2$s premium templates. Get the most out of your email campaigns and start utilizing the vast amount of add ons.', 'mailster' ), count( $addons ), '80+' ); ?></h3>

					<div class="cta-buttons">
						<a class="button button-primary button-hero" href="edit.php?post_type=newsletter&page=mailster_addons"><?php esc_html_e( 'Browse Addons', 'mailster' ); ?></a>
						<a class="button button-primary button-hero" href="edit.php?post_type=newsletter&page=mailster_templates&more"><?php esc_html_e( 'Browse Templates', 'mailster' ); ?></a>
					</div>

			</div>
		</div>
	</div>
<?php endif; ?>

<div id="ajax-response"></div>
<br class="clear">
</div>
