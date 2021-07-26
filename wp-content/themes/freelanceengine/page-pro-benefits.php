<?php
/**
 * Template Name: PRO benefits For Client
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

$pid   = get_the_ID();
$parid = wp_get_post_parent_id( $pid );

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
                        <div class="col-sm-6 col-xs-12">
                            <div class="masterhan-create-wrap">
								<?php if ( get_field( 'pro_sign_up_header' ) ) { ?>
                                    <div class="masterhan-create__title"><?php the_field( 'pro_sign_up_header' ); ?></div>
								<?php } ?>
                                <div class="masterhan-create__text"><?php the_field( 'pro_sign_up_text' ); ?></div>
                                <a href="<?php echo bloginfo( 'url' ); ?>/register/?role=professional"
                                   class="fre-submit-btn masterhan-create__link"><?php echo _( 'ACTIVATE A PRO PLAN' ) ?></a>
                            </div>
							<?php if ( ( get_field( 'pro_text_right_side_header' ) ) || ( get_field( 'pro_text_right_side' ) ) ) { ?>
                                <div class="masterhand-everything-wrap">
									<?php if ( get_field( 'pro_text_right_side_header' ) ) { ?>
                                        <div class="masterhand-everything__title"><?php the_field( 'pro_text_right_side_header' ); ?></div>
									<?php } ?>
                                    <div class="masterhand-everything__text"><?php the_field( 'pro_text_right_side' ); ?></div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="row">
						<?php if ( get_field( 'pro_first_image' ) ) { ?>
                            <div class="col-sm-6 col-xs-6">
                                <div class="masterhand-first__img">
                                    <img src="<?php the_field( 'pro_first_image' ); ?>" alt=""/>
                                </div>
                            </div>
						<?php } ?>
						<?php if ( get_field( 'pro_second_image' ) ) { ?>
                            <div class="col-sm-6 col-xs-6">
                                <div class="masterhand-second__img">
                                    <img src="<?php the_field( 'pro_second_image' ); ?>">
                                </div>
                            </div>
						<?php } ?>
                    </div>
                </div>
                <div class="masterhand-sign-up">
                    <div class="sign-up-wrap">
                        <div class="sign-up__before"></div>
						<?php if ( get_field( 'pro_sign_up_bottom_header' ) ) { ?>
                            <div class="sign-up__title"><?php the_field( 'pro_sign_up_bottom_header' ); ?></div>
						<?php } ?>
						<?php if ( get_field( 'pro_sign_up_bottom_text' ) ) { ?>
                            <div class="sign-up__text"><?php the_field( 'pro_sign_up_bottom_text' ); ?></div>
						<?php } ?>
                        <div class="sign-up__link">
                            <a class="fre-submit-btn"
                               href="<?php echo bloginfo( 'url' ); ?>/register/?role=professional"><?php echo _( 'Activate a PRO plan' ); ?></a>
                        </div>
                        <div class="sign-up__after"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
get_footer();