<?php
	/**
	 * The main template file
	 *
	 * This is the most generic template file in a WordPress theme and one
	 * of the two required files for a theme (the other being style.css).
	 * It is used to display a page when nothing more specific matches a query,
	 * e.g., it puts together the home page when no home.php file exists.
	 *
	 * @link       http://codex.wordpress.org/Template_Hierarchy
	 *
	 * @package    WordPress
	 * @subpackage FreelanceEngine
	 * @since      FreelanceEngine 1.0
	 */

	$category = get_the_category();
	get_header();
	wp_enqueue_script( 'likesUsers' );
?>

    <div class="fre-page-wrapper">
        <div class="container">

			<?php
				wpp_get_template_part( 'wpp/templates/blog/blog-company-list' );
				blog_breadcrumbs();
			?>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                <div class="block-posts" id="post-control">
                    <h1 class="title-blog"><?php the_title() ?></h1>
					<?php wpp_get_template_part( 'wpp/templates/media/blog-slider', [
						'images'   => wpp_get_post_images(),
						'category' => $category
					] ); ?>
                </div>


                <div class="row">

                    <div class="col-sm-8 col-xs-12">
						<?php
							wpp_get_template_part( 'wpp/templates/blog/blog-author-block', [ 'author' => $post->post_author ] );
							wpp_get_template_part( 'wpp/templates/blog/blog-content' );
							wpp_get_template_part( 'wpp/templates/universal/likes', [ 'post_id' => $post->ID ] );
							wpp_get_template_part( 'wpp/templates/universal/sharing_block' );
						?>
                    </div>


                    <div class="col-sm-4 category hidden-xs post-sidebar" id="right_content">
						<?php

                            $new_query = new WP_Query( [
							'post_status'    => 'publish',
							'posts_per_page' => 1,
							'page' => 1,
							'post__not_in'   => [ $post->ID ],
							'cat'            => [ $category[ 0 ]->cat_id ]
						] );
						wpp_dump($new_query->post_count);
						?>
                        <div class="fre-blog-list-sticky">
							<?php while ( $new_query->have_posts() ) {
								$new_query->the_post();
								get_template_part( 'template/blog', 'stickynoimg' );
							} ?>
                        </div>
						<?php wp_reset_query(); ?>
                    </div>
                </div>
				<?php comments_template();

			endwhile;
			else : endif; ?>


            <div class="fre-blog fre-blog-fst_bl">
				<?php $query = new WP_Query( [
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'offset'         => 5,
					'post__not_in'   => [ $post->ID ],
					'cat'            => $category[ 0 ]->cat_id
				] );
				?>
                <div class="profs-cat_t"><span><?php echo __( 'Related news', ET_DOMAIN ); ?></span></div>
                <div class="fre-blog-list owl-carousel">
					<?php while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part( 'template/blog', 'item' );
					} ?>
                </div>
				<?php wp_reset_query(); ?>
            </div>

            <!-- Mailster subscribtion form -->
            <div class="fre-blog-subscribe-form mailster-subscribe__block">
                <div class='fre-blog-subscribe-form_title'><?php echo __( 'Subscribe', ET_DOMAIN ) ?></div>
                <div class="emaillist">
					<?php echo mailster_form( 3 ); ?>
                </div>
            </div>
        </div>


    </div>

<?php get_footer();