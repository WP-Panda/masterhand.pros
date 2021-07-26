<?php
/**
 * register post type fre_order to handle escrow order
 * @author Dakachi
 */
function fre_register_order() {
	register_post_type( 'fre_order', $args = array(
		'labels'              => array(
			'name'          => __( 'Fre Order', ET_DOMAIN ),
			'singular_name' => __( 'Fre Order', ET_DOMAIN )
		),
		'hierarchical'        => true,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
	) );
}

add_action( 'init', 'fre_register_order' );

/**
 * enqueue script to open modal accept bid
 * @author Dakachi
 */
function fre_enqueue_escrow() {
	if ( is_singular( PROJECT ) ) {
		wp_enqueue_script( 'escrow-accept', TEMPLATEURL . '/assets/js/accept-bid.js', array(
			'jquery',
			'underscore',
			'backbone',
			'appengine'
		), ET_VERSION, true );
	}
}

add_action( 'wp_print_scripts', 'fre_enqueue_escrow' );

/**
 * ajax callback to setup bid info and send to client
 * @author Dakachi
 */
function fre_get_accept_bid_info() {
	$bid_id = $_GET['bid_id'];
	global $user_ID;

	$error = array(
		'success' => false,
		'msg'     => __( 'Invalid bid', ET_DOMAIN )
	);

	if ( ! isset( $_REQUEST['bid_id'] ) ) {
		wp_send_json( $error );
	}

	$bid_id = $_REQUEST['bid_id'];
	$bid    = get_post( $bid_id );

	// check bid is valid
	if ( ! $bid || is_wp_error( $bid ) || $bid->post_type != BID ) {
		wp_send_json( $error );
	}

	$bid_budget = get_post_meta( $bid_id, 'bid_budget', true );

	// get commission settings
	$commission     = ae_get_option( 'commission', 0 );
	$commission_fee = $commission;

	// caculate commission fee by percent
	$commission_type = ae_get_option( 'commission_type' );
	if ( $commission_type != 'currency' ) {
		$commission_fee = ( (float) ( $bid_budget * (float) $commission ) ) / 100;
	}

	$commission          = $commission_fee;
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

	$data = apply_filters( 'ae_accept_bid_infor', $data );

	wp_send_json( array(
		'success' => true,
		'data'    => array(
			'budget'          => fre_price_format( $data['budget'], false, $bid->post_parent ),
			'commission'      => fre_price_format( $data['commission'], false, $bid->post_parent ),
			'total'           => fre_price_format( $data['total'], false, $bid->post_parent ),
			'data_not_format' => $data
		)
	) );
}

add_action( 'wp_ajax_ae-accept-bid-info', 'fre_get_accept_bid_info' );

/**
 * ajax callback process bid escrow and send redirect url to client
 * This method run after employer accept project for a freelancer.
 * @author Dakachi
 */
