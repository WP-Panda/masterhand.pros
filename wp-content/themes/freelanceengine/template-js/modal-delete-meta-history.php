<div class="modal fade" id="modal_delete_meta_history">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( 'Are your sure you want to delete this item?', ET_DOMAIN ) ?>
            </div>
            <div class="modal-body">
                <form action="" class="fre-modal-form form_delete_meta_history" data-processing='no' data-last="0">
                    <div class="fre-content-confirm">
                        <p><?php _e( "Once the item is deleted, it will be permanently removed from the site and its information won't be recovered.", ET_DOMAIN ) ?></p>
                    </div>

                    <div class="fre-form-btn">
                        <input type="hidden" value="" name="ID">
                        <input class="fre-submit-btn btn-left btn-submit" type="submit"
                               value="<?php _e( 'Confirm', ET_DOMAIN ) ?>">
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->