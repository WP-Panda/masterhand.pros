<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Contains the logic for deactivation popups
 * @since 1.0.0
 * @author XLPlugins
 * @package XLCore
 */
class XL_deactivate {

	public static $deactivation_str;

	/**
	 * Initialization of hooks where we prepare the functionality to ask use for survey
	 */
	public static function init() {

		self::load_all_str();

		add_action( 'admin_footer', array( __CLASS__, 'maybe_load_deactivate_options' ) );

		add_action( 'wp_ajax_xl_submit_uninstall_reason', array( __CLASS__, '_submit_uninstall_reason_action' ) );
	}

	/**
	 * Localizes all the string used
	 */
	public static function load_all_str() {

		self::$deactivation_str = array(
			'deactivation-share-reason'                => __( 'If you have a moment, please let us know why you are deactivating', 'xlplugins' ),
			'reason-no-longer-needed'                  => __( 'I no longer need the plugin', 'xlplugins' ),
			'reason-found-a-better-plugin'             => __( 'I found a better plugin', 'xlplugins' ),
			'reason-needed-for-a-short-period'         => __( 'I only needed the plugin for a short period', 'xlplugins' ),
			'placeholder-plugin-name'                  => __( 'What\'s the plugin\'s name?', 'xlplugins' ),
			'reason-broke-my-site'                     => __( 'The plugin broke my site', 'xlplugins' ),
			'reason-suddenly-stopped-working'          => __( 'The plugin suddenly stopped working', 'xlplugins' ),
			'reason-other'                             => _x( 'Other', 'the text of the "other" reason for deactivating the plugin that is shown in the modal box.', 'xlplugins' ),
			'deactivation-modal-button-submit'         => __( 'Submit & Deactivate', 'xlplugins' ),
			'deactivate'                               => __( 'Deactivate', 'xlplugins' ),
			'deactivation-modal-button-deactivate'     => __( 'Deactivate', 'xlplugins' ),
			'deactivation-modal-button-skip'           => __( 'Skip', 'xlplugins' ),
			'deactivation-modal-button-confirm'        => __( 'Yes - Deactivate', 'xlplugins' ),
			'deactivation-modal-button-cancel'         => _x( 'Cancel', 'the text of the cancel button of the plugin deactivation dialog box.', 'xlplugins' ),
			'reason-cant-pay-anymore'                  => __( "I can't pay for it anymore", 'xlplugins' ),
			'placeholder-comfortable-price'            => __( 'What price would you feel comfortable paying?', 'xlplugins' ),
			'reason-couldnt-make-it-work'              => __( "I couldn't understand how to make it work", 'xlplugins' ),
			'reason-great-but-need-specific-feature'   => __( "The plugin is great, but I need specific feature that you don't support", 'xlplugins' ),
			'reason-not-working'                       => __( 'The plugin is not working', 'xlplugins' ),
			'reason-not-what-i-was-looking-for'        => __( "It's not what I was looking for", 'xlplugins' ),
			'reason-didnt-work-as-expected'            => __( "The plugin didn't work as expected", 'xlplugins' ),
			'placeholder-feature'                      => __( 'What feature?', 'xlplugins' ),
			'placeholder-share-what-didnt-work'        => __( "Kindly share what didn't work so we can fix it for future users...", 'xlplugins' ),
			'placeholder-what-youve-been-looking-for'  => __( "What you've been looking for?", 'xlplugins' ),
			'placeholder-what-did-you-expect'          => __( 'What did you expect?', 'xlplugins' ),
			'reason-didnt-work'                        => __( "The plugin didn't work", 'xlplugins' ),
			'reason-dont-like-to-share-my-information' => __( "I don't like to share my information with you", 'xlplugins' ),
		);
	}

	/**
	 * Checking current page and pushing html, js and css for this task
	 * @global string $pagenow current admin page
	 * @global array $VARS global vars to pass to view file
	 */
	public static function maybe_load_deactivate_options() {

		global $pagenow;

		if ( $pagenow == 'plugins.php' || ! is_null( XL_dashboard::$currentPage ) ) {
			global $VARS;

			$VARS = array(
				'slug'    => 'asvbsd',
				'reasons' => self::deactivate_options(),
			);
			include_once dirname( dirname( __FILE__ ) ) . '/views/xl-deactivate-modal.phtml';
		}
	}

	/**
	 * deactivation reasons in array format
	 * @return array reasons array
	 * @since 1.0.0
	 */
	public static function deactivate_options() {
		$reason_found_better_plugin = array(
			'id'                => 2,
			'text'              => self::load_str( 'reason-found-a-better-plugin' ),
			'input_type'        => 'textfield',
			'input_placeholder' => self::load_str( 'placeholder-plugin-name' ),
		);

		$reason_other = array(
			'id'                => 7,
			'text'              => self::load_str( 'reason-other' ),
			'input_type'        => 'textfield',
			'input_placeholder' => '',
		);

		$long_term_user_reasons = array(
			array(
				'id'                => 1,
				'text'              => self::load_str( 'reason-no-longer-needed' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			$reason_found_better_plugin,
			array(
				'id'                => 3,
				'text'              => self::load_str( 'reason-needed-for-a-short-period' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 4,
				'text'              => self::load_str( 'reason-broke-my-site' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 5,
				'text'              => self::load_str( 'reason-suddenly-stopped-working' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
		);

		$uninstall_reasons['default'] = $long_term_user_reasons;

		$uninstall_reasons = apply_filters( 'xl_uninstall_reasons', $uninstall_reasons );
		array_push( $uninstall_reasons['default'], $reason_other );

		return $uninstall_reasons;
	}

	/**
	 * get exact str against the slug
	 *
	 * @param type $slug
	 *
	 * @return type
	 */
	public static function load_str( $slug ) {
		return self::$deactivation_str[ $slug ];
	}

	/**
	 * Called after the user has submitted his reason for deactivating the plugin.
	 *
	 * @since  1.1.2
	 */
	public static function _submit_uninstall_reason_action() {

		if ( ! isset( $_POST['reason_id'] ) ) {
			exit;
		}

		$reason_info = isset( $_REQUEST['reason_info'] ) ? trim( stripslashes( $_REQUEST['reason_info'] ) ) : '';

		$reason = array(
			'id'   => $_POST['reason_id'],
			'info' => substr( $reason_info, 0, 128 ),
		);

		$licenses = XL_addons::get_installed_plugins();

		$version = 'NA';
		if ( $licenses && count( $licenses ) > 0 ) {
			foreach ( $licenses as $key => $license ) {

				if ( $key == $_POST['plugin_basename'] ) {
					$version = $license['Version'];
				}
			}
		}

		$deactivations = array(
			$_POST['plugin_basename'] . '(' . $version . ')' => $reason,
		);

		$license_info = isset( $_REQUEST['licenses'] ) ? json_decode( stripslashes( $_REQUEST['licenses'] ) ) : '';

		$licenses_info_pass = array();

		if ( $license_info && is_object( $license_info ) ) {

			if ( property_exists( $license_info, sha1( $_POST['plugin_basename'] ) ) ) {
				$basename           = sha1( $_POST['plugin_basename'] );
				$licenses_info_pass = $license_info->$basename;
			} elseif ( property_exists( $license_info, ( $_POST['plugin_basename'] ) ) ) {
				$basename           = $_POST['plugin_basename'];
				$licenses_info_pass = $license_info->$basename;
			}
		}

		XL_API::post_deactivation_data_v2( $deactivations, $licenses_info_pass );
		// Print '1' for successful operation.
		echo 1;
		exit;
	}

}

//initialization
XL_deactivate::init();
