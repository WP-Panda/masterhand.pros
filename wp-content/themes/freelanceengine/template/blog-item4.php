<?php
	/**
	 * The template for displaying post details in a loop
	 *
	 * @since    1.0
	 * @package  FreelanceEngine
	 * @category Template
	 */
?>
<div class="col-sm-6 col-xs-12">
    <div class="fre-blog-item">
        <div class="col-sm-3 col-xs-4">
            <a class="fre-blog-item_img" style="background:url(<?php if ( has_post_thumbnail() ) {
				the_post_thumbnail_url();
			} else {
				echo get_template_directory_uri() . '/img/noimg.png';
			} ?>) top center no-repeat;" href="<?php the_permalink(); ?>"></a>
        </div>
        <div class="col-sm-9 col-xs-8">
            <div class="fre-blog-item_t"><a
                        href="<?php the_permalink(); ?>"><?php echo mb_substr( strip_tags( get_the_title() ), 0, 100 ); ?></a>
            </div>
            <div class="fre-blog-item_exp hidden-xs"><?php echo mb_substr( trim( strip_tags( get_the_content() ) ), 0, 50 ); ?>
                ...
            </div>
        </div>
    </div>
</div>