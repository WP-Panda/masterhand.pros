<?php

global $wp_query, $ae_post_factory, $post;
$query_args = [
	'post_type'      => PROJECT,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'is_block'       => 'projects'
];
$obj_name   = new WP_Query( $query_args );
$num        = $obj_name->post_count;
?>
<div class="fre-jobs-list owl-carousel">
	<?php
	$post_object = $ae_post_factory->get( 'project' );
	$i           = 0;
	while ( $obj_name->have_posts() ) {
		$obj_name->the_post();
		$convert    = $post_object->convert( $post );
		$postdata[] = $convert; ?>
        <div class="fre-jobs-item">
            <div class="jobs-date row">
                <div class="col-sm-7"><?php echo get_the_date( 'F d, Y' ); ?></div>
                <div class="col-sm-5"><?php echo fre_price_format( $convert->et_budget ); ?></div>
            </div>
            <div class="jobs-t">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <p><?php echo substr( strip_tags( $convert->post_content ), 0, 180 ); ?></p>
            </div>
            <div class="jobs-v">
                <a href="<?php the_permalink(); ?>"><?php _e( 'View details', ET_DOMAIN ) ?></a>
            </div>
        </div>
	<?php }
	wp_reset_query(); ?>
</div>