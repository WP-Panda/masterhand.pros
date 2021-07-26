<div class="modal fade" id="modal_delete_project">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( 'Are you sure you want to delete this project?', ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="form-delete-project" class="form-delete-project fre-modal-form">
                    <div class="fre-content-confirm">
                        <p><?php _e( "Once the project is deleted, it will be permanently removed from the site and its information won't be recovered.", ET_DOMAIN ); ?></p>
                    </div>
                    <input type="hidden" id="project-id" value="">
                    <div class="fre-form-btn">
                        <button type="submit"
                                class="fre-submit-btn btn-left btn-submit btn-delete-project"><?php _e( 'Confirm', ET_DOMAIN ) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->