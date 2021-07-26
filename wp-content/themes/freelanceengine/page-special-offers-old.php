<?php
/**
 * Template Name: Special Offers OLD
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( et_get_page_link( 'login', [ 'ae_redirect_url' => get_permalink( $post->ID ) ] ) );
}

global $wpdb, $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user_role = ae_user_role( $user_ID );

define( 'NO_RESULT', __( '<span class="project-no-results">There are no activities yet.</span>', ET_DOMAIN ) );

if ( ! defined( 'FRE_ADVERT' ) ) {
	define( 'FRE_ADVERT', 'advert' );
}

get_header();
?>
    <div class="fre-page-wrapper">
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
                    <div id="current-project-tab" class="fre-work-project-box">
						<?php

						$freelancer_current_project_query = new WP_Query( [
							'post_status'      => [
								'publish',
							],
							'post_type'        => FRE_ADVERT,
							'suppress_filters' => false,
							'orderby'          => 'date',
							'order'            => 'DESC',
							'posts_per_page'   => 5,
							'paged'            => get_query_var( 'paged' ) ?: 1
						] );
						$no_result_current                = '';
						?>
                        <div class="current-freelance-project">
                            <div class="fre-table">
                                <div class="fre-current-table-rows" style="display: table-row-group;">
									<?php
									$postData = [];
									if ( $freelancer_current_project_query->have_posts() ) {
									while ( $freelancer_current_project_query->have_posts() ) {
										$freelancer_current_project_query->the_post();
										$postData[] = $post;
										$country    = ! empty( get_post_meta( $post->ID, 'country', true ) ) ? get_post_meta( $post->ID, 'country', true ) : '';
										$state      = ! empty( get_post_meta( $post->ID, 'state', true ) ) ? get_post_meta( $post->ID, 'state', true ) : '';
										$city       = ! empty( get_post_meta( $post->ID, 'city', true ) ) ? get_post_meta( $post->ID, 'city', true ) : '';

										$location = getLocation( 0, [
											'country' => $country,
											'state'   => $state,
											'city'    => $city
										] );
										if ( ! empty( $location['country'] ) ) {
											$str_location = [];
											foreach ( $location as $key => $item ) {
												if ( ! empty( $item['name'] ) ) {
													$str_location[] = $item['name'];
												}
											}
											$str_location = ! empty( $str_location ) ? implode( ' - ', $str_location ) : 'Error';
										} else {
											$str_location = '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
										}
										?>
                                        <div class="fre-table-row">
                                            <div class="fre-table-col advert-title-col">
                                                <a class="secondary-color"
                                                   href="<?php echo get_permalink(); ?>"><?php echo $post->post_title; ?></a>
												<?= $str_location ?>
                                            </div>
                                            <div class="fre-table-col advert-content-col">
												<?php if ( strlen( $post->post_content ) > 50 ) {
													echo substr( $post->post_content, 0, 49 ) . '...';
												} else {
													echo $post->post_content;
												}
												?>
                                            </div>
                                            <div class="fre-table-col advert-author-col">
												<? _e( 'Author' ); ?>:
                                                <a href="<?php echo get_author_posts_url( $post->post_author ); ?>"
                                                   class="">
													<?php echo get_the_author_meta( 'display_name', $post->post_author ); ?>
                                                </a>
                                            </div>
                                        </div>
									<?php } ?>
                                        <script type="data/json"
                                                id="current_project_post_data"><?php echo json_encode( $postData ); ?></script>
									<?php } else {
										$no_result_current = NO_RESULT;
									}
									?>
                                </div>
                            </div>
							<?php
							if ( $no_result_current != '' ) {
								echo $no_result_current;
							}
							?>
                        </div>
                    </div>
                    <div class="fre-paginations paginations-wrapper">
                        <div class="paginations">
							<?php
							ae_pagination( $freelancer_current_project_query, get_query_var( 'paged' ) ); ?>
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