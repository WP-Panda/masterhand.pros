<?php
/**
 * Template part for user reviews history block
 * # This template is loaded in page-profile.php , author.php
 */
defined('ABSPATH') || exit;
global $user_data, $user_ID, $ae_post_factory;

$reviewsUser = (int) $user_data->ID;
$objReviews = review_rating_init();
//$objReviews->setLimitOffset(1);
$onlyPublish  = ( $reviewsUser == $user_ID ) ? 0 : 1;
$total        = $objReviews->getCountReviews( $reviewsUser, $onlyPublish );
$list_reviews = $objReviews->getReviewsUser( $reviewsUser, $onlyPublish );
$page_links   = $objReviews->getRwPagination( $total, 1 );
$user_role    = ae_user_role( $user_ID );
//get all revies ids
$list_reviews_ids = array_column( $list_reviews, 'id' );

foreach ( $list_reviews as $key => $review ) {
	$post_object  = $ae_post_factory->get( PROJECT );
	$convert      = $project = $post_object->convert( $review );
	$bid_accepted = $convert->accepted;

	$review_for_freelancer = review_rating_init()->getReviewDoc( $bid_accepted );
	$review_for_employer   = review_rating_init()->getReviewDoc( $review['ID'] );

	if ( $user_role == FREELANCER ) {
		$reply_to_review                       = review_rating_init()->getReviewReply( $review['id'] );
		$list_reviews[ $key ]['reply_comment'] = $reply_to_review['comment'];
		$user_employer                         = new WP_User( $reply_to_review['user_id'] );
		$list_reviews[ $key ]['reply_name']    = $user_employer->display_name;
	}
	if ( $user_role == EMPLOYER ) {
		$reply_to_review_employer              = review_rating_init()->getReviewReply( $review['id'] );
		$list_reviews[ $key ]['reply_comment'] = $reply_to_review_employer['comment'];
		$user_employer                         = new WP_User( $reply_to_review_employer['user_id'] );
		$list_reviews[ $key ]['reply_name']    = $user_employer->display_name;
	}

	$list_reviews[ $key ]['its_reply'] = false;

	$review_created          = strtotime( $review_for_freelancer['created'] );
	$review_created_employer = strtotime( $review_for_employer['created'] );
	$reply_deadline          = strtotime( '+20 day', $review_created );
	$reply_deadline_employer = strtotime( '+20 day', $review_created_employer );
	$today                   = time();

	$list_reviews[ $key ]['reply_to_review'] = '';
	if ( $user_role == EMPLOYER ) {

		$check_key = array_search( $reply_to_review_employer['parent'], $list_reviews_ids );
		if ( $today < $reply_deadline && $check_key === false ) {
			$list_reviews[ $key ]['reply_to_review'] = $review_for_employer['id'];
		}
		if ( ! is_null( $reply_to_review_employer ) && $check_key === false ) {
			$list_reviews[ $key ]['its_reply'] = true;
		} elseif ( $review['additional_data'] === 'is_reply' ) {
			$list_reviews[ $key ]['its_reply'] = true;
		}
	}

	if ( $user_role == FREELANCER ) {
		$bid_id_accepted = get_post_meta( $convert->post_parent, 'accepted', true );
		if ( $bid_id_accepted ) {
			$check_key = array_search( $reply_to_review['parent'], $list_reviews_ids );


			if ( $today < $reply_deadline_employer && $check_key === false ) {
				$list_reviews[ $key ]['reply_to_review'] = $review_for_employer['id'];
			}
			if ( ! is_null( $reply_to_review ) && $check_key === false ) {
				$list_reviews[ $key ]['its_reply'] = true;
			} elseif ( $review['additional_data'] === 'is_reply' ) {
				$list_reviews[ $key ]['its_reply'] = true;
			}
		}
	}
}
?>

<div class="freelancer-project-history">
    <div class="profile-freelance-work">
		<?php
		if ( ! empty( $list_reviews ) ) {
			?>
            <ul class="list-work-history-profile author-project-list" data-user-id="<?php echo $reviewsUser; ?>"><?
			$vars['list_reviews'] = $list_reviews;
			$vars['user_ID']      = $user_ID;
			ReviewsRating\TplRender::getInstance()->display( 'reviewsProfile.tpl', $vars );
			?></ul><?
			wp_enqueue_script( '', '/wp-content/plugins/reviews_rating/js/reviews.js', [], false, true );
			get_template_part( 'template-js/modal-showed-review' );
		} else {
			?>
            <div class="fre-profile-box">
                <div class="profile-no-results"
                     style="padding: 0"><?php _e( 'There are no activities yet.', ET_DOMAIN ); ?></div>
            </div>
		<?php } ?>
		<?php echo $page_links; ?>
    </div>
</div>