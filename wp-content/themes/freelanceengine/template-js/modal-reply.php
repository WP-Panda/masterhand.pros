<?php
global $post, $user_ID;
?>
<!-- MODAL REVIEW REPLY-->
<div class="modal fade designed-modal" id="modal_reply" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Reply to review", ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="review_form" class="review-form fre-modal-form">
                    <input type="hidden" name="is_reply" value="true">
                    <input type="hidden" name="project_id"
                           value="<?= is_page_template( 'page-profile.php' ) ? '' : $post->ID ?>">
                    <input type="hidden" name="action" value="rwRating"/>
                    <input type="hidden" name="from_is"
                           value="<?php echo ( ae_user_role( $user_ID ) == FREELANCER ) ? 'freelancer' : 'employer' ?>"/>
                    <input type="hidden" name="reviewing_id" value="0"/>
                    <p class="notify-form">
						<?php _e( "You can leave your reply on the freelancer's review within 20 days", ET_DOMAIN ); ?>
                    </p>

                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="comment-content"><?php _e( 'Your Reply', ET_DOMAIN ); ?></label>
                        <textarea id="comment-content" name="comment"
                                  placeholder="<?php _e( 'Leave a reply...', ET_DOMAIN ); ?>"></textarea>
                    </div>

                    <div class="fre-form-btn">
                        <button type="submit" class="fre-submit-btn btn-left btn-submit">
							<?php _e( 'Send reply', ET_DOMAIN ) ?>
                        </button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL REVIEW REPLY-->

<?
wp_enqueue_script( '', '/wp-content/plugins/reviews_rating/js/reviews.js', [], false, true );
?>
