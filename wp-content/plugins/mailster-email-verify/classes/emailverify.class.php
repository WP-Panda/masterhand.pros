<?php

class MailsterEmailVerify {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_EMAILVERIFY_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_EMAILVERIFY_FILE );

		register_activation_hook( MAILSTER_EMAILVERIFY_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_EMAILVERIFY_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-email-verify' );

		add_action( 'init', array( &$this, 'init' ), 1 );
		add_action( 'init', array( &$this, 'abandoned' ), 1 );
	}



	public function abandoned() {

		if ( function_exists( 'mailster' ) && version_compare( MAILSTER_VERSION, '3.0', '>=' ) ) {
			mailster_notice( '<strong>' . sprintf( __( 'Mailster Email Verify is no longer needed since Mailster 3.0. Please check your security settings on the %s. Plugin deactivated.', 'mailster-email-verify' ), '<a href="edit.php?post_type=newsletter&page=mailster_settings#security">Settings Page</a>' ) . '</strong>', 'info', 3600, 'emailverificationabandoned' );

			if ( ! function_exists( 'wp-admin/includes/plugin.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			deactivate_plugins( MAILSTER_EMAILVERIFY_FILE );
		}
	}


	/**
	 *
	 *
	 * @param unknown $network_wide
	 */
	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) && version_compare( MAILSTER_VERSION, '3.0', '<' ) ) {

			mailster_notice( sprintf( __( 'Define your verification options on the %s!', 'mailster-email-verify' ), '<a href="edit.php?post_type=newsletter&page=mailster_settings&mailster_remove_notice=emailverification#emailverification">Settings Page</a>' ), '', 3600, 'emailverification' );

			$defaults = array(
				'sev_import'        => false,
				'sev_check_mx'      => true,
				'sev_check_smtp'    => false,
				'sev_check_error'   => __( 'Sorry, your email address is not accepted!', 'mailster-email-verify' ),
				'sev_dep'           => true,
				'sev_dep_error'     => __( 'Sorry, your email address is not accepted!', 'mailster-email-verify' ),
				'sev_domains'       => '',
				'sev_domains_error' => __( 'Sorry, your email address is not accepted!', 'mailster-email-verify' ),
				'sev_emails'        => '',
				'sev_emails_error'  => __( 'Sorry, your email address is not accepted!', 'mailster-email-verify' ),
			);

			$mailster_options = mailster_options();

			foreach ( $defaults as $key => $value ) {
				if ( ! isset( $mailster_options[ $key ] ) ) {
					mailster_update_option( $key, $value );
				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $network_wide
	 */
	public function deactivate( $network_wide ) {}


	public function init() {

		if ( ! function_exists( 'mailster' ) ) {

			add_action( 'admin_notices', array( $this, 'notice' ) );

		} else {

			if ( is_admin() ) {

				add_filter( 'mailster_setting_sections', array( &$this, 'settings_tab' ) );
				add_action( 'mailster_section_tab_emailverification', array( &$this, 'settings' ) );

				add_filter( 'mailster_verify_options', array( &$this, 'verify_options' ) );
			}

			add_action( 'mailster_verify_subscriber', array( $this, 'verify_subscriber' ) );

		}

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function verify_subscriber( $entry ) {

		if ( is_wp_error( $entry ) ) {
			return $entry;
		}

		if ( ! isset( $entry['email'] ) ) {
			return $entry;
		}
		if ( ! mailster_option( 'sev_import' ) && defined( 'MAILSTER_DO_BULKIMPORT' ) && MAILSTER_DO_BULKIMPORT ) {
			return $entry;
		}

		if ( is_admin() && isset( $entry['ID'] ) && ! isset( $_POST['action'] ) ) {
			$subscriber = mailster( 'subscribers' )->get( $entry['ID'], false );
			if ( $subscriber && $subscriber->email == $entry['email'] ) {
				return $entry;
			}
		}

		$is_valid = $this->verify( $entry['email'] );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		return $entry;

	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @return unknown
	 */
	public function verify( $email ) {

		list( $user, $domain ) = explode( '@', $email );

		// check for email addresses
		$blacklisted_emails = $this->textarea_to_array( mailster_option( 'sev_emails', '' ) );
		if ( in_array( $email, $blacklisted_emails ) ) {
			return new WP_Error( 'sev_emails_error', mailster_option( 'sev_emails_error' ), 'email' );
		}

		// check for white listed
		$whitelisted_domains = $this->textarea_to_array( mailster_option( 'sev_whitelist', '' ) );
		if ( in_array( $domain, $whitelisted_domains ) ) {
			return true;
		}

		// check for domains
		$blacklisted_domains = $this->textarea_to_array( mailster_option( 'sev_domains', '' ) );
		if ( in_array( $domain, $blacklisted_domains ) ) {
			return new WP_Error( 'sev_domains_error', mailster_option( 'sev_domains_error' ), 'email' );
		}

		// check DEP
		if ( $dep_domains = $this->get_dep_domains( false ) ) {
			if ( in_array( $domain, $dep_domains ) ) {
				return new WP_Error( 'sev_dep_error', mailster_option( 'sev_dep_error' ), 'email' );
			}
		}

		// check MX record
		if ( mailster_option( 'sev_check_mx' ) && function_exists( 'checkdnsrr' ) ) {
			if ( ! checkdnsrr( $domain, 'MX' ) ) {
				return new WP_Error( 'sev_mx_error', mailster_option( 'sev_check_error' ), 'email' );
			}
		}

		// check via SMTP server
		if ( mailster_option( 'sev_check_smtp' ) ) {

			require_once $this->plugin_path . '/classes/smtp-validate-email.php';

			$from = mailster_option( 'from' );

			$validator    = new SMTP_Validate_Email( $email, $from );
			$smtp_results = $validator->validate();
			$valid        = ( isset( $smtp_results[ $email ] ) && 1 == $smtp_results[ $email ] ) || ! ! array_sum( $smtp_results['domains'][ $domain ]['mxs'] );
			if ( ! $valid ) {
				return new WP_Error( 'sev_smtp_error', mailster_option( 'sev_check_error' ), 'email' );
			}
		}

		return true;

	}


	private function textarea_to_array( $value ) {

		return array_map( 'trim', explode( "\n", $value ) );
	}


	/**
	 *
	 *
	 * @param unknown $check (optional)
	 * @return unknown
	 */
	public function get_dep_domains() {

		if ( ! mailster_option( 'sev_dep' ) ) {
			return array();
		}

		$file = $this->plugin_path . '/dep.txt';
		if ( ! file_exists( $file ) ) {
			mailster_update_option( 'sev_dep', false );
			return array();
		}
		$raw     = file_get_contents( $file );
		$domains = explode( "\n", $raw );
		return $domains;

	}


	/**
	 *
	 *
	 * @param unknown $settings
	 * @return unknown
	 */
	public function settings_tab( $settings ) {

		$position = 3;
		$settings = array_slice( $settings, 0, $position, true ) +
			array( 'emailverification' => __( 'Email Verification', 'mailster-email-verify' ) ) +
			array_slice( $settings, $position, null, true );

		return $settings;
	}


	public function settings() {

		include $this->plugin_path . '/views/settings.php';

	}


	/**
	 *
	 *
	 * @param unknown $options
	 * @return unknown
	 */
	public function verify_options( $options ) {

		$options['sev_domains'] = trim( preg_replace( '/(?:(?:\r\n|\r|\n|\s)\s*){2}/s', "\n", $options['sev_domains'] ) );
		$options['sev_domains'] = explode( "\n", $options['sev_domains'] );
		$options['sev_domains'] = array_unique( $options['sev_domains'] );
		sort( $options['sev_domains'] );
		$options['sev_domains'] = implode( "\n", $options['sev_domains'] );

		$options['sev_whitelist'] = trim( preg_replace( '/(?:(?:\r\n|\r|\n|\s)\s*){2}/s', "\n", $options['sev_whitelist'] ) );
		$options['sev_whitelist'] = explode( "\n", $options['sev_whitelist'] );
		$options['sev_whitelist'] = array_unique( $options['sev_whitelist'] );
		sort( $options['sev_whitelist'] );
		$options['sev_whitelist'] = implode( "\n", $options['sev_whitelist'] );

		return $options;
	}


	public function notice() {
		?>
	<div id="message" class="error">
	  <p>
	   <strong>Email Verify for Mailster</strong> requires the <a href="https://mailster.co/?utm_campaign=wporg&utm_source=Email+Verify+for+Mailster&utm_medium=plugin">Mailster Newsletter Plugin</a>, at least version <strong><?php echo MAILSTER_EMAILVERIFY_REQUIRED_VERSION; ?></strong>. Plugin deactivated.
	  </p>
	</div>
		<?php
	}


}
