<div class="modal fade designed-modal" id="modal_archive_project">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( 'Are you sure you want to archive this project?', ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="form-archive-project" class="form-archive-project fre-modal-form">
                    <div class="fre-content-confirm">
                        <p><?php _e( 'Once project is archived, you can only renew it or permanently delete it.', ET_DOMAIN ); ?></p>
                    </div>
                    <input type="hidden" id="project-id" value="">
                    <div class="fre-form-btn">
                        <button type="submit"
                                class="fre-submit-btn btn-left btn-submit btn-archive-project"><?php _e( 'Confirm', ET_DOMAIN ) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->