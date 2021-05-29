<?php
	/**
	 * Template Name: Business Promotion with KNOW-HOW
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
	get_header();

?>

    <div class="fre-page-wrapper page-benefits page-pro-benefits">
        <div class="container">

            <div id="if-pro">
				<?php if ( ( get_field( 'pro_plenty_jobs_header' ) ) || ( get_field( 'pro_plenty_jobs_text' ) ) ) { ?>
                    <div class="plenty-jobs-wrap">
                        <div class="plenty-jobs-item">
                            <div class="plenty-jobs">
                                <div class="plenty-jobs__before"></div>
								<?php if ( get_field( 'pro_plenty_jobs_header' ) ) { ?>
                                    <div class="plenty-jobs__title"><?php the_field( 'pro_plenty_jobs_header' ); ?></div>
								<?php } ?>
                                <div class="plenty-jobs__text"><?php the_field( 'pro_plenty_jobs_text' ); ?></div>
                                <div class="plenty-jobs__after"></div>
                            </div>
                        </div>
                    </div>
				<?php } ?>

                <div class="masterhand-professional">
                    <div class="row">
						<?php if ( ( get_field( 'pro_text_left' ) ) || ( get_field( 'pro_first_image' ) ) ) { ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="masterhand-list-wrap pro-wrap">
									<?php the_field( 'pro_text_left' ); ?>
                                </div>
                            </div>
						<?php } ?>

                        <div class="col-sm-6 col-xs-12 lh-text">
                            <div class="masterhan-create-wrap">
								<?php if ( get_field( 'pro_sign_up_header' ) ) { ?>
                                    <div class="masterhan-create__title"><?php the_field( 'pro_sign_up_header' ); ?></div>
								<?php } ?>
                                <div class="masterhan-create__text"><?php the_field( 'pro_sign_up_text' ); ?></div>
                            </div>
							<?php if ( ( get_field( 'pro_text_right_side_header' ) ) || ( get_field( 'pro_text_right_side' ) ) ) { ?>
                                <div class="masterhan-create-wrap">
									<?php if ( get_field( 'pro_text_right_side_header' ) ) { ?>
                                        <div class="masterhan-create__title"><?php the_field( 'pro_text_right_side_header' ); ?></div>
									<?php } ?>
                                    <div class="masterhan-create__text"><?php the_field( 'pro_text_right_side' ); ?></div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
                <div class="masterhand-sign-up">
                    <div class="sign-up-wrap how">
                        <div class="sign-up__before"></div>
						<?php if ( get_field( 'pro_sign_up_bottom_header' ) ) { ?>
                            <div class="sign-up__title"><?php the_field( 'pro_sign_up_bottom_header' ); ?></div>
						<?php } ?>
						<?php if ( get_field( 'pro_sign_up_bottom_text' ) ) { ?>
                            <div class="sign-up__text"><?php the_field( 'pro_sign_up_bottom_text' ); ?></div>
						<?php } ?>
						<?php if ( function_exists( 'user_submitted_posts' ) ) {
							user_submitted_posts();
						} ?>

                        <div class="plenty-jobs__title">
                            Your published articles:
                        </div>
                        <div class="row">
                            <ul>
								<?php
									global $post;
									if ( is_user_logged_in() ) {
										global $current_user;
										if ( ! is_object( $current_user ) ) {
											$current_user = wp_get_current_user();
										}
									}
									$author_name = $current_user->user_login;
									$args        = [
										'posts_per_page' => 3,
										'post_type'      => 'post',
										'author_name'    => $author_name
									];
									$myposts     = get_posts( $args );
									foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
                                        <div class="col-md-6 col-xs-12">
                                            <a href="<?php the_permalink(); ?>"
                                               class="link-social"><?php the_title(); ?></a>
                                        </div>

                                        <div class="col-md-6 col-xs-12">
											<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
												ADDTOANY_SHARE_SAVE_KIT();
											} ?>

                                        </div>
									<?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="sign-up__after"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
	get_footer();