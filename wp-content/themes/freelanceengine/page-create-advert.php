<?php
/**
 * Template Name: Сreate Special Offer
 */

global $wpdb, $wp_query, $ae_post_factory, $post, $current_user, $user_ID;

if ( ! is_user_logged_in() ) {
	wp_redirect( et_get_page_link( 'login', [ 'ae_redirect_url' => get_permalink( $post->ID ) ] ) );
}

$user_role = ae_user_role( $user_ID );
if ( $user_role != FREELANCER ) {
	wp_redirect( home_url( '/' ) );
}

get_header();

$statusUserPro = get_user_pro_status( $user_ID );
$access_advert = (int) getValueByProperty( $statusUserPro, 'access_advert' );
$limitAdverts  = ( $access_advert ) ? $access_advert : 0;
//$limitAdverts = ($statusUserPro == 2)? 3 : (($statusUserPro == 3)? 25 : 0);
?>
    <div class="fre-page-wrapper">
        <div class="fre-page-title">
            <div class="container">
                <h1><?php the_title(); ?></h1>
            </div>
        </div>

        <div class="fre-page-section">
            <div class="container">
				<? if ( $limitAdverts == 0 ) { ?>
                    <div class="fre-post-project-box">
                        <a href="/pro"
                           class="go-to-pro-account"><? _e( 'Activate Account Pro for Creating Special Offers' ) ?></a>
                    </div>
				<? } else { ?>
                    <div class="fre-post-project-box">
                    <div>
                        <p><i class="fa fa-check primary-color"
                              aria-hidden="true"></i>&nbsp;<?php _e( 'Your plan includes limited number of Special Offers per month.', ET_DOMAIN ); ?>
                        </p>
                        <p>
							<?php
							$advertPerMonth = get_total_adverts_per_month_user( $user_ID );

							$totalLeft = $limitAdverts - $advertPerMonth;

							printf( __( 'You have <span class="post-number">%s</span> ad(s) left on this month.', ET_DOMAIN ), $totalLeft );
							?>
                        </p>
                    </div>
					<?php
					if ( $totalLeft > 0 ) {
						?>
                        <form id="create-ad" class="" role="form" method="POST" onsubmit="return false;">
                            <div class="fre-input-field">
                                <label class="fre-field-title"
                                       for="fre-project-title"><?php _e( 'Title', ET_DOMAIN ); ?></label>
                                <input class="input-item text-field" id="fre-project-title" type="text"
                                       name="post_title"
                                       maxlength="100">
                            </div>
                            <div class="fre-input-field">
                                <label class="fre-field-title"
                                       for="fre-project-describe"><?php _e( 'Description', ET_DOMAIN ); ?></label>
								<?php wp_editor( '', 'post_content', ae_editor_settings() ); ?>
                            </div>
							<?
							$private_bid = getValueByProperty( get_user_pro_status( $user_ID ), 'examples_jobs_in_advert' );
							if ( $private_bid ) {
								?>
                                <div class="fre-input-field box_upload_img">
                                    <p><? _e( 'Work examples' ); ?></p>
                                    <ul id="listImgPreviews" class="portfolio-thumbs-list row image">
                                    </ul>
                                    <div class="upfiles-container">
                                        <div class="fre-upload-file">
                                            Upload Files
                                            <input id="upfiles" type="file" multiple=""
                                                   accept="image/jpeg,image/gif,image/png">
                                        </div>
                                    </div>
                                    <p class="fre-allow-upload">
										<? _e( '(Maximum upload file size is limited to 2MB, maximum for 10 items, allowed file types in the png, jpg.)' ); ?>
                                    </p>
                                </div>
							<? } ?>
                            <div class="fre-input-field">
                                <label class="fre-field-title"
                                       for="project-location"><?php _e( 'Location', ET_DOMAIN ); ?></label>
                                <div>
									<?php
									$location = getLocation( $user_ID );
									include 'dbConfig.php';
									$query_country = $db->query( "SELECT * FROM wp_location_countries ORDER BY name ASC" );
									?>
                                    <div class="fre-input-field select">
                                        <select name="country" id="country"
                                                data-selected_id="<?= ! empty( $location['country']['id'] ) ? $location['country']['id'] : '' ?>">
                                            <option value="">Select Country</option>
											<?php if ( $query_country->num_rows > 0 ) {
												while ( $row = $query_country->fetch_assoc() ) {
													if ( ! empty( $location['country'] ) && $location['country']['id'] == $row['id'] ) {
														$flag = 'selected';
													} else {
														$flag = '';
													}
													echo '<option value="' . $row['id'] . '"' . $flag . '>' . $row['name'] . '</option>';
												}
											} else {
												echo '<option value="">Country not available</option>';
											} ?>
                                        </select>
                                    </div>

                                    <div class="fre-input-field select">
                                        <select name="state" id="state"
                                                data-selected_id="<?= ! empty( $location['state']['id'] ) ? $location['state']['id'] : '' ?>">
                                            <option value="">Select country first</option>
                                        </select>
                                    </div>

                                    <div class="fre-input-field select">
                                        <select name="city" id="city"
                                                data-selected_id="<?= ! empty( $location['city']['id'] ) ? $location['city']['id'] : '' ?>">
                                            <option value="">Select state first</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="fre-post-project-btn">
                                <button class="fre-submit-btn btn-left fre-post-project-next-btn btn-crt-advert"
                                        type="submit"><?php _e( "Submit", ET_DOMAIN ); ?></button>
                                <button class="fre-cancel-btn" onclick="history.back(-1)"
                                        type="cancel"><?php _e( "Cancel", ET_DOMAIN ); ?></button>
                            </div>
                        </form>
                        </div>
                        <style>
                            .upfiles-container {
                                position: relative;
                                height: 50px;
                            }

                            .fre-upload-file {
                                position: absolute;
                                top: 0px;
                                left: 0px;
                                width: 100%;
                                height: 44px;
                                overflow: hidden;
                            }

                            #upfiles {
                                cursor: pointer;
                                display: block;
                                font-size: 999px;
                                opacity: 0;
                                position: absolute;
                                top: 0px;
                                left: 0px;
                                width: 100%;
                                height: 100%;
                            }

                            .fre-allow-upload {
                                text-align: center;
                            }

                            .delete-file {
                                cursor: pointer;
                            }
                        </style>
						<?php
						wp_enqueue_script( '', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true );
					}
				}
				?>        </div>
        </div>
    </div>
    <div id="itemPreviewTemplate" style="display: none;">
        <li class="col-sm-3 col-xs-12 item">
            <div class="portfolio-thumbs-wrap">
                <div class="portfolio-thumbs img-wrap">
                    <img src="">
                </div>
                <div class="portfolio-thumbs-action delete-file">
                    <i class="fa fa-trash-o"></i>Remove
                </div>
            </div>
        </li>
    </div>
<?
get_footer();