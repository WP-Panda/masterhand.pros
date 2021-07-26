<?php
$query = new WP_Query( [
	'post_type'     => 'testimonial',
	'showposts'     => 12,
	'post_per_page' => 12,
	'orderby'       => 'date',
	'order'         => 'DESC',
] );
$num   = $query->post_count;
?>

<div class="owl-carousel item">
	<?php global $post;
	$i = 0;
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post(); ?>
            <div class="stories-content">
                <div class="row">
                    <div class="col-sm-2 col-xs-12">
                        <div class="stories-img" style="background:url(<?php if ( has_post_thumbnail( $post ) ) {
							the_post_thumbnail_url( 'thumbnail' );
						} else {
							echo get_template_directory_uri() . '/img/noimg.png';
						} ?>) center no-repeat;"></div>
                    </div>
                    <div class="col-sm-7 col-md-6 col-lg-7 col-xs-12 post-author">
						<?php echo $post->post_title; ?>
						<?php $position = get_post_meta( $post->ID, '_test_category', true );
						if ( $position ) {
							echo '<span>' . $position . '</span>';
						} ?>
                    </div>
                    <div class="col-sm-3 col-md-4 col-lg-3 col-xs-12">
                        <div class="free-rating-new">+ <?php the_field( 'rating_score' ); ?></div>
                        <div class="free-rating rate-it" data-score="<?php the_field( 'rating_stars' ); ?>"></div>
                    </div>
                    <div class="post_txt col-sm-12"><?php echo $post->post_content; ?></div>
                </div>
            </div>
		<?php }
	}
	wp_reset_query(); ?>
</div>