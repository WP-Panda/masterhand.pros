<div class="modal fade" id="reject_post">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<?php printf(__("Reject <span>%s</span>", ET_DOMAIN), 'post' ) ; ?>
			</div>
			<div class="modal-body">
            	<form class="reject-ad reject-project form_modal_style">
                    <div class="form-group fre-input-field">
                        <label><?php _e("Message", ET_DOMAIN) ?><span class="alert-icon">*</span></label>
                        <textarea name="reject_message" ></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                        <button type="submit" class="fre-submit-btn btn-submit">
						<?php _e('Reject', ET_DOMAIN) ?>
					</button>
                    </div>
                </form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->