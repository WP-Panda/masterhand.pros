<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_send_quote_company() {
	global $wpdb, $user_ID;
	$table_name = $wpdb->prefix . 'wpp_company_data';

	if ( ! empty( $_POST['companyId'] ) && ! empty( trim( $_POST['message'] ) ) ) {

		$company_ids = [];
		if ( is_array( $_POST['companyId'] ) ) {
			foreach ( $_POST['companyId'] as $id ) {
				$company_ids[] = (int) $id;
			}
		} else {
			$company_ids[] = (int) $_POST['companyId'];
		}

		$user_Data = get_userdata( $user_ID );

		$sql  = "SELECT `email` FROM {$table_name} WHERE `id` IN(" . implode( ', ', $company_ids ) . ")";
		$rows = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $rows ) ) {
			wp_send_json( [ 'success' => false, 'msg' => __( "Error! Not found emails", ET_DOMAIN ) ] );
		}

		$listEmails      = array_column( $rows, 'email' );
		$checkListEmails = [];


		foreach ( $listEmails as $email ) {

			if ( strpos( $email, ',' ) !== false ) {
				$em = explode( ',', $email );
				foreach ( $em as $e ) {
					$checkListEmails[] = trim( $e );
				}
			} elseif ( strpos( $email, ';' ) !== false ) {
				$em = explode( ';', $email );
				foreach ( $em as $e ) {
					$checkListEmails[] = trim( $e );
				}
			} else {
				$checkListEmails[] = trim( $email );
			}
		}


		// обновление данных компании для статистики
		foreach ( $company_ids as $one_company_id ) {
			$sql = sprintf( "SELECT `email_count`,`users_mailer` FROM %s WHERE `id` = %s", $table_name, $one_company_id );
			$results = $wpdb->get_results( $sql, ARRAY_A );

			$count  = empty( (int) $results[0]['email_count'] ) ? 1 : (int) $results[0]['email_count'] + 1;
			$mailer = empty( $results[0]['users_mailer'] ) ? $user_ID : explode( ',', $results[0]['users_mailer'] );

			if ( is_array( $mailer ) ) {
				if ( in_array( $user_ID, $mailer ) ) {
					$mailer = $results[0]['users_mailer'];
				} else {
					$mailer[] = $user_ID;
					$mailer   = implode( ',', $mailer );
				}
			}

			$wpdb->update( $table_name, [
				'email_count'  => $count,
				'users_mailer' => $mailer
			], [
				'id' => $one_company_id
			] );

			$user_sends = get_user_meta( $user_ID, '_sends_company_requests', true );

			if ( empty( $user_sends ) ) {
				$new_sends = $one_company_id;
			} else {
				$new_sends = explode( ',', $user_sends );

				if ( is_array( $new_sends ) ) {
					if ( in_array( $one_company_id, $new_sends ) ) {
						$new_sends = $user_sends;
					} else {
						$new_sends[] = $one_company_id;
						$new_sends   = implode( ',', $new_sends );
					}
				}

			}

			update_user_meta( $user_ID, '_sends_company_requests', $new_sends );
		}

		$subject      = ae_get_option( 'get_quote_company_subject' ) ?: 'Get Quote Company';
		$display_name = ! empty( trim( $_POST['display_name'] ) ) ? trim( $_POST['display_name'] ) : $user_Data->display_name;

		$message = ae_get_option( 'get_quote_company' );
		$message = str_replace( '[message]', trim( $_POST['message'] ), $message );
		$message = str_replace( '[display_name]', $display_name, $message );
		$message = str_replace( '[user_email]', $user_Data->user_email, $message );

		$headerFrom = "From: " . $display_name . " < " . $user_Data->user_email . "> \r\n";

		$mailing = AE_Mailing::get_instance();

		$result = $mailing->wp_mail( $checkListEmails, $subject, $message, [], $headerFrom );

		if ( $result ) {


			wp_send_json( [
				'success' => true,
				'msg'     => __( "Message was sent successfully", ET_DOMAIN )
			] );
		} else {
			wp_send_json( [
				'success' => false,
				'msg'     => __( "Sending a message was unsuccessful", ET_DOMAIN )
			] );
		}
	}

	wp_send_json( [ 'success' => false, 'msg' => __( "Something  went wrong", ET_DOMAIN ) ] );
}