<?php
/**
 * The template for displaying post details in a loop
 *
 * @since    1.0
 * @package  FreelanceEngine
 * @category Template
 */
?>
<div class="fre-blog-list-item">
    <div class="fre-blog-item">
        <div class="fre-blog-item_img" style="background:url(<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail_url( $post->ID );
		} else {
			echo get_template_directory_uri() . '/img/noimg.png';
		} ?>) center no-repeat;"><a href="<?php the_permalink( $post->ID ); ?>"></a></div>
        <div class="fre-blog-item_t"><a
                    href="<?php the_permalink( $post->ID ); ?>"><?php echo mb_substr( strip_tags( get_the_title( $post->ID ) ), 0, 152 ); ?></a>
        </div>
        <p class="fre-blog-item_date"><?php echo _e( 'Updated', ET_DOMAIN ) . ' ' . get_the_date(); ?></p>
    </div>
</div>