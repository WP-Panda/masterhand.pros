<?php
/**
 * Template Name: Page Benefits
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

    <div class="fre-page-wrapper page-benefits">
        <div class="container">
            <div class="client-prof">
                <ul class="row" id="Tabs" role="tablist">
                    <li class="nav-item col-sm-6 col-xs-6">
                        <div class="pull-right client-prof__client">
                            <a class="nav-link" data-toggle="tab" href="#if-client" role="tab">
								<?php _e("if you`re client", ET_DOMAIN ); ?>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item col-sm-6 col-xs-6">
                        <div class="pull-left client-prof__professional">
                            <a class="nav-link" data-toggle="tab" href="#if-pro" role="tab">
                                <span class="hidden-xs"><?php _e("if you`re professional", ET_DOMAIN ); ?></span>
                                <span class="visible-xs"><?php _e("if you`re pro", ET_DOMAIN ); ?></span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div id="if-client" class="tab-pane fade">
					<?php if ( ( get_field( 'plenty_jobs_header' ) ) || ( get_field( 'plenty_jobs_text' ) ) ) { ?>
                        <div class="plenty-jobs-wrap">
                            <div class="plenty-jobs-item">
                                <div class="plenty-jobs">
                                    <div class="plenty-jobs__before cli"></div>
									<?php if ( get_field( 'plenty_jobs_header' ) ) { ?>
                                        <div class="plenty-jobs__title"><?php the_field( 'plenty_jobs_header' ); ?></div>
									<?php } ?>
                                    <div class="plenty-jobs__text"><?php the_field( 'plenty_jobs_text' ); ?></div>
                                    <div class="plenty-jobs__after cli"></div>
                                </div>
                            </div>
                        </div>
					<?php } ?>
                    <div class="masterhand-professional">
                        <div class="row">
							<?php if ( ( get_field( 'text_left' ) ) || ( get_field( 'first_image' ) ) ) { ?>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="masterhand-list-wrap">
										<?php the_field( 'text_left' ); ?>
                                    </div>
                                    <div class="row hidden-xs">
										<?php if ( get_field( 'first_image' ) ) { ?>
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="masterhand-first__img">
                                                    <img src="<?php the_field( 'first_image' ); ?>" alt=""/>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( get_field( 'second_image' ) ) { ?>
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="masterhand-second__img">
                                                    <img src="<?php the_field( 'second_image' ); ?>">
                                                </div>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
							<?php } ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="masterhan-create-wrap">
									<?php if ( get_field( 'sign_up_header' ) ) { ?>
                                        <div class="masterhan-create__title"><?php the_field( 'sign_up_header' ); ?></div>
									<?php } ?>
                                    <div class="masterhan-create__text"><?php the_field( 'sign_up_text' ); ?></div>
                                    <a href="<?php echo bloginfo( 'url' ); ?>/register/?role=client"
                                       class="fre-submit-btn masterhan-create__link"><?php echo _( 'SIGN UP AS A CLIENT' ) ?></a>
                                </div>
                                <div class="row visible-xs">
									<?php if ( get_field( 'first_image' ) ) { ?>
                                        <div class="col-sm-6 col-xs-6">
                                            <div class="masterhand-first__img">
                                                <img src="<?php the_field( 'first_image' ); ?>" alt=""/>
                                            </div>
                                        </div>
									<?php } ?>
									<?php if ( get_field( 'second_image' ) ) { ?>
                                        <div class="col-sm-6 col-xs-6">
                                            <div class="masterhand-second__img">
                                                <img src="<?php the_field( 'second_image' ); ?>">
                                            </div>
                                        </div>
									<?php } ?>
                                </div>
								<?php if ( ( get_field( 'text_right_side_header' ) ) || ( get_field( 'text_right_side' ) ) ) { ?>
                                    <div class="masterhand-everything-wrap">
										<?php if ( get_field( 'text_right_side_header' ) ) { ?>
                                            <div class="masterhand-everything__title"><?php the_field( 'text_right_side_header' ); ?></div>
										<?php } ?>
                                        <div class="masterhand-everything__text"><?php the_field( 'text_right_side' ); ?></div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="masterhand-sign-up">
                        <div class="sign-up-wrap">
                            <div class="sign-up__before cli"></div>
							<?php if ( get_field( 'sign_up_bottom_header' ) ) { ?>
                                <div class="sign-up__title"><?php the_field( 'sign_up_bottom_header' ); ?></div>
							<?php } ?>
							<?php if ( get_field( 'sign_up_bottom_text' ) ) { ?>
                                <div class="sign-up__text"><?php the_field( 'sign_up_bottom_text' ); ?></div>
							<?php } ?>
                            <div class="sign-up__link">
                                <a class="fre-submit-btn"
                                   href="<?php echo bloginfo( 'url' ); ?>/register/?role=client"><?php echo _( 'SIGN UP AS A CLIENT' ); ?></a>
                            </div>
                            <div class="sign-up__after cli"></div>
                        </div>
                        <p class="sign-up__details"><?php echo _( '* More great features, benefits, options are inside. In your profile' ); ?></p>
                    </div>
                </div>
                <div id="if-pro" class="tab-pane fade">
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
                                    <div class="masterhand-list-wrap">
										<?php the_field( 'pro_text_left' ); ?>
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
							<?php } ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="masterhan-create-wrap">
									<?php if ( get_field( 'pro_sign_up_header' ) ) { ?>
                                        <div class="masterhan-create__title"><?php the_field( 'pro_sign_up_header' ); ?></div>
									<?php } ?>
                                    <div class="masterhan-create__text"><?php the_field( 'pro_sign_up_text' ); ?></div>
                                    <a href="<?php echo bloginfo( 'url' ); ?>/register/?role=professional"
                                       class="fre-submit-btn masterhan-create__link"><?php echo _( 'SIGN UP AS A PRO' ) ?></a>
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
                                   href="<?php echo bloginfo( 'url' ); ?>/register/?role=professional"><?php echo _( 'SIGN UP AS A PRO' ); ?></a>
                            </div>
                            <div class="sign-up__after"></div>
                        </div>
                        <p class="sign-up__details"><?php echo _( '* More great features, benefits, options are inside. In your profile' ); ?></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php
get_footer();