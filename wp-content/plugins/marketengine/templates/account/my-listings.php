<?php
/**
 *	The Template for displaying listings posted by the current user.
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/my-listings.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$paged = get_query_var('paged') ? absint( get_query_var('paged') ) : 1;
$listing_status = isset($_GET['status']) ? esc_sql( $_GET['status'] ) : 'publish';
$args = array(
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'listing',
	'author'	   	   => get_current_user_id(),
	'post_status'      => $listing_status,
	'paged'			   => $paged,
);

$query = new WP_Query( $args );

?>
	<div class="marketengine-content marketengine-snap-column listing-post">

		<?php if( $query->have_posts() ): ?>

		<div class="marketengine-filter">
			<div class="marketengine-filter-listing pull-right">
				<div class="filter-listing-status">
					<select class="me-chosen-select" name="" id="" onchange="window.location.href='<?php echo marketengine_get_auth_url('listings'); ?>' + this.value;">
						<option value="<?php echo '?status=any'; ?>" <?php selected( $listing_status, 'any'); ?>><?php _e('All status', 'enginethemes'); ?></option>
					<?php
						$filter_options = marketengine_listings_status_list();
						foreach( $filter_options as $key => $label) :
					?>
						<option value="<?php echo '?status=' . $key; ?>" <?php selected( $listing_status, $key); ?>><?php echo $label; ?></option>
					<?php
						endforeach;
					?>
					</select>

				</div>
			</div>
		</div>

		<div class="marketengine-listing-post">
			<ul class="me-listing-post me-row">
			<?php
				while($query->have_posts()) : $query->the_post();
					$post_obj = get_post(get_the_ID());
					$listing = new ME_Listing($post_obj);
					$listing_type = $listing->get_listing_type();
					$listing_status = get_post_status_object(get_post_status());
			?>
				<li class="me-item-post me-col-md-3 me-col-sm-6">
					<div class="me-item-wrap">
						<a href="<?php the_permalink(); ?>" class="me-item-img">

							<?php if( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail' ); ?>
							<?php else : ?>
							<i class="icon-me-image"></i>
							<?php endif; ?>

							<span><?php echo __('VIEW DETAILS', 'enginethemes'); ?></span>
							<div class="me-label-<?php echo str_replace('me-', '', $listing_status->name); ?>">
								<span><?php echo ucfirst($listing_status->label); ?></span>
							</div>
							<?php //echo '<i class="icon-me-image"></i>' ?>
						</a>
						<div class="me-item-content">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="me-item-price">
							<?php
							if( $listing_type ) :
								if( 'purchasion' !== $listing_type ) :
							?>
								<span class="me-price pull-left"><b><?php echo __('Contact', 'enginethemes'); ?></b></span>
							<?php else : ?>
							<?php
							$purchasion = new ME_Listing_Purchasion($post_obj);
							$price = $purchasion->get_price();
							$pricing_unit = $purchasion->get_pricing_unit();
							?>
								<span class="me-price pull-left">
									<?php echo marketengine_price_html( $price, $pricing_unit ) ?>
								</span>

								<div class="me-rating pull-right">
									<div class="result-rating" data-score="<?php echo $purchasion->get_review_score(); ?>"></div>
								</div>

							<?php
								endif;
							endif;
							?>
							</div>
							<div class="me-item-action">
								<form method="post">

								<?php marketengine_get_template('account/my-listing-action', array( 'listing_status' => $listing_status, 'listing_id' => get_the_ID()) ); ?>
								<?php wp_nonce_field( 'marketengine_update_listing_status' ); ?>
									<input type="hidden" id="listing_id" value="<?php the_ID(); ?>" />
									<input type="hidden" id="redirect_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />

								</form>
							</div>
						</div>
					</div>
				</li>
			<?php
				endwhile;
				wp_reset_postdata();
			?>
			</ul>
			<div class="me-paginations">
				<?php marketengine_paginate_link ($query); ?>
			</div>
		</div>

	<!--// marketengine-content -->
<?php
elseif(isset($_GET['status'])) : ?>
		<div class="marketengine-filter">
			<div class="marketengine-filter-listing pull-right">
				<div class="filter-listing-status">
					<select class="me-chosen-select" name="" id="" onchange="window.location.href=this.value;">
						<option value="<?php echo '?status=any'; ?>" <?php selected( $listing_status, 'any'); ?>><?php _e('All status', 'enginethemes'); ?></option>
					<?php
						$filter_options = marketengine_listings_status_list();
						foreach( $filter_options as $key => $label) :
					?>
						<option value="<?php echo '?status=' . $key; ?>" <?php selected( $listing_status, $key); ?>><?php echo $label; ?></option>
					<?php
						endforeach;
					?>
					</select>

				</div>
			</div>
		</div>
<?php
	echo '<div class="me-listing-filter-none"><p>';
	_e('Sorry! There are no listings matching your filter.');
	echo '</p></div>';
else:
	marketengine_get_template('account/my-listing-none');
endif;
?>
</div>