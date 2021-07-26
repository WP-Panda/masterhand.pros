<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

global $user_ID;

$user_data = get_userdata( $user_ID );
?>

<div class="modal fade designed-modal" id="modal_get_quote" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Request a Quote", ET_DOMAIN ); ?>
                <ul id="listGQ" class="list-for-quote-company"></ul>
            </div>
            <div class="modal-body">
                <form role="form" id="form_get_quote" class="fre-modal-form" method="POST">
                    <input type="hidden" name="action" value="wpp_send_quote_company"/>
                    <div class="fre-input-field">
                        <label class="fre-field-title"><?php _e( 'Your name', ET_DOMAIN ); ?></label>
                        <input type="text" name="display_name" value="<?php echo $user_data->display_name; ?>">
                    </div>
                    <div class="fre-input-field">
                        <label class="fre-field-title"><?php _e( 'Your email', ET_DOMAIN ); ?></label>
                        <input type="text" value="<?php echo $user_data->user_email; ?>" readonly>
                    </div>
                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="message-content"><?php _e( 'Your message', ET_DOMAIN ); ?></label>
                        <textarea id="message-content" name="message"
                                  placeholder="<?php _e( 'Your message...', ET_DOMAIN ); ?>"></textarea>
                    </div>
                    <div class="">
                        <button type="submit"
                                class="fre-submit-btn btn-submit btn-left"><?php _e( 'Send message', ET_DOMAIN ) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade designed-modal" id="modal_get_multiQuote" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Request a Quote", ET_DOMAIN ); ?>
                <ul id="listGMQ" class="list-for-quote-company"></ul>
            </div>
            <div class="modal-body">
                <form role="form" id="form_get_multiQuote" class="fre-modal-form" method="POST">
                    <input type="hidden" name="action" value="wpp_send_quote_company"/>
                    <div class="fre-input-field">
                        <label class="fre-field-title"><?php _e( 'Your name', ET_DOMAIN ); ?></label>
                        <input type="text" name="display_name" value="<?php echo $user_data->display_name; ?>">
                    </div>
                    <div class="fre-input-field">
                        <label class="fre-field-title"><?php _e( 'Your email', ET_DOMAIN ); ?></label>
                        <input type="text" value="<?php echo $user_data->user_email; ?>" readonly>
                    </div>
                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="message-content2"><?php _e( 'Your message', ET_DOMAIN ); ?></label>
                        <textarea id="message-content2" name="message"
                                  placeholder="<?php _e( 'Your message...', ET_DOMAIN ); ?>"></textarea>
                    </div>
                    <div class="">
                        <button type="submit"
                                class="fre-submit-btn btn-submit btn-left"><?php _e( 'Send message', ET_DOMAIN ) ?></button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>