<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Basic process class that detect request and pass to respective class
 *
 * @author XLPlugins
 * @package XLCore
 */
class XL_process {
	/**
	 * Initiate hooks
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'parse_request_and_process' ), 14 );
		add_action( 'admin_notices', array( 'XL_admin_notifications', 'render' ) );
		add_action( 'wp_loaded', array( 'XL_admin_notifications', 'hide_notices' ) );
		add_action( 'admin_head', array( $this, 'register_in_update_plugin_message' ) );
	}

	public function parse_request_and_process() {
		//Initiating the license instance to handle submissions  (submission can redirect page two that can cause "header already sent" issue to be arised)
		// Initiating this to over come that issue
		if ( isset( $_GET['page'] ) && $_GET['page'] == XL_dashboard::get_expected_slug() && isset( $_GET['tab'] ) && $_GET['tab'] == 'licenses' ) {
			XL_licenses::get_instance();
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] == XL_dashboard::get_expected_slug() && isset( $_GET['tab'] ) && $_GET['tab'] == 'licenses' ) {
			if ( isset( $_GET['ts'] ) && isset( $_GET['response'] ) && ( time() - $_GET['ts'] ) < 5 && $_GET['response'] == 1 ) {
				XL_admin_notifications::add_notification( array(
						'plugin_license_notif' => array(
							'type'           => 'success',
							'is_dismissable' => true,
							'content'        => sprintf( __( '<p> Plugin successfully deactivated. </p>', 'xlplugins' ) ),
						),
					) );
			}
		}

		//Handling Optin
		if ( isset( $_GET['xl-optin-choice'] ) && isset( $_GET['_xl_optin_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_xl_optin_nonce'], 'xl_optin_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'xlplugins' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheating huh?', 'xlplugins' ) );
			}

			$optin_choice = sanitize_text_field( $_GET['xl-optin-choice'] );
			if ( $optin_choice == 'yes' ) {
				XL_optIn_Manager::Allow_optin();
				if ( isset( $_GET['ref'] ) ) {
					XL_optIn_Manager::update_optIn_referer( filter_input( INPUT_GET, 'ref' ) );
				}
			} else {
				XL_optIn_Manager::block_optin();
			}

			do_action( 'xl_after_optin_choice', $optin_choice );
		}

		//Initiating the license instance to handle submissions  (submission can redirect page two that can cause "header already sent" issue to be arised)
		// Initiating this to over come that issue
		/*if ( isset( $_GET['page'] ) && $_GET['page'] == XL_dashboard::get_expected_slug() && isset( $_GET['tab'] ) && $_GET['tab'] == 'support' && isset( $_POST['xl_submit_support'] ) ) {
			$instance_support = XL_Support::get_instance();


			if ( filter_input( INPUT_POST, 'choose_addon' ) == "" || filter_input( INPUT_POST, 'comments' ) == "" ) {
				$instance_support->validation = false;
				XL_admin_notifications::add_notification( array(
					'support_request_failure' => array(
						'type'           => 'error',
						'is_dismissable' => true,
						'content'        => __( '<p> Unable to submit your request.All fields are required. Please ensure that all the fields are filled out.</p>', 'xlplugins' ),

					)
				) );
			} else {
				$instance_support->xl_maybe_push_support_request( $_POST );
			}
		}*/

	}

	public function register_in_update_plugin_message() {

		$get_in_update_message_support = apply_filters( 'xl_in_update_message_support', array() );

		if ( empty( $get_in_update_message_support ) ) {
			return;
		}
		$this->in_update_messages = $get_in_update_message_support;
		foreach ( $get_in_update_message_support as $basename => $changelog_file ) {
			add_action( 'in_plugin_update_message-' . $basename, array( $this, 'in_plugin_update_message' ), 10, 2 );

		}
	}

	/**
	 * Show plugin changes on the plugins screen. Code adapted from W3 Total Cache.
	 *
	 * @param array $args Unused parameter.
	 * @param stdClass $response Plugin update response.
	 */
	public function in_plugin_update_message( $args, $response ) {

		$this->new_version    = $response->new_version;
		$changelog_path       = $this->in_update_messages[ $args['plugin'] ];
		$current_version      = $args['Version'];
		$this->upgrade_notice = $this->get_upgrade_notice( $response->new_version, $changelog_path, $current_version );

		echo apply_filters( 'xl_in_plugin_update_message', $this->upgrade_notice ? '</br>' . wp_kses_post( $this->upgrade_notice ) : '', $args['plugin'] ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		echo '<style>span.xl_plugin_upgrade_notice::before {
    content: ' . '"\f463";
    margin-right: 6px;
    vertical-align: bottom;
    color: #f56e28;
    display: inline-block;
    font: 400 20px/1 dashicons;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    vertical-align: top;
}</style>';

	}

	/**
	 * Get the upgrade notice from WordPress.org.
	 *
	 * @param  string $version WooCommerce new version.
	 *
	 * @return string
	 */
	protected function get_upgrade_notice( $version, $path, $current_version ) {

		$transient_name = 'xl_upgrade_notice_' . $version . md5( $path );
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {
			$response = wp_safe_remote_get( $path );
			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = $this->parse_update_notice( $response['body'], $version, $current_version );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		return $upgrade_notice;
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param  string $content WooCommerce readme file content.
	 * @param  string $new_version WooCommerce new version.
	 *
	 * @return string
	 */
	private function parse_update_notice( $content, $new_version, $current_version ) {
		$version_parts     = explode( '.', $new_version );
		$check_for_notices = array(
			$version_parts[0] . '.0', // Major.
			$version_parts[0] . '.0.0', // Major.
			$version_parts[0] . '.' . $version_parts[1], // Minor.
		);

		$notice_regexp  = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $new_version ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		foreach ( $check_for_notices as $check_version ) {
			if ( version_compare( $current_version, $check_version, '>' ) ) {
				continue;
			}

			$matches = null;
			if ( preg_match( $notice_regexp, $content, $matches ) ) {

				$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

				if ( version_compare( trim( $matches[1] ), $check_version, '=' ) ) {
					$upgrade_notice .= '<span class="xl_plugin_upgrade_notice">';

					foreach ( $notices as $index => $line ) {
						$upgrade_notice .= preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
					}

					$upgrade_notice .= '</span>';
				}
				break;
			}
		}

		return wp_kses_post( $upgrade_notice );
	}

}

new XL_process();
