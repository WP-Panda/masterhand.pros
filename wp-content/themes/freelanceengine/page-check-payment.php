<?php
/**
 *    Template Name: Check Payment
 */
global $user_ID;

$redirectUrl = !empty($_REQUEST['returnUrl'])? $_REQUEST['returnUrl'] : '/';
$orderId = getQueryOrderId();
$order = getDataPayGateId($orderId);

if($_REQUEST['payment'] == 'cancel'){
	setClosePayGateId($orderId);

	header("Location: {$redirectUrl}");
	exit;
} else {
    if($order['status'] == 'pending') {
		$ppAdaptive = AE_PPAdaptive::get_instance();
		$trzDetail = $ppAdaptive->PaymentDetails($order['trz_id']);

//		var_dump($trzDetail);
		//var_dump($trzDetail->status);
		if(strtoupper($trzDetail->status) == 'COMPLETED'){
			setCompletePayGateId($orderId);
			$order = getDataPayGateId($orderId);

			if($order['type_order'] == 'review'){
				do_action('activityRating_amountPayment', $user_ID, $order['amount']);
				review_rating_init()->setStatus($order['source_id'], review_rating_init()::STATUS_APPROVED);
				$review = review_rating_init()->getReview($order['source_id']);
				review_rating_init()->setUserIdForRating($review['for_user_id'])->addVote($review['vote']);

                    $user = get_userdata($user_ID);
                    $user_email = $user->user_email;

                    $subject = 'New review and rating are activated';

                    $mailing = AE_Mailing::get_instance();
                    $message = ae_get_option('bay_review_template');
                    $message = str_replace('[price_review]', fre_price_format($order['amount']), $message);
                    $message = str_replace('[review_detail]', $review['comment'], $message);
                    $message = str_replace('[review_stars]', $review['vote'], $message);

                    $mailing->wp_mail($user_email, $subject, $message, array('post' => $review['doc_id'], 'user_id' => $user_ID));
			}
		}
	}
}

$order = getDataPayGateID($orderId);
//var_dump($order);
//exit;

if ($orderId && $order['type_order'] == 'review' && $order['status'] == 'success') {
	get_header();
?>
<div class="fre-page-wrapper">
	<div class="fre-page-title">
		<div class="container">
			<h2><?php _e('Payment for review', ET_DOMAIN); ?></h2>
		</div>
	</div>
	<div class="fre-page-section">
		<div class="container">
			<div class="page-purchase-package-wrap">
				<div class="fre-purchase-package-box">
					<div class="step-payment-complete">
						<h2><?php _e( "Payment Successfully Completed", ET_DOMAIN ); ?></h2>
						<p><?php _e( "Thank you. Your payment has been received.", ET_DOMAIN ); ?></p>
						<div class="fre-table">
							<div class="fre-table-row">
								<div class="fre-table-col fre-payment-date"><?php _e( "Date:", ET_DOMAIN ); ?></div>
								<div class="fre-table-col"><?php echo date('d M Y', strtotime($order['updated'])); ?></div>
							</div>
							<div class="fre-table-row">
								<div class="fre-table-col fre-payment-total"><?php _e( "Total:", ET_DOMAIN ); ?></div>
								<div class="fre-table-col"><?php echo fre_price_format( $order['amount'] ); ?></div>
							</div>
						</div>
                        <p><?php _e( "Your review is now available for you to view.", ET_DOMAIN ); ?></p>
						<div class="fre-view-project-btn">
                            <a class="fre-submit-btn" href="<?php echo get_page_url('page-profile');?>"><?php _e( "Return to Profile", ET_DOMAIN ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	get_footer();
} else {
	// Redirect to 404
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit;
}
?>
