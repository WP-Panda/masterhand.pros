<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Add new fields above 'Update' button.
	 *
	 * @param WP_User $user User object.
	 */
	function tm_additional_profile_fields( $user ) {

		$_send_company = get_user_meta( $user->ID, '_sends_company_requests', true );

		if ( ! empty( $_send_company ) ) :
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpp_company_data';
			$_send_company = implode( ', ', explode( ',', $_send_company ) );
			$result = $wpdb->get_results( "SELECT `title` FROM $table_name WHERE `id` IN ( $_send_company )", ARRAY_A );
			if ( ! empty( $result ) ) {
				$companies = wp_list_pluck( $result, 'title' );
				?>

                <table class="form-table">
                    <tr>
                        <th><label for="birth-date-day">Sends Requests</label></th>
                        <td>
                            <ul>
								<?php
									foreach ( $companies as $company ) :
										printf( '<li>%s</li>', $company );
									endforeach;
								?>
                        </td>
                        </ul>
                    </tr>
                </table>
				<?php
			}
		endif;
	}

	add_action( 'show_user_profile', 'tm_additional_profile_fields' );
	add_action( 'edit_user_profile', 'tm_additional_profile_fields' );