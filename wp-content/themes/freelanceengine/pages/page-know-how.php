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


	$page_setting = wpp_pages_settins( 'page_know_how' );
?>

    <div class="fre-page-wrapper page-benefits page-pro-benefits">
        <div class="container">

            <div id="if-pro">

                <div class="plenty-jobs-wrap">
                    <div class="plenty-jobs-item">
                        <div class="plenty-jobs">
                            <div class="plenty-jobs__before"></div>
                            <div class="plenty-jobs__title">
								<?php wpp_setting( $page_setting, 'pro_plenty_jobs_header' ); ?>
                            </div>
                            <div class="plenty-jobs__text">
								<?php wpp_setting( $page_setting, 'pro_plenty_jobs_text' ); ?>
                            </div>
                            <div class="plenty-jobs__after"></div>
                        </div>
                    </div>
                </div>

                <div class="masterhand-professional">
                    <div class="row">

                        <div class="col-sm-6 col-xs-12">
                            <div class="masterhand-list-wrap pro-wrap">
								<?php wpp_setting( $page_setting, 'pro_text_left' ); ?>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xs-12 lh-text">

                            <div class="masterhan-create-wrap">
                                <div class="masterhan-create__title">
									<?php wpp_setting( $page_setting, 'pro_sign_up_header' ); ?>
                                </div>
                                <div class="masterhan-create__text">
									<?php wpp_setting( $page_setting, 'pro_sign_up_text' ); ?>
                                </div>
                            </div>

                            <div class="masterhan-create-wrap">
                                <div class="masterhan-create__title">
									<?php wpp_setting( $page_setting, 'pro_text_right_side_header' ); ?>
                                </div>
                                <div class="masterhan-create__text">
									<?php wpp_setting( $page_setting, 'pro_text_right_side' ); ?>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="masterhand-sign-up">

                    <div class="sign-up-wrap how">
                        <div class="sign-up__before"></div>
                        <div class="sign-up__title">
							<?php wpp_setting( $page_setting, 'pro_sign_up_bottom_header' ); ?>
                        </div>
                        <div class="sign-up__text">
							<?php wpp_setting( $page_setting, 'pro_sign_up_bottom_text' ); ?>
                        </div>
                        <div class="row">
                            <form id="wpp-send-post-form" enctype="multipart/form-data" >
                                <div class="col-md-6 col-xs-12">
                                    <div class="fre-input-field">
                                        <label for="" class="fre-field-title">
											<?php _e( 'Category', WPP_TEXT_DOMAIN ); ?>
                                        </label>
                                        <div class="select_style">
                                            <select name="" id="">
                                                <option value=""><?php _e( 'Please select a category..', WPP_TEXT_DOMAIN ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div id="media-uploader" class="dropzone  dz-clickable">
                                                <div class="dz-message" data-dz-message>
                                                    <span>
                                                        <?php _e( 'Select or drop files here to upload', WPP_TEXT_DOMAIN ); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="media-ids" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="fre-input-field">
                                        <label for="" class="fre-field-title">
											<?php _e( 'Message', WPP_TEXT_DOMAIN ); ?>
                                        </label>
                                        <div class="textarea_style">
                                            <div id="editor-container"> </div>
                                            <input name="message_text" type="hidden">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="fre-submit-btn">
			                            <?php _e( 'SUBMIT', WPP_TEXT_DOMAIN ); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
						<?php printf( '<div class="plenty-jobs__title">%s</div>', __( 'Your published articles:', WPP_TEXT_DOMAIN ) ); ?>

                        <div class="row">

								<?php
									if ( is_user_logged_in() ) {
										global $current_user;
										if ( ! is_object( $current_user ) ) {
											$current_user = wp_get_current_user();
										}
									}

									$args = [
										'show_posts'  => 3,
										'page'        => 1,
										'post_type'   => 'post',
										'author_name' => $current_user->user_login
									];

									$author_posts = get_posts( $args );

									foreach ( $author_posts as $post ) :
										setup_postdata( $post ); ?>

                                        <div class="col-md-6 col-xs-12">
                                            <a href="<?php the_permalink(); ?>" class="link-social" title="">
												<?php the_title(); ?>
                                            </a>
                                        </div>

                                        <div class="col-md-6 col-xs-12">
											<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
												ADDTOANY_SHARE_SAVE_KIT();
											} ?>
                                        </div>

									<?php endforeach;
								?>

                        </div>

                        <div class="sign-up__after"></div>

                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
	get_footer();