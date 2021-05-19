<?php
/**
 * Custom functions for creating admin menu settings for the plugin.
 *
 * @package Orion SMS OTP Verification
 */

add_action( 'admin_menu', 'ihs_otp_create_menu' );

if ( ! function_exists( 'ihs_otp_create_menu' ) ) {
	/**
	 * Creates Menu for Orion Plugin in the dashboard.
	 */
	function ihs_otp_create_menu() {

		$menu_plugin_title = 'Orion OTP <i class="fab fa-product-hunt"></i>';

		// Create new top-level menu.
		add_menu_page( 'Orion OTP Plugin Settings', $menu_plugin_title, 'administrator', __FILE__, 'ihs_otp_plugin_settings_page', 'dashicons-email' );


		// Add submenu Page.
		$parent_slug = __FILE__;
		$page_title = 'Woocommerce SMS Settings';
		$menu_title = 'Woocommerce Settings';
		$capability = 'manage_options';
		$menu_slug = 'ihs_otp_plugin_woocommerce_settings_page';
		$function = 'ihs_otp_woocommerce_setting_page_func';
		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		// Call register settings function.
		add_action( 'admin_init', 'register_ihs_otp_plugin_settings' );
	}
}

if ( ! function_exists( 'register_ihs_otp_plugin_settings' ) ) {

	/**
	 * Register our settings.
	 */
	function register_ihs_otp_plugin_settings() {
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_api_type' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_auth_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_twilio_api_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_twilio_sid_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_twilio_auth_token' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_twilio_phone_number' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_sender_id' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_mobile_length' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_mgs_route' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_submit_btn-selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_required' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_name' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_msg_template' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_meta_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_login_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_login_form_input_name' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_no_saved_with_country' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_reset_template' );
		

		// Woo commerce Checkout OTP Settings
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_api_type' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_auth_key' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_twilio_api_key' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_twilio_sid_key' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_twilio_auth_token' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_twilio_phone_number' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_sender_id' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_country_code' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_woo_mobile_length' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_woo_mgs_route' );

		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_form_selector' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_submit_btn-selector' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_mobile_input_required' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_mobile_input_name' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_otp_woo_msg_template' );

		// Woocommerce Admin Mob No
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_admin_mob_no' );

		// Woocommerce Order SMS Settings
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_pending' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_failed' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_hold' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_processing' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_completed' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_refunded' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_cancelled' );

		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_pending_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_failed_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_hold_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_processing_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_completed_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_refunded_template' );
		register_setting( 'ihs-otp-woo-plugin-settings-group', 'ihs_order_cancelled_template' );

	}
}

if ( ! function_exists( 'ihs_get_checked_val' ) ) {

	/**
	 * Find the value of checked mobile input value and return an array.
	 *
	 * @return {array} $checked_array Array containing values yes or no.
	 */
	function ihs_get_checked_val() {
		$checked_array = array(
			'checked-yes' => '',
			'checked-no' => '',
		);
		$checkbox_val = esc_attr( get_option( 'ihs_otp_mobile_input_required' ) );
		if ( 'Yes' === $checkbox_val ) {
			$checked_array['checked-yes'] = 'checked';
		} else if ( 'No' === $checkbox_val ) {
			$checked_array['checked-no'] = 'checked';
		}
		return $checked_array;
	}
}

if ( ! function_exists( 'ihs_otp_plugin_settings_page' ) ) {

	/**
	 * Settings Page for Orion Plugin.
	 */
	function ihs_otp_plugin_settings_page() {

		include_once IHS_OTP_PATH . '/inc/otp-form-template.php';
	}
}

if ( ! function_exists( 'ihs_otp_woocommerce_setting_page_func' ) ) {
	/**
	 * Woo Commerce Setting tab contents
	 */
	function ihs_otp_woocommerce_setting_page_func() {
	?>
		<?php include_once 'woo-commerce-setting-template.php'; ?>
	<?php
	}
}