function fre_escrow_bid() {
	global $user_ID;
	$error = array(
		'success' => false,
		'msg'     => __( 'Invalid bid', ET_DOMAIN )
	);
	if ( ! isset( $_REQUEST['bid_id'] ) ) {
		wp_send_json( $error );
	}
	$bid_id = $_REQUEST['bid_id'];
	$bid    = get_post( $bid_id );

	// check bid is valid
	if ( ! $bid || is_wp_error( $bid ) || $bid->post_type != BID ) {
		wp_send_json( $error );
	}

	// currency settings
	//$currency = ae_get_option('currency');
	$currency = fre_currency_data( $bid->post_parent );
	$currency = $currency['code'];

	$bid_budget = get_post_meta( $bid_id, 'bid_budget', true );

	// get commission settings
	$commission     = ae_get_option( 'commission', 0 );
	$commission_fee = $commission;

	// caculate commission fee by percent
	$commission_type = ae_get_option( 'commission_type' );
	if ( $commission_type != 'currency' ) {
		$commission_fee = ( (float) ( $bid_budget * (float) $commission ) ) / 100;
	}
	$payer_of_commission = ae_get_option( 'payer_of_commission', 'project_owner' );

	if ( $payer_of_commission == 'project_owner' ) {
		$total = (float) $bid_budget + (float) $commission_fee;
	} else {
		$total      = $bid_budget;
		$bid_budget = (float) $total - (float) $commission_fee;
	}

	// get URL Project
	$post_id  = get_post_field( 'post_parent', $bid_id );
	$post_url = get_permalink( $post_id );

	$escrow_data = array(
		'total'               => $total,
		'currency'            => $currency,
		'bid_budget'          => $bid_budget,
		'commission_fee'      => $commission_fee,
		'payer_of_commission' => $payer_of_commission,
		'bid_author'          => $bid->post_author,
		'bid_id'              => $bid_id
	);

	do_action( 'ae_escrow_payment_gateway', $escrow_data );
	//  when using escrow, employer must setup an paypal account
	$paypal_account = get_user_meta( $user_ID, 'paypal', true );
	if ( ! $paypal_account ) {
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "Client's PayPal account is invalid. Please update your Settings", ET_DOMAIN )
		) );
	}

	$receiver = get_user_meta( $bid->post_author, 'paypal', true );

	// paypal adaptive process payment and send reponse to client
	$ppadaptive = AE_PPAdaptive::get_instance();
	// get paypal adaptive settings
	$ppadaptive_settings = ae_get_option( 'escrow_paypal' );

	// the admin's paypal business account
	$primary = $ppadaptive_settings['business_mail'];

	// get from setting
	$feesPayer = $ppadaptive_settings['paypal_fee'];

	/**
	 * paypal adaptive order data
	 */
	//$order_data = array(
	//    'actionType' => 'PAY_PRIMARY',
	//    'returnUrl' => et_get_page_link('process-payment', [
	//        'paymentType' => 'paypaladaptive',
	//        'bidEscrow' => $bid_id
	//    ]) ,
	//    'cancelUrl' => et_get_page_link('cancel-payment', array(
	//        'paymentType'   => 'paypaladaptive',
	//        'returnUrl'     => $post_url
	//    )) ,
//
	//    // 'maxAmountPerPayment' => '35.00',
	//    'currencyCode' => $currency,
	//    'feesPayer' => $feesPayer,
	//    'receiverList.receiver(0).amount' => $total,
	//    'receiverList.receiver(0).email' => $primary,
	//    'receiverList.receiver(0).primary' => true,
	//    // freelancer receiver
	//    'receiverList.receiver(1).amount' => $bid_budget,
	//    'receiverList.receiver(1).email' => $receiver,
	//    'receiverList.receiver(1).primary' => false,
	//    'requestEnvelope.errorLanguage' => 'en_US'
	//);

	//wp_send_json(array(
	//   'success' => false,
	//   'msg' => [
	//    "id"        => $bid_id,
	//    "currency"  => $currency,
	//    "primary"   => $primary,
	//    "tax"       => $commission_fee,
	//    "total"     => $total,
	//    "reciever"  => $reciever,
	//    "returnUrl" => et_get_page_link('process-payment', [
	//        'paymentType' => 'paypaladaptive',
	//        'bidEscrow' => $bid_id
	//    ]),
	//    "canselUrl" => et_get_page_link('cancel-payment', array(
	//        'paymentType'   => 'paypaladaptive',
	//        'returnUrl'     => $post_url
	//    ))
	//]
	//));

	$payment = new FP_WC_PP_PayPal_Payment( [
		"id"        => $bid_id,
		"currency"  => $currency,
		"primary"   => $primary,
		"tax"       => $commission_fee,
		"total"     => $total,
		"returnUrl" => et_get_page_link( 'process-payment', [
			'paymentType' => 'paypaladaptive',
			'bidEscrow'   => $bid_id
		] ),
		"canselUrl" => et_get_page_link( 'cancel-payment', array(
			'paymentType' => 'paypaladaptive',
			'returnUrl'   => $post_url
		) )
	] );
	$payment->preparePayment();
	$response    = $payment->pay();
	$approvalUrl = $response->getApprovalLink();

	// create order
	$order_post = array(
		'post_type'    => 'fre_order',
		'post_status'  => 'pending',
		'post_parent'  => $bid_id,
		'post_author'  => $user_ID,
		'post_title'   => 'Pay for accept bid',
		'post_content' => 'Pay for accept bid ' . $bid_id
	);

	if ( $approvalUrl ) {

		$freelancer_id = get_post_field( 'post_author', $bid_id );
		if ( userHaveProStatus( $freelancer_id ) ) {
			do_action( 'fre_accept_bid', $bid_id );
		}
		$order_id = wp_insert_post( $order_post );

		$order_id = wp_insert_post( $order_post );
		update_post_meta( $order_id, 'execute_id', $response->getId() );
		//update_post_meta($order_id, 'fre_paykey', null);
		update_post_meta( $order_id, 'gateway', 'PPadaptive' );

		update_post_meta( $bid_id, 'fre_bid_order', $order_id );
		update_post_meta( $bid_id, 'fre_paykey', $response->payKey );

		et_write_session( 'payKey', $response->payKey );
		et_write_session( 'order_id', $order_id );
		et_write_session( 'bid_id', $bid_id );
		et_write_session( 'ad_id', $bid->post_parent );
		et_write_session( 'total', $total );
		et_write_session( 'bid_budget', $bid_budget );
		et_write_session( 'currency', $currency );

		//$response->redirect_url = $ppadaptive->paypal_url . $response->payKey;
		wp_send_json( [
			"success"      => true,
			"redirect_url" => $approvalUrl
		] );
	} else {
		wp_send_json( array(
			'success' => false,
			'msg'     => "Unknow error"
		) );
	}
}

