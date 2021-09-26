<?php
/**
 * the template for displaying the freelancer work (bid success a project)
 * # this template is loaded in template/bid-history-list.php
 */
defined( 'ABSPATH' ) || exit;
global $rwProject, $user_ID;

$convert               = $project = $post_object->convert( $post );
$bid_accepted          = $convert->accepted;
$review_for_freelancer = review_rating_init()->getReviewDoc( $bid_accepted );
$review_for_employer   = review_rating_init()->getReviewDoc( $post->ID );
$reply_to_review       = review_rating_init()->getReviewReply( $post->ID );

$review_created = strtotime( $review_for_freelancer['created'] );
$reply_deadline = strtotime( '+20 day', $review_created );
$today          = time();
?>
<li>
    <div class="fre-author-project-box row" data-id="<?= $rwProject['user_id']; ?>">
        <div class="col-sm-1 col-xs-3 avatar_wp"><?php echo get_avatar( $rwProject['user_id'] ); ?></div>
        <div class="col-sm-11 col-xs-9">
            <div class="col-sm-9 col-md-10 col-lg-10 col-xs-12 fre-author-project">TEST1
                <span class="fre-author-project-box_t"><?php echo $rwProject['author_project']; ?></span>
				<?php $user_status = get_user_pro_status( $rwProject['user_id'] );
				if ( $user_status && ( $user_status != PRO_BASIC_STATUS_EMPLOYER || $user_status != PRO_BASIC_STATUS_FREELANCER ) ) {
					pro_label();
				} ?>
                <span class="hidden-xs rating-new">+1000</span>
            </div>
            <span class="visible-xs col-xs-5 rating-new">+1000</span>
            <div class="col-sm-8 col-md-10 col-lg-10 hidden-xs fre-project_lnk">
                <span><?php _e( 'Project:', ET_DOMAIN ); ?></span>
                <a href="<?php echo $rwProject['guid']; ?>"
                   title="<?php echo esc_attr( $rwProject['post_title'] ); ?>">
					<?php echo $rwProject['post_title']; ?>
                </a>
            </div>

            <span class="hidden-xs col-sm-4 col-md-2 col-lg-2 posted text-right"><?php _e( date( 'F d, Y', strtotime( $rwProject['post_date'] ) ), ET_DOMAIN ); ?></span>
			<?php if ( $role == EMPLOYER && ! empty( $review_for_employer['comment'] ) ) { ?>
                <div class="project-detail-box">
                    <div class="project-employer-review">
                        <span class="employer-avatar-review"><?php echo $freelancer_data->avatar; ?></span>
                        <p><a href="<?php echo $freelancer_data->author_url; ?>" class="review-author-name"
                              target="_blank"><?php echo $freelancer_data->display_name; ?></a>
                        </p>
                        <p><?php echo '"' . $review_for_employer['comment'] . '"'; ?></p>
                    </div>

					<?php if ( $today < $reply_deadline && empty( $reply_to_review ) ) { ?>
                        <a title="Reply to review" href="#" data-review_id="<?= $review_for_freelancer['id'] ?>"
                           id="<?= $review_for_freelancer['id'] ?>"
                           class="fre-submit-btn btn-left project-employer__reply project-employer_reply_history  main_bl-btn"><?php _e( 'Reply to review', ET_DOMAIN ); ?></a>
					<?php } ?>
                </div>
			<?php } ?>
        </div>
        <div class="visible-xs col-xs-12 fre-project_lnk">
            <span><?php echo __( 'Project:', ET_DOMAIN ) ?></span>
            <a href="<?php echo $rwProject['guid']; ?>" title="<?php echo esc_attr( $rwProject['post_title'] ); ?>">
				<?php echo $rwProject['post_title']; ?>
            </a>
        </div>
        <div class="col-sm-12 col-xs-12 author-project-comment">
            <div class="col-sm-9 col-md-10 col-lg-10 col-xs-12">
				<? string_is_nl2br( $rwProject['comment'] ); ?>
            </div>
            <div class="col-sm-3 col-md-2 col-lg-2 col-xs-7">
				<?php
				if ( $rwProject['post_title'] == review_rating_init()::STATUS_HIDDEN && ( $user_ID == $rwProject['for_user_id'] ) ) {
					?>
                    <a href="#modal_showed_review" data-review_id="<?php echo $rwProject['id']; ?>"
                       data-is_showed="1"
                       data-text="<?php _e( 'Show', ET_DOMAIN ); ?>"
                       data-toggle="modal"
                       class="showed_review btn-over employer-info-edit-btn profile-show-edit-tab-btn"><?php _e( 'Show', ET_DOMAIN ); ?></a>
				<?php } ?>
            </div>
        </div>
        <span class="visible-xs col-sm-2 col-xs-12 posted text-right"><?php _e( $rwProject['post_date'], ET_DOMAIN ); ?></span>
    </div>
</li>