<?php
// silence is gold
require_once dirname( __FILE__ ) . '/settings.php';
if ( ae_get_option( 'use_escrow' ) ) {
	require_once dirname( __FILE__ ) . '/ppadaptive.php';
	require_once dirname( __FILE__ ) . '/paypal.php';
	require_once TEMPLATEPATH . '/includes/payouts/PayPal/class-masterha-paypal-payment.php';
}

function fre_process_escrow( $payment_type, $data ) {
	$payment_return = array(
		'ACK' => false
	);

	if ( $payment_type == 'paypaladaptive' ) {
		$ppadaptive = AE_PPAdaptive::get_instance();

		$response                         = $ppadaptive->PaymentDetails( $data['payKey'] );
		$payment_return['payment_status'] = $response->responseEnvelope->ack;

		// email confirm
		if ( strtoupper( $response->responseEnvelope->ack ) == 'SUCCESS' ) {
			$payment_return['ACK'] = true;
			// UPDATE order
			$paymentInfo = $response->paymentInfoList->paymentInfo;
			if ( $paymentInfo[0]->transactionStatus == 'COMPLETED' ) {
				$post_status = get_post_field( 'post_status', $data['order_id'] );

				wp_update_post( array(
					'ID'          => $data['order_id'],
					'post_status' => 'publish'
				) );

				do_action( 'fre_process_escrow_complete', $data );

				$postAuthor = get_post_field( 'post_author', $data['order_id'] );
				do_action( 'activityRating_amountPayment', $postAuthor, $data['total'] );

				// assign project
				$bid_action = Fre_BidAction::get_instance();
				$bid_action->assign_project( $data['bid_id'] );

				if ( $post_status != 'publish' ) {
					$project_id        = wp_get_post_parent_id( $data['bid_id'] );
					$author_project_id = get_post_field( 'post_author', $project_id );

					$author_project       = get_userdata( $author_project_id );
					$author_project_email = $author_project->user_email;

					$bid_budget     = $response->paymentInfoList->paymentInfo[1]->receiver->amount;
					$total          = $response->paymentInfoList->paymentInfo[0]->receiver->amount;
					$commission_fee = $total - $bid_budget;
					$str_price_bid  = 'Bid budget $' . $bid_budget . '<br>
                            Commission $' . $commission_fee . '<br>
                            Total amount $' . $total;

					$bid_content    = get_post_field( 'post_content', $data['bid_id'] );
					$bid_time       = get_post_meta( $data['bid_id'], 'bid_time' );
					$bid_type_time  = get_post_meta( $data['bid_id'], 'type_time' );
					$bid_budget     = get_post_meta( $data['bid_id'], 'bid_budget' );
					$str_bid_detail = $bid_content . ' - ' . $bid_time[0] . $bid_type_time[0] . ' - $' . $bid_budget[0];

					$subject = 'Your payment for SafePay Deal at Masterhand PRO has been received';

					$mailing = AE_Mailing::get_instance();
					$message = ae_get_option( 'bay_pay_bid' );
					$message = str_replace( '[price_bid]', $str_price_bid, $message );
					$message = str_replace( '[bid_detail]', $str_bid_detail, $message );

					$mailing->wp_mail( $author_project_email, $subject, $message, array( 'post'    => $project_id,
					                                                                     'user_id' => $author_project_id
					) );
				}
			}

			if ( $paymentInfo[0]->transactionStatus == 'PENDING' ) {
				//pendingReason
				$payment_return['pending_msg'] = $ppadaptive->get_pending_message( $paymentInfo[0]->pendingReason );
				$payment_return['msg']         = $ppadaptive->get_pending_message( $paymentInfo[0]->pendingReason );
			}
		}

		if ( strtoupper( $response->responseEnvelope->ack ) == 'FAILURE' ) {
			$payment_return['msg'] = $response->error[0]->message;
		}
	}

	return apply_filters( 'fre_process_escrow', $payment_return, $payment_type, $data );
}

function bidDataInProcessPay( $bidId = 0 ) {
	global $wpdb;
	$bidId = (int) $bidId;
	$data  = [];
	if ( $bidId ) {
		$sql  = "SELECT p.ID as bid_id, p.post_parent as ad_id, mOrder.meta_value as order_id, mPayKey.meta_value as payKey 
    FROM {$wpdb->posts} p 
    LEFT JOIN {$wpdb->postmeta} mOrder ON mOrder.post_id = p.ID AND mOrder.meta_key = 'fre_bid_order' 
    LEFT JOIN {$wpdb->postmeta} mPayKey ON mPayKey.post_id = p.ID AND mPayKey.meta_key = 'fre_paykey' 
     WHERE p.ID = {$bidId}";
		$data = $wpdb->get_row( $sql, ARRAY_A );
	}

	return $data;
}

/**
 * @since version 1.8.6.2
 * @author: danng
 *
 * @param bid: int or array/object
 * get detail for 1 accept bid info  - it use to show in modal accept bid such as commision, bid_budget, total amout must deposit ...
 */
function fre_get_deposit_info( $bid = 0 ) {

	global $user_ID;

	$error = array(
		'success' => false,
		'msg'     => __( 'Invalid bid', ET_DOMAIN )
	);
	if ( ! $bid ) {
		return new WP_Error( 'empty_id', __( "Bid is empty.", "enginetheme" ) );
	}

	if ( is_numeric( $bid ) ) {
		$bid = get_post( $bid );
	}

	// check bid is valid
	if ( ! $bid || is_wp_error( $bid ) || $bid->post_type != BID ) {
		wp_send_json( $error );

		return new WP_Error( 'invalid_bid', __( "Invalid bid.", "enginetheme" ) );
	}

	$bid_budget = get_post_meta( $bid->ID, 'bid_budget', true );

	// get commission settings
	$commission     = ae_get_option( 'commission', 0 );
	$commission_fee = $commission;

	// caculate commission fee by percent
	$commission_type = ae_get_option( 'commission_type' );
	if ( $commission_type != 'currency' ) {
		$commission_fee = ( (float) ( $bid_budget * (float) $commission ) ) / 100;
	}

	$commission          = fre_price_format( $commission_fee );
	$payer_of_commission = ae_get_option( 'payer_of_commission', 'project_owner' );
	if ( $payer_of_commission == 'project_owner' ) {
		$total = (float) $bid_budget + (float) $commission_fee;
	} else {
		$commission = 0;
		$total      = $bid_budget;
	}
	$number_format = ae_get_option( 'number_format' );
	$decimal       = ( isset( $number_format['et_decimal'] ) ) ? $number_format['et_decimal'] : get_theme_mod( 'et_decimal', 2 );
	$data          = array(
		'budget'     => $bid_budget,
		'commission' => $commission,
		'total'      => round( (double) $total, $decimal )
	);
	$data          = apply_filters( 'ae_accept_bid_infor', $data );

	return array(
		'budget'          => fre_price_format( $data['budget'] ),
		'commission'      => $data['commission'],
		'total'           => fre_price_format( $data['total'] ),
		'data_not_format' => $data
	);
}