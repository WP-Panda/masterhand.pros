<?php
/**
 * The template for displaying post details in a loop
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
?>
<div class="fre-blog-item">
       <div class="fre-blog-item_t"><a href="<?php the_permalink();?>"><?php the_title();?></a></div>
       <div class="fre-blog-item_exp hidden-xs"><?php echo mb_substr( strip_tags( get_the_content() ), 0, 152 ); ?></div>
       <div class="fre-blog-item_exp hidden-sm"><?php echo mb_substr( strip_tags( get_the_content() ), 0, 110 ); ?></div>
</div>    
