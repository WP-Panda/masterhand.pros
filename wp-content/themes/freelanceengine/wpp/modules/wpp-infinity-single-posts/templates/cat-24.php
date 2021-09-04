<?php
/**
 * шаблон для вывода остального
 */

// Молчание - золото
defined( 'ABSPATH' ) || exit;

extract( $args );
?>
<div class="wpp-permalink" data-link="<?php echo get_the_permalink( $post->ID ); ?>">
    <h1 class="wpp-permalink"><?php echo get_the_title( $post->ID ); ?></h1>
    <div class="content_project_text">
		<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $post->ID ) ); ?>
    </div>
</div>