add_action( 'wp_ajax_ae-escrow-bid', 'fre_escrow_bid' );

function split_price( $total, $freelancer_value, $employer_value, $type ) {
	$copy_total = intval( $total );
	if ( $type == "percent" ) {
		$freelancer_value = ( $copy_total * $freelancer_value ) / 100;
		$employer_value   = ( $copy_total * $employer_value ) / 100;
	}
	if ( $total < $freelancer_value + $employer_value ) {
		return false;
	}

	return [
		"freelancer_value" => $freelancer_value,
		"employer_value"   => $employer_value
	];
}

/**
 * dispute process execute payment and send money to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_execute_payment() {
	// only the admin or the user have manage_options cap can execute the dispute

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "You do not have permission to do this action.", ET_DOMAIN )
		) );
	}

	$admin_comment    = $_REQUEST["comment"];
	$project_id       = $_REQUEST['project_id'];
	$split_type       = $_REQUEST['split_type'];
	$split_comment    = $_REQUEST['split_comment'];
	$freelancer_value = $_REQUEST['split_value_freelancer'];
	$client_value     = $_REQUEST['split_value_client'];
	$credit_api       = ae_get_option( 'escrow_credit_settings' );
	$bid_id_accepted  = get_post_meta( $project_id, 'accepted', true );

	$price    = get_post_meta( $project_id, 'bid_average', true );
	$currency = get_post_meta( $project_id, 'project_currency', true );
	$bid      = get_post( $bid_id_accepted );
	update_post_meta( $project_id, 'split_value_freelancer', $freelancer_value );
	update_post_meta( $project_id, 'split_value_client', $client_value );
	update_post_meta( $project_id, 'split_comment', $split_comment );
	$employer_id = get_post_meta( $project_id, "dispute_by", true );

	$user_paypal = get_user_meta( $bid->post_author, 'paypal', true );

	$employer_paypal = get_user_meta( $employer_id, 'paypal', true );

	if ( ! empty( $admin_comment ) ) {
		$comment = new AE_Comments( 'fre_report' );
		$comment->insert( [
			"comment_post_ID" => $project_id,
			"comment_content" => $admin_comment
		] );
	}

	isConfirmEmail( $employer_id );
	isConfirmEmail( $bid->post_author );

	$data_prices = split_price( $price, $freelancer_value, $client_value, $split_type );

	if ( ! $user_paypal ) {
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "The profesional doesn't have an email address for paypal payment in his profile", ET_DOMAIN )
		) );
	}

	if ( ! $employer_paypal ) {
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "The employer doesn't have an email address for paypal payment in his profile", ET_DOMAIN )
		) );
	}

	//checking data
	if ( $user_paypal && $employer_paypal && $data_prices ) {
		$recievers = [];
		//part of freelancer
		if ( $data_prices["freelancer_value"] > 0 ) {
			$recievers[] = [
				"email"    => $user_paypal,
				"price"    => $data_prices["freelancer_value"],
				"currency" => $currency
			];
		}
		//part of employer
		if ( $data_prices["employer_value"] > 0 ) {
			$recievers[] = [
				"email"    => $employer_paypal,
				"price"    => $data_prices["employer_value"],
				"currency" => $currency
			];
		}

		if ( count( $recievers ) > 0 ) {
			//paying for dispute
			$payout = new FP_WC_PP_PayPal_Payout( [
				"receivers" => $recievers
			] );
			$payout->init();

			// success update order data
			$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
			if ( $order ) {
				wp_update_post( array(
					'ID'          => $order,
					'post_status' => 'completed'
				) );
			}

			// success update project status
			wp_update_post( array(
				'ID'          => $project_id,
				'post_status' => 'disputed'
			) );

			wp_update_post( array(
				'ID'          => $bid_id_accepted,
				'post_status' => 'disputed'
			) );

			// update meta when admin arbitrate
			if ( isset( $_REQUEST['comment'] ) && isset( $_REQUEST['winner'] ) ) {
				$comment = $_REQUEST['comment'];
				//$winner = $_REQUEST['winner'];
				update_post_meta( $project_id, 'comment_of_admin', $comment );
				//update_post_meta($project_id, 'winner_of_arbitrate', $winner);
			}

			do_action( 'fre_dispute_execute_payment', $project_id, $bid_id_accepted, $order );
			do_action( 'fre_resolve_project_notification', $project_id );

			// send mail
			$mail = Fre_Mailing::get_instance();
			$mail->execute_payment( $project_id, $bid_id_accepted );

			wp_send_json( array(
				'success' => true,
				'msg'     => __( "Send payment successful.", ET_DOMAIN )
			) );
		}
	}

	wp_send_json( array(
		'success' => false,
		'msg'     => __( "Payment error. Payment data is not correct.", ET_DOMAIN )
	) );

}

add_action( 'wp_ajax_execute_payment', 'fre_execute_payment' );

/**
 * dispute process refund payment to employer
 * @since 1.3
 * @author Dakachi
 */
