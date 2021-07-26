<?php
if ( ! is_author() ) {
	global $post;
	$send_to = $post->post_author;
} else {
	$send_to = get_query_var( 'author' );
}
$email = '';
global $user_ID;
$current_user = get_userdata( $user_ID );
if ( $current_user ) {
	$email = $current_user->user_email;
}
?>
<div class="modal fade" id="modal_contact">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( 'Send an email to this professional', ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="submit_contact" class="bid-form fre-modal-form submit_contact">
                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="sender_email"><?php _e( 'Your email', ET_DOMAIN ); ?></label>
                        <div class="fre-project-budge2">
                            <input type="text" name="sender_email" required value="<?php echo $email; ?>"
                                   id="sender_email" class="form-control required"/>
                        </div>
                    </div>
                    <div class="fre-input-field no-margin-bottom">
                        <label class="fre-field-title"
                               for="post_content"><?php _e( 'Your messsage here', ET_DOMAIN ); ?></label>
                        <textarea id="message" name="message" required class="required"></textarea>
                    </div>
                    <input type="hidden" name="send_to" value="<?php echo $send_to; ?>">

                    <div class="fre-form-btn">
                        <button type="submit" class="fre-submit-btn btn-left btn-submit">
							<?php _e( 'Submit', ET_DOMAIN ) ?>
                        </button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- MODAL BIG -->
