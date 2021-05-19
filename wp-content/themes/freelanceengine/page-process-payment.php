<?php
	/**
	 *    Template Name: Process Payment
	 */

	$session = et_read_session();

	global $ad, $payment_return, $order_id, $user_ID;

	$confirm_paypal = $_GET[ 'confirmPaypal' ] ? true : false;

	$payment_type = get_query_var( 'paymentType' );

	$user_ID = ( (int) $user_ID > 0 ) ? $user_ID : (int) $_GET[ 'uid' ];

	if ( $payment_type == 'usePackage' || $payment_type == 'free' ) {

		$payment_return = ae_process_payment( $payment_type, $session );
		if ( $payment_return[ 'ACK' ] ) {
			$project_url = get_the_permalink( $session[ 'ad_id' ] );
			// Destroy session for order data
			et_destroy_session();
			// Redirect to project detail
			wp_redirect( $project_url );
			exit;
		}
	}

	if ( $confirm_paypal ) {
		confirm_paypal_field();

		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}

	/**
	 * get order
	 */

	$order_id = isset( $_GET[ 'order-id' ] ) ? $_GET[ 'order-id' ] : '';

	if ( empty( $order_id ) && isset( $_POST[ 'orderid' ] ) ) {
		$order_id = $_POST[ 'orderid' ];
	}

	$order      = new AE_Order( $order_id );
	$order_data = $order->get_order_data();

	if ( ( $payment_type == 'paypaladaptive' || $payment_type == 'frecredit' || $payment_type == 'stripe' ) && ! $order_id ) {
		//frecredit --> accept bid.

		$session        = empty( $session ) ? bidDataInProcessPay( $_REQUEST[ 'bidEscrow' ] ) : $session;
		$payment_return = fre_process_escrow( $payment_type, $session );
		$payment_return = wp_parse_args( $payment_return, [ 'ACK' => false, 'payment_status' => '' ] );

		extract( $payment_return );
		if ( isset( $ACK ) && $ACK ):
			//change charge status transaction accept bid to pending from ver 1.8.2
			do_action( 'fre_change_status_accept_bid', $session[ 'payKey' ] );

			// Accept bid
			$ad_id       = $session[ 'ad_id' ];
			$order_id    = $session[ 'order_id' ];
			$permalink   = get_permalink( $ad_id );
			$permalink   = add_query_arg( [ 'workspace' => 1 ], $permalink );
			$workspace   = '<a href="' . $permalink . '">' . get_the_title( $ad_id ) . '</a>';
			$bid_id      = get_post_field( 'post_parent', $order_id );
			$bid_budget  = get_post_meta( $bid_id, 'bid_budget', true );
			$content_arr = [
				'paypaladaptive' => __( 'Paypal', ET_DOMAIN ),
				'frecredit'      => __( 'Credit', ET_DOMAIN ),
				'stripe'         => __( 'Stripe', ET_DOMAIN )
			];

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

			get_header();
			?>
            <div class="fre-page-wrapper">
                <div class="fre-page-title">
                    <div class="container">
                        <h1><?php the_title(); ?></h1>
                    </div>
                </div>
                <div class="fre-page-section">
                    <div class="container">
                        <div class="page-purchase-package-wrap">
                            <div class="fre-purchase-package-box">
                                <div class="step-payment-complete">
                                    <h2><?php _e( "Payment Successfully Completed", ET_DOMAIN ); ?></h2>
                                    <p><?php _e( "Thank you. Your payment has been received and the process is now being run.", ET_DOMAIN ); ?></p>
                                    <div class="fre-table">
                                        <div class="fre-table-row">
                                            <div class="fre-table-col fre-payment-date"><?php _e( "Date:", ET_DOMAIN ); ?></div>
                                            <div class="fre-table-col"><?php echo get_the_date( get_option( 'date_format' ), $order_id ); ?></div>
                                        </div>
                                        <div class="fre-table-row">
                                            <div class="fre-table-col fre-payment-type"><?php _e( "Payment Type:", ET_DOMAIN ); ?></div>
                                            <div class="fre-table-col"><?php echo $content_arr[ $payment_type ]; ?></div>
                                        </div>
                                        <div class="fre-table-row">
                                            <div class="fre-table-col fre-payment-total"><?php _e( "Total:", ET_DOMAIN ); ?></div>
                                            <div class="fre-table-col"><?php echo fre_price_format( $total, false, $ad_id ); ?></div>
                                        </div>
                                    </div>
                                    <p><?php _e( "Your project detail is now available for you to view.", ET_DOMAIN ); ?></p>
                                    <div class="fre-view-project-btn">
                                        <a class="fre-submit-btn"
                                           href="<?php echo get_page_url( 'page-profile' ); ?>"><?php _e( "Return to Profile", ET_DOMAIN ); ?></a>

                                        <a class="fre-submit-btn fre-submit-btn-dd"
                                           href="<?php $project_url = get_the_permalink( $session[ 'ad_id' ] );
											   echo $project_url; ?>"><?php _e( "Return to Project", ET_DOMAIN ); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<?php
			get_footer();
		else:
			if ( ! isset( $msg ) ):
				# code...
				// Redirect to 404
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit();
			else:
				?>
                <div class="fre-page-wrapper">
                    <div class="fre-page-title">
                        <div class="container">
                            <h2><?php the_title(); ?></h2>
                        </div>
                    </div>
                    <div class="fre-page-section">
                        <div class="container">
                            <div class="page-purchase-package-wrap">
                                <div class="fre-purchase-package-box">
                                    <div class="step-payment-complete">
                                        <h2><?php _e( "Pending payment", ET_DOMAIN ); ?></h2>
                                        <div class="fre-view-project-btn">
                                            <p><?php $msg ?></p>
                                            <a class="fre-btn"
                                               href="#"><?php _e( "Update now", ET_DOMAIN ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?
				header( 'refresh: 10' );
			endif;
		endif;
	} else if ( $order_id && ( $user_ID == $order_data[ 'payer' ] || is_super_admin( $user_ID ) ) ) {
		// Process submit project
		get_header();
		$ad             = get_post( $order_data[ 'product_id' ] );
		$payment_return = fre_process_escrow( $payment_type, $session );
		$project_id     = ( isset( $session[ 'project_id' ] ) ) ? $session[ 'project_id' ] : '';
		?>
        <div class="fre-page-wrapper">
            <div class="fre-page-title">
                <div class="container">
                    <h2><?php the_title(); ?></h2>
                </div>
            </div>
            <div class="fre-page-section">
                <div class="container">
                    <div class="page-purchase-package-wrap">
                        <div class="fre-purchase-package-box">
                            <div class="step-payment-complete">
                                <h2><?php _e( "Payment Successfully Completed", ET_DOMAIN ); ?></h2>
                                <p><?php _e( "Thank you. Your payment has been received and the process is now being run.", ET_DOMAIN ); ?></p>
                                <div class="fre-table">
                                    <div class="fre-table-row">
                                        <div class="fre-table-col fre-payment-id"><?php _e( "Invoice No:", ET_DOMAIN ); ?></div>
                                        <div class="fre-table-col"><?php echo $order_data[ 'ID' ]; ?></div>
                                    </div>
                                    <div class="fre-table-row">
                                        <div class="fre-table-col fre-payment-date"><?php _e( "Date:", ET_DOMAIN ); ?></div>
                                        <div class="fre-table-col"><?php echo get_the_date( get_option( 'date_format' ), $order_id ); ?></div>
                                    </div>
                                    <div class="fre-table-row">
                                        <div class="fre-table-col fre-payment-type"><?php _e( "Payment Type:", ET_DOMAIN ); ?></div>
                                        <div class="fre-table-col"><?php echo $order_data[ 'payment' ]; ?></div>
                                    </div>
                                    <div class="fre-table-row">
                                        <div class="fre-table-col fre-payment-total"><?php _e( "Total:", ET_DOMAIN ); ?></div>
                                        <div class="fre-table-col"><?php echo fre_price_format( $order_data[ 'total' ] ); ?></div>
                                    </div>
                                </div>
                                <div class="fre-view-project-btn">
                                    <!-- <p><?php _e( "Your project detail is now available for you to view.", ET_DOMAIN ); ?></p>
								<a class="fre-btn" href="<?php //echo $permalink;?>"><?php //_e("Move now", ET_DOMAIN);?></a> -->
									<?php
										if ( isset( $order_data[ 'products' ] ) ) {
											$product = current( $order_data[ 'products' ] );
											$type    = $product[ 'TYPE' ];

											switch ( $type ) {
												case 'pro_plan':
													$tablename = $wpdb->prefix . 'pro_paid_users';

													$data         = $order_data;
													$user_id      = intval( $data[ 'payer' ] );
													$status_order = $product[ 'L_DESC' ]; // string 8_10

													$payment_status = $data[ "payment_status" ];

													$mc_gross       = $data[ "total" ];
													$mc_currency    = $data[ 'mc_currency' ];
													$receiver_email = $data[ 'receiver_email' ];

													// check that user exists
													$user = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'users
                                                WHERE id = ' . $user_id );

													$full_name = $user->display_name;
													$full_name = explode( ' ', $full_name );

													$first_name = $full_name[ 0 ];
													if ( isset( $full_name[ 1 ] ) ) {
														$last_name = $full_name[ 1 ];
													} else {
														$last_name = '';
													}

													$payer_email = $user->user_email;

													if ( ! $user ) {
														echo 'User not exists';
														exit;
													}

													// get status ID and order duration time
													$status_order_exp = explode( '_', $status_order );

													$status_order_id    = $status_order_exp[ 0 ];
													$status_property_id = $status_order_exp[ 1 ];

													/*
													// check that status exists
													$status_id = $wpdb->get_row(
															'SELECT id FROM ' . $wpdb->prefix . 'pro_status
															WHERE id = ' . $status_order_id
													);

													if (!$status_id) {
														echo 'Status not exists';
														exit;
													}
													*/

													// check time
													$option_property = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pro_options
                                                WHERE property_id = ' . $status_property_id );

													if ( ! $option_property ) {
														echo 'Option property';
														exit;
													}

													$order_duration = $option_property->option_value;

													/*
													// check that payment_amount are correct
													$get_values = $wpdb->get_row(
														'SELECT * FROM ' . $wpdb->prefix . 'pro_values
														WHERE status_id = ' . $status_order_id . '
														AND property_id = ' . $status_property_id
													);

													echo 'SELECT * FROM ' . $wpdb->prefix . 'pro_values
														WHERE status_id = ' . $status_order_id . '
														AND property_id = ' . $status_property_id.' ';

													if (!$get_values){
														echo 'Payment_amount are incorrect';
														exit;
													}

													if ($mc_gross != number_format((float)$get_values->property_value, 2, '.', '')) {
														echo 'Check mc_gross';
														exit;
													}
													*/

													// write data to DB if all is OK
													// if user exists - update row
													// update expired status time
													$expired_date = ( new DateTime() )->add( new DateInterval( 'P' . $order_duration . 'M' ) )->format( 'Y-m-d h:i:s' );

													$duplicate_user = $wpdb->get_row( 'SELECT id FROM ' . $wpdb->prefix . 'pro_paid_users WHERE user_id = ' . $user_id );

													if ( $duplicate_user ) {
														$result = $wpdb->update( $tablename, [
															'txn_id'          => '',
															'user_id'         => $user_id,
															'first_name'      => $first_name,
															'last_name'       => $last_name,
															'payer_email'     => $payer_email,
															'status_id'       => $status_order_id,
															'order_duration'  => $order_duration,
															'price'           => $mc_gross,
															'activation_date' => current_time( 'mysql' ),
															'expired_date'    => $expired_date,
														], [ 'user_id' => $user_id ] );

													} else {
														$result = $wpdb->insert( $tablename, [
																'txn_id'          => '',
																'user_id'         => $user_id,
																'first_name'      => $first_name,
																'last_name'       => $last_name,
																'payer_email'     => $payer_email,
																'status_id'       => $status_order_id,
																'order_duration'  => $order_duration,
																'price'           => $mc_gross,
																'activation_date' => current_time( 'mysql' ),
																'expired_date'    => $expired_date,
															] );
													}

													/*
													$setData = [
														'result' => $result,
														'last_error' => $wpdb->last_error,
														'last_query' => $wpdb->last_query,
														'last_result' => $wpdb->last_result,
													];
													file_put_contents(__DIR__ . '/ipn.log', json_encode($setData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

													$user = get_userdata($user_id);
													$user_email = $user->user_email;

													$subject = 'You PRO plan at Masterhand PRO has been activated';
													$mailing = AE_Mailing::get_instance();

													if (0 < $result) {
														$profile_id = get_user_meta($user_id, 'user_profile_id', true);
														if (!add_post_meta($profile_id, 'pro_status', $status_order_id, true)) {
															update_post_meta($profile_id, 'pro_status', $status_order_id);
														}

														do_action('activityRating_amountPayment', $user_id, $mc_gross);

														$message = ae_get_option('bay_pro_status_template');
														$message = str_replace('[display_name]', $user->display_name, $message);
														$message = str_replace('[status_data]', get_name_pro_status($status_order_id) . ' - $' . $mc_gross, $message);
														$message = str_replace('[expired_date]', date("Y-m-d", strtotime($expired_date)), $message);

														$mailing->wp_mail($user_email, $subject, $message, array('user_id' => $user_id));
													*/
													break;

												// review payment for PRO-users
												case 'review_payment':
													$order_amount = $product[ 'AMT' ];
													$review_id    = $product[ 'REVIEW_ID' ];

													$review = review_rating_init()->getReview( $review_id );

													// if review has been already added to PRO - redirect to profile
													if ( $review[ 'status' ] == 'approved' ) {
														header( 'location:/profile/' );
													}

													do_action( 'activityRating_amountPayment', $user_ID, $order_amount );
													review_rating_init()->setStatus( $review_id, review_rating_init()::STATUS_APPROVED );
													review_rating_init()->setUserIdForRating( $review[ 'for_user_id' ] )->addVote( $review[ 'vote' ] );

													$user       = get_userdata( $user_ID );
													$user_email = $user->user_email;

													$subject = 'New review and rating are activated';

													$mailing = AE_Mailing::get_instance();
													$message = ae_get_option( 'bay_review_template' );
													$message = str_replace( '[price_review]', fre_price_format( $order_amount ), $message );
													$message = str_replace( '[review_detail]', $review[ 'comment' ], $message );
													$message = str_replace( '[review_stars]', $review[ 'vote' ], $message );

													$mailing->wp_mail( $user_email, $subject, $message, [ 'post'    => $review[ 'doc_id' ],
													                                                      'user_id' => $user_ID
													] );
													break;

												/* case 'bid_plan':
													 // buy bid
													 if ($project_id) {
														 $permalink = get_the_permalink($project_id);
													 } else {
														 $permalink = et_get_page_link('my-project');
													 }
													 echo "<p>" . __('Now you can return to the project pages', ET_DOMAIN) . "</p>";
													 echo "<a class='fre-btn' href='" . $permalink . "'>" . __('Return', ET_DOMAIN) . "</a>";
													 break;
												 case 'fre_credit_plan':
													 // deposit credit
													 if ($project_id) {
														 $permalink = get_the_permalink($project_id);
													 } else {
														 $permalink = et_get_page_link('my-credit');
													 }
													 echo "<p>" . __('Return to Project page', ET_DOMAIN) . "</p>";
													 echo "<a class='fre-btn' href='" . $permalink . "'>" . __('Click here', ET_DOMAIN) . "</a>";
													 break;
												 case 'fre_credit_fix':
													 // deposit credit
													 if ($ad) {
														 $permalink = get_the_permalink($ad->post_parent);
													 } else {
														 $permalink = et_get_page_link('my-credit');
													 }
													 echo "<p>" . __('Return to Project page', ET_DOMAIN) . "</p>";
													 echo "<a class='fre-btn' href='" . $permalink . "'>" . __('Click here', ET_DOMAIN) . "</a>";
													 break;*/

												default:
													//echo "<a class='fre-submit-btn' href='" . get_page_url('page-profile') . "'>" . __('Return to Profile', ET_DOMAIN) . "</a>";
													echo "";
													/*if ($order_data['status'] == 'publish') { //Buy package
														echo "<p>" . __('Click the button below to be redirected to the previous page', ET_DOMAIN) . "</p>";
														echo "<a class='fre-btn' href='" . et_get_page_link('my-project') . "'>" . __('Go', ET_DOMAIN) . "</a>";
													} else { // Submit project
														$permalink = get_the_permalink($ad->ID);
														echo "<p>" . __('Your project details is now available for you to view', ET_DOMAIN) . "</p>";
														echo "<a class='fre-btn' href='" . $permalink . "'>" . __('Go', ET_DOMAIN) . "</a>";
													}*/
													break;
											}
										}
									?>
                                </div>

                                <div class="fre-view-project-btn">
                                    <a class="fre-submit-btn"
                                       href="<?php echo get_page_url( 'page-profile' ); ?>"><?php _e( "Return to Profile", ET_DOMAIN ); ?></a>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
		if ( $order_id ) {

			//processs payment
			if ( $payment_type == 'paypaladaptive' || $payment_type == 'frecredit' ) {
				$payment_return = fre_process_escrow( $payment_type, $session );
			} else {
				$payment_type   = $order_data[ 'payment' ];
				$payment_return = ae_process_payment( $payment_type, $session );
			}
			update_post_meta( $order_id, 'et_order_is_process_payment', true );
			et_destroy_session();
		}
		get_footer();
	} else {
		// Redirect to 404
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}