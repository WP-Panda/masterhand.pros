<div class="modal fade" id="modal_forgot">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Forgot Password?", ET_DOMAIN ) ?>
            </div>
            <div class="modal-body">
                <form role="form" id="forgot_form" class="fre-authen-lost-pass auth-form forgot_form">
                    <div class="fre-input-field">
                        <label for="forgot_user_email"><?php _e( 'Enter your email here', ET_DOMAIN ) ?></label>
                        <input type="text" class="need_valid" id="user_email" name="user_email"/>
                    </div>
                    <button type="submit" class="fre-submit-btn btn-submit btn-sumary btn-sub-create">
						<?php _e( 'Send', ET_DOMAIN ) ?>
                    </button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->