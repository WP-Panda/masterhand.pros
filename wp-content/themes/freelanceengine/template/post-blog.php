<?php
/**
 * The template for displaying post details in a loop
 *
 * @since    1.0
 * @package  FreelanceEngine
 * @category Template
 */

$queried_object = get_queried_object();
$taxonomy       = $queried_object->taxonomy;
$term_id        = $queried_object->term_id;
?>
<div class="blog-wrapper post-item">
    <div class="row">

        <div class="col-sm-4 col-xs-12" style="background:url(<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail_url( 'blogpost' );
		} else {
			echo get_stylesheet_directory_uri() . '/img/noimg.png';
		} ?>) center no-repeat;">
            <a href="<?php the_permalink(); ?>"></a>
        </div>
        <div class="col-sm-8 col-xs-12 pull-right">
            <div class="blog-content">
                <span class="tag">
                    <?php $categories = get_the_category();
                    $col              = count( $categories ) - 1;

                    echo '<a href="' . get_category_link( $categories[ $col ]->term_id ) . '">' . $categories[ $col ]->name . '</a>';
                    ?>
                </span>
                <h6 class="title-blog"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h6>
				<?php
				if ( is_single() ) {
					the_content();
					wp_link_pages( [
						'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					] );
				} else {
					the_excerpt();
					?>
				<?php } ?>
            </div>
        </div>

    </div>
</div>