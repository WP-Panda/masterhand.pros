<?php
/**
 * Template Name: Member Pro-order Payment Page
 */
global $user_ID;

$role_template = 'employer';
if ( fre_share_role() || ae_user_role( $user_ID ) == FREELANCER ) {
	$role_template = 'freelance';
}

//$res = сheckout_order();

if ( ! isset( $_POST['price'] ) ) {
	die();
}

$path = plugins_url();

$escrow_paypal = ae_get_option( 'escrow_paypal' );
$businessAcc   = $escrow_paypal['business_mail'];

$pro_status_period = get_user_pro_status_duration( $user_ID );
$pro_status        = get_user_pro_status( $user_ID );

$currency = ae_get_option( 'currency', [
	'align' => 'left',
	'code'  => 'USD',
	'icon'  => '$'
] );

$urlRequest = ( (int) ae_get_option( 'test_mode' ) == 1 ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

$paymentType = isset( $_POST['payment_type'] ) ? $_POST['payment_type'] : '';

get_header();
?>

    <div class="fre-page-section">
        <div class="container">
            <div class="page-post-project-wrap" id="post-place">

                <div id="fre-post-project-3 step-payment" class="fre-post-project-step step-wrapper step-payment"
                     style="display: block; margin: 100px 0;">

					<?php if ( isset( $_POST['amount'] ) ) { ?>
                        <div class="fre-post-project-box">
                            <div class="step-change-package show_select_package">
                                <p class="package_title"><?php _e( 'Your order:', ET_DOMAIN ); ?> <strong></strong></p>
                                <p class="package_description" style="font-weight: bold">
									<?php
									//if ( $paymentType === 'review' ) {
									$amount = '$' . $_POST['amount'];
									echo "TOTAL: <span style='color:#2C33C1'>$amount</span>";
									//}
									?>
                                </p>
                            </div>
                        </div>
					<?php } ?>

                    <div class="fre-post-project-box">
                        <div class="step-choose-payment">
                            <!-- fix by figma -->
                            <div class="pay_method"><?php _e( 'Payment Method', ET_DOMAIN ); ?></div>
                            <p><?php _e( 'Select your most appropriate payment method', ET_DOMAIN ); ?></p>
							<?php do_action( 'before_payment_list_wrapper' ); ?>
                            <form method="post" action="" id="checkout_form">
                                <div class="payment_info"></div>
                                <div style="position:absolute; left : -7777px; ">
                                    <input type="text" id="txn_type" name="txn_type" value="web_accept"/>
                                    <input type="submit" id="payment_submit"/>
                                </div>
                            </form>

                            <ul id="fre-payment-accordion" class="fre-payment-list panel-group">
								<?php
								$paypal = ae_get_option( 'paypal' );
								if ( $paypal['enable'] ) { ?>
                                    <li class="panel">
                                        <div class="plan_t title-plan--paypal" data-type="paypal">
											<?php _e( "PayPal / Credit Card via PayPal", ET_DOMAIN ); ?>
                                        </div>

                                        <form method="post"
                                              class="<?= $paymentType == 'review' ? 'review-payment__form' : 'order-payment__form' ?>"
                                              action="<?= $urlRequest; ?>">

                                            <!--URL, куда покупатель будет перенаправлен после успешной оплаты.
											Если этот параметр не передать, покупатель останется на сайте PayPal-->
                                            <input type="hidden" name="return"
                                                   value="<?= bloginfo( 'home' ) ?>/payment-completed">

                                            <!--URL, куда покупатель будет перенаправлен при отмене им оплаты.
											Если этот параметр не передать, покупатель останется на сайте PayPal-->
                                            <input type="hidden" name="cancel_return"
                                                   value="<?= bloginfo( 'home' ) ?>/cancel-payment">

                                            <!--URL, на который PayPal будет предавать информацию о транзакции (IPN).
											Если не передавать этот параметр, будет использоваться значение, указанное в настройках аккаунта.
											Если в настройках аккаунта это также не определено, IPN использоваться не будет-->
                                            <input type="hidden" name="notify_url"
                                                   value="<?php bloginfo( 'stylesheet_directory' ); ?>/ipn.php">

                                            <input type="hidden" name="cmd" value="_xclick">
                                            <input type="hidden" name="business" value="<?= $businessAcc; ?>">
                                            <input type="hidden" name="review_id"
                                                   value="<?= $_POST['review_id'] ?>">
                                            <input type="hidden" name="item_name"
                                                   value="<?= $_POST['plan_name'] ?>">
                                            <input type="hidden" name="item_number" value="<?= $user_ID ?>">
                                            <input type="hidden" name="no_shipping" value="1">
                                            <input type="hidden" name="rm" value="2">

                                            <input type="hidden" name="currency_code"
                                                   value="<?= ae_currency_code() ?>">

                                            <input type="hidden" name="amount" value="<?= $_POST['amount'] ?>">
                                            <input type="hidden" name="status" value="<?= $_POST['status'] ?>">
                                            <input type="hidden" name="custom" value="<?= $_POST['custom'] ?>">
                                            <input type="hidden" name="time" value="<?= $_POST['time'] ?>">
                                            <input type="hidden" name="price" value="<?= $_POST['price'] ?>">

                                            <!--input type="submit" class="btn-left collapsed fre-submit-btn" value="<?php _e( "Pay with PayPal", ET_DOMAIN ); ?>"-->

                                        </form>
                                        <a data-toggle="collapse" data-parent="#fre-payment-accordion"
                                           href="#fre-payment-paypal"
                                           class="btn-left collapsed select-payment fre-submit-btn"
                                           data-type="paypal"><?php _e( "Pay with PayPal", ET_DOMAIN ); ?></a>
                                    </li>
								<?php } ?>

								<?php $cash = ae_get_option( 'cash' );
								if ( $cash['enable'] ) { ?>
                                    <li class="panel">
                                        <div class="plan_t" data-type="cash">
											<?php _e( "Cash", ET_DOMAIN ); ?>
                                            <span><?php _e( "Transfer money directly to our bank account.", ET_DOMAIN ); ?></span>
                                        </div>
                                        <a data-toggle="collapse" data-type="cash"
                                           data-parent="#fre-payment-accordion" href="#fre-payment-cash"
                                           class="btn-left collapsed fre-submit-btn other-payment"><?php _e( "Select", ET_DOMAIN ); ?></a>
                                        <div id="fre-payment-cash"
                                             class="panel-collapse collapse fre-payment-proccess">
                                            <div class="fre-payment-cash">
                                                <p>
													<?php _e( 'Amount need to be transferred:', ET_DOMAIN ); ?>
                                                    <br/>
                                                    <span class="cash_amount">...</span>
                                                </p>
                                                <p>
													<?php _e( 'Transfer to bank account:', ET_DOMAIN ); ?>
                                                    <br/>
                                                    <span class="info_cash">
                                                <?php
                                                $cash_options = ae_get_option( 'cash' );

                                                ?>
                                            </span>
                                                </p>
                                                <strong class="cash-message"><?php echo $cash_options['cash_message']; ?></strong>
                                            </div>
                                            <a href="#" class="fre-btn select-payment"
                                               data-type="cash"><?php _e( "Make Payment", ET_DOMAIN ); ?></a>
                                        </div>
                                    </li>
								<?php } ?>

								<?php
								do_action( 'after_payment_list' );
								?>

								<?php
								$co = ae_get_option( '2checkout' );
								if ( $co['enable'] ) { ?>
                                    <li class="panel">
                                        <div class="plan_t title-plan--2checkout" data-type="2checkout">
											<?php /*_e("2Checkout", ET_DOMAIN); */ ?>
                                            <span><?php _e( "Payment via 2Checkout.", ET_DOMAIN ); ?></span>
                                        </div>
                                        <a href="#"
                                           class="btn-left collapsed btn-submit-price-plan fre-submit-btn select-payment"
                                           data-type="2checkout"><?php _e( "Pay with a card", ET_DOMAIN ); ?></a>
                                    </li>
								<?php } ?>

                            </ul>
							<?php do_action( 'after_payment_list_wrapper' ); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php get_footer();