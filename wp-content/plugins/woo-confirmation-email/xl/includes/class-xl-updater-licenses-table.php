<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class XL_Updater_Licenses_Table
 * @package XLCore
 */
class XL_Updater_Licenses_Table extends WP_List_Table {

	public $per_page = 100;
	public $data;

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			'singular' => 'license', //singular name of the listed records
			'plural'   => 'licenses', //plural name of the listed records
			'ajax'     => false,        //does this table support ajax?
		) );
		$status = 'all';

		$page = $this->get_pagenum();

		$this->data = array();

		// Make sure this file is loaded, so we have access to plugins_api(), etc.
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		parent::__construct( $args );
	}

	// End __construct()

	/**
	 * Text to display if no items are present.
	 * @since  1.0.0
	 * @return  void
	 */
	public function no_items() {
		echo wpautop( __( 'No plugins available for activation.', 'xlplugins' ) );
	}

	// End no_items(0)

	/**
	 * The content of each column.
	 *
	 * @param  array $item The current item in the list.
	 * @param  string $column_name The key of the current column.
	 *
	 * @since  1.0.0
	 * @return string              Output for the current column.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'plugin':
			case 'product_status':
			case 'product_version':
			case 'license_expiry':
				return $item[ $column_name ];
				break;
		}
	}

	// End column_default()

	/**
	 * Content for the "product_name" column.
	 *
	 * @param  array $item The current item.
	 *
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_plugin( $item ) {
		return wpautop( '<strong>' . $item['plugin'] . '</strong>' );
	}

	// End get_sortable_columns()

	/**
	 * Content for the "product_version" column.
	 *
	 * @param  array $item The current item.
	 *
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_version( $item ) {
		if ( isset( $item['latest_version'], $item['product_version'] ) && version_compare( $item['product_version'], $item['latest_version'], '<' ) ) {
			$version_text = '<strong>' . $item['product_version'] . '<span class="update-available"> - ' . sprintf( __( 'version %1$s available', 'xlplugins' ), esc_html( $item['latest_version'] ) ) . '</span></strong>' . "\n";
		} else {
			$version_text = '<strong class="latest-version">' . $item['product_version'] . '</strong>' . "\n";
		}

		return wpautop( $version_text );
	}

	// End get_columns()

	/**
	 * Content for the "status" column.
	 *
	 * @param  array $item The current item.
	 *
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_status( $item ) {

		$response = '';

		if ( 'active' == $item['product_status'] ) {
			$deactivate_url = wp_nonce_url( add_query_arg( 'action', 'xl_deactivate-product', add_query_arg( 'filepath', $item['product_file_path'], add_query_arg( 'page', $_GET['page'], add_query_arg( 'tab', 'licenses' ), network_admin_url( 'admin.php' ) ) ) ), 'bulk-licenses' );

			$key                   = $item['existing_key'];
			$last_six              = substr( $key, - 6 );
			$initial_string        = str_replace( $last_six, '', $key );
			$initial_string_length = strlen( $initial_string );
			$final_string          = str_repeat( 'x', $initial_string_length ) . $last_six;

			$response = '<p>' . $final_string . '</p>';
			$response .= '<a href="' . esc_url( $deactivate_url ) . '">' . __( 'Deactivate', 'xlplugins' ) . '</a>' . "\n";
		} else {
			$response .= '<input name="license_keys[' . esc_attr( $item['product_file_path'] ) . ']" id="license_keys-' . esc_attr( $item['product_file_path'] ) . '" type="text" size="37" aria-required="true" value="' . $item['existing_key'] . '" placeholder="' . esc_attr__( '
Place your license key here', 'xlplugins' ) . '" />' . "\n";

			if ( $item['existing_key'] && $item['existing_key'] !== '' ) {

				if ( isset( $item['license_info'] ) && $item['license_info'] ) {

					$license_info = $item['license_info'];

				} else {
					$license_info = get_option( $this->edd_slugify_module_name( $item['plugin'] ) . 'license_data' );
				}

				if ( isset( $license_info->error ) && ( 'expired' == $license_info->error ) ) {
					if ( isset( $license_info->expires ) && $license_info->expires && ( strtotime( $license_info->expires ) < current_time( 'timestamp' ) ) ) {
						$response_notice = '<span class="below_input_message">' . __( sprintf( 'This license has expired. Login to <a target="_blank" href="%s">Your Account</a> and renew your license.', 'https://xlplugins.com/login/' ), 'xlplugins' ) . '</span>';
					} else {
						$response_notice = '<span class="below_input_message">' . __( sprintf( 'There is some issue with the license. Contact the XLPlugins <a target="_blank" href="%s">Support Team</a>.', 'https://xlplugins.com/support/' ), 'xlplugins' ) . '</span>';
					}
				} elseif ( isset( $license_info->activations_left ) && $license_info->activations_left == '0' ) {
					$response_notice = '<span class="below_input_message">' . __( sprintf( 'This license is associated with another site(s). And usage quota for this license has exceeded it\'s limit.  Purchase a new license or <a target="_blank" href="%s">transfer your license from one site to another</a>', 'https://xlplugins.com/documentation/general-documentation/transfer-license-from-one-site-to-another/' ), 'xlplugins' ) . '</span>';
				} else {
					$response_notice = '<span class="below_input_message">' . __( 'Invalid Key', 'xlplugins' ) . '</span>';
				}
				$response .= apply_filters( 'xl_license_notice_bewlow_field', $response_notice, $item );

			}
		}

		return $response;
	}

	public function edd_slugify_module_name( $name ) {
		return preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $name ) ) );
	}

	// End column_product_name()

	public function column_product_expiry( $item ) {

		if ( ( '-' != $item['license_expiry'] && 'Please Activate' != $item['license_expiry'] ) && $item['license_expiry'] !== 'lifetime' ) {
			$date        = new DateTime( $item['license_expiry'] );
			$date_string = $date->format( get_option( 'date_format' ) );

			return $date_string;
		}

		return esc_html( ucfirst( $item['license_expiry'] ) );
	}

	// End column_product_version()

	/**
	 * Retrieve an array of possible bulk actions.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array();

		return $actions;
	}

	// End column_status()

	/**
	 * Prepare an array of items to be listed.
	 * @since  1.0.0
	 * @return array Prepared items.
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = count( $this->data );

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $total_items,                   //WE have to determine how many items to show on a page
		) );
		$this->items = $this->data;
	}

	public function get_columns() {
		$columns = array(
			'plugin'          => __( 'Plugin', 'xlplugins' ),
			'product_version' => __( 'Version', 'xlplugins' ),
			'product_status'  => __( 'Key', 'xlplugins' ),
			'product_expiry'  => __( 'Renews On', 'xlplugins' ),
		);

		return $columns;
	}

	// End get_bulk_actions()

	public function get_sortable_columns() {
		return array();
	}

}
