<?php
/**
 * The template for displaying order related listing slider
 *
 * This template can be overridden by copying it to yourtheme/marketengine/purchases/listing-slider.php.
 *
 * @package     MarketEngine/Templates
 * @since 		1.0.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$args = apply_filters( 'marketengine_related_listing', $args );

if(!empty($listings)) :
?>

<div class="marketengine-related-wrap">
	<?php ?>
	<h2><?php _e('You may like these listings', 'enginethemes'); ?></h2>
	<div id="me-related-slider" class="me-related-slider">

		<?php
			foreach( $listings as $listing ) :
				$listing = marketengine_get_listing($listing);
				$listing_type = $listing->get_listing_type();
		?>
			<div class="me-item-post">

				<div class="me-item-wrap">

					<a href="<?php echo $listing->get_permalink(); ?>" class="me-item-img">
						<?php echo $listing->get_listing_thumbnail() ? $listing->get_listing_thumbnail() : '<i class="icon-me-image"></i>'; ?>
						<span><?php _e('VIEW DETAILS', 'enginethemes'); ?></span>
					</a>

					<div class="me-item-content">
						<h2><a href="<?php echo $listing->get_permalink() ?>"><?php echo $listing->get_title(); ?></a></h2>

						<?php
							if('purchasion' == $listing_type) :
								marketengine_get_template('loop/purchasion', array('listing' => $listing));
							else :
								marketengine_get_template('loop/contact', array('listing' => $listing));
							endif;
						 ?>

						<div class="me-item-author">
						<?php
							$seller = get_userdata( $listing->get_author() );
						?>
							<a href="#"><?php printf( __('<i>by</i>%s'), $seller->display_name ); ?></a>
						</div>
					</div>

				</div>

			</div>

		<?php endforeach; ?>

	</div>
</div>

<?php endif; ?>

