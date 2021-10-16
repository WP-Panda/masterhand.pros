<div class="modal fade designed-modal" id="select-type-accept-bid" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( 'Do you want to accept this bid?', ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" class="fre-modal-form">
                    <div class="fre-content-confirm fre-form-btn btn-wrap bidding-btns">
                        <a href="" id="<?php echo get_the_ID(); ?>"
                           class="fre-normal-btn fre-submit-btn btn-left btn-accept-bid"><?php _e( 'SafePay Deal', ET_DOMAIN ); ?></a>
                        <a href="" id="<?php echo get_the_ID(); ?>"
                           class="fre-normal-btn fre-cancel-btn btn-right btn-accept-bid-no-escrow"><?php _e( 'Regular Deal', ET_DOMAIN ); ?></a>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<div class="modal fade designed-modal" id="accept-bid-no-escrow" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Accept Bid with Regular Deal", ET_DOMAIN ) ?>
            </div>
            <div class="modal-body">
                <form role="form" id="accept_bid_no_escrow" class="fre-modal-form">
                    <div class="fre-content-confirm">
                        <p><?php _e( "Once you accept this bid, your project will start processing<br/> and chosen Professional will begin working on it.", ET_DOMAIN ) ?></p>
                        <p><?php _e( "If you have any dispute over the project or payment disagreement<br/> you cannot get help from our Resolution Center.", ET_DOMAIN ) ?></p>
                    </div>
                    <div class="fre-form-btn">
                        <button type="button" class="fre-submit-btn btn-left fre-normal-btn"
                                id="submit_accept_bid"><?php _e( "Confirm", ET_DOMAIN ) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->