function fre_refund_payment() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "You do not have permission to do this action.", ET_DOMAIN )
		) );
	}
	$project_id = $_REQUEST['project_id'];
	$credit_api = ae_get_option( 'escrow_credit_settings' );
	// cho nay co the dung action
	$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
	if ( ! use_paypal_to_escrow() || ( isset( $credit_api['use_credit_escrow'] ) && $credit_api['use_credit_escrow'] ) ) {
		// only stripe escrow process
		do_action( 'ae_escrow_refund', $project_id, $bid_id_accepted );
	} else {
		// only paypal escrow process
		// execute payment and send money to freelancer
		$pay_key    = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
		$bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
		$currency   = ae_get_option( 'currency' );
		$currency   = $currency['code'];

		if ( $pay_key ) {
			$ppadaptive_settings = ae_get_option( 'escrow_paypal' );
			// the admin's paypal business account
			$primary    = $ppadaptive_settings['business_mail'];
			$ppadaptive = AE_PPAdaptive::get_instance();

			$order    = array(
				'payKey'                          => $pay_key,
				'receiverList.receiver(0).email'  => $primary,
				'receiverList.receiver(0).amount' => $bid_budget,
				'requestEnvelope.errorLanguage'   => 'en_US',
				'currencyCode'                    => $currency
			);
			$response = $ppadaptive->Refund( $order );
			if ( strtoupper( $response->responseEnvelope->ack ) == 'SUCCESS' ) {

				// success update order data
				$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
				if ( $order ) {
					wp_update_post( array(
						'ID'          => $order,
						'post_status' => 'refund'
					) );
				}

				// success update project status
				wp_update_post( array(
					'ID'          => $project_id,
					'post_status' => 'disputed'
				) );

				// success update bid status
				wp_update_post( array(
					'ID'          => $bid_id_accepted,
					'post_status' => 'disputed'
				) );

				// update meta when admin arbitrate
				if ( isset( $_REQUEST['comment'] ) && isset( $_REQUEST['winner'] ) ) {
					$comment = $_REQUEST['comment'];
					$winner  = $_REQUEST['winner'];
					update_post_meta( $project_id, 'comment_of_admin', $comment );
					update_post_meta( $project_id, 'winner_of_arbitrate', $winner );
				}
				/**
				 * do action after admin finish dispute and refund payment
				 *
				 * @param int $project_id
				 * @param int $bid_id_accepted
				 * @param int $order
				 *
				 * @since 1.3
				 * @author Dakachi
				 */
				do_action( 'fre_dispute_refund_payment', $project_id, $bid_id_accepted, $order );
				do_action( 'fre_resolve_project_notification', $project_id );
				$mail = Fre_Mailing::get_instance();
				$mail->refund( $project_id, $bid_id_accepted );

				// send json back
				wp_send_json( array(
					'success' => true,
					'msg'     => __( "Send payment successful.", ET_DOMAIN ),
					'data'    => $response
				) );
			} else {
				wp_send_json( array(
					'success' => false,
					'msg'     => $response->error[0]->message
				) );
			}
		}
		wp_send_json( array(
			'success' => false,
			'msg'     => __( "Payment error. Invalid payment key.", ET_DOMAIN )
		) );
	}
}

