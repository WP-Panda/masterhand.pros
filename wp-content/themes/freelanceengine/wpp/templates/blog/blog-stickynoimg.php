<?php
/**
 * The template for displaying post details in a loop
 *
 * @since    1.0
 * @package  FreelanceEngine
 * @category Template
 */
extract( $args );
$num = wp_is_mobile() ? 110 : 152;
?>
<div class="fre-blog-item">
    <div class="fre-blog-item_t">
        <a href="<?php the_permalink( $post->ID ); ?>" title="">
			<?php echo get_the_title( $post->ID ); ?>
        </a>
    </div>
    <div class="fre-blog-item_exp">
		<?php echo mb_substr( strip_tags( get_the_content( '', '', $post->ID ) ), 0, $num ); ?>
    </div>
</div>