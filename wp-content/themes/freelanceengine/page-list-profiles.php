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
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PROFILE );
get_header(); ?>
    <div class="fre-page-wrapper section-archive-profile">
        <div class="fre-page-title">
            <div class="container">
                <h1><?php _e( 'Available Profiles', WPP_TEXT_DOMAIN ); ?></h1>
                <br/>
                <a href="<?php echo bloginfo( 'url' ); ?>/professionals-by-category/">PROFESSIONALS BY CATEGORY</a>
            </div>
        </div>

        <div class="fre-page-section">
            <div class="container">
                <div class="page-profile-list-wrap">
                    <div class="fre-profile-list-wrap">
						<?php get_template_part( 'template/filter', 'profiles' ); ?>
                        <div class="fre-profile-list-box">
                            <div class="fre-profile-list-wrap">
                                <div class="fre-profile-result-sort">
                                    <div class="row">
                                        <div class="col-sm-4 col-sm-push-8">
                                            <div class="fre-profile-sort">
                                                <select class="fre-chosen-single sort-order" name="orderby">
                                                    <option value="newest"><?php _e( 'Newest Profiles', WPP_TEXT_DOMAIN ); ?></option>
                                                    <option value="hour_rate"><?php _e( 'Highest Hourly Rate', WPP_TEXT_DOMAIN ); ?></option>
                                                    <option value="rating"><?php _e( 'Highest Rating', WPP_TEXT_DOMAIN ); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 col-sm-pull-4">
                                            <div class="fre-profile-result">
                                                <p>
													<?php
													$found_posts = '<span class="found_post">' . $wp_query->found_posts . '</span>';
													$plural      = sprintf( __( '%s profiles available', WPP_TEXT_DOMAIN ), $found_posts );
													$singular    = sprintf( __( '%s profile available', WPP_TEXT_DOMAIN ), $found_posts );
													?>
                                                    <span class="plural <?php if ( $wp_query->found_posts <= 1 ) {
														echo 'hide';
													} ?>">
                                                    <?php echo $plural; ?>
                                                </span>
                                                    <span class="singular <?php if ( $wp_query->found_posts > 1 ) {
														echo 'hide';
													} ?>">
                                                    <?php echo $singular; ?>
                                                </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php get_template_part( 'list', 'profiles' ); ?>
                            </div>
                        </div>
						<?php
						echo '<div class="fre-paginations paginations-wrapper">';
						ae_pagination( $wp_query, get_query_var( 'paged' ) );
						echo '</div>';
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
get_footer();