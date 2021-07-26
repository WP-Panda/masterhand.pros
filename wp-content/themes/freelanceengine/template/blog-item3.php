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
        <div class="row">
            <div class="fre-blog-item_t col-sm-9 col-xs-12">
                <a href="<?php the_permalink(); ?>"><?php echo mb_substr( strip_tags( get_the_title() ), 0, 152 ); ?></a>
            </div>
            <div class="fre-blog-item_link col-sm-3 hidden-xs">
                <a target="_blank" rel="nofollow"
                   href="<?php the_field( 'post_link' ); ?>"><?php the_field( 'post_link' ); ?></a>
            </div>
        </div>
        <div class="fre-blog-item_exp hidden-xs"><?php echo mb_substr( strip_tags( get_the_content() ), 0, 152 ); ?></div>
        <div class="fre-blog-item_exp hidden-sm"><?php echo mb_substr( strip_tags( get_the_content() ), 0, 90 ); ?></div>
        <div class="fre-blog-item_link hidden-sm">
            <a target="_blank" rel="nofollow"
               href="<?php the_field( 'post_link' ); ?>"><?php the_field( 'post_link' ); ?></a>
        </div>
    </div>
</div>