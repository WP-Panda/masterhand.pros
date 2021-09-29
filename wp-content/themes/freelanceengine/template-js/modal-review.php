<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 *
 * MODAL FINISH PROJECT
 */

defined( 'ABSPATH' ) || exit;
global $post, $user_ID;
/**
 * делаю заранее для исключения лишких вызовов
 * @todo  надо будет Добавить в объект текущего юзера или в глобальную переменную
 */
$user_flag   = wpp_fre_is_freelancer();
$author_id_S = 0;

if ( (int)$post->post_author === (int)$user_ID ) {// employer finish project form
	$val  = 'employer';
	$text = __( "Great! Your project is going to be finished, it's time to review and rate for your Professional. Your review and rating will affect the Professional's reputation.", ET_DOMAIN );
} else { // freelancer finish project form
	$val  = 'freelancer';
	$text = __( 'Congratulation! The Client has been marked your working project as finished. Please check your personal account to make sure money is successfully transferred.', ET_DOMAIN );
}

$bid_id_accepted = get_post_meta( $post->ID, 'accepted', true );
if ( get_post_meta( $bid_id_accepted, 'fre_bid_order' ) ) {
	if ( ! empty( $user_flag ) ) {
		$author_id_S = $post->post_author;
	} else {
		$bid_author  = get_post_field( 'post_author', $bid_id_accepted );
		$author_id_S = $bid_author;
	}
}

$text_2 = ! empty( $user_flag ) ? __( "Please give endorsement to the Client’s skills", ET_DOMAIN ) : __( "Please give endorsement to the Professional’s skills", ET_DOMAIN );
?>
    <div class="modal fade designed-modal" id="modal_review" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
					<?php _e( "Project Completion", ET_DOMAIN ); ?>
                </div>
                <div class="modal-body">
                    <form role="form" id="review_form" class="review-form fre-modal-form wpp-01">
                        <input type="hidden" name="is_reply" value="false">
                        <input type="hidden" name="project_id" value="<?php echo $post->ID ?>">
                        <input type="hidden" name="action" value="rwRating"/>
						<?php printf( '<input type="hidden" name="from_is" value="%s"/><p class="notify-form">%s</p>', $val, $text ); ?>
                        <div class="fre-input-field">
                            <label class="fre-field-title" for="comment-content">
								<?php _e( 'Your Rating', ET_DOMAIN ); ?>
                            </label>
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
                            <label class="fre-field-title" for="comment-content">
								<?php _e( 'Your Review', ET_DOMAIN ); ?>
                            </label>
                            <textarea id="comment-content" name="comment" placeholder="<?php _e( 'Leave a review...', ET_DOMAIN ); ?>"></textarea>
                        </div>
                        <div class="modal-endors">
							<?php wpp_get_template_part( 'wpp/templates/profile/tabs/skill-list', [ 'user_ID' => $author_id_S,'no_vals'=>true ] );
							printf( '<p class="hide notify-form">%s</p>', $text_2 ); ?>
                        </div>
                        <div class="fre-form-btn">
                            <button type="submit" class="fre-submit-btn btn-left btn-submit">
								<?php _e( 'Complete Project', ET_DOMAIN ) ?>
                            </button>
                            <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php wp_enqueue_script( '', '/wp-content/plugins/reviews_rating/js/reviews.js', [], false, true );