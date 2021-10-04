<?php
/**
 * The template for displaying a bid info item,
 * this template is used to display bid info in a project details,
 * and called at template/list-bids.php
 */
global $wp_query, $ae_post_factory, $post, $user_ID;

$project_object = $ae_post_factory->get( PROJECT );
$project        = $project_object->current_post;
$post_object    = $ae_post_factory->get( BID );
$convert        = $post_object->convert( $post );
$project_status = $project->post_status;
$user_role      = ae_user_role( $user_ID );

$freelancer_status = get_post_meta( get_user_meta( $post->post_author, "user_profile_id", true ), 'pro_status', true );

$userCanIsAdmin        = current_user_can( 'manage_options' );
$bidCanPrivate         = getIsPublishProperty( 'private_bid' );
$show_bid_info         = ae_get_option( 'hide_bid_info', false ) ? false : true;
$user_can_see_bid_info = can_see_bid_info( $convert, $project, $userCanIsAdmin );
$profile_id            = get_user_meta( $convert->post_author, 'user_profile_id', true );

if ( $show_bid_info && ! $user_can_see_bid_info && $user_ID > 0 ) {
	//	$show_bid_info = getValueByProperty(get_user_pro_status($convert->post_author), 'private_bid')? false : true;
	$user_can_see_bid_info = $bidCanPrivate ? ( ( (int) $convert->bid_private ) ? false : true ) : true;
}
$can_watch_text = false;
if ( $freelancer_status != 1 ) {
	if ( $project->post_author == $user_ID || $post->post_author == $user_ID || $userCanIsAdmin ) {
		$can_watch_text = true;
	}
} else {
	$can_watch_text = $user_can_see_bid_info;
}

$conversation_data = ae_private_message_created_a_conversation( [
	'bid_id'     => $post->ID,
	'project_id' => $project->ID,
	'author'     => $post->post_author
] );


$conversation_id     = $conversation_data['conversation_id'] ?? 0;
$conversation_status = get_post_meta( $conversation_id, 'conversation_status', true );

$conversation_exists = $conversation_data['success'] === false ? true : false;
if ( $user_role != 'employer' && $post->post_author != get_current_user_id() ) {
	$conversation_exists = false;
}

$conversation_has_read = get_post_meta( $conversation_id, "{$user_role}_has_read", true );

$final_bid_already_asked = get_post_meta( $post->ID, 'final_bid_asked', true );
?>

