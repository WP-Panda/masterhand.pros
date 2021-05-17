<?php

class MailsterRegister {

	public function __construct() {

		add_action( 'mailster_register_mailster', array( &$this, 'on_register' ), 10, 3 );
		add_action( 'mailster_remove_notice_verify', array( &$this, 'verified_notice_closed' ) );
		add_action( 'wp_version_check', array( &$this, 'verified_notice' ) );

		mailster_localize_script(
			'register',
			array(
				'error' => esc_html__( 'There was an error while processing your request!', 'mailster' ),
				'help'  => esc_html__( 'Help me!', 'mailster' ),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $args     (optional)
	 */
	public function form( $args = array() ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-register-style', MAILSTER_URI . 'assets/css/register-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_script( 'mailster-register-script', MAILSTER_URI . 'assets/js/register-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		$slug     = 'mailster';
		$verified = mailster()->is_verified();

		$page = isset( $_GET['page'] ) ? str_replace( 'mailster_', '', $_GET['page'] ) : 'dashboard';

		$args = wp_parse_args(
			$args,
			array(
				'pretext'      => sprintf( esc_html__( 'Enter Your Purchase Code To Register (Don\'t have one for this site? %s)', 'mailster' ), '<a href="' . esc_url( 'https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=' . $page . '&utm_source=mailster_plugin' ) . '" class="external">' . esc_html__( 'Buy Now!', 'mailster' ) . '</a>' ),
				'purchasecode' => mailster()->license(),
			)
		);

		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );

		$username  = mailster()->username( '' );
		$useremail = mailster()->email( '' );

		wp_print_styles( 'mailster-register-style' );

		?>

		<div class="register_form_wrap register_form_wrap-<?php echo sanitize_key( $slug ); ?> loading<?php echo $verified ? ' step-3' : ' step-1'; ?>">
			<input type="hidden" class="register-form-slug" name="slug" value="<?php echo esc_attr( $slug ); ?>">
			<div class="register-form-info">
				<span class="step-1"><?php esc_html_e( 'Verifying Purchase Code', 'mailster' ); ?>&hellip;</span>
				<span class="step-2"><?php esc_html_e( 'Finishing Registration', 'mailster' ); ?>&hellip;</span>
			</div>
			<form class="register_form" action="" method="POST">
				<div class="howto"><?php echo $args['pretext']; ?></div>
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat register-form-purchasecode" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" name="purchasecode" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" maxlength="36" value="<?php echo esc_attr( $args['purchasecode'] ); ?>">
				<input type="submit" class="button button-hero button-primary dashboard-register" value="<?php esc_attr_e( 'Verify Purchase Code', 'mailster' ); ?>">
				<div class="howto">
					<a href="https://static.mailster.co/images/purchasecode.gif" class="howto-purchasecode"><?php esc_html_e( 'Where can I find my item purchase code?', 'mailster' ); ?></a>
				</div>
			</form>
			<form class="register_form_2" action="" method="POST">
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat username" placeholder="<?php esc_attr_e( 'Username', 'mailster' ); ?>" name="username" value="<?php echo esc_attr( $username ); ?>">
				<input type="email" class="widefat email" placeholder="Email" name="email" value="<?php echo esc_attr( $useremail ); ?>">
				<div class="howto tos-field"><input type="checkbox" name="tos" class="tos" value="<?php echo time(); ?>"> <?php printf( esc_html__( 'I agree to the %1$s and the %2$s by completing the registration.', 'mailster' ), '<a href="https://mailster.co/legal/tos/" class="external">' . esc_html__( 'Terms of service', 'mailster' ) . '</a>', '<a href="https://mailster.co/legal/privacy-policy/" class="external">' . esc_html__( 'Privacy Policy', 'mailster' ) . '</a>' ); ?></div>
				<input type="submit" class="button button-hero button-primary" value="<?php esc_attr_e( 'Complete Registration', 'mailster' ); ?>">
			</form>
			<form class="registration_complete">
				<div class="registration_complete_check"></div>
				<div class="registration_complete_text"><?php esc_html_e( 'All Set!', 'mailster' ); ?></div>
			</form>
		</div>
		<?php
		mailster( 'helper' )->dialog(
			'<img src="https://static.mailster.co/images/purchasecode.gif">',
			array(
				'id'      => 'registration-dialog',
				'buttons' => array(
					array(
						'label'   => esc_html__( 'OK got it', 'mailster' ),
						'classes' => 'button button-primary right notification-dialog-dismiss',
					),
				),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $username
	 * @param unknown $email
	 * @param unknown $purchasecode
	 */
	public function on_register( $username, $email, $purchasecode ) {

		update_option( 'mailster_license', $purchasecode );
		delete_transient( 'mailster_verified' );
		mailster_remove_notice( 'verify' );

	}


	public function verified_notice_closed() {

		set_transient( 'mailster_skip_verifcation_notices', true, WEEK_IN_SECONDS );

	}


	public function verified_notice() {

		if ( mailster_is_local() ) {
			return;
		}

		if ( get_transient( 'mailster_skip_verifcation_notices' ) ) {
			return;
		}

		if ( ! mailster()->is_verified() ) {
			if ( time() - get_option( 'mailster' ) > WEEK_IN_SECONDS
				&& get_option( 'mailster_setup' ) ) {
				mailster_notice( sprintf( esc_html__( 'Hey! Would you like automatic updates and premium support? Please %s of Mailster.', 'mailster' ), '<a href="admin.php?page=mailster_dashboard">' . esc_html__( 'activate your copy', 'mailster' ) . '</a>' ), 'error', false, 'verify', 'mailster_manage_licenses' );
			}
		} else {
			mailster_remove_notice( 'verify' );
		}

		if ( mailster()->is_outdated() ) {
			mailster_notice( sprintf( esc_html__( 'Hey! Looks like you have an outdated version of Mailster! It\'s recommended to keep the plugin up to date for security reasons and new features. Check the %s for the most recent version.', 'mailster' ), '<a href="https://mailster.co/changelog?v=' . MAILSTER_VERSION . '">' . esc_html__( 'changelog page', 'mailster' ) . '</a>' ), 'error', false, 'outdated', 'mailster_manage_licenses' );
		} else {
			mailster_remove_notice( 'outdated' );
		}
	}


}
