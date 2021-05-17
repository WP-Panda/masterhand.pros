<?php

global $current_user, $wp_post_statuses, $wp_roles;

$customfields = mailster()->get_custom_fields();
$roles        = $wp_roles->get_names();

?>
<form id="mailster-settings-form" method="post" action="options.php" autocomplete="off" enctype="multipart/form-data">
<input style="display:none" autocomplete="off" readonly ><input type="password" style="display:none" autocomplete="off" readonly>
<div class="wrap">
	<p class="alignright">
		<input type="submit" class="submit-form button-primary" value="<?php esc_attr_e( 'Save Changes', 'mailster' ); ?>" disabled />
	</p>
<h1><?php esc_html_e( 'Newsletter Settings', 'mailster' ); ?></h1>
<?php

$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );
if ( ! ( $test_email = get_user_meta( $current_user->ID, '_mailster_test_email', true ) ) ) {
	$test_email = $current_user->user_email;
}
$test_email = apply_filters( 'mailster_test_email', $test_email );


?>
<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php settings_fields( 'mailster_settings' ); ?>
<?php settings_errors(); ?>
<?php do_settings_sections( 'mailster_settings' ); ?>

<?php
$sections = array(
	'general'         => esc_html__( 'General', 'mailster' ),
	'template'        => esc_html__( 'Template', 'mailster' ),
	'frontend'        => esc_html__( 'Front End', 'mailster' ),
	'privacy'         => esc_html__( 'Privacy', 'mailster' ),
	'subscribers'     => esc_html__( 'Subscribers', 'mailster' ),
	'wordpress-users' => esc_html__( 'WordPress Users', 'mailster' ),
	'texts'           => esc_html__( 'Text Strings', 'mailster' ),
	'tags'            => esc_html__( 'Tags', 'mailster' ),
	'delivery'        => esc_html__( 'Delivery', 'mailster' ),
	'cron'            => esc_html__( 'Cron', 'mailster' ),
	'capabilities'    => esc_html__( 'Capabilities', 'mailster' ),
	'bounce'          => esc_html__( 'Bouncing', 'mailster' ),
	'authentication'  => esc_html__( 'Authentication', 'mailster' ),
	'advanced'        => esc_html__( 'Advanced', 'mailster' ),
	'system_info'     => esc_html__( 'System Info', 'mailster' ),
	'manage-settings' => esc_html__( 'Manage Settings', 'mailster' ),
);
$sections = apply_filters( 'mymail_setting_sections', apply_filters( 'mailster_setting_sections', $sections ) );

if ( ! current_user_can( 'mailster_manage_capabilities' ) && ! current_user_can( 'manage_options' ) ) {
	unset( $sections['capabilities'] );
}

if ( ! current_user_can( 'manage_options' ) ) {
	unset( $sections['manage_settings'] );
}

?>

	<div class="settings-wrap">
		<div class="settings-nav">
			<div class="mainnav contextual-help-tabs hide-if-no-js">
			<ul>
			<?php foreach ( $sections as $id => $name ) { ?>
				<li><a href="#<?php echo $id; ?>" class="nav-<?php echo $id; ?>"><?php echo $name; ?></a></li>
			<?php } ?>
			<?php do_action( 'mailster_settings_tabs' ); ?>
			</ul>
			</div>
		</div>

		<div class="settings-tabs"> <div class="tab"><h3>&nbsp;</h3></div>

		<?php foreach ( $sections as $id => $name ) : ?>
			<div id="tab-<?php echo esc_attr( $id ); ?>" class="tab">
				<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
				<?php do_action( 'mailster_section_tab', $id ); ?>
				<?php do_action( 'mailster_section_tab_' . $id ); ?>

				<?php
				if ( file_exists( MAILSTER_DIR . 'views/settings/' . $id . '.php' ) ) :
					include MAILSTER_DIR . 'views/settings/' . $id . '.php';
				endif;
				?>

			</div>
		<?php endforeach; ?>

	<?php $extra_sections = apply_filters( 'mymail_extra_setting_sections', apply_filters( 'mailster_extra_setting_sections', array() ) ); ?>

	<?php foreach ( $extra_sections as $id => $name ) : ?>
			<div id="tab-<?php echo esc_attr( $id ); ?>" class="tab">
				<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
				<?php do_action( 'mailster_section_tab', $id ); ?>
				<?php do_action( 'mailster_section_tab_' . $id ); ?>
			</div>
	<?php endforeach; ?>
			<p class="submitbutton">
				<input type="submit" class="submit-form button-primary" value="<?php esc_attr_e( 'Save Changes', 'mailster' ); ?>" disabled />
			</p>
		</div>

	</div>

	<?php do_action( 'mailster_settings' ); ?>

	<input type="text" class="hidden" name="mailster_options[profile_form]" value="<?php echo esc_attr( mailster_option( 'profile_form', 1 ) ); ?>">
	<input type="text" class="hidden" name="mailster_options[ID]" value="<?php echo esc_attr( mailster_option( 'ID' ) ); ?>">

	<br class="clearfix">
<span id="settingsloaded"></span>
</div>
</form>
