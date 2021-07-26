<?php
/**
 * Template Name: Special Offers
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( et_get_page_link( 'login', [ 'ae_redirect_url' => get_permalink( $post->ID ) ] ) );
}

define( 'NO_RESULT', __( '<span class="project-no-results">There are no activities yet.</span>', ET_DOMAIN ) );

global $wp_query;
$loop      = new WP_Query( $wp_query->query );
$col_posts = $loop->found_posts;

get_header();

$userId = get_current_user_id();
?>
    <style>
        .section-archive-offer .offer-list-container {
            padding-left: 0;
        }

        .section-archive-offer .offer-item {
            list-style-type: none;
        }

        .section-archive-offer .offer_author {
            font-size: 14px;
        }
    </style>

<? if ( userHaveProStatus( $userId ) == false ) { ?>
    <p class="only-pro-warn__caption">
        Only for users with the PRO status
        <br>
        You can get the PRO status by <a href="/pro/">following the link</a></p>
<? } ?>

    <div class="fre-page-wrapper section-archive-offer <?= userHaveProStatus( $userId ) ? '' : 'only-pro-warn' ?>">

        <div class="fre-page-title">
            <div class="container">
                <h1>
					<?php echo _( 'Special offers' ); ?>
                </h1>
            </div>
        </div>
        <div class="fre-page-section">
            <div class="container">
				<?php if ( $user_role !== FREELANCER ) { ?>
                    <div class="page-project-list-wrap">
                        <div class="fre-project-list-wrap">
							<?php get_template_part( 'template/filter', 'offers' ); ?>
                            <div class="fre-offer-list-box">
                                <div class="fre-project-list-wrap">
                                    <div class="fre-profile-result-sort">
                                        <div class="row">

											<?php $found_posts = '<span class="found_post">' . $col_posts . '</span>';
											$plural            = sprintf( __( '%s offers', ET_DOMAIN ), $found_posts );
											$singular          = sprintf( __( '%s offer', ET_DOMAIN ), $found_posts ); ?>

                                            <div class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6 col-sm-6 col-sm-push-6 hidden-xs">
                                                <div id="profile_orderby" class="fre-profile-sort">
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6 col-sm-6 col-sm-pull-6 col-xs-12">
                                                <div class="fre-profile-result">
                                             <span class="plural <?php if ( $col_posts <= 1 ) {
	                                             echo 'hide';
                                             } ?>">
                                                    <?php echo $plural; ?>
                                                </span>
                                                    <div class="visible-xs">
                                                        <div id="profile_orderby" class="fre-profile-sort">
                                                            <span class="sort-label">
                                                    <?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                            <span class="option" id="hour_rate">
                                                    <?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                            <span class="option" id="rating">
                                                    <?php _e( 'Ranking', ET_DOMAIN ); ?></span>
                                                            <span class="option" id="projects_worked">
                                                    <?php _e( 'Projects', ET_DOMAIN ); ?></span>
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
									<?php get_template_part( 'list', 'offers' ); ?>
                                </div>
                            </div>
                            <div class="fre-paginations paginations-wrapper">
                                <div class="paginations">
									<?php
									ae_pagination( $loop, get_query_var( 'paged' ) );
									?>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
					wp_reset_postdata();
					wp_reset_query();
					?>
				<?php } ?>
            </div>
        </div>
    </div>
<?php get_footer();