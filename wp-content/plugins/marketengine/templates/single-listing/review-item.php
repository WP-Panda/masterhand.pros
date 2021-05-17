<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<li itemprop="review" itemscope itemtype="http://schema.org/Review" class="me-media">	
	<div class="">
		<a href="<?php echo get_author_posts_url($comment->user_id); ?>" class="avatar-comment pull-left">
			<?php echo marketengine_get_avatar($comment->user_id); ?>
		</a>
		<div class="me-media-body">
			<div class="me-media-heading">
				<h4 itemprop="author" class="me-media-heading"><?php echo esc_html( get_the_author_meta( 'display_name', $comment->user_id ) );  ?></h4>
				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="me-rating">
					<div class="result-rating" data-score="<?php echo get_comment_meta( $comment->comment_ID, '_me_rating_score', true ); ?>"></div>
					<div style="display:none" >
						<meta itemprop="worstRating" content = "1">
				      	<span itemprop="ratingValue"><?php echo get_comment_meta( $comment->comment_ID, '_me_rating_score', true ); ?></span>/
				      	<span itemprop="bestRating">5</span><?php _e("stars", "enginethemes"); ?>
				    </div>
				</div> 
				<span itemprop="datePublished" content="2011-03-25" class="pull-right"><?php comment_date(); ?></span>
			</div>
			<div itemprop="description" class="me-comment-text">
				<?php comment_text(); ?>
			</div>
		</div>
	</div>