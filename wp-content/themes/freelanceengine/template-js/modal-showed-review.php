<div class="modal fade designed-modal" id="modal_show_review">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="fre-content-confirm">
                    <div class="modal_t-big">
						<?php _e( "You can make this review visible on your profile and add it to your rating for", ET_DOMAIN ) ?>
                        <span id="rwTotalPrice"></span> <span id="rwCurrency"></span>
                    </div>
                </div>
                <input type="hidden" id="reviewId" name="rwId" value="">
                <div class="show-review-btn-group">
                    <form method="POST" action="/order/payment/" style="float:left">
                        <!--URL, куда покупатель будет перенаправлен после успешной оплаты.
                        Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="return" value="<?= bloginfo( 'home' ) ?>/payment-completed">

                        <!--URL, куда покупатель будет перенаправлен при отмене им оплаты.
                        Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="cancel_return" value="<?= bloginfo( 'home' ) ?>/cancel-payment">

                        <!--URL, на который PayPal будет предавать информацию о транзакции (IPN).
                        Если не передавать этот параметр, будет использоваться значение, указанное в настройках аккаунта.
                        Если в настройках аккаунта это также не определено, IPN использоваться не будет-->
                        <input type="hidden" name="notify_url"
                               value="<?php bloginfo( 'stylesheet_directory' ); ?>/ipn.php">

                        <input type="hidden" name="payment_type" value="review">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="review_id" value="">
                        <input type="hidden" name="item_name" value="Pay for Review">
                        <input type="hidden" name="item_number" value="<?= $user_ID ?>">
                        <input type="hidden" name="amount" value="<?= $review_price ?>">
                        <input type="hidden" name="price" value="<?= $review_price ?>">
                        <input type="hidden" name="currency_code" value="<?= $review_currency ?>">
                        <input type="hidden" name="no_shipping" value="1">
                        <input type="hidden" name="rm" value="2">
                        <input type="hidden" name="plan_name" value="Review Payment">

                        <input type="submit" class="fre-submit-btn btn-left" value="<? _e( 'Pay', ET_DOMAIN ); ?>">
                    </form>

                    <div class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Close', ET_DOMAIN ); ?></div>

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
