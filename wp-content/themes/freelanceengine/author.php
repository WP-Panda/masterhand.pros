<?php
/**
 * The Template for displaying a user profile
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
add_action( 'wp_head', 'gretathemes_meta_tags_author' );

global $wp_query, $ae_post_factory, $post, $user_ID;
$style            = "";
$post_object      = $ae_post_factory->get( PROFILE );
$author_id        = get_query_var( 'author' );
$author_name      = get_the_author_meta( 'display_name', $author_id );
$author_available = get_user_meta( $author_id, 'user_available', true );

// get user profile id
$profile_id = get_user_meta( $author_id, 'user_profile_id', true );
$pro_status = get_post_meta( $profile_id, 'pro_status', true );

// get current user profile id
$profile_id_current_user = get_user_meta( $user_ID, 'user_profile_id', true );
$pro_status_current_user = get_post_meta( $profile_id_current_user, 'pro_status', true );

$isFreelancer      = ( ae_user_role( $author_id ) == FREELANCER ) ? 1 : 0;
$currentFreelancer = ( ae_user_role( $user_ID ) == FREELANCER ) ? 1 : 0;

$convert = '';
if ( $profile_id ) {
	// get post profile
	$profile = get_post( $profile_id );

	if ( $profile && ! is_wp_error( $profile ) ) {
		$convert = $post_object->convert( $profile );

	}

}

// try to check and add profile up current user dont have profile
if ( ! $convert && ( fre_share_role() || $isFreelancer ) ) {
	$profile_post = get_posts( [ 'post_type' => PROFILE, 'author' => $author_id ] );

	if ( ! empty( $profile_post ) ) {
		$profile_post = $profile_post[0];
		$convert      = $post_object->convert( $profile_post );
		$profile_id   = $convert->ID;
		update_user_meta( $author_id, 'user_profile_id', $profile_id );
	} else {
		$convert    = $post_object->insert( [
			'post_status'  => 'publish',
			'post_author'  => $author_id,
			'post_title'   => $author_name,
			'post_content' => ''
		] );
		$convert    = $post_object->convert( get_post( $convert->ID ) );
		$profile_id = $convert->ID;
	}
}

//  count author review number
$count_reviews = get_count_reviews_user( $author_id );

get_header();
$next_post = false;

if ( $convert ) {
	$next_post = ae_get_adjacent_post( $convert->ID, false, '', true, 'skill' );
}

$author_status = get_user_pro_status( $author_id );

$class_name = 'employer';
if ( fre_share_role() || $isFreelancer ) {
	$class_name = 'freelance';
}

$projects_worked = get_post_meta( $profile_id, 'total_projects_worked', true );
$project_posted  = fre_count_user_posts_by_type( $author_id, 'project', '"publish","complete","close","disputing","disputed" ', true );
$hire_freelancer = fre_count_hire_freelancer( $author_id );

$user      = get_userdata( $author_id );
$ae_users  = AE_Users::get_instance();
$user_data = $ae_users->convert( $user );
$hour_rate = 0;

if ( isset( $convert->hour_rate ) ) {
	$hour_rate = (int) $convert->hour_rate;
}

$user_info = get_userdata( $author_id );
$location  = getLocation( $author_id );

$max_portfolio  = getValueByProperty( $author_status, 'max_portfolio' );
$personal_cover = getValueByProperty( $author_status, 'personal_cover' );

if ( $personal_cover ) {
	$img_url = get_user_meta( $author_id, 'cover_url' );
	if ( $img_url ) {
		$style = 'style="background-image: url(' . $img_url[0] . '); background-repeat: no-repeat; background-size: 100% 100%;"';
	}
}

$visualFlag = getValueByProperty( $author_status, 'visual_flag' );
if ( $visualFlag ) {
	$visualFlagNumber = get_user_meta( $author_id, 'visual_flag', true );
}


?>

    <div class="fre-page-wrapper list-profile-wrapper" <?php echo $style ?>>
        <div class="fre-page-title hidden-xs">
            <div class="container">
                <h1>
					<?php printf( __( "Profile of %s", ET_DOMAIN ), $author_name ); ?>
                </h1>
            </div>
        </div>

        <div class="container">

            <div class="fre-page-section">
                <div class="author-freelance-wrap">

                    <div class="fre-profile-box">
                        <div class="profile-freelance-info-wrap">
                            <div class="profile-freelance-info top row">
                                <div class="col-sm-2 col-xs-3 avatar_wp">
									<?php echo get_avatar( $author_id, 145 ); ?>
                                </div>
                                <div class="col-lg-3 col-sm-4 col-md-3 col-xs-8">
                                    <div class="col-sm-8 col-xs-12 freelance-name">
										<?php echo $author_name ?>
										<?php
										if ( $author_status && $author_status != PRO_BASIC_STATUS_EMPLOYER && $author_status != PRO_BASIC_STATUS_FREELANCER ) {
											pro_label();
										} ?>
										<?php if ( $visualFlag ) {
											switch ( $visualFlagNumber ) {
												case 1:
													echo '<span class="status">' . translate( 'Master', ET_DOMAIN ) . '</span>';
													break;
												case 2:
													echo '<span class="status">' . translate( 'Creator', ET_DOMAIN ) . '</span>';
													break;
												case 3:
													echo '<span class="status">' . translate( 'Expert', ET_DOMAIN ) . '</span>';
													break;
											}
										} ?>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 free-rating"><?php HTML_review_rating_user( $author_id ) ?></div>
                                    <div class="col-sm-12 col-xs-12 freelance-profile-country">
										<?php
										if ( $location && ! empty( $location['country'] ) ) {
											$str = [];
											foreach ( $location as $key => $item ) {
												if ( ! empty( $item['name'] ) ) {
													$str[] = $item['name'];
												}
											}
											echo ! empty( $str ) ? implode( ' - ', $str ) : 'Error';
										} else { ?>
											<?php echo '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>'; ?>
											<?php
										}
										?>
                                    </div>
									<?php if ( fre_share_role() || $isFreelancer ) { ?>
                                        <div class="col-sm-6 hidden-xs skill">
											<?php echo ! empty( $convert->experience ) ? $convert->experience : ''; ?>
                                        </div>
                                        <div class="col-sm-6 hidden-xs skill">
											<?php printf( __( '<span>%s</span> projects worked', ET_DOMAIN ), intval( $projects_worked ) ); ?>
                                        </div>
									<?php } else { ?>
                                        <div class="col-sm-6 hidden-xs skill">
											<?php printf( __( '<span>%s</span> projects posted', ET_DOMAIN ), intval( $project_posted ) ); ?>
                                        </div>
                                        <div class="col-sm-6 hidden-xs skill">
											<?php printf( __( '<span>%s</span> professionals hired', ET_DOMAIN ), intval( $hire_freelancer ) ); ?>
                                        </div>
									<?php } ?>
                                </div>
								<?php if ( fre_share_role() || $isFreelancer ) { ?>
                                    <div class="col-xs-6 visible-xs skill">
										<?php echo ! empty( $convert->experience ) ? $convert->experience : ''; ?>
                                    </div>
                                    <div class="col-xs-6 visible-xs skill">
										<?php printf( __( '<span>%s</span> projects worked', ET_DOMAIN ), intval( $projects_worked ) ); ?>
                                    </div>
								<?php } else { ?>
                                    <div class="col-xs-6 visible-xs skill">
										<?php printf( __( '<span>%s</span> projects posted', ET_DOMAIN ), intval( $project_posted ) ); ?>
                                    </div>
                                    <div class="col-xs-6 visible-xs skill">
										<?php printf( __( '<span>%s</span> professionals hired', ET_DOMAIN ), intval( $hire_freelancer ) ); ?>
                                    </div>
								<?php } ?>
                                <div class="col-sm-3 col-xs-12">
                                    <div class="rating-new">
										<?php echo __( 'Rating:', ET_DOMAIN ); ?>
                                        <span>+<?php echo getActivityRatingUser( $author_id ) ?></span>
                                    </div>
                                    <div class="secure-deals">
										<?php echo __( 'SafePay Deals:', ET_DOMAIN ); ?>
										<? $safe_deals_count = get_user_meta( $author_id, 'safe_deals_count', 1 ); ?>
                                        <span><?php echo ( $safe_deals_count == '' ) ? 0 : $safe_deals_count ?></span>
                                    </div>
                                    <div class="reviews">
										<?php echo __( 'Reviews:', ET_DOMAIN ); ?>
                                        <span><?php echo $count_reviews; ?></span>
                                    </div>
                                    <div class="city">
										<?php
										if ( $location && ! empty( $location['country'] ) ) {
											$str = [];
											foreach ( $location as $key => $item ) {
												if ( ! empty( $item['name'] ) ) {
													$str[] = $item['name'];
												}
											}
											echo ! empty( $str ) ? __( 'City:', ET_DOMAIN ) . '<span>' . $str[2] . '</span>' : '';
										} ?>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-12 skills">
                                    <div class="skill col-sm-12 col-xs-6">
										<?php echo __( 'skills & endorsements', ET_DOMAIN ); ?>
                                        <span><?php echo countEndorseSkillsUser( $author_id ) ?></span>
                                    </div>
                                    <div class="skill col-sm-12 col-xs-6">
										<?php echo __( 'awards', ET_DOMAIN ); ?><span>0</span>
                                    </div>
                                </div>
								<?php if ( fre_share_role() || $isFreelancer ) {
									if ( $convert ) { ?>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 fre-profile_category">
											<?php echo '<span>' . __( 'Specializations:', ET_DOMAIN ) . '</span>';
											if ( isset( $convert->tax_input['project_category'] ) && $convert->tax_input['project_category'] ) {
												echo baskserg_profile_categories2( $convert->tax_input['project_category'] );
											} else {
												echo '<span>No Categories</span>';
											}
											?>
                                        </div>
									<?php }
								} ?>
                            </div>
                        </div>
                    </div>
                    <!--profile-box--->
                    <div class="fre-profile-box skills_awards_wp">
                        <div class="row skills_awards">
                            <div class="col-sm-12 col-xs-12 skill-list">
                                <!-- пока нет наград - ставим 12 вместо col-sm-6-->
                                <div class="bl_t">
									<?php echo __( 'Skills and Endorsements:', ET_DOMAIN ); ?>
                                </div>
								<? /* wp_enqueue_style( 'endoSk' );
									if ( is_plugin_active( 'referral_code/referral_code.php' ) ) {
										$list_referral = get_referral( $user_ID );
										$res           = false;
										if ( ! empty( $list_referral ) ) {
											foreach ( $list_referral as $value ) {
												if ( $value[ 'user_id' ] == $author_id && $value[ 'user_id_referral' ] == $user_ID ) {
													$res = true;
												}
												if ( $value[ 'user_id_referral' ] == $author_id && $value[ 'user_id' ] == $user_ID ) {
													$res = true;
												}
											}
										}
									} else {
										$res = false;
									}

									$modeEndorse = ( $author_id != $user_ID && $res ) ? true : false;
									if ( $modeEndorse ) {
										wp_enqueue_script( 'endoSk' );
									}
									renderSkillsInProfile( $author_id, $modeEndorse, $user_ID );*/
								?>
                                <?php 	wpp_get_template_part( 'wpp/templates/profile/tabs/skill-list', [ 'user_ID' => $author_id ] );?>
                            </div>
                            <!-- пока нет наград - скрываем НЕ УДАЛЯТЬ!!!!!!!!!!!!
                        <div class="col-sm-6 col-xs-12 award-list">
                            <div class="bl_t">
                                <?php echo __( 'Awards:', ET_DOMAIN ); ?>
                            </div>
                            <ul class="row">
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw1.png" alt="" /> 1-st place on DC 2018
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw2.png" alt="" /> Pro league in Germany
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw3.png" alt="" /> Gold Members
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw4.png" alt="" /> League of Masters
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw1.png" alt="" /> League of Masters
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw2.png" alt="" /> Best proffesional
                                </li>
                                <li class="col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw3.png" alt="" /> 100 deals of Octomber
                                </li>
                            </ul>
                        </div>-->
                        </div>
                        <div class="show_more">
							<?php echo __( 'Show more ', ET_DOMAIN ); ?><i class="fa fa-angle-down"></i></div>
                        <div class="hide_more">
							<?php echo __( 'Hide more ', ET_DOMAIN ); ?><i class="fa fa-angle-up"></i>
                        </div>
                    </div>
                    <!--profile-box--->
					<?php if ( fre_share_role() || $isFreelancer ) { ?>
                        <div class="row info_bl">
                            <div class="col-sm-7 col-md-8 col-lg-9 col-xs-12 fre-profile-box">
								<?php if ( ! empty( $convert ) ) { ?>
                                    <div class="freelance-about">
                                        <span class="bl_t"><?php echo __( "About me:", ET_DOMAIN ); ?></span>
										<?php
										global $post;
										$post = isset( $profile );
										if ( $post ) {
											setup_postdata( $profile );
											the_content();
											wp_reset_postdata();
										} ?>
                                    </div>
								<?php } ?>

                                <div class="hidden freelance-about hidden-contacts">
                                    <span class="bl_t"><?php echo __( "Contacts:", ET_DOMAIN ); ?></span>
									<?php $fb = get_post_meta( $profile_id, 'facebook', true );
									$skype    = get_post_meta( $profile_id, 'skype', true );
									$web      = get_post_meta( $profile_id, 'website', true );
									$viber    = get_post_meta( $profile_id, 'viber', true );
									$wapp     = get_post_meta( $profile_id, 'whatsapp', true );
									$telegram = get_post_meta( $profile_id, 'telegram', true );
									$wechat   = get_post_meta( $profile_id, 'wechat', true );
									$ln       = get_post_meta( $profile_id, 'linkedin', true ); ?>
                                    <p>
										<?php if ( $user_data->user_email ) {
											_e( '<strong>Email:</strong>', ET_DOMAIN );
											echo $user_data->user_email;
										} ?></p>
                                    <p>
										<?php if ( $user_data->user_phone ) {
											_e( '<strong>Phone:</strong>', ET_DOMAIN );
											echo $user_data->user_phone;
										} ?></p>
									<?php if ( $fb ) { ?>
                                        <p><a href="<?php echo $fb; ?>" target="_blank"
                                              rel="nofollow"><?php echo $fb; ?></a></p>
									<?php }
									if ( $skype ) { ?>
                                        <p><?php echo $skype; ?></p>
									<?php }
									if ( $web ) { ?>
                                        <p><?php echo $web; ?></p>
									<?php }
									if ( $viber ) { ?>
                                        <p><?php echo $viber; ?></p>
									<?php } ?>
									<?php if ( $wapp ) { ?>
                                        <p><?php echo $wapp; ?></p>
									<?php }
									if ( $telegram ) { ?>
                                        <p><?php echo $telegram; ?></p>
									<?php }
									if ( $wechat ) { ?>
                                        <p><?php echo $wechat; ?></p>
									<?php }
									if ( $ln ) { ?>
                                        <p><a href="<?php echo $ln; ?>" target="_blank"
                                              rel="nofollow"><?php echo $ln; ?></a></p>
									<?php } ?>
                                </div>

								<?php if ( isset( $convert->tax_input['profile_category'] ) && $convert->tax_input['profile_category'] ) { ?>
                                    <div class="freelance-cat-list">
                                        <span class="bl_t"><?php echo __( "Categories:", ET_DOMAIN ); ?></span>
										<?php echo baskserg_profile_categories3( $convert->tax_input['profile_category'] ); ?>
                                    </div>
								<?php } ?>
                            </div>
                            <!--about left -->
                            <div class="col-sm-5 col-md-4 col-lg-3 col-xs-12">

                                <div class="freelance-hourly">
									<?php if ( fre_share_role() || $isFreelancer ) {
										if ( $hour_rate > 0 ) { ?>
                                            <span class="bl_t"><?php echo __( "Rate:", ET_DOMAIN ); ?></span>
											<?php echo '<span>' . sprintf( __( '<b>%s</b> /hr ', ET_DOMAIN ), fre_price_format( $hour_rate ) ) . '</span>'; ?>
                                            <!--<span><?php echo $convert->earned ?></span>-->
										<?php }
									} ?>
                                    <div class="freelance-info-edit">
										<?php if ( ( ae_user_role( $user_ID ) == EMPLOYER || current_user_can( 'manage_options' ) ) && $user_ID != $author_id ) { ?>
                                            <a href="#"
                                               class="fre-submit-btn btn-center <?php if ( is_user_logged_in() ) {
												   echo 'invite-open';
											   } else {
												   echo 'login-btn';
											   } ?>" data-user="<?php echo $convert->post_author ?>">
												<?php _e( "Invite to Project", ET_DOMAIN ) ?>
                                            </a>
										<?php } ?>
										<?php
										$show_btn = apply_filters( 'show_btn_contact', true ); // @since 1.8.5
										if ( $show_btn ) { ?>

                                            <a href="<?php if ( $user_ID == 0 ) {
												echo et_get_page_link( "login" );
											} else {
												echo '#';
											} ?>" class="<?php if ( $user_ID == 0 ) {
												echo 'fre-submit-btn btn-center';
											} else {
												echo 'fre-submit-btn btn-center contact-me';
											} ?>" data-user="<?php if ( $user_ID != 0 ) {
												echo $convert->post_author;
											} ?>">
												<?php _e( "Send Message", ET_DOMAIN ) ?>
                                            </a>
										<?php } ?>
										<?php if ( $author_status && $author_status != PRO_BASIC_STATUS_EMPLOYER && $author_status != PRO_BASIC_STATUS_FREELANCER ) { ?>
                                            <a href="<?php if ( $user_ID == 0 ) {
												echo et_get_page_link( "login" );
											} else {
												echo '#';
											} ?>" class="<?php if ( $user_ID == 0 ) {
												echo 'fre-submit-btn btn-center';
											} else {
												echo 'fre-submit-btn btn-center info-open';
											} ?>" data-user="<?php if ( $user_ID != 0 ) {
												echo $convert->post_author;
											} ?>">
												<?php _e( "Show Contacts", ET_DOMAIN ) ?>
                                            </a>

										<?php } ?>
                                    </div>
                                </div>
                                <!--btns-->
                            </div>
                        </div>
					<?php } ?>
                    <!--profile-box--->

                </div>
            </div>

            <ul class="nav nav-justify-content-center" id="Tabs" role="tablist">
				<?php if ( fre_share_role() || $isFreelancer ) { ?>
                    <li class="nav-item">
                        <a class="nav-link active" id="portfolio-tab" data-toggle="tab" href="#portfolio" role="tab"
                           aria-controls="portfolio" aria-selected="true">
							<?php echo __( "Portfolio", ET_DOMAIN ); ?>
                        </a>
                    </li>
				<?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link active" id="projects-tab" data-toggle="tab" href="#projects" role="tab"
                           aria-controls="projects" aria-selected="false">
							<?php echo __( "Projects", ET_DOMAIN ); ?>
                        </a>
                    </li>
				<?php } ?>
				<?php if ( $currentFreelancer ): ?>
                    <li class="nav-item">
                        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
                           aria-controls="reviews" aria-selected="false">
							<?php echo __( "Reviews", ET_DOMAIN ); ?>
                        </a>
                    </li>
				<?php endif; ?>
            </ul>
            <div class="tab-content" id="TabsContent">
				<?php if ( fre_share_role() || $isFreelancer ) { ?>
                    <div class="tab-pane fade in active" id="portfolio" role="tabpanel" aria-labelledby="portfolio-tab">
                        <div class="tabs_wp">
							<?php get_template_part( 'list', 'educations' );
							get_template_part( 'list', 'certifications' );
							get_template_part( 'list', 'experiences' );
							wp_reset_query();
							get_template_part( 'template/author', 'freelancer-historyshort' );
							wp_reset_query();
							if ( $max_portfolio ) {
								get_template_part( 'list', 'portfoliosbest' );
								wp_reset_query();
								get_template_part( 'list', 'portfoliosclient' );
								wp_reset_query();
							} ?>
                        </div>
						<?php
						//$max_portfolio ? get_template_part('list', 'portfolioscat') : get_template_part('list', 'portfolioscat_num_list'); //it was so wth cats
						get_template_part( 'list', 'portfoliosall' );

						wp_reset_query();
						get_template_part( 'list', 'documents_author' );
						wp_reset_query();
						?>
                    </div>
				<?php } else { ?>
                    <div class="tab-pane fade in active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                        <!--author--projects-->
                        <ul class="fre-tabs nav-tabs-my-work">
                            <li class="active"><a data-toggle="tab"
                                                  href="#all-project-tab"><span><?php _e( 'All', ET_DOMAIN ); ?></span></a>
                            </li>
                            <li class="next"><a data-toggle="tab"
                                                href="#open-project-tab"><span><?php _e( 'Open', ET_DOMAIN ); ?></span></a>
                            </li>
                            <li class="next"><a data-toggle="tab"
                                                href="#closed-project-tab"><span><?php _e( 'Closed', ET_DOMAIN ); ?></span></a>
                            </li>
                        </ul>
                        <div class="fre-tab-content projects-tab">
                            <!--all projects-->
                            <div id="all-project-tab" class="employer-current-project-tab fre-panel-tab active">
								<?php $employer_current_project_query = new WP_Query( [
									'post_status'      => [
										'close',
										'disputing',
										'publish',
										'reject',
										'archive',
										'complete',
										'disputed'
									],
									'is_author'        => true,
									'post_type'        => PROJECT,
									'author'           => $author_id,
									'suppress_filters' => true,
									'orderby'          => 'date',
									'order'            => 'DESC'
								] );

								$post_object = $ae_post_factory->get( PROJECT ); ?>

                                <div class="current-employer-project">
									<?php if ( $employer_current_project_query->have_posts() ) {
										$postdata = [];
									while ( $employer_current_project_query->have_posts() ) {
										$employer_current_project_query->the_post();
										$convert        = $post_object->convert( $post, 'thumbnail' );
										$postdata[]     = $convert;
										$project_status = $convert->post_status;
										$optionsProject = optionsProject( $convert ); ?>
                                        <div class="fre-profile-box">
                                            <div class="row">
                                                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                                                    <div class="project-title-col">
                                                        <span><?php _e( 'Project:', ET_DOMAIN ); ?></span>
                                                        <a href="<?php echo $convert->permalink; ?>">
															<?php echo $convert->post_title; ?>
                                                        </a>
                                                        <span class="status">
                                                    <?php echo $convert->project_status_view; ?>
                                                </span>
                                                    </div>
                                                    <div class="project-category-col"><?php list_tax_of_project( get_the_ID() ); ?></div>
                                                    <div class="project-txt-col"><?php echo $convert->post_content_trim; ?></div>
                                                </div>
                                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                                    <div class="project-open-col">
                                                        <span><?php _e( 'Date', ET_DOMAIN ); ?></span>
														<?php echo $convert->post_date; ?>
                                                    </div>
                                                    <div class="project-budget-col">
                                                        <span><?php _e( 'Budget', ET_DOMAIN ); ?></span>
														<?php echo $convert->budget; ?>
                                                    </div>
                                                    <div class="project-bids-col">
                                                        <span><?php _e( 'Bids', ET_DOMAIN ); ?></span>
														<?php echo $convert->total_bids; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php } ?>
                                        <script type="data/json" id="current_project_post_data">
                                    <?php echo json_encode( $postdata ); ?>














                                        </script>
									<?php } else {
										_e( 'No results', ET_DOMAIN );
									} ?>

									<?php ae_pagination( $employer_current_project_query, get_query_var( 'paged' ) ); ?>

									<?php wp_reset_postdata();
									wp_reset_query(); ?>
                                </div>
                            </div>

                            <!--open projects-->
                            <div id="open-project-tab" class="employer-previous-project-tab fre-panel-tab">
								<?php $employer_open_project_query = new WP_Query( [
									'post_status'      => [
										'publish'
									],
									'is_author'        => true,
									'post_type'        => PROJECT,
									'author'           => $author_id,
									'suppress_filters' => true,
									'orderby'          => 'date',
									'order'            => 'DESC'
								] );
								$post_object                       = $ae_post_factory->get( PROJECT ); ?>

                                <div class="previous-employer-project">
									<?php if ( $employer_open_project_query->have_posts() ) {
										$postdata = [];
									while ( $employer_open_project_query->have_posts() ) {
										$employer_open_project_query->the_post();
										$convert    = $post_object->convert( $post, 'thumbnail' );
										$postdata[] = $convert;
										?>
                                        <div class="fre-profile-box">
                                            <div class="row">
                                                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                                                    <div class="project-title-col">
                                                        <span><?php _e( 'Project:', ET_DOMAIN ); ?></span>
                                                        <a href="<?php echo $convert->permalink; ?>">
															<?php echo $convert->post_title; ?>
                                                        </a>
                                                        <span class="status">
                                                    <?php echo $convert->project_status_view; ?>
                                                </span>
                                                    </div>
                                                    <div class="project-category-col"><?php list_tax_of_project( get_the_ID() ); ?></div>
                                                    <div class="project-txt-col"><?php echo $convert->post_content_trim; ?></div>
                                                </div>
                                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                                    <div class="project-open-col">
                                                        <span><?php _e( 'Date', ET_DOMAIN ); ?></span>
														<?php echo $convert->post_date; ?>
                                                    </div>
                                                    <div class="project-budget-col">
                                                        <span><?php _e( 'Budget', ET_DOMAIN ); ?></span>
														<?php echo $convert->budget; ?>
                                                    </div>
                                                    <div class="project-bids-col">
                                                        <span><?php _e( 'Bids', ET_DOMAIN ); ?></span>
														<?php echo $convert->total_bids; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php } ?>
                                        <script type="data/json" id="open_project_post_data">
                                        <?php echo json_encode( $postdata ); ?>














                                        </script>
									<?php } else {
										_e( 'No results', ET_DOMAIN );
									} ?>

									<?php ae_pagination( $employer_open_project_query, get_query_var( 'paged' ) ); ?>

									<?php wp_reset_postdata();
									wp_reset_query(); ?>
                                </div>
                            </div>

                            <div id="closed-project-tab" class="employer-previous-project-tab fre-panel-tab">
								<?php $employer_closed_project_query = new WP_Query( [
									'post_status'      => [ 'complete', 'close', 'disputing', 'archive' ],
									'is_author'        => true,
									'post_type'        => PROJECT,
									'author'           => $author_id,
									'suppress_filters' => true,
									'orderby'          => 'date',
									'order'            => 'DESC'
								] );
								$post_object                         = $ae_post_factory->get( PROJECT ); ?>

                                <div class="previous-employer-project">
									<?php if ( $employer_closed_project_query->have_posts() ) {
										$postdata = [];
									while ( $employer_closed_project_query->have_posts() ) {
										$employer_closed_project_query->the_post();
										$convert    = $post_object->convert( $post, 'thumbnail' );
										$postdata[] = $convert;
										?>
                                        <div class="fre-profile-box">
                                            <div class="row">
                                                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                                                    <div class="project-title-col">
                                                        <span><?php _e( 'Project:', ET_DOMAIN ); ?></span>
                                                        <a href="<?php echo $convert->permalink; ?>">
															<?php echo $convert->post_title; ?>
                                                        </a>
                                                        <span class="status">
                                                    <?php echo $convert->project_status_view; ?>
                                                </span>
                                                    </div>
                                                    <div class="project-category-col"><?php list_tax_of_project( get_the_ID() ); ?></div>
                                                    <div class="project-txt-col"><?php echo $convert->post_content_trim; ?></div>
                                                </div>
                                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                                    <div class="project-open-col">
                                                        <span><?php _e( 'Date', ET_DOMAIN ); ?></span>
														<?php echo $convert->post_date; ?>
                                                    </div>
                                                    <div class="project-budget-col">
                                                        <span><?php _e( 'Budget', ET_DOMAIN ); ?></span>
														<?php echo $convert->budget; ?>
                                                    </div>
                                                    <div class="project-bids-col">
                                                        <span><?php _e( 'Bids', ET_DOMAIN ); ?></span>
														<?php echo $convert->total_bids; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php } ?>
                                        <script type="data/json" id="previous_project_post_data">
                                        <?php echo json_encode( $postdata ); ?>









                                        </script>
									<?php } else {
										_e( 'No results', ET_DOMAIN );
									} ?>

									<?php ae_pagination( $employer_closed_project_query, get_query_var( 'paged' ) ); ?>

									<?php wp_reset_postdata();
									wp_reset_query(); ?>
                                </div>
                            </div>
                            <!--author--projects-->
                        </div>
                    </div>
				<?php } ?>
				<?php if ( $currentFreelancer ): ?>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
						<?php get_template_part( 'template/author', 'freelancer-history' );
						wp_reset_query(); ?>
                    </div>
				<?php endif; ?>
            </div>

        </div>
    </div>
<?php get_footer();