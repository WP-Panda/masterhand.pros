<?php
	/**
	 * The search template file
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
	get_header();
?>
    <div class="fre-page-wrapper section-archive-profile">
        <div class="container">
            <div class="fre-page-title">
                <h1><?php printf( __( 'Search Results for: %s', 'twentyfourteen' ), get_search_query() ); ?></h1>
            </div>
            <form id="search-bar" action="<?php echo home_url() ?>">
                <div class="fre-input-field">
                    <input type="text" name="s" placeholder="<?php _e( "Search at blog", ET_DOMAIN ) ?>"
                           value="<?php echo get_search_query(); ?>">
                </div>
            </form>
            <div class="block-posts" id="post-control">
				<?php
					if ( have_posts() ) {
						get_template_part( 'list', 'posts' );
					} else {
						echo '<div>' . __( 'No results', ET_DOMAIN ) . '</div>';
					}
				?>
            </div>
            <!--// block control  -->
        </div>
    </div>
<?php get_footer();