<div class="row freelancer-bidding-item <? if ( $user_ID == $convert->post_author ) { ?>user-bid<? } ?>"
     style="
     <? //if ($final_bid_already_asked) { echo "background: #b6fff9"; } ?>
     <?php if ( ! empty( $convert->bid_background_color ) ) {
	     echo "background: #{$convert->bid_background_color}";
     } ?>
             ">
    <div class="col-md-10 col-sm-9">
        <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="fre-freelancer-wrap col-free-bidding">
                    <a class="free-bidding-avatar" href="<?php echo get_author_posts_url( $convert->post_author ); ?>">
						<?php echo $convert->et_avatar; ?>
                    </a>

                    <div class="name">
                        <a href="<?php echo get_author_posts_url( $convert->post_author ); ?>"><?php echo $convert->profile_display; ?></a>
						<?php if ( ! empty( $convert->str_pro_account ) ) { ?>
                            <span class="status">
                            <?php _e( 'PRO', ET_DOMAIN ); ?>
                        </span>
						<?php } ?>
						<?php echo $convert->str_status; ?>

						<?php if ( $conversation_exists ) { ?>
                            <a class="fre-notification__link"
                               href="/private-message/?pr_msg_c_id=<?= $conversation_id ?>">
                                <img src="<?php echo get_template_directory_uri() ?>/img/mail.svg"
                                     class="fre-notification__mail-icon <?php if ( ! $conversation_has_read ) { ?>fre-notification__mail-icon--unread<? } ?>">
								<?php if ( ! $conversation_has_read ) { ?><span
                                        class="fre-notification__circle-icon"></span><?php } ?>
                            </a>
						<?php } ?>
                    </div>

                    <p><?php echo $convert->et_professional_title ?></p>
                    <p><?php echo $convert->str_location; ?></p>

					<?php $cur_terms0 = get_the_terms( $profile_id, 'profile_category' );
					if ( is_array( $cur_terms0 ) ) {
						?>
                        <div class="free-category">
							<?php $cur_terms = array_slice( $cur_terms0, 0, 3 );
							foreach ( $cur_terms as $cur_term ) {
								$names .= $cur_term->name . ', ';
							}
							echo mb_substr( $names, 0, - 2 ); ?>
                        </div>
					<?php } ?>

                    <div class="hidden-xs client-bid-btns col-content-bid-<?php echo $convert->ID ?>">
						<?php if ( $user_ID == $project->post_author ) {
							if ( $convert->flag == 1 ) {
								echo '<a data-bid-type="' . $convert->bid_type . '" id="' . get_the_ID() . '" class="fre-submit-btn btn-left btn-select-type-accept">' . __( 'Accept Bid', ET_DOMAIN ) . '</a>';
							}

							if ( ( $convert->bid_type == 'not_final' && ! $final_bid_already_asked ) && $convert->flag != 2 ) {
								echo '<a data-bid-id="' . get_the_ID() . '" class="fre-submit-btn btn-left btn-select-type-get-final-bid">' . __( 'Get Final Bid', ET_DOMAIN ) . '</a>';
							}

							if ( in_array( $project_status, [ 'publish' ] ) ) {
								do_action( 'ae_bid_item_template', $convert, $project );
							}
						}

						// if this freelancer's bid & user asked him about final bid
						// show dialog button for freelancer
						if ( $user_ID == $post->post_author ) {

							?>
                            <a class="fre-action-btn fre-submit-btn btn-right bid-action" data-action="edit"
                               data-bid-id="<?php echo $post->ID ?>">
								<?php echo __( 'Edit', ET_DOMAIN ) ?>
                            </a>
							<?php
							if ( $conversation_exists ) {
								do_action( 'ae_bid_item_template', $convert, $project );
							}
						} ?>
                    </div>

                </div>
                <div class="fre-bid-works fre-page-section">
					<? if ( $can_watch_text ) {
						echo $post->post_content;
						$attachments = $wpdb->get_results( "SELECT guid FROM {$wpdb->prefix}posts WHERE post_parent = {$post->ID} AND post_type='attachment'" );
						if ( ! empty( $attachments ) ) {
							?>
                            <br>
                            <p><? _e( 'Work examples' ); ?></p>
                            <ul class="portfolio-thumbs-list row image">
								<? foreach ( $attachments as $attachment ) { ?>
                                    <li class="col-sm-3 col-xs-4 item">
                                        <div class="portfolio-thumbs-wrap">
                                            <img src="<?= $attachment->guid; ?>">
                                        </div>
                                    </li>
								<? } ?>
                            </ul>
						<? }
					} ?>
                </div>
				<?php
				if ( $convert->flag == 2 ) {
					echo '<div class="free-ribbon hidden-xs"><span class="ribbon"><i class="fa fa-trophy"></i></span></div>';
				}
				?>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
				<?php if ( $can_watch_text ) { ?>
                    <div class="col-free-reputation">
                        <div class="free-rating-new"><?php _e( 'Rating: ', ET_DOMAIN ); ?>
                            +<?= wpp_get_user_rating(  $convert->post_author ); ?>
                        </div>

                        <div class="free-rating_wp">
                            <div class="free-rating"><? HTML_review_rating_user( $post->post_author ); ?></div>
                        </div>

						<?php printf( __( '<p>%s year(s) experience</p>', ET_DOMAIN ), $convert->experience );
						printf( __( '<p>%s project(s) worked</p>', ET_DOMAIN ), $convert->total_projects_worked ); ?>
                    </div>
				<?php } else { ?>
                    <span class="msg-secret-bid"><?php _e( 'Only project owner can view this information.', ET_DOMAIN ); ?></span>
				<?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-3 col-xs-12">
        <div class="col-free-bid">
			<?php if ( $user_can_see_bid_info ) { ?>
                <p class="hidden-lg hidden-md hidden-sm"><?php _e( 'Bid', ET_DOMAIN ); ?></p>
                <p><b><?php echo $convert->bid_budget_text; ?></b></p>
                <p><?php echo $convert->bid_time_text; ?></p>
                <p><?php echo $convert->bid_type == 'not_final' ? _e( 'Preliminary Quote', ET_DOMAIN ) : _e( 'Final Bid', ET_DOMAIN ); ?></p>
			<?php } ?>
        </div>
		<?php
		if ( $convert->flag == 2 ) {
			echo '<div class="free-ribbon visible-xs"><span class="ribbon"><i class="fa fa-trophy"></i></span></div>';
		}
		?>
        <div class="visible-xs client-bid-btns bid-btns col-content-bid-<?php echo $convert->ID ?>">
			<?php if ( $user_ID == $project->post_author ) {
				if ( $convert->flag == 1 ) {
					echo '<a data-bid-type="' . $convert->bid_type . '" id="' . get_the_ID() . '" class="fre-submit-btn btn-left btn-select-type-accept">' . __( 'Accept Bid', ET_DOMAIN ) . '</a>';
				}

				if ( ( $convert->bid_type == 'not_final' && ! $final_bid_already_asked ) && $convert->flag != 2 ) {
					echo '<a data-bid-id="' . get_the_ID() . '" class="fre-submit-btn btn-left btn-select-type-get-final-bid">' . __( 'Get Final Bid', ET_DOMAIN ) . '</a>';
				}

				if ( in_array( $project_status, [ 'publish' ] ) ) {
					do_action( 'ae_bid_item_template', $convert, $project );
				}
			} ?>
        </div>
    </div>
</div>