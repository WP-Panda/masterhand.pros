<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="fre-blog fre-blog-fst_bl">
	<?php
	$_posts = get_posts( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'offset'         => 5,
		'post__not_in'   => [ $post->ID ],
		'cat'            => $category[0]->term_id
	] );


	?>
    <div class="profs-cat_t"><span><?php echo __( 'Related news', ET_DOMAIN ); ?></span></div>
    <div class="fre-blog-list owl-carousel">
		<?php foreach ( $_posts as $post ) :
			setup_postdata( $post );
			wpp_get_template_part( 'wpp/templates/blog/blog-item', [ 'post' => $post ] );
			wp_reset_postdata();
		endforeach; ?>
    </div>
</div>