add_action( 'wp_ajax_refund_payment', 'fre_refund_payment' );

/**
 * ajax callback to transfer payment to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_transfer_money() {
	if ( current_user_can( 'manage_options' ) ) {
		$project_id = $_REQUEST['project_id'];
		// cho nay co the dung action
		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$credit_api      = ae_get_option( 'escrow_credit_settings' );
		if ( ! use_paypal_to_escrow() || ( isset( $credit_api['use_credit_escrow'] ) && $credit_api['use_credit_escrow'] ) ) {
			do_action( 'fre_transfer_money_ajax', $project_id, $bid_id_accepted );
		} else {
			// execute payment and send money to freelancer
			$pay_key = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
			if ( $pay_key ) {

				$ppadaptive = AE_PPAdaptive::get_instance();
				$response   = $ppadaptive->executePayment( $pay_key );
				if ( strtoupper( $response->responseEnvelope->ack ) == 'SUCCESS' ) {
					// success update order data
					$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
					if ( $order ) {
						wp_update_post( array(
							'ID'          => $order,
							'post_status' => 'finish'
						) );
					}
					// send mail
					$mail = Fre_Mailing::get_instance();
					$mail->execute( $project_id, $bid_id_accepted );
					// send json back
					wp_send_json( array(
						'success' => true,
						'msg'     => __( "The payment has been successfully transferred.", ET_DOMAIN ),
						'data'    => $response
					) );
				} else {
					wp_send_json( array(
						'success' => false,
						'msg'     => $response->error[0]->message
					) );
				}
			} else {
				wp_send_json( array(
					'success' => false,
					'msg'     => __( "Payment error. Invalid payment key.", ET_DOMAIN )
				) );
			}
		}
	}
}

add_action( 'wp_ajax_transfer_money', 'fre_transfer_money' );

/**
 * ajax callback to get information transfer payment to freelancer
 * @since 1.3
 * @author ThanhTu
 */
