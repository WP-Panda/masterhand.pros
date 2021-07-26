<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

extract( $args );

$_posts = get_posts( [
	'post_status'    => [ 'publish' ],
	'posts_per_page' => 5,
	'post__not_in'   => [ $post->ID ],
	'cat'            => [ $category[0]->term_id ]
] );
if ( ! empty( $_posts ) ) : ?>
    <div class="col-sm-4 category hidden-xs post-sidebar" id="right_content">
        <div class="fre-blog-list-sticky">
			<?php
			$post_none_in = [];
			foreach ( $_posts as $post ) :
				setup_postdata( $post );
				wpp_get_template_part( 'wpp/templates/blog/blog-stickynoimg', [ 'post' => $post ] );
				wp_reset_postdata();
			endforeach;
			?>
        </div>
    </div>
<?php endif;