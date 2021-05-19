<?php
	/**
	 * The template for displaying post details in a loop
	 *
	 * @since    1.0
	 * @package  FreelanceEngine
	 * @category Template
	 */
?>
<div class="fre-blog-item">
    <div class="fre-blog-item_img" style="background:url(<?php if ( has_post_thumbnail() ) {
		the_post_thumbnail_url();
	} else {
		echo get_template_directory_uri() . '/img/noimg.png';
	} ?>) top center no-repeat;">
        <a href="<?php the_permalink(); ?>"></a>
    </div>
    <div class="fre-blog-item_wp">
        <div class="fre-blog-item_t"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
        <div class="fre-blog-item_exp"><?php echo mb_substr( trim( strip_tags( get_the_content() ) ), 0, 100 ); ?>...
        </div>
    </div>
</div>