function fre_transfer_money_info() {
	if ( ae_get_option( 'use_escrow' ) ) {
		$project_id      = $_REQUEST['project_id'];
		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$bid_parent      = get_post( $bid_id_accepted );
		$bid_parent      = $bid_parent->post_parent;

		$credit_api = ae_get_option( 'escrow_credit_settings' );
		// get commission settings
		$commission          = ae_get_option( 'commission', 0 );
		$commission_fee      = $commission;
		$payer_of_commission = ae_get_option( 'payer_of_commission' );
		$data                = array(
			'bid_budget'     => '',
			'commission_fee' => '',
			'amount'         => '',
			'message'        => '',
			'success'        => false
		);
		if ( ae_get_option( 'manual_transfer' ) ) {
			$bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
			// caculate commission fee by percent
			$commission_type = ae_get_option( 'commission_type' );
			if ( $commission_type != 'currency' ) {
				$commission_fee = ( (float) ( $bid_budget * (float) $commission ) ) / 100;
			}
			$payer_of_commission = ae_get_option( 'payer_of_commission', 'project_owner' );
			if ( $payer_of_commission == 'project_owner' ) {
				$amount  = (float) $bid_budget;
				$message = sprintf( __( "You are about to transfer the money %s to the freelancer. Please check the info below about the money.", ET_DOMAIN ), '<strong>' . fre_price_format( $amount, false, $bid_parent ) . '</strong>' );
				$data    = array(
					'bid_budget'     => fre_price_format( $bid_budget, false, $bid_parent ),
					'commission_fee' => '-' . fre_price_format( 0, false, $bid_parent ),
					'amount'         => fre_price_format( $amount, false, $bid_parent ),
					'message'        => $message,
					'success'        => true
				);
			} else if ( $payer_of_commission == 'worker' ) {
				$total   = $bid_budget;
				$amount  = (float) $total - (float) $commission_fee;
				$message = sprintf( __( "You are about to transfer the money %s to the freelancer. Please check the info below about the money.", ET_DOMAIN ), '<strong>' . fre_price_format( $amount, false, $bid_id_accepted ) . '</strong>' );
				$data    = array(
					'bid_budget'     => fre_price_format( $bid_budget, false, $bid_parent ),
					'commission_fee' => '-' . fre_price_format( $commission_fee, false, $bid_parent ),
					'amount'         => fre_price_format( $amount, false, $bid_parent ),
					'message'        => $message,
					'success'        => true
				);
			}
		}
		wp_send_json( array( 'data' => $data ) );
	}
}

add_action( 'wp_ajax_transfer_money_info', 'fre_transfer_money_info' );

function get_bid_info( $bid_id ) {
	$bid      = get_post( $bid_id );
	$currency = fre_currency_data( $bid->post_parent );
	$currency = $currency['code'];

	$bid_budget = get_post_meta( $bid_id, 'bid_budget', true );

	return [
		'bid'        => $bid,
		'currency'   => $currency,
		'bid_budget' => $bid_budget
	];
}

/**
 * finish project, send money when freelancer review project
 *
 * @param int $project_id
 *
 * @since 1.3
 * @author Dakachi
 */
function fre_finish_escrow( $project_id ) {
	if ( ae_get_option( 'use_escrow' ) ) {
		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$credit_api      = ae_get_option( 'escrow_credit_settings' );
		if ( ! ae_get_option( 'manual_transfer' ) ) {
			do_action( 'activityRating_paymentEscrowProject', $bid_id_accepted );
			$user = get_user_by( "id", get_post_meta( $project_id, 'professional_id', true ) );

			isConfirmEmail( $user->ID );

			$paypal_email = get_user_meta( $user->ID, "paypal", true );

			$price    = get_post_meta( $project_id, 'bid_average', true );
			$currency = get_post_meta( $project_id, 'project_currency', true );

			if ( $paypal_email ) {
				//paying to freelancer
				$payout = new FP_WC_PP_PayPal_Payout( [
					"receivers" => [
						[
							"email"    => $paypal_email,
							"price"    => $price,
							"currency" => $currency
						]
					]
				] );
				$payout->init();
				// success update order data
				$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
				if ( $order ) {
					wp_update_post( array(
						'ID'          => $order,
						'post_status' => 'finish'
					) );
					$mail = Fre_Mailing::get_instance();
					$mail->alert_transfer_money( $project_id, $bid_id_accepted );
					$mail->notify_execute( $project_id, $bid_id_accepted );

				}
			}
		} else {
			$mail = Fre_Mailing::get_instance();
			$mail->alert_transfer_money( $project_id, $bid_id_accepted );
		}
	}
}

