<?php
	/**
	 * The template for displaying post details in a loop
	 *
	 * @since    1.0
	 * @package  FreelanceEngine
	 * @category Template
	 */
	extract($args);
?>
<div class="fre-blog-item">
    <div class="fre-blog-item_t"><a href="<?php the_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></div>
    <div class="fre-blog-item_exp hidden-xs"><?php echo mb_substr( strip_tags( get_the_content('','',$post->ID) ), 0, 152 ); ?></div>
    <div class="fre-blog-item_exp hidden-sm"><?php echo mb_substr( strip_tags( get_the_content('','',$post->ID) ), 0, 110 ); ?></div>
</div>