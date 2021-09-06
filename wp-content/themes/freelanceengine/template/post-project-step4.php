<?php
global $user_ID, $packs;

$step = 4;

wpp_dump($step);
$disable_plan = ae_get_option( 'disable_plan', false );

if ( $disable_plan ) {
	$step --;
}
wpp_dump($step);
if ( $user_ID ) {
	$step --;
}
wpp_dump($step);

?>
<div id="fre-post-project-3 step-payment" class="fre-post-project-step step-wrapper step-payment">

    <div class="fre-post-project-box">
        <div class="step-edit-project">
            <div class="edit-step"><?php _e( 'Your posted project has been saved and waiting for the payment to become available on site.', ET_DOMAIN ); ?></div>
            <div><a class="go-edit-project btn-center" href=""><?php _e( 'Edit', ET_DOMAIN ); ?></a></div>
        </div>
    </div>

    <div class="fre-post-project-box">
        <div class="step-choose-payment">
			<?php $number_free_plan_used = AE_Package::get_used_free_plan( $user_ID );

			$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
			if ( $id ) {
				$post    = get_post( $id );
				$pack_id = $post->et_payment_package;
				if ( empty( $pack_id ) ) {
					$pack_id = 'B1';
				}
				wpp_dump( $pack_id );
				foreach ( $packs as $key => $package ) {
					if ( $pack_id == $package->sku ) {
						$number_of_post = $package->et_number_posts;

						if ( $number_of_post >= 1 ) { ?>
                            <div class="show_select_package wpp-pack-type"
                                 data-package-type="<?php echo $package->post_type; ?>">
                                <p class="package_title"><?php _e( 'Your package:', ET_DOMAIN ); ?>
                                    <a data-toggle="collapse" href="#packinfo2"
                                       role="button"><strong><?php echo $package->post_title; ?></strong></a>
                                </p>
                            </div>
                            <div id="packinfo2" class="collapse pack-desk">
                                <p><?php _e( 'If you want to get more posts, you can directly move to purchase page.', ET_DOMAIN ); ?></p>
                                <div class="pack-left"><?php echo __( 'Your post(s) left:', ET_DOMAIN ) ?>
                                    <span>
                        <?php if ( isset( $orders[ $sku ] ) ) {
	                        $order = get_post( $orders[ $sku ] );
                        }

                        if ( isset( $package_data[ $sku ] ) && isset( $order->post_status ) && $order->post_status != 'draft' ) {
	                        $package_data_sku = $package_data[ $sku ];
	                        if ( isset( $package_data_sku['qty'] ) && $package_data_sku['qty'] > 0 ) {
		                        /**
		                         * print text when company has job left in package
		                         */
		                        $number_of_post = $package_data_sku['qty'];
	                        }
                        }

                        if ( ! $package->et_price ) { // if free package.
	                        $number_of_post = (int) $number_of_post - (int) $number_free_plan_used;
	                        if ( $number_of_post < 0 ) {
		                        $number_of_post = 0;
	                        }
                        }


                        if ( $number_of_post > 0 ) {
	                        if ( $number_of_post == 1 ) {
		                        $texthidden = $number_of_post;
	                        } else {
		                        $texthidden = $number_of_post;
	                        }
                        } else {
	                        $texthidden = $number_of_post;
                        }
                        echo $texthidden . ' post(s)'; ?></span>
                                </div>
                            </div>
						<?php }
					}
				}
			} else { ?>
                <div class="show_select_package">
                    <p class="package_title"><?php _e( 'Your package:', ET_DOMAIN ); ?>

                    </p>
                </div>
                <div id="packinfo3" class="collapse pack-desk">
                    <p><?php _e( 'If you want to get more posts, you can directly move to purchase page.', ET_DOMAIN ); ?></p>
                    <div class="pack-left"><?php echo __( 'Your post(s) left:', ET_DOMAIN ) ?>
                        <span></span>
                    </div>
                </div>
			<?php } ?>
			<?php /*if ( ! $disable_plan ) { */?>
                <div class="show_select_options">
                    <div class="option_title"><?php _e( 'Your option(s):', ET_DOMAIN ); ?>
                        <p><strong></strong></p>
                        <div class="total">
                            Total: <span></span>
                        </div>
                    </div>
                    <p class="option_description"></p>
                </div>
			<?php /*} */?>
            <!-- fix by figma -->
        </div>
    </div>
    <div class="fre-post-project-box">
        <div class="step-choose-payment">
            <!-- fix by figma -->
            <div class="pay_method"><?php _e( 'Payment Method', ET_DOMAIN ); ?></div>
            <p><?php _e( 'Select your most appropriate payment method', ET_DOMAIN ); ?></p>
			<?php do_action( 'before_payment_list_wrapper' ); ?>
            <form method="post" action="" id="checkout_form">
                <div class="payment_info"></div>
                <div style="position:absolute; left : -7777px; ">
                    <input type="submit" id="payment_submit"/>
                    <input type="submit" id="txn_type"/>
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
                        <a data-toggle="collapse" data-parent="#fre-payment-accordion" href="#fre-payment-paypal"
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
                        <a data-toggle="collapse" data-type="cash" data-parent="#fre-payment-accordion"
                           href="#fre-payment-cash"
                           class="btn-left collapsed fre-submit-btn other-payment"><?php _e( "Select", ET_DOMAIN ); ?></a>
                        <div id="fre-payment-cash" class="panel-collapse collapse fre-payment-proccess">
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

					<?php /*<script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
                    <script src="<?=get_template_directory_uri()?>/js/2checkout.js"></script>

                    <!-- JS-code for 2checkout is located in ../js/custom.js -->
                    <li class="panel">
                        <form class="modal-form form__block two-checkout__form" action="<?=get_template_directory_uri()?>/libs/2checkout/payment.php" method="POST">
                            <h3>Card information</h3>

                            <input type="hidden" class="two-checkout__token" name="token">

                            <div class="input-block" data-children-count="1">
                                <label for="two-ch_name_card">Name on card</label>
                                <input type="text" class="form-control two-checkout__card-holder" name="cardHolder" id="two-ch_name_card" placeholder="Name on card">
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-xs-12 input-block" data-children-count="1">
                                    <label for="two-ch_stripe_number">Card number</label>
                                    <input type="text" class="form-control two-checkout__card-number" id="two-ch_stripe_number" placeholder="****  ****  ****  ****">
                                </div>

                                <div class="col-md-3 col-xs-6 input-block" data-children-count="1">
                                    <label for="two-ch_expiration">Expiration</label>
                                    <input type="text" class="form-control two-checkout__expire" id="two-ch_expiration" placeholder="MM / YY">
                                </div>

                                <div class="col-md-3 col-xs-6 input-block" data-children-count="1">
                                    <label for="two-ch_cvc">CVV/CVC Code</label>
                                    <input type="text" class="form-control two-checkout__cvv" size="3" id="two-ch_cvc" placeholder="CVC">
                                </div>
                            </div>

                            <div class="submit__button">
                                <button class="btn-left fre-submit-btn">Make Payment</button>
                            </div>
                        </form>
                    </li> */ ?>
                    <li class="panel">
                        <div class="plan_t title-plan--2checkout" data-type="2checkout">
							<?php /*_e("2Checkout", ET_DOMAIN); */ ?>
                            <span><?php _e( "Payment via 2Checkout.", ET_DOMAIN ); ?></span>
                        </div>
                        <a href="#" class="btn-left collapsed btn-submit-price-plan fre-submit-btn select-payment"
                           data-type="2checkout"><?php _e( "Pay with a card", ET_DOMAIN ); ?></a>
                    </li>
				<?php } ?>

            </ul>
			<?php do_action( 'after_payment_list_wrapper' ); ?>
        </div>
    </div>

</div>