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

global $wp_query, $ae_post_factory, $post, $user_ID;

if ( ! empty( $wp_query->query_vars['project_category'] ) ) {

	$term_category = $wp_query->query_vars['project_category'];

	$term_category_id = get_term_by( 'slug', $term_category, 'project_category' );
	$term_category_id = $term_category_id->term_id;

	$term_parent_id   = wp_get_term_taxonomy_parent_id( $term_category_id, 'project_category' );
	$term_parent_obj  = get_term( $term_parent_id, 'project_category' );
	$term_parent_slug = $term_parent_obj->slug;

	// total query
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = [
		'post_type'        => PROFILE,
		'post_'            => PROFILE,
		'posts_per_page'   => 10,
		'with_companies'   => true,
		'project_category' => $term_category,
		'paged'            => $paged,
	];


	if ( $term_parent_slug === null ) {
		if ( $term_category !== '' ) {
			$args['cat'] = $term_category;
			$args['sub'] = null;
		}
	} else {
		if ( $term_parent_slug !== '' ) {
			$args['cat'] = $term_parent_slug;
		}

		if ( $term_category !== '' ) {
			$args['sub'] = $term_category;
		}
	}

	$inner_query = new WP_Query( $args );
}

$col_posts = isset( $inner_query ) ? $inner_query->found_posts : $wp_query->found_posts;

get_header();
?>
    <div class="fre-page-wrapper section-archive-profile">
        <div class="fre-page-title profs-cat">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <div class="profs-cat_t">
                            <h1><?php single_cat_title(); ?></h1>
                            <i class="hidden-xs"><?php _e( 'find a professional here.', ET_DOMAIN ) ?></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="profs-cat_desc">
                            <a class="profs-cat_link"
                               href="<?php echo get_option( 'siteurl' ) . '/profile_category'; ?>">
								<?php echo __( 'Professionals by category', ET_DOMAIN ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fre-page-section tax-cat">
            <div class="container">
                <div class="page-profile-list-wrap">
                    <div class="fre-profile-list-wrap">
						<?php get_template_part( 'template/filter', 'profiles' ); ?>
                        <div class="fre-profile-list-box">
                            <div class="fre-profile-list-wrap">
                                <div class="fre-profile-result-sort">
                                    <div class="row">

										<?php $found_posts = '<span class="found_post">' . $col_posts . '</span>';
										$plural            = sprintf( __( '%s profiles available', ET_DOMAIN ), $found_posts );
										$singular          = sprintf( __( '%s profile available', ET_DOMAIN ), $found_posts ); ?>

                                        <div
                                                class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6 col-sm-6 col-sm-push-6 hidden-xs">

                                            <div id="profile_orderby" class="fre-profile-sort">
                                                <select class="hidden sort-order" name="orderby">
                                                    <option
                                                            value="date"><?php _e( 'Newest Profiles', ET_DOMAIN ); ?></option>
                                                    <option
                                                            value="hour_rate"><?php _e( 'Highest Hourly Rate', ET_DOMAIN ); ?></option>
                                                    <option
                                                            value="rating"><?php _e( 'Highest Rating', ET_DOMAIN ); ?></option>
                                                    <option value="projects_worked"><?php _e( 'Most Projects Worked', ET_DOMAIN ); ?></option>
                                                </select>
                                                <span class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="hour_rate"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="rating"><?php _e( 'Ranking', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="projects_worked"><?php _e( 'Projects', ET_DOMAIN ); ?></span>
                                            </div>

                                        </div>
                                        <div
                                                class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6 col-sm-6 col-sm-pull-6 col-xs-12">
                                            <div class="fre-profile-result">

                                             <span class="plural <?php if ( $col_posts <= 1 ) {
	                                             echo 'hide';
                                             } ?>">
                                                    <?php echo $plural; ?>
                                                </span>

                                                <div class="visible-xs">
                                                    <div id="profile_orderby" class="fre-profile-sort">
                                                        <span
                                                                class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="hour_rate"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="rating"><?php _e( 'Ranking', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="projects_worked"><?php _e( 'Projects', ET_DOMAIN ); ?></span>
                                                    </div>
                                                </div>

                                                <span class="singular <?php if ( $col_posts > 1 ) {
													echo 'hide';
												} ?>">
                                                    <?php echo $singular; ?>
                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php get_template_part( 'list', 'profiles' ); ?>
                            </div>
                        </div>
						<?php
						echo '<div class="fre-paginations paginations-wrapper">';
						if ( isset( $inner_query ) ) {
							ae_pagination( $inner_query, get_query_var( 'paged' ) );
						} else {
							ae_pagination( $wp_query, get_query_var( 'paged' ) );
						}
						echo '</div>';
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
get_footer();
