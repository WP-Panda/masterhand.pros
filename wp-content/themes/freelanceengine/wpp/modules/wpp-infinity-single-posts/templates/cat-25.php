<?php
/**
 * шаблон для вывода проектов
 */

// Молчание - золото
defined( 'ABSPATH' ) || exit;

extract( $args );
?>
<div class="wpp-permalink" data-link="<?php echo get_the_permalink( $post->ID ); ?>">
    <h1><?php echo get_the_title( $post->ID ); ?></h1>
    <div class="wrap_content">
        <div class="content_project_image">
			<?php $tb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), "medium" ); ?>
            <img src="<?php echo $tb_url[0] ?>" alt="alt">
        </div>
        <div class="content_project_description">
			<?php echo get_field( 'description_short', $post->ID ); ?>
        </div>
    </div>
    <div class="content_project_text">
		<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $post->ID ) ); ?>
    </div>
    <div class="gallery">
        <div class="animated-thumbnails-gallery">

			<?php if ( have_rows( '', $post->ID ) ): ?>


				<?php while ( have_rows( '', $post->ID ) ): the_row();
					$image       = get_sub_field( 'img' );
					$picture     = $image['sizes']['large'];
					$picture_min = $image['sizes']['thumbnail'];
					?>
                    <a href="<?php echo $picture; ?>">
                        <img src="<?php echo $picture_min; ?>" alt=""/>
                    </a>

				<?php endwhile; ?>
			<?php endif; ?>

        </div>
    </div>
</div>
