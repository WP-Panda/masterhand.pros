<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if(!$listing->allow_rating()) {
	return;
}

$review_count = $listing->get_review_count();
$review_score = $listing->get_review_score();
$comments = get_comments(array('type' => 'review', 'post_id' => $listing->ID, 'status' => 'approve'));

$review_count_details = $listing->get_review_count_details();

?>

<?php do_action('marketengine_before_single_listing_rating', $listing); ?>

<div class="me-comments me-desc-box">
	<div class="marketengine-comments">
		<h3 class="me-title-comment"><?php printf(_n("Review (%d)", "Reviews (%d)", $review_count,"enginethemes"),$review_count ); ?></h3>

		<?php if ( $review_count ) : ?>

			<div class="me-row">
				<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"  class="me-col-md-3">
					<div class="me-count-rating">
						<meta itemprop="worstRating" content = "1">
						<meta itemprop="bestRating" content = "5">
						<span itemprop="ratingValue" class="me-count"><?php echo $review_score; ?></span>
						<div class="me-rating">
							<div class="result-rating" data-score="<?php echo $review_score; ?>"></div>
						</div>
						<span class="me-base-review">
							<?php printf(_n('Based on <b itemprop="reviewCount">%d</b> review', 'Based on <span itemprop="reviewCount">%d</span> reviews', $review_count,"enginethemes"),$review_count ); ?>
						</span>
					</div>
				</div>
				<div class="me-col-md-9">
					<div class="me-count-author">
						<div class="me-count-author">
							<?php for ($i=5; $i > 0 ; $i--) : ?>

							<?php
								$percent = round(($review_count_details[$i. '_star']/$review_count)*100);
							?>

							<div class="me-rating-author">
								<span class="me-star-quantity"><i class="icon-me-star"></i><?php echo $i; ?></span>
								<div class="me-line-bg">
									<div class="me-line-range" style="width: <?php echo $percent; ?>%;"></div>
								</div>
								<span class="me-star-per"><?php echo $percent; ?>%</span>
							</div>
							<?php endfor; ?>
						</div>
					</div>
				</div>
			</div>

			<ul class="me-comment-list">
				<?php wp_list_comments( array('callback' => 'marketengine_comments'), $comments ); ?>
			</ul>
            <?php if ( get_comment_pages_count($comments) > 1 ) : ?>

				<div class="comment-pagination">
                	<a href="#" class="read-more-review" id="read-more-review" data-post-id="<?php the_ID(); ?>" data-page="<?php echo get_comment_pages_count($comments); ?>" data-loading-text="<?php _e("Loading ...", "enginethemes"); ?>">
                		<?php _e("Read more", "enginethemes") ?>
                	</a>
                </div>

            <?php endif; // Check for comment navigation. ?>

		<?php else : ?>

			<p class="me-noreviews"><?php _e( 'There are no reviews yet.', 'enginethemes' ); ?></p>

		<?php endif; ?>

	</div>

</div>

<?php do_action('marketengine_after_single_listing_rating', $listing); ?>