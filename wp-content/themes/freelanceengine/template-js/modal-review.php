<?php
global $post, $user_ID;
?>
<!-- MODAL FINISH PROJECT-->
<div class="modal fade designed-modal" id="modal_review" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Project Completion", ET_DOMAIN ); ?>
            </div>
            <div class="modal-body">
                <form role="form" id="review_form" class="review-form fre-modal-form">
                    <input type="hidden" name="is_reply" value="false">
                    <input type="hidden" name="project_id" value="<?=$post->ID?>">
                    <input type="hidden" name="action" value="rwRating"/>
					<?php if ( $post->post_author == $user_ID ) {  // employer finish project form ?>
                        <input type="hidden" name="from_is" value="employer"/>
                        <p class="notify-form">
							<?php _e( "Great! Your project is going to be finished, it's time to review and rate for your Professional. Your review and rating will affect the Professional's reputation.", ET_DOMAIN ); ?>
                        </p>
					<?php } else { // freelancer finish project form ?>
                        <input type="hidden" name="from_is" value="freelancer"/>
                        <p class="notify-form">
							<?php _e( 'Congratulation! The Client has been marked your working project as finished. Please check your personal account to make sure money is successfully transferred.', ET_DOMAIN ); ?>
                        </p>
					<?php } ?>
                    <div class="fre-input-field">
                        <label class="fre-field-title" for="comment-content"><?php _e( 'Your Rating', ET_DOMAIN ); ?></label>

                        <input class="val_review_rating" type="hidden" name="vote" value="">
                        <div class="review-select-vote">
                            <i class="review-star-vote rating-1 fa fa-star-o" data-vote="1"></i>
                            <i class="review-star-vote rating-2 fa fa-star-o" data-vote="2"></i>
                            <i class="review-star-vote rating-3 fa fa-star-o" data-vote="3"></i>
                            <i class="review-star-vote rating-4 fa fa-star-o" data-vote="4"></i>
                            <i class="review-star-vote rating-5 fa fa-star-o" data-vote="5"></i>
                        </div>
                    </div>

                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="comment-content"><?php _e( 'Your Review', ET_DOMAIN ); ?></label>
                        <textarea id="comment-content" name="comment" placeholder="<?php _e( 'Leave a review...', ET_DOMAIN ); ?>"></textarea>
                    </div>

                    <div class="modal-endors">
                    <?php $bid_id_accepted = get_post_meta($post->ID, 'accepted', true);
                    if(get_post_meta($bid_id_accepted, 'fre_bid_order')) {
                        if (ae_user_role($user_ID) == FREELANCER) {
                           renderSkillsInProject($post->post_author, $user_ID);
                        } else {
                            $bid_author = get_post_field('post_author', $bid_id_accepted);
                            renderSkillsInProject($bid_author, $user_ID);
                        }
                    } ?> 
                  
                        <?php if (ae_user_role($user_ID) == FREELANCER) { ?>
                         <p class="hide notify-form"><?php _e( "Please give endorsement to the Client’s skills", ET_DOMAIN );?></p>
                       <?php } else { ?>
                         <p class="hide notify-form"><?php _e( "Please give endorsement to the Professional’s skills", ET_DOMAIN );?></p>
                       <?php } ?>     
                    </div>
                    <div class="fre-form-btn">
                        <button type="submit" class="fre-submit-btn btn-left btn-submit">
							<?php _e( 'Complete Project', ET_DOMAIN ) ?>
                        </button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL FINISH PROJECT-->

<?
wp_enqueue_script('', '/wp-content/plugins/reviews_rating/js/reviews.js', [], false, true);
?>
