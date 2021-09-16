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
			if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article>
                    <div class="block-posts" id="post-control">
                        <h1 class="title-blog">
							<?php the_title() ?>
                        </h1>
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

						<?php wpp_get_template_part( 'wpp/templates/blog/blog-sidebar', [
							'post'     => $post,
							'category' => $category
						] ); ?>

                    </div>
                </article>
				<?php comments_template();
			endwhile;
			endif;

			wpp_get_template_part( 'wpp/templates/universal/recent_posts', [
				'post'     => $post,
				'category' => $category
			] );

			wpp_get_template_part( 'wpp/templates/universal/suscribe_form', [
				'id' => 3
			] );
			?>
        </div>
    </div>
<?php get_footer();