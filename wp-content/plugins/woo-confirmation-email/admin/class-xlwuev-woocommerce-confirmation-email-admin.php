<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class XLWUEV_Woocommerce_Confirmation_Email_Admin {

	private static $ins = null;
	public $pl_url;
	public $is_new_version_saved = '0';
	public $plugin_settings = null;
	public $my_account_id;
	public $is_plugin_settings_saved = false;
	public $is_bulk_users_verified = false;
	public $bulk_users_verified_message = '';

	public function __construct() {
		$this->my_account_id = get_option( 'woocommerce_myaccount_page_id' );
		$this->pl_url        = untrailingslashit( plugin_dir_url( XLWUEV_PLUGIN_FILE ) );

		add_filter( 'manage_users_custom_column', array( $this, 'new_modify_user_table_row' ), 10, 3 );
		add_filter( 'plugin_action_links_' . XLWUEV_PLUGIN_BASENAME, array( $this, 'plugin_actions' ) );
		add_filter( 'manage_users_columns', array( $this, 'update_user_table' ), 10, 1 );
		add_action( 'init', array( $this, 'save_tab_settings' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_style' ) );
		add_action( 'admin_head', array( $this, 'manual_verify_user' ) );
		add_action( 'admin_init', array( $this, 'insert_plugin_email_services_notice' ) );
		add_action( 'admin_init', array( $this, 'insert_server_email_capability_notice' ) );
		add_action( 'admin_init', array( $this, 'insert_plugin_takenover_notice' ) );
		add_action( 'admin_init', array( $this, 'insert_old_users_notice' ) );
		add_action( 'admin_notices', array( $this, 'check_plugin_email_services' ) );
		add_action( 'admin_notices', array( $this, 'check_plugin_takenover_notice' ) );
		add_action( 'admin_notices', array( $this, 'check_server_email_capability' ) );
		add_action( 'admin_notices', array( $this, 'test_email_success_admin' ) );
		add_action( 'admin_notices', array( $this, 'show_save_admin_notice' ) );
		add_action( 'admin_notices', array( $this, 'check_old_users_verification' ) );
		add_action( 'admin_notices', array( $this, 'bulk_users_verification' ) );
		add_action( 'restrict_manage_users', array( $this, 'add_email_verification_section_filter' ) );
		add_filter( 'pre_get_users', array( $this, 'filter_users_by_email_verification_section' ) );
	}

	public static function instance() {
		if ( null === self::$ins ) {
			return new self;
		}

		return self::$ins;
	}

	/**
	 * This function adds admin js.
	 */
	public function wp_admin_style() {
		wp_register_style( XLWUEV_SLUG . '-css', $this->pl_url . '/assets/css/woo-confirmation-email.css', false, XLWUEV_VERSION );
		wp_enqueue_style( XLWUEV_SLUG . '-css' );
		wp_enqueue_script( XLWUEV_SLUG . '-custom-js', $this->pl_url . '/assets/js/woo-confirmation-email-admin.js', false, XLWUEV_VERSION, true );
	}

	public static function activate_plugins_wc_email() {
		// Any activation code here
		update_option( 'new_plugin_activated', true );
	}

	/*
	 * This function gets executed from support file of the plugin to make the plugin settings page.
	 */
	public static function add_admin_page() {
		$admin_class_object = new self();
		$admin_class_object->create_settings_tabs();

	}

	/**
	 * This function saves the settings from plugin settings screen in options table.
	 */
	public function save_tab_settings() {
		if ( isset( $_POST['wuev_form_type'] ) ) { // WPCS: input var ok, CSRF ok.

			check_admin_referer( 'wuev_form_submit', '_nonce' );
			XlWUEV_Common::update_is_new_version();
			switch ( $_POST['wuev_form_type'] ) { // WPCS: input var ok, CSRF ok.
				case 'wuev-bulk-verification':
					$this->bulk_verify_users( $_POST ); // WPCS: input var ok, CSRF ok.
					break;
				case 'wuev-test-email':
					$this->test_email( ( filter_input( INPUT_POST, 'wc_email_test_recipient', FILTER_VALIDATE_EMAIL ) ? $_POST['wc_email_test_recipient'] : null ) ); // WPCS: input var ok, CSRF ok.
					break;
				case 'wuev-email-template':
					$this->maybe_save_tabs_settings();

					$this->is_plugin_settings_saved = true;
					break;

				case 'wuev-messages':
					$this->maybe_save_tabs_settings();

					$this->is_plugin_settings_saved = true;
					break;

				case 'wuev-general-settings':
					$this->maybe_save_tabs_settings();
					$this->is_plugin_settings_saved = true;
					break;

			}
		}
	}

	public function maybe_save_tabs_settings() {
		$tab_slug = $_POST['wuev_form_type']; // WPCS: input var ok, CSRF ok.

		$settings_array = $_POST; // WPCS: input var ok, CSRF ok.
		if ( function_exists( 'icl_register_string' ) ) {
			foreach ( $settings_array as $key1 => $value1 ) {
				icl_register_string( 'admin_texts_' . $tab_slug, '[' . $tab_slug . ']' . $key1, $value1 );
			}
		}
		if ( isset( $settings_array['xlwuev_email_header'] ) ) {
			$settings_array['xlwuev_email_header'] = apply_filters( 'xlwuev_decode_html_content', wp_kses_post( $settings_array['xlwuev_email_header'] ) );
		}

		if ( is_array( $settings_array ) && count( $settings_array ) > 0 ) {
			$settings_array_temp = array();
			foreach ( $settings_array as $key1 => $value1 ) {
				$settings_array_temp[ $key1 ] = apply_filters( 'xlwuev_decode_html_content', wp_kses_post( $value1 ) );
			}
			$settings_array = $settings_array_temp;

		}
		update_option( $tab_slug, $settings_array );

	}

	/**
	 * This function shows admin notices on settings save.
	 */
	public function show_save_admin_notice() {
		if ( $this->is_plugin_settings_saved ) {
			?>
            <div class="updated fade no-margin-left"><p><b><?php _e( 'Changes Saved', 'woo-confirmation-email' ); ?>
                </p></b>
            </div>
			<?php
		}
	}

	/**
	 * This function shows admin notices on bulk users verify.
	 */
	public function bulk_users_verification() {
		if ( $this->is_bulk_users_verified ) {
			?>
            <div class="updated fade no-margin-left"><p><b><?php echo $this->bulk_users_verified_message; ?>
                </p></b>
            </div>
			<?php
		}
	}

	/**
	 * This function bulk verifies all the users of a particular role.
	 */
	public function bulk_verify_users( $form_values ) {
		$args = array(
			'meta_query' => array(
				array(
					'key'     => 'wcemailverified',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		if ( isset( $form_values['site_bulk_users'] ) ) {
			// do nothing, verify all users
		} else {
			$args['role'] = $form_values['wc_email_user_role'];
		}

		$user_query         = new WP_User_Query( $args );
		$user_email         = array();
		$no_user_text       = __( ' All users are verified for this role.', 'woo-confirmation-email' );
		$single_user_text   = __( ' user is verified.', 'woo-confirmation-email' );
		$multiple_user_text = __( ' users are verified.', 'woo-confirmation-email' );
		$text               = '0' . $multiple_user_text;

		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				update_user_meta( $user->ID, 'wcemailverified', 'true' );
				$user_email[] = 'verified';
			}

			$total_count = count( $user_email );
			if ( 0 == $total_count ) {
				$text = $no_user_text;
			} elseif ( 1 == $total_count ) {
				$text = $total_count . $single_user_text;
			} elseif ( $total_count > 1 ) {
				$text = $total_count . $multiple_user_text;
			}
		}
		$this->is_bulk_users_verified      = true;
		$this->bulk_users_verified_message = $text;
	}

	/**
	 * This function sends a test email from wp-admin side to see if the server is capable of sending emails.
	 */
	public function test_email( $to_email ) {

		XlWUEV_Common::check_plugin_settings();
		$admin_id                                     = get_current_user_id();
		XlWUEV_Common::$wuev_user_id                  = $admin_id;
		XlWUEV_Common::$is_xlwuev_resend_link_clicked = true;
		XlWUEV_Common::$is_test_email                 = true;
		$is_secret_code_present                       = get_user_meta( $admin_id, 'wcemailverifiedcode', true );

		if ( '' === $is_secret_code_present ) {
			$secret_code = md5( $admin_id . time() );
			update_user_meta( $admin_id, 'wcemailverifiedcode', $secret_code );
		}
		XlWUEV_Common::$wuev_user_id           = $admin_id;
		XlWUEV_Common::$wuev_myaccount_page_id = $this->my_account_id;
		$result                                = XlWUEV_Common::code_mail_sender( $to_email );

		if ( $result ) {
			$result = __( 'Email Sent Successfully. Please check your Mail Box.', 'woo-confirmation-email' );
		} else {
			$result = __( 'Email not sent.', 'woo-confirmation-email' );
		}

		XlWUEV_Common::$is_new_version_saved = $result;
	}

	public function test_email_success_admin() {
		if ( false !== XlWUEV_Common::$is_new_version_saved ) {
			?>
            <div class="updated notice">
                <p><?php echo XlWUEV_Common::$is_new_version_saved; ?></p>
            </div>
			<?php
		}
	}

	/**
	 * This function creates all the settings tabs in the plugin settings screen.
	 */
	public function create_settings_tabs() {
		$my_plugin_tabs = XlWUEV_Common::get_default_plugin_settings_tabs();
		echo '<div class="wrap">';
		echo '<h1>' . XLWUEV_FULL_NAME . '</h1>';
		echo $this->create_tabs( $my_plugin_tabs );
		echo '</div>';
	}

	/**
	 * This function outputs the settings tabs in plugin settings screen.
	 */
	public function create_tabs( $tabs, $current = null ) {
		$plugin_default_options = XlWUEV_Common::get_default_plugin_options();
		if ( is_null( $current ) ) {
			if ( isset( $_GET['tab'] ) ) { // WPCS: input var ok, CSRF ok.
				$current = $_GET['tab']; // WPCS: input var ok, CSRF ok.
			} else {
				$current = 'wuev-email-template';
			}
		}
		$content = '';
		$content .= '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $location => $tabname ) {
			if ( $current === $location ) {
				$class           = ' nav-tab-active';
				$current_tabname = $tabname;
			} else {
				$class = '';
			}
			$content .= '<a class="nav-tab' . $class . '" href="?page=woo-confirmation-email&tab=' . $location . '">' . $tabname . '</a>';
		}
		$content .= '</h2>';

		switch ( $current ) {
			case 'wuev-bulk-verification':
				$submit_button_text = __( 'Verify All', 'woo-confirmation-email' );
				break;
			case 'wuev-test-email':
				$submit_button_text = __( 'Send Test Email', 'woo-confirmation-email' );
				break;
			default:
				$submit_button_text = __( 'Save Changes', 'woo-confirmation-email' );
				break;
		}

		$tab_options = XlWUEV_Common::get_tab_options( $current );

		ob_start();
		?>
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <div id="post-body-content">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable wuev_content">
                        <div id="dashboard_right_now" class="postbox">
                            <h2 class="hndle ui-sortable-handle"><span><?php echo $current_tabname; ?></span></h2>
                            <div class="inside">
                                <div class="main">
                                    <form method="post" class="wuev-forms">
										<?php
										include_once 'settings-tabs/' . $current . '.php';
										?>
                                        <input type="hidden" name="wuev_form_type" value="<?php echo $current; ?>">
                                        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'wuev_form_submit' ); ?>">
										<?php
										if ( 'wuev-bulk-verification' !== $current ) {
											submit_button( $submit_button_text );
										}
										?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="postbox-container wuev_sidebar" id="postbox-container-1">
					<?php do_action( 'xlwuev_options_page_right_content' ); ?>
                </div>
            </div>
        </div>

		<?php
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * This function adds custom columns in user listing screen in wp-admin area.
	 */
	public function update_user_table( $column ) {
		$column['wuev_verified']            = __( 'Verification Status', 'woo-confirmation-email' );
		$column['wuev_manual_verified']     = __( 'Manual Verify', 'woo-confirmation-email' );
		$column['wuev_manual_confirmation'] = __( 'Confirmation Email', 'woo-confirmation-email' );

		return $column;
	}

	/**
	 * This function adds custom values to custom columns in user listing screen in wp-admin area.
	 */
	public function new_modify_user_table_row( $val, $column_name, $user_id ) {
		$user_role = get_userdata( $user_id );
		$verified  = get_user_meta( $user_id, 'wcemailverified', true );

		if ( 'wuev_verified' === $column_name ) {

			if ( 'administrator' !== $user_role->roles[0] ) {
				if ( 'true' === $verified ) {
					return '<span class="wuev-circle wuev-iconright" title="' . __( 'Verified', 'woo-confirmation-email' ) . '"></span>';
				} else {
					return '<span class="wuev-circle wuev-iconwrong" title="' . __( 'Not Verified', 'woo-confirmation-email' ) . '"></span>';
				}
			} else {
				return 'Admin';
			}
		} elseif ( 'wuev_manual_verified' === $column_name ) {
			if ( 'administrator' !== $user_role->roles[0] ) {
				if ( 'true' !== $verified ) {
					$text = __( 'Verify', 'woo-confirmation-email' );

					return '<a href=' . add_query_arg( array(
							'user_id'    => $user_id,
							'wp_nonce'   => wp_create_nonce( 'wc_email' ),
							'wc_confirm' => 'true',
						), get_admin_url() . 'users.php' ) . '>' . apply_filters( 'wc_email_confirmation_manual_verify', $text ) . '</a>';
				} else {
					$text = __( 'Unverify', 'woo-confirmation-email' );

					return '<a href=' . add_query_arg( array(
							'user_id'    => $user_id,
							'wp_nonce'   => wp_create_nonce( 'wc_email' ),
							'wc_confirm' => 'false',
						), get_admin_url() . 'users.php' ) . '>' . apply_filters( 'wc_email_confirmation_manual_verify', $text ) . '</a>';
				}
			}
		} elseif ( 'wuev_manual_confirmation' === $column_name ) {
			if ( 'administrator' != $user_role->roles[0] ) {
				$text = __( 'Send Email', 'woo-confirmation-email' );

				if ( 'true' === $verified ) {
					return '';
				}

				return '<a href=' . add_query_arg( array(
						'user_id'         => $user_id,
						'wp_nonce'        => wp_create_nonce( 'wc_email_confirmation' ),
						'wc_confirmation' => 'true',
					), get_admin_url() . 'users.php' ) . '>' . apply_filters( 'wc_email_confirmation_manual', $text ) . '</a>';
			}
		}

		return $val;
	}

	/**
	 * This function manually verifies a user from wp-admin area.
	 */
	public function manual_verify_user() {
		if ( isset( $_GET['user_id'] ) && isset( $_GET['wp_nonce'] ) && wp_verify_nonce( $_GET['wp_nonce'], 'wc_email' ) ) { // WPCS: input var ok, CSRF ok.
			if ( isset( $_GET['wc_confirm'] ) && 'true' === $_GET['wc_confirm'] ) { // WPCS: input var ok, CSRF ok.
				update_user_meta( $_GET['user_id'], 'wcemailverified', 'true' );
				add_action( 'admin_notices', array( $this, 'manual_verify_email_success_admin' ) );
			} else {
				delete_user_meta( $_GET['user_id'], 'wcemailverified' ); // WPCS: input var ok, CSRF ok.
				add_action( 'admin_notices', array( $this, 'manual_verify_email_unverify_admin' ) );
			}
		}

		if ( isset( $_GET['user_id'] ) && isset( $_GET['wp_nonce'] ) && wp_verify_nonce( $_GET['wp_nonce'], 'wc_email_confirmation' ) ) { // WPCS: input var ok, CSRF ok.
			XlWUEV_Common::$wuev_user_id                  = $_GET['user_id']; // WPCS: input var ok, CSRF ok.
			XlWUEV_Common::$is_xlwuev_resend_link_clicked = true;
			$current_user                                 = get_user_by( 'id', $_GET['user_id'] ); // WPCS: input var ok, CSRF ok.
			$is_secret_code_present                       = get_user_meta( $_GET['user_id'], 'wcemailverifiedcode', true ); // WPCS: input var ok, CSRF ok.

			if ( '' === $is_secret_code_present ) {
				$secret_code = md5( $_GET['user_id'] . time() ); // WPCS: input var ok, CSRF ok.
				update_user_meta( $_GET['user_id'], 'wcemailverifiedcode', $secret_code ); // WPCS: input var ok, CSRF ok.
			}

			XlWUEV_Common::$wuev_user_id           = $_GET['user_id']; // WPCS: input var ok, CSRF ok.
			XlWUEV_Common::$wuev_myaccount_page_id = $this->my_account_id;
			XlWUEV_Common::code_mail_sender( $current_user->user_email );
			add_action( 'admin_notices', array( $this, 'manual_confirmation_email_success_admin' ) );
		}

	}

	public function manual_confirmation_email_success_admin() {
		$text = __( 'Verification Email Successfully Sent.', 'woo-confirmation-email' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}

	public function manual_verify_email_success_admin() {
		$text = __( 'User Verified Successfully.', 'woo-confirmation-email' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}

	public function manual_verify_email_unverify_admin() {
		$text = __( 'User Unverified.', 'woo-confirmation-email' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}

	public function check_plugin_email_services() {
		$is_email_services = get_option( 'xl_email_services', 'no' );
		if ( 'no' === $is_email_services ) {
			$license_invalid_text = sprintf( __( '<p>Important Note: Many Web Hosts severely limit SMTP access and can be slow in sending verification emails. Because of this, we highly recommend you sign up for a dedicated SMTP service such as Send13, Mandrill, SendGrid, SparkPost, or Amazon SES.</p><p><a href="%s" class="button">Dismiss this notice</a></p>', XLWUEV_TEXTDOMAIN ), esc_url( add_query_arg( 'xl-email-services', 'yes' ) ) );
			?>
            <div id="message" class="notice notice-info">
				<?php echo $license_invalid_text; ?>
            </div>
			<?php
		}
	}

	public function check_plugin_takenover_notice() {
		$is_takenover_dismissed = get_option( 'xl_takeover_notice', 'no' );
		if ( 'no' === $is_takenover_dismissed ) {
			$license_invalid_text = sprintf( __( '<p> <a href="%1$s" target="_blank">XLPlugins</a> has taken over development of WooCommerce User Email Verification.</p><p> We would be pushing new features. Feel free to contact us via <a href="%2$s" target="_blank">Support ticket </a> to tell us about New ideas or just to say simple Hi :)</p><p><a href="%3$s" class="button">Dismiss this notice</a></p>', XLWUEV_TEXTDOMAIN ), 'https://xlplugins.com/', admin_url( 'admin.php?page=xlplugins&tab=support' ), esc_url( add_query_arg( 'xl-take-over-notice-dismiss', 'yes' ) ) );
			?>
            <div id="message" class="notice notice-info">
				<?php echo $license_invalid_text; ?>
            </div>
			<?php
		}
	}

	public function check_server_email_capability() {
		$is_check_server_email_dismissed = get_option( 'xl_check_server_email', 'no' );
		if ( 'no' === $is_check_server_email_dismissed ) {
			$display_text = sprintf( __( '<p> You can test to see if your server is capable of sending Emails.</p><p><a href="%1$s" class="button button-primary">Yes, I want to test</a> <a href="%2$s" class="button">No, I don\'t want to test</a></p>', XLWUEV_TEXTDOMAIN ), admin_url( 'admin.php?page=woo-confirmation-email&tab=wuev-test-email' ), esc_url( add_query_arg( 'xl-server-email-capability-dismiss', 'yes' ) ) );
			?>
            <div id="message" class="notice notice-info">
				<?php echo $display_text; ?>
            </div>
			<?php
		}
	}

	public function check_old_users_verification() {
		$is_check_old_users = get_option( 'xl_check_old_users', 'no' );
		if ( 'no' === $is_check_old_users ) {
			$args                   = array(
				'role__not_in' => 'administrator',
				'meta_query'   => array(
					array(
						'key'     => 'wcemailverified',
						'compare' => 'NOT EXISTS',
					),
				),
			);
			$user_query             = new WP_User_Query( $args );
			$total_unverified_users = $user_query->get_total();

			if ( $total_unverified_users > 0 ) {
				$display_text = sprintf( __( '<p> Your website contains %1$s users whose emails are not verified.</p><p><a href="%2$s" class="button button-primary">I want to verify</a> <a href="%3$s" class="button">No, I don\'t want to verify</a></p>', XLWUEV_TEXTDOMAIN ), $total_unverified_users, admin_url( 'admin.php?page=woo-confirmation-email&tab=wuev-bulk-verification' ), esc_url( add_query_arg( 'xl-old-users-check', 'yes' ) ) );
				?>
                <div id="message" class="notice notice-info">
					<?php echo $display_text; ?>
                </div>
				<?php
			}
		}
	}

	/*
	 * This function updates the email services notice key so that this notice won't show again.
	 */
	public function insert_plugin_email_services_notice() {
		if ( isset( $_GET['xl-email-services'] ) && 'yes' === $_GET['xl-email-services'] ) { // WPCS: input var ok, CSRF ok.
			update_option( 'xl_email_services', 'yes' );
		}
	}

	/*
	 * This function updates the takenover notice key so that this notice won't show again.
	 */
	public function insert_plugin_takenover_notice() {
		if ( isset( $_GET['xl-take-over-notice-dismiss'] ) && 'yes' === $_GET['xl-take-over-notice-dismiss'] ) { // WPCS: input var ok, CSRF ok.
			update_option( 'xl_takeover_notice', 'yes' );
		}
	}

	/*
	 * This function updates the check server capability notice key so that this notice won't show again.
	 */
	public function insert_server_email_capability_notice() {
		if ( isset( $_GET['xl-server-email-capability-dismiss'] ) && 'yes' === $_GET['xl-server-email-capability-dismiss'] ) { // WPCS: input var ok, CSRF ok.
			update_option( 'xl_check_server_email', 'yes' );
		}
	}

	/*
	 * This function updates the check old users to verifiy notice key so that this notice won't show again.
	 */
	public function insert_old_users_notice() {
		if ( isset( $_GET['xl-old-users-check'] ) && 'yes' === $_GET['xl-old-users-check'] ) { // WPCS: input var ok, CSRF ok.
			update_option( 'xl_check_old_users', 'yes' );
		}
	}

	/**
	 * Hooked over 'plugin_action_links_{PLUGIN_BASENAME}' WordPress hook to add deactivate popup support
	 *
	 * @param array $links array of existing links
	 *
	 * @return array modified array
	 */
	public function plugin_actions( $links ) {
		$links['deactivate'] .= '<i class="xl-slug" data-slug="' . XLWUEV_PLUGIN_BASENAME . '"></i>';

		return $links;
	}

	/*
	 * Add a dropdown in users listing screen to filter out the verified and unverified users
	 */
	function add_email_verification_section_filter() {
		$verification_values = array(
			'0' => 'Unverified',
			'1' => 'Verified',
		);
		if ( isset( $_GET['verification_status'] ) ) { // WPCS: input var ok, CSRF ok.
			$status = $_GET['verification_status']; // WPCS: input var ok, CSRF ok.
			$status = $status[0];
			if ( '' === $status ) {
				$status = - 1;
			}
		} else {
			$status = - 1;
		}
		echo ' <select name="verification_status[]" style="float:none;"><option value="">Email Verification Status</option>';
		foreach ( $verification_values as $key1 => $value1 ) {
			$selected = '';
			if ( isset( $verification_values[ $status ] ) ) {
				$selected = ( $status == $key1 ) ? 'selected' : '';
			}
			echo '<option value="' . $key1 . '"' . $selected . '>' . $value1 . '</option>';
		}
		echo '</select>';
		echo '<input type="submit" class="button" value="Filter">';
	}

	/*
	 * Modify user query based on email verification status of the user
	 */
	function filter_users_by_email_verification_section( $query ) {
		global $pagenow;

		if ( is_admin() && 'users.php' === $pagenow && isset( $_GET['verification_status'] ) && is_array( $_GET['verification_status'] ) ) { // WPCS: input var ok, CSRF ok.
			$status = $_GET['verification_status']; // WPCS: input var ok, CSRF ok.
			$status = $status[0];

			if ( '' !== $status ) {
				$meta_query = array(
					array(
						'key'     => 'wcemailverified',
						'compare' => 'NOT EXISTS',
					),
				);
				if ( '1' == $status ) {
					$meta_query = array(
						array(
							'key'   => 'wcemailverified',
							'value' => 'true',
						),
					);
					$query->set( 'meta_key', 'wcemailverified' );
				}
				$query->set( 'meta_query', $meta_query );
			}
		}
	}
}

XLWUEV_Woocommerce_Confirmation_Email_Admin::instance();
