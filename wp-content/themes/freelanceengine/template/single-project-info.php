<?php

global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get( PROJECT );

$convert        = $project = $post_object->convert( $post );
$project_status = $project->post_status;
$author_id      = $convert->post_author;

$user_role = ae_user_role( $user_ID );

$et_expired_date = $convert->et_expired_date;
$bid_accepted    = $convert->accepted;
$project_status  = $convert->post_status;

$profile_id   = get_user_meta( $post->post_author, 'user_profile_id', true );
$project_link = get_permalink( $post->ID );
$currency     = ae_get_option( 'currency', [ 'align' => 'left', 'code' => 'USD', 'icon' => '$' ] );
$avg          = 0;
$user_status  = get_user_pro_status( $user_ID );
if ( is_user_logged_in() && ( ( fre_share_role() || $user_role == FREELANCER ) ) ) {
	$bidding_id  = 0;
	$child_posts = get_children( [
		'post_parent' => $project->ID,
		'post_type'   => BID,
		'post_status' => 'publish',
		'author'      => $user_ID
	] );
	if ( ! empty( $child_posts ) ) {
		foreach ( $child_posts as $key => $value ) {
			$bidding_id = $value->ID;
		}
	}
}

if ( function_exists( 'optionsProject' ) ) {
	$optionsProject = optionsProject( $project );
} else {
	$optionsProject = null;
}
?>
<div class="project-detail-box" <?php echo $optionsProject['highlight_project'] ?> >
    <div class="project-detail-info">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <h1 class="project-detail-title">
                    <p><?php the_title(); ?></p>
                    <span class="project-detail-status">
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
                    echo $status_arr[ $post->post_status ];
                    // echo '<span>'; // 1.8.5 add
                    if ( ! empty( $et_expired_date ) ) {
	                    //    printf(__(' - %s left', ET_DOMAIN), human_time_diff(time(), strtotime($et_expired_date)));
                    }
                    // echo '</span>';
                    ?>
                    </span>
                </h1>
                <ul class="project-bid-info-list row">
                    <li class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <?php if ($project->total_bids > 0) {
                            if ($project->total_bids == 1) {
                                printf(__('<span>Bid:</span><span>%s</span>', ET_DOMAIN), $project->total_bids);
                            } else {
                                printf(__('<span>Bids:</span><span>%s</span>', ET_DOMAIN), $project->total_bids);
                            }
                        } else {
                            printf(__('<span>Bids:</span><span>%s</span>', ET_DOMAIN), $project->total_bids);
                        } ?>
                    </li>
                    <li class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                        <span><?php _e( 'Average Bid:', ET_DOMAIN ); ?></span>
                        <span class="secondary-color">
                            <?php
                            if ( $project->total_bids > 0 ) {
	                            $avg = get_total_cost_bids( $project->ID ) / $project->total_bids;
                            }
                            echo fre_price_format( $avg );
                            ?>
                        </span>
                    </li>
                    <li class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <span><?php _e( 'Budget:', ET_DOMAIN ); ?></span>
                        <span><?php echo $project->budget; ?></span>
                    </li>
                    <!--new-->
                    <li class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                        <span><?php _e( 'Location:', ET_DOMAIN ); ?></span><span><?php echo $project->str_location; ?></span>
                    </li>
                    <!--new-->
                    <!--new2-->
                    <li class="col-sm-12 col-xs-12"><span><?php _e( 'Option:', ET_DOMAIN ); ?></span>
                        <span><i>
                        <?php
                        echo $optionsProject['create_project_for_all'];
                        echo $optionsProject['priority_in_list_project'];
                        echo $optionsProject['urgent_project'];
                        echo $optionsProject['hidden_project'];
                        ?>
                                <script>
                                    var ee = '<?php echo $optionsProject["hidden_project"] ?>';
                                    if (ee) {
                                        jQuery('head').prepend('<meta name="robots" content="noindex, nofollow">')
                                    }
                                </script>
                        </i></span>
                        <!--new2-->
                    </li>
                </ul>
            </div>
            <div class="col-md-3 col-lg-3 col-sm-4 col-xs-12">
                <p class="project-detail-posted"><?php printf( __( 'Posted on %s', ET_DOMAIN ), $project->post_date ); ?></p>

                <div class="project-detail-action">
					<?php
					if ( is_user_logged_in() ) {
						if ( $project_status == 'publish' ) {
							if ( ( fre_share_role() || $user_role == FREELANCER ) && $user_ID != $project->post_author ) {
								$has_bid = fre_has_bid( get_the_ID() );
								if ( $has_bid ) { ?>

                                    <a class="fre-action-btn fre-submit-btn btn-right bid-action" data-action="edit"
                                       data-bid-id="<?php echo $bidding_id ?>">
										<?php echo __( 'Edit', ET_DOMAIN ) ?>
                                    </a>

                                    <a class="fre-cancel-btn btn-right bid-action" data-action="cancel"
                                       data-bid-id="<?php echo $bidding_id ?>">
										<?php echo __( 'Cancel', ET_DOMAIN ) ?>
                                    </a>

								<?php } else {
									fre_button_bid( $project->ID, $optionsProject );
								}
							} else if ( ( ( fre_share_role() || $user_role == EMPLOYER ) || current_user_can( 'manage_options' ) ) && $user_ID == $project->post_author ) {
								echo '<a class="fre-action-btn fre-submit-btn btn-right project-action" data-action="archive" data-project-id="' . $project->ID . '">' . __( 'Archive', ET_DOMAIN ) . '</a>';
							} else {
								echo '<a href="' . et_get_page_link( 'submit-project' ) . '" class="fre-submit-btn btn-right">' . __( 'Post Project Like This', ET_DOMAIN ) . '</a>';
							}
						} else if ( $project_status == 'disputing' || $project_status == 'disputed' ) {
							$bid_accepted_author = get_post_field( 'post_author', $bid_accepted );
							if ( (int) $project->post_author == $user_ID || $bid_accepted_author == $user_ID || current_user_can( 'manage_options' ) ) {
								echo '<a class="fre-submit-btn btn-right" href="' . add_query_arg( [ 'dispute' => 1 ], $project_link ) . '">' . __( 'Dispute Page', ET_DOMAIN ) . '</a>';
							}
						} else if ( $project_status == 'close' ) {
							$bid_accepted_author = get_post_field( 'post_author', $bid_accepted );
							if ( (int) $project->post_author == $user_ID || $bid_accepted_author == $user_ID ) {
								echo '<a class="fre-submit-btn btn-right" href="' . add_query_arg( [ 'workspace' => 1 ], $project_link ) . '">' . __( 'Workspace', ET_DOMAIN ) . '</a>';

								// for button Contact
								$bid_query = new WP_Query( [
									'post_type'      => BID,
									'post_parent'    => get_the_ID(),
									'post_status'    => [
										'accept',
									],
									'posts_per_page' => - 1,
								] );
								if ( $bid_query->have_posts() ) {
									do_action( 'ae_bid_item_template', $bid_query->posts[0], $project );
								}
								// for button Contact
							}
						} else if ( $project_status == 'complete' ) {
							$bid_accepted_author = get_post_field( 'post_author', $bid_accepted );
							if ( (int) $project->post_author == $user_ID || $bid_accepted_author == $user_ID ) {
								echo '<a class="fre-submit-btn btn-right" href="' . add_query_arg( [ 'workspace' => 1 ], $project_link ) . '">' . __( 'Workspace', ET_DOMAIN ) . '</a>';
							} else if ( current_user_can( 'manage_options' ) && ae_get_option( 'use_escrow' ) ) {
								$bid_id_accepted = get_post_meta( $post->ID, 'accepted', true );
								$order           = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
								$order_status    = get_post_field( 'post_status', $order );
								$commission      = get_post_meta( $bid_id_accepted, 'commission_fee', true );
								if ( $commission ) {
									if ( $order_status != 'finish' ) {
										echo '<a class="fre-submit-btn btn-right manual-transfer" data-project-id="' . $project->ID . '">' . __( "Transfer Money", ET_DOMAIN ) . '</a>';
									} else {
										if ( ae_get_option( 'manual_transfer', false ) ) {
											echo '<span class="fre-money-transfered">';
											_e( "Already transfered", ET_DOMAIN );
											echo '</span>';
										}
									}
								}
							}
						} else if ( $project_status == 'pending' ) {
							if ( ( fre_share_role() || $user_role == EMPLOYER ) && $user_ID == $project->post_author ) {
								echo '<a class="fre-action-btn btn-right fre-submit-btn" href="' . et_get_page_link( 'edit-project', [ 'id' => $project->ID ] ) . '">' . __( 'Edit', ET_DOMAIN ) . '</a>';
							} else if ( current_user_can( 'manage_options' ) ) {
								echo '<a class="fre-submit-btn btn-right project-action" data-action="approve" data-project-id="' . $project->ID . '">' . __( 'Approve', ET_DOMAIN ) . '</a>';
								echo '<a class="fre-cancel-btn btn-right project-action" data-action="reject" data-project-id="' . $project->ID . '">' . __( 'Reject', ET_DOMAIN ) . '</a>';
							}
						} else if ( $project_status == 'reject' ) {
							if ( ( fre_share_role() || $user_role == EMPLOYER ) && $user_ID == $project->post_author ) {
								echo '<a class="fre-action-btn fre-submit-btn btn-right" href="' . et_get_page_link( 'edit-project', [ 'id' => $project->ID ] ) . '">' . __( 'Edit', ET_DOMAIN ) . '</a>';
							}
						} else if ( $project_status == 'draft' ) {
							if ( ( fre_share_role() || $user_role == EMPLOYER ) && $user_ID == $project->post_author ) {
								echo '<a class="fre-action-btn fre-submit-btn btn-right" href="' . et_get_page_link( 'submit-project', [ 'id' => $project->ID ] ) . '">' . __( 'Edit', ET_DOMAIN ) . '</a>';
								echo '<a class="fre-action-btn fre-cancel-btn btn-right project-action" data-action="delete" data-project-id="' . $project->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
							} else if ( current_user_can( 'manage_options' ) ) {
								echo '<a class="fre-action-btn fre-cancel-btn btn-right project-action" data-action="delete" data-project-id="' . $project->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
							}
						} else if ( $project_status == 'archive' ) {
							if ( ( fre_share_role() || $user_role == EMPLOYER ) && $user_ID == $project->post_author ) {
								echo '<a class="fre-action-btn fre-submit-btn btn-right" href="' . et_get_page_link( 'submit-project', [ 'id' => $project->ID ] ) . '">' . __( 'Renew', ET_DOMAIN ) . '</a>';
								echo '<a class="fre-action-btn fre-cancel-btn btn-right project-action" data-action="delete" data-project-id="' . $project->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
							} else if ( current_user_can( 'manage_options' ) ) {
								echo '<a class="fre-action-btn fre-cancel-btn btn-right project-action" data-action="delete" data-project-id="' . $project->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
							}
						}
					} else {
						if ( $project_status == 'publish' ) {
							echo '<a class="fre-submit-btn btn-right" href="' . et_get_page_link( 'login', [ 'ae_redirect_url' => $project->permalink ] ) . '">' . __( 'Bid', ET_DOMAIN ) . '</a>';
						}
					}
					?>
					<?php if ( ( $author_id == $user_ID ) && $user_status && ( $project_status == 'publish' || $project_status == 'draft' || $project_status == 'pending' ) ) { ?>
                        <a class="btn-right fre-cancel-btn"
                           href="<?php echo bloginfo( 'url' ); ?>/options-project/?id=<?php echo $project->ID; ?>"
                           target="_blank"><?php _e( "Add Options", ET_DOMAIN ); ?></a>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>