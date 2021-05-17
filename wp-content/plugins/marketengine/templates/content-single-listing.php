<?php
/**
 * 	The Template for displaying content of listing detail.
 * 	This template can be overridden by copying it to yourtheme/marketengine/content-single-listing.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>
<div id="marketengine-page">
	<div class="marketengine me-container">
		<div itemscope itemtype="http://schema.org/Product" class="marketengine-listing-detail">
			<div class="me-row">
				<div class="me-col-md-12">
					<?php marketengine_get_template('single-listing/title');?>
					<?php marketengine_get_template('single-listing/statistic', array('listing' => $listing));?>
				</div>
			</div>
			<div class="me-row">
				<div class="me-col-md-9">

					<?php do_action('marketengine_before_single_listing_information'); ?>

					<div class="marketengine-content-detail">

						<?php marketengine_get_template('single-listing/gallery', array('listing' => $listing));?>
						
						<?php marketengine_get_template('single-listing/notices'); ?>
							
						<?php marketengine_get_template('single-listing/control', array('listing' => $listing) ); ?>
							
						<?php marketengine_get_template('single-listing/category', array('listing' => $listing));?>
						
						<?php marketengine_get_template('single-listing/description', array('listing' => $listing)); ?>

						<?php marketengine_get_template('single-listing/rating', array('listing' => $listing));?>

					</div>
					
					<div class="me-visible-xs">
						<?php if( $listing->post_author != get_current_user_id() ) {

							marketengine_get_template('user-info', array('author_id' => $listing->post_author));

						} ?>
					</div>
					
					<?php do_action('marketengine_after_single_listing_information'); ?>

				</div>

				<div class="me-col-md-3 me-hidden-sm me-hidden-xs">

					<?php do_action('marketengine_before_single_listing_sidebar'); ?>

					<div class="marketengine-sidebar-detail">

						<?php marketengine_get_template('single-listing/notices'); ?>

						<?php marketengine_get_template('single-listing/control', array('listing' => $listing) ); ?>

						<?php marketengine_get_template('single-listing/category');?>
						<?php
						if( $listing->post_author != get_current_user_id() ) :
							marketengine_get_template('user-info', array('author_id' => $listing->post_author));
						endif;
						?>

					</div>

					<?php do_action('marketengine_after_single_listing_sidebar'); ?>

				</div>
			</div>
		</div>
	</div>
</div>
