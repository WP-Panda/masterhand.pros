<?php

	global $wp_query, $wpdb, $ae_post_factory, $post, $user_ID;
	$post_object         = $ae_post_factory->get( PROJECT );
	$convert             = $project = $post_object->convert( $post );
	$et_expired_date     = $convert->et_expired_date;
	$bid_accepted        = $convert->accepted;
	$project_status      = $convert->post_status;
	$project_link        = get_permalink( $post->ID );
	$role                = ae_user_role();
	$bid_accepted_author = get_post_field( 'post_author', $bid_accepted );
	$profile_id          = $post->post_author;

	if ( ( fre_share_role() || $role != FREELANCER ) ) {
		$profile_id = $bid_accepted_author;
	}
	$currency = ae_get_option( 'currency', [ 'align' => 'left', 'code' => 'USD', 'icon' => '$' ] );

	$review_for_freelancer = review_rating_init()->getReviewDoc( $bid_accepted );

	$review_for_employer = review_rating_init()->getReviewDoc( $post->ID );

	$reply_to_review = review_rating_init()->getReviewReply( $post->ID );

	$freelancer_info = get_userdata( $bid_accepted_author );
	$ae_users        = AE_Users::get_instance();
	$freelancer_data = $ae_users->convert( $freelancer_info->data );

	if ( ( fre_share_role() || $role == FREELANCER ) && $project_status == 'complete' ) { ?>
        <div class="project-detail-box">
            <div class="project-employer-review">
                <span class="employer-avatar-review"><?php echo $convert->et_avatar; ?></span>
                <p><a href="<?php echo $convert->author_url; ?>" class="review-author-name"
                      target="_blank"><?php echo $convert->author_name; ?></a></p>

				<?php if ( ! empty( $review_for_freelancer[ 'comment' ] ) ) { ?>
                    <p>"<? echo nl2br( stripcslashes( $review_for_freelancer[ 'comment' ] ) ); ?>"</p>
				<?php } ?>

				<?php if ( ! empty( $reply_to_review[ 'comment' ] ) ) { ?>
                    <p>"<?php echo nl2br( stripcslashes( $reply_to_review[ 'comment' ] ) ); ?>"</p>
				<?php } ?>

				<?php if ( empty( $review_for_employer ) ) { ?>
                    <a href="#" id="<?php the_ID(); ?>"
                       class="fre-normal-btn fre-submit-btn btn-complete-project btn-review-for-client"> <?php _e( 'Review for Client', ET_DOMAIN ); ?></a>
				<?php } ?>

				<?php
					if ( $bid_accepted_author == $user_ID && ! empty( $review_for_freelancer ) ) {
						if ( $review_for_freelancer[ 'status' ] == review_rating_init()::STATUS_HIDDEN ) { ?>
                            <div class="showed-review">
                                <a href="#" data-review_id="<?= $review_for_freelancer[ 'id' ] ?>"
                                   class="review-must-paid fre-submit-btn fre-normal-btn"><?php _e( 'Showed', ET_DOMAIN ); ?></a>
                            </div>
							<?php get_template_part( 'template-js/modal-showed-review', 'item' ); ?>
						<?php }
					} ?>
            </div>
        </div>
	<?php } else if ( ( fre_share_role() || $role == EMPLOYER ) && $project_status == 'complete' && ! empty( $review_for_employer[ 'comment' ] ) ) { ?>
        <div class="project-detail-box" style="display:flex">
            <div class="project-employer-review">
                <span class="employer-avatar-review"><?php echo $freelancer_data->avatar; ?></span>
                <p><a href="<?php echo $freelancer_data->author_url; ?>" class="review-author-name"
                      target="_blank"><?php echo $freelancer_data->display_name; ?></a>
                </p>
                <p><?php echo '"' . $review_for_employer[ 'comment' ] . '"'; ?></p>
            </div>

			<?php
				$review_created = strtotime( $review_for_freelancer[ 'created' ] );
				$reply_deadline = strtotime( '+20 day', $review_created );
				$today          = time();

				/*if ($today < $reply_deadline && empty($reply_to_review)){ ?>
					<a title="Reply to a review" href="#" data-review_id="<?=$review_for_freelancer['id']?>" id="<?=$review_for_freelancer['id']?>" class="fre-submit-btn btn-left project-employer__reply main_bl-btn"><?php _e( 'Reply to a review', ET_DOMAIN ); ?></a>
				<?php }*/ ?>
        </div>
	<?php } ?>


