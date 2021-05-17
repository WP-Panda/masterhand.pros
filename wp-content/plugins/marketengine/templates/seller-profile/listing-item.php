<?php
/**
 * 	The Template for displaying listing information of seller
 * 	This template can be overridden by copying it to yourtheme/marketengine/seller-profile/listing-item.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$listing = marketengine_get_listing( get_the_ID() );
$listing_type = $listing->get_listing_type();
$review_score = $listing->get_review_score();
?>

<li class="me-item-listing">
	<div class="me-item-listing-wrap">

		<a href="<?php the_permalink(); ?>" class="me-item-img">
			<?php has_post_thumbnail() ? the_post_thumbnail( array(260, 190) ) : _e('<i class="icon-me-image"></i>', 'enginethemes') ; ?>
		</a>

		<div class="me-item-content">
			<a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
			<p><?php echo get_the_excerpt(); ?></p>
		</div>

	<?php if( $listing_type == 'contact' ) : ?>
		<span class="me-contact"><?php _e('Contact', 'enginethemes'); ?></span>
	<?php else : ?>
		<div class="me-rating">
			<div class="result-rating" data-score="<?php echo $review_score; ?>"></div>
		</div>
		<span class="me-price"><?php echo marketengine_price_html( $listing->get_price() ); ?></span>
	<?php endif; ?>

	</div>
</li>