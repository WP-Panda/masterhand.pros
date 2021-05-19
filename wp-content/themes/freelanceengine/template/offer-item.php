<?php
	global $wp_query, $ae_post_factory, $post;
	$post_object = $ae_post_factory->get( ADVERT );
	$current     = $post_object->current_post;
?>

<li class="offer-item">
    <div class="project-content fre-freelancer-wrap">
        <a class="secondary-color"
           href="<?php echo get_permalink(); ?>"><?php echo $current->post_title; ?></a>
        <div class="project-list-info">
            <span class="fre-location"><?php echo $current->str_location; ?></span>
        </div>
        <div class="project-list-desc">
            <p>
				<?php if ( strlen( $current->post_content ) > 50 ) {
					echo substr( $current->post_content, 0, 49 ) . '...';
				} else {
					echo $current->post_content;
				}
				?>
            </p>
        </div>
        <div class="">
			<? _e( 'Author' ); ?>:
            <a href="<?php echo get_author_posts_url( $current->post_author ); ?>" class="offer_author">
				<?php echo get_the_author_meta( 'display_name', $current->post_author ); ?>
            </a>
        </div>
    </div>
</li>