<div class="workspace project-detail-box">
    <div class="project-detail-info">
        <div class="row">
            <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
                <h1 class="project-detail-title"><a href="<?php echo $project_link; ?>"><?php the_title(); ?></a></h1>
                <p class="pull-right project-detail-posted visible-xs"><?php printf( __( 'Posted on %s', ET_DOMAIN ), $project->post_date ); ?></p>
                <ul class="project-bid-info-list">
                    <li>
						<?php if ( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ) { ?>
                            <span><?php _e( 'Client', ET_DOMAIN ); ?></span>
                            <a href="<?php echo $convert->author_url; ?>"
                               target="_blank"><?php echo $convert->author_name; ?></a>
						<?php } else if ( ( fre_share_role() || $role !== FREELANCER ) && $user_ID == $project->post_author ) { ?>
                            <span><?php _e( 'Professional', ET_DOMAIN ); ?></span>
                            <a href="<?php echo $freelancer_data->author_url; ?>"
                               target="_blank"><?php echo the_author_meta( 'display_name', $profile_id ); ?></a>
						<?php } ?>
                    </li>
                    <li>
                        <span><?php _e( 'Deadline', ET_DOMAIN ); ?></span>
						<?php echo date_i18n( "F j, Y", strtotime( $project->project_deadline ) );; ?>
                    </li>
                    <li>
                        <span><?php _e( 'Status', ET_DOMAIN ); ?></span>
						<?php
							$status_arr = [
								'close'     => __( "Processing", ET_DOMAIN ),
								'complete'  => __( "Completed", ET_DOMAIN ),
								'disputing' => __( "Disputed", ET_DOMAIN ),
								'disputed'  => __( "Resolved", ET_DOMAIN ),
								'publish'   => __( "Active", ET_DOMAIN ),
								'pending'   => __( "Pending", ET_DOMAIN ),
								'draft'     => __( "Draft", ET_DOMAIN ),
								'reject'    => __( "Rejected", ET_DOMAIN ),
								'archive'   => __( "Archived", ET_DOMAIN ),
							];
							echo $status_arr[ $post->post_status ]; ?>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-5  col-sm-12 col-xs-12">
                <p class="project-detail-posted hidden-xs"><?php printf( __( 'Posted on %s', ET_DOMAIN ), $project->post_date ); ?></p>
                <div class="winning-bid">
					<?php _e( 'Winning Bid', ET_DOMAIN ); ?>
                    <span><?php echo $project->bid_budget_text; ?></span>
                </div>
                <div class="project-detail-action workspace-actions">
					<?php
						if ( $post->post_status == 'close' ) {
							if ( (int) $project->post_author == $user_ID ) { ?>
                                <a title="<?php _e( 'Finish', ET_DOMAIN ); ?>" href="#" id="<?php the_ID(); ?>"
                                   class="fre-submit-btn btn-left btn-complete-project"> <?php _e( 'Finish', ET_DOMAIN ); ?></a>
								<?php if ( ae_get_option( 'use_escrow' ) ) { ?>
                                    <a title="<?php _e( 'Discontinue', ET_DOMAIN ); ?>" href="#" id="<?php the_ID(); ?>"
                                       class="fre-submit-btn btn-right btn-close-project"><?php _e( 'Discontinue', ET_DOMAIN ); ?></a>
								<?php }
							} else {
								if ( $bid_accepted_author == $user_ID && ae_get_option( 'use_escrow' ) ) { ?>
                                    <a title="<?php _e( 'Discontinue', ET_DOMAIN ); ?>" href="#" id="<?php the_ID(); ?>"
                                       class="fre-submit-btn btn-right btn-quit-project"><?php _e( 'Discontinue', ET_DOMAIN ); ?></a>
								<?php }
							}
							//					} else if ( $post->post_status == 'complete' ) {
							//                        if ( $bid_accepted_author == $user_ID) {
							//                            if(review_rating_init()->isReviewNotExists($post->ID, $user_ID)) {
							//                                add_action('wp_footer', 'init_js_modal_review_rating', 100);
							//                            ?>
                            <!--                            <a title="--><?php //_e( 'Finish', ET_DOMAIN ); ?>
                            <!--" href="#" id="--><?php //the_ID(); ?>
                            <!--"-->
                            <!--                                 class="fre-action-btn btn-complete-project"> --><?php //_e( 'Finish', ET_DOMAIN ); ?>
                            <!--</a>-->
                            <!--                            -->
							<? //
							//                            }
							//                        }
						} else if ( $post->post_status == 'disputing' ) { ?>
                            <a href="<?php echo add_query_arg( [ 'dispute' => 1 ], $project_link ) ?>"
                               class="fre-normal-btn"><?php _e( 'Dispute Page', ET_DOMAIN ) ?></a>
						<?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>