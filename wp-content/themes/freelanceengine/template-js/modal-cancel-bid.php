<div class="modal fade" id="modal_cancel_bid">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <?php _e( 'Are you sure you want to cancel your bid on this project?', ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="form-cancel-bid" class="form-cancel-bid fre-modal-form">
                    <div class="fre-content-confirm">
                        <p><?php _e( 'Once you cancel the bid, this project will be removed from your working list. However, you can bid this project again after canceling.', ET_DOMAIN ); ?></p>
                    </div>
                    <input type="hidden" id="bid-id" value="">
                    <div class="fre-form-btn">
                        <button type="submit" class="fre-submit-btn btn-left btn-submit btn-cancel-bid"><?php _e('Confirm', ET_DOMAIN) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->