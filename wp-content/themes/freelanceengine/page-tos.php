<?php
/**
 * Template Name: Term of Use
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */

global $post;
get_header();
the_post();
?>

    <div class="container page-container">

        <div class="row block-posts block-page">
			<?php
			if ( is_social_connect_page() ) {
				the_content();
				wp_link_pages( [
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				] );
			} else { ?>
                <div class="col-sm-3 col-xs-12 page-sidebar blog-sidebar" id="right_content">
					<?php get_sidebar( 'page' ); ?>
                    <button class="visible-xs blog-sidebar_more"><?php echo __( 'More information', ET_DOMAIN ); ?>
                        <i class="fa fa-angle-down animated-hover fa-falling"></i></button>
                </div>
                <div class="col-sm-9 col-xs-12 posts-container" id="left_content">
                    <div class="blog-content">
                        <h1><?php the_title(); ?></h1>
						<?php
						the_content();
						wp_link_pages( [
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						] );
						?>

                        <div class="clearfix"></div>
                    </div>
                </div>
			<?php } ?>
        </div>

    </div>
<?php
get_footer();