add_action( 'fre_employer_review_freelancer', 'fre_finish_escrow' );

/**
 * alternative version function fre_finish_escrow() for action after payCode
 *
 * @param $project_id
 *
 * @return bool
 */
function bid_finish_escrow( $project_id ) {
	if ( ae_get_option( 'use_escrow' ) ) {
		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$employer_id     = get_post_meta( $project_id, "dispute_by", true );
		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$price           = get_post_meta( $project_id, 'bid_average', true );
		$currency        = get_post_meta( $project_id, 'project_currency', true );
		$bid             = get_post( $bid_id_accepted );

		$user_paypal = get_user_meta( $bid->post_author, 'paypal', true );

		isConfirmEmail( $bid->post_author );

		if ( $user_paypal ) {
			$recievers = [
				[
					"email"    => $user_paypal,
					"price"    => $price,
					"currency" => $currency
				]
			];
			$payout    = new FP_WC_PP_PayPal_Payout( [
				"receivers" => $recievers
			] );
			$payout->init();
			// paypal escrow process
			// execute payment and send money to freelancer
			do_action( 'activityRating_paymentEscrowProject', $bid_id_accepted );

			// success update order data
			$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
			if ( $order ) {
				wp_update_post( array(
					'ID'          => $order,
					'post_status' => 'finish'
				) );
				$mail = Fre_Mailing::get_instance();
				$mail->alert_transfer_money( $project_id, $bid_id_accepted );
				$mail->notify_execute( $project_id, $bid_id_accepted );

			}

			return true;
		}
	}

	return false;
}

/**
 * Add escrow account field
 *
 * @param bool true/false
 *
 * @return string $html
 * @since FrE-v1.7
 * @package AE_ESCROW
 * @category PPADAPTIVE
 * @author Tambh
 */
function ae_ppadaptive_recipient_field() {
	if ( use_paypal_to_escrow() ) {
		global $user_ID;
		ob_start();
		?>
        <div class="fre-input-field">
            <label><?php _e( 'Paypal Account', ET_DOMAIN ) ?></label>
            <input type="email" id="paypal" value="<?php echo get_user_meta( $user_ID, 'paypal', true ); ?>"
                   name="paypal" placeholder="<?php _e( 'Enter your paypal email', ET_DOMAIN ) ?>">
        </div>

		<?php
		$html = ob_get_clean();
		$html = apply_filters( 'ae_escrow_recipient_field_html', $html );
		echo $html;
	}
}

add_action( 'ae_escrow_recipient_field', 'ae_ppadaptive_recipient_field' );

add_action( 'wp_ajax_confirm_paypal_account', 'confirm_paypal_account_function' );
function confirm_paypal_account_function() {
	global $user_ID;
	$payment = new FP_WC_PP_PayPal_Payment( [
		"id"        => uniqid(),
		"currency"  => "USD",
		"total"     => 1,
		"returnUrl" => et_get_page_link( 'process-payment', [
			'confirmPaypal' => 'Y',
			'user'          => $user_ID
		] ),
		"canselUrl" => et_get_page_link( 'cancel-payment', [] )
	] );
	$payment->preparePayment();
	$response    = $payment->pay();
	$approvalUrl = $response->getApprovalLink();
	if ( $approvalUrl ) {
		wp_send_json( array(
			'success' => true,
			'msg'     => $approvalUrl
		) );
	}
	wp_send_json( array(
		'success' => false,
		'msg'     => __( "Something went wrong", ET_DOMAIN )
	) );
}