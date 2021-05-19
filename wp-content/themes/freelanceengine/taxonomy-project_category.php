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
	$post_object = $ae_post_factory->get( PROJECT );
	get_header();

?>
    <div class="fre-page-wrapper">
        <div class="fre-page-title">
            <div class="container">
                <div class="profs-cat_t">
                    <h1><?php _e( 'Available Projects', ET_DOMAIN ); ?></h1>
                </div>
            </div>
        </div>
        <div class="fre-page-section section-archive-project tax-cat">
            <div class="container">
                <div class="page-project-list-wrap">
                    <div class="fre-project-list-wrap">
						<?php get_template_part( 'template/filter', 'projects' ); ?>
                        <div class="fre-project-list-box">
                            <div class="fre-project-list-wrap">
                                <div class="fre-project-result-sort">
                                    <div class="row">
                                        <div class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6 col-sm-6 col-sm-push-6 hidden-xs">

                                            <div class="fre-project-sort sort-order" id="project_orderby">
                                                <select class="hidden sort-order" id="project_orderby"
                                                        name="orderby">
                                                    <option value="date"><?php _e( 'Latest Projects', ET_DOMAIN ); ?></option>
                                                    <option value="et_budget"><?php _e( 'Highest Budget', ET_DOMAIN ); ?></option>
                                                </select>
                                                <span class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="et_budget"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                <span class="option" id="date"><?php _e( 'Date', ET_DOMAIN ); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6 col-sm-6 col-sm-pull-6 col-xs-12">
                                            <div class="fre-project-result">

												<?php $found_posts = '<span class="found_post">' . $wp_query->found_posts . '</span>';
													$plural        = sprintf( __( '%s projects found', ET_DOMAIN ), $found_posts );
													$singular      = sprintf( __( '%s project found', ET_DOMAIN ), $found_posts ); ?>

                                                <span class="plural <?php if ( $wp_query->found_posts <= 1 ) {
													echo 'hide';
												} ?>">
                                                    <?php echo $plural; ?>
                                                </span>


                                                <div class="visible-xs">
													<?php if ( $query_post >= 1 ) { ?>
                                                        <div class="fre-project-sort">
                                                            <span class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                            <span class="option"
                                                                  id="et_budget"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                            <span class="option"
                                                                  id="date"><?php _e( 'Date', ET_DOMAIN ); ?></span>
                                                        </div>
													<?php } ?>
                                                </div>


                                                <span class="singular <?php if ( $wp_query->found_posts > 1 ) {
													echo 'hide';
												} ?>">
                                                    <?php echo $singular; ?>
                                                </span>

                                            </div>
                                        </div>

                                    </div>
                                </div>
								<?php get_template_part( 'list', 'projects' ); ?>
                            </div>
                        </div>
						<?php
							$wp_query->query = array_merge( $wp_query->query, [ 'is_archive_project' => is_post_type_archive( PROJECT ) ] );
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
