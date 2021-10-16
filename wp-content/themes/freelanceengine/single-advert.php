<?php
global $post, $user_ID, $authordata, $wpdb;
get_header();
?>
    <div class="fre-page-wrapper">
        <div class="container">
			<?
			if ( isset( $_GET['post_edit'] ) && $user_ID == $post->post_author ) {
				wp_enqueue_script( '', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true );
				?>
                <h2><?php _e( 'Editing advert' ); ?></h2>
                <div class="fre-post-project-box">
                    <form id="edit-advert" class="" role="form" method="POST" onsubmit="return false;">
                        <div class="fre-input-field">
                            <label class="fre-field-title"
                                   for="fre-project-title"><?php _e( 'Advert title', ET_DOMAIN ); ?></label>
                            <input type="hidden" name="post_id" value="<?php echo $post->ID ?>">
                            <input class="input-item text-field" id="fre-project-title" type="text"
                                   name="post_title" maxlength="100"
                                   value="<?php echo $post->post_title ?>">
                        </div>
                        <div class="fre-input-field">
                            <label class="fre-field-title"
                                   for="fre-project-describe"><?php _e( 'Description', ET_DOMAIN ); ?></label>
							<?php wp_editor( $post->post_content, 'post_content', ae_editor_settings() ); ?>
                        </div>
						<?
						$private_bid = getValueByProperty( get_user_pro_status( $user_ID ), 'examples_jobs_in_advert' );
						if ( $private_bid ) {
							?>
                            <div class="fre-input-field box_upload_img">
                                <p><?php _e( 'Work examples' ); ?></p>
                                <ul id="listImgPreviews" class="portfolio-thumbs-list row image">
									<?
									$attachments = $wpdb->get_results( "SELECT ID, guid FROM {$wpdb->prefix}posts WHERE post_parent = {$post->ID}" );
									if ( ! empty( $attachments ) ) {
										foreach ( $attachments as $attachment ) { ?>
                                            <li class="col-sm-3 col-xs-12 item"
                                                data-id="<?php echo $attachment->ID; ?>">
                                                <div class="portfolio-thumbs-wrap">
                                                    <div class="portfolio-thumbs img-wrap">
                                                        <img src="<?php echo $attachment->guid; ?>">
                                                    </div>
                                                    <div class="portfolio-thumbs-action delete-file">
                                                        <i class="fa fa-trash-o"></i>Remove
                                                    </div>
                                                </div>
                                            </li>
										<?php }
									}
									?>
                                </ul>
                                <div class="upfiles-container">
                                    <div class="fre-upload-file">
                                        Upload Files
                                        <input id="upfiles" type="file" multiple=""
                                               accept="image/jpeg,image/gif,image/png">
                                    </div>
                                </div>
                                <p class="fre-allow-upload">
									<?php _e( '(Maximum upload file size is limited to 2MB, maximum for 10 items, allowed file types in the png, jpg.)' ); ?>
                                </p>
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
                            </div>
						<?php } ?>
                        <div class="fre-input-field">
                            <label class="fre-field-title"
                                   for="project-location"><?php _e( 'Location', ET_DOMAIN ); ?></label>
                            <div>
								<?php
								$country = ! empty( get_post_meta( $post->ID, 'country', true ) ) ? get_post_meta( $post->ID, 'country', true ) : '';
								$state   = ! empty( get_post_meta( $post->ID, 'state', true ) ) ? get_post_meta( $post->ID, 'state', true ) : '';
								$city    = ! empty( get_post_meta( $post->ID, 'city', true ) ) ? get_post_meta( $post->ID, 'city', true ) : '';
								include 'dbConfig.php';
								$query_country = $db->query( "SELECT * FROM wp_location_countries ORDER BY name ASC" );
								?>
                                <div class="fre-input-field select">
                                    <select name="country" id="country"
                                            data-selected_id="<?php echo $country ?>">
                                        <option value="">Select Country</option>
										<?php if ( $query_country->num_rows > 0 ) {
											while ( $row = $query_country->fetch_assoc() ) {
												if ( ! empty( $country ) && $country == $row['id'] ) {
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
                                    <select name="state" id="state" data-selected_id="<?php echo $state ?>">
                                        <option value="">Select country first</option>
                                    </select>
                                </div>

                                <div class="fre-input-field select">
                                    <select name="city" id="city" data-selected_id="<?php echo $city ?>">
                                        <option value="">Select state first</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="fre-post-project-btn btn-grp-advert">
                            <button class="fre-btn fre-submit-btn btn-left fre-post-project-next-btn primary-bg-color"
                                    type="submit"><?php _e( "Save", ET_DOMAIN ); ?></button>
                            <a href="<?php echo get_permalink() ?>"
                               class="fre-cancel-btn fre-post-project-next-btn primary-bg-color"><?php _e( "Cancel", ET_DOMAIN ); ?></a>
                        </div>
                    </form>
                </div>
                <div id="itemPreviewTemplate" style="display: none;">
                    <li class="col-sm-3 col-xs-12 item">
                        <div class="portfolio-thumbs-wrap">
                            <div class="portfolio-thumbs img-wrap">
                                <img src="/">
                            </div>
                            <div class="portfolio-thumbs-action delete-file">
                                <i class="fa fa-trash-o"></i>Remove
                            </div>
                        </div>
                    </li>
                </div>
				<?
			} else {
				?>
                <div class="fre-page-title">
                    <h2><?php the_title(); ?></h2>
					<?php if ( $user_ID == $post->post_author && $post->post_status == 'publish' ) { ?><a
                            href="?post_edit"><?php _e( 'Edit' ) ?></a> <?php }
					?>
                </div>
                <div class="fre-page-section">
					<?
					echo $post->post_content;
					$attachments = $wpdb->get_results( "SELECT guid FROM {$wpdb->prefix}posts WHERE post_parent = {$post->ID}" );
					if ( ! empty( $attachments ) ) {
						?>
                        <br><br>
                        <p><?php _e( 'Work examples' ); ?></p>
                        <ul class="portfolio-thumbs-list row image">
							<?php foreach ( $attachments as $attachment ) { ?>
                                <li class="col-sm-3 col-xs-12 item">
                                    <div class="portfolio-thumbs-wrap">
                                        <img src="<?php echo $attachment->guid; ?>">
                                    </div>
                                </li>
							<?php } ?>
                        </ul>
					<?php } ?>
                    <br><br>
					<?php _e( 'Author' ) ?>: <a href="<?php echo get_author_posts_url( $post->post_author ); ?>">
						<?php echo get_the_author_meta( 'display_name', $post->post_author ); ?>
                    </a>, <?php echo get_the_date( 'd M Y', $post ); ?>
                </div>
				<?
			}
			?>
        </div>
    </div>
<?
get_footer();