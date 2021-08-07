<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
$args['class']           = fre_share_role() || wpp_fre_is_freelancer() ? 6 : 10;
$args['confirmed_email'] = ! empty( $user_confirm_email ) ? sprintf( ' <span>%s</span>', __( '(Confirmed email address)', WPP_TEXT_DOMAIN ) ) : '';
$args['confirmed_phone'] = ! empty( $user_phone ) ? sprintf( ' <span>%s</span>', __( '(Confirmed by sms)', WPP_TEXT_DOMAIN ) ) : '';

?>
<div class="modal fade" id="editprofile" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

			<?php wpp_get_template_part( 'wpp/templates/modal-elements/modal-header', [ 'text' => __( 'My settings', WPP_TEXT_DOMAIN ) ] ); ?>

            <div class="modal-body">
                <div class="profile-employer-info-edit cnt-profile-hide" id="ctn-edit-profile">
                    <div class="fre-employer-info-form" id="accordion" role="tablist" aria-multiselectable="true">

                        <form id="profile_form" class="row form-detail-profile-page" method="post" novalidate>


							<?php

							wpp_get_template_part( 'wpp/templates/profile/form/avatar', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/top-section', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/phone', $args );
							wpp_get_template_part( 'inc/select-location-profile-edit', [ 'location' => $location ] );
							wpp_get_template_part( 'wpp/templates/profile/form/currency', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/experience', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/pass', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/pay-pall', $args );
							wpp_get_template_part( 'wpp/templates/profile/form/social', $args );


							//do_action( 'ae_edit_post_form', PROFILE, $profile );

							if ( $visualFlag ) { ?>
                                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                    <label class="fre-field-title"><?php _e( 'Choose your level', ET_DOMAIN ); ?></label>
                                    <div class="fre-radio-container">
                                        <label class="fre-radio" for="flag_no">
                                            <input id="flag_no" type="radio"
                                                   name="visual_flag"
                                                   value="0" <?php checked( $visualFlagNumber, '' ); ?>><span></span>
											<?php _e( 'No', ET_DOMAIN ) ?>
                                        </label>
                                        <label class="fre-radio" for="flag_master">
                                            <input id="flag_master" type="radio"
                                                   name="visual_flag"
                                                   value="1" <?php checked( $visualFlagNumber, 1 ); ?>><span></span>
											<?php _e( 'Master', ET_DOMAIN ) ?>
                                        </label>
                                        <label class="fre-radio" for="flag_creator">
                                            <input id="flag_creator" type="radio"
                                                   name="visual_flag"
                                                   value="2" <?php checked( $visualFlagNumber, 2 ); ?> ><span></span>
											<?php _e( 'Creator', ET_DOMAIN ) ?>
                                        </label>
                                        <label class="fre-radio" for="flag_expert">
                                            <input id="flag_expert" type="radio"
                                                   name="visual_flag"
                                                   value="3" <?php checked( $visualFlagNumber, 3 ); ?> ><span></span>
											<?php _e( 'Expert', ET_DOMAIN ) ?>
                                        </label>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( wpp_fre_is_freelancer() ) { ?>
                                <div class="col-sm-12 col-xs-12 fre-input-field">
									<?php $email_skill = isset( $profile->email_skill ) ? (int) $profile->email_skill : 1; ?>
                                    <label class="checkline" for="email-skill">
                                        <input id="email-skill" type="checkbox"
                                               name="email_skill"
                                               value="1" <?php checked( $email_skill, 1 ); ?> >
                                        <span class="<?php echo( $email_skill ? 'active' : '' ) ?>"><?php _e( 'Email me jobs that are relevant to my skills', ET_DOMAIN ) ?></span>
                                    </label>
                                </div>
							<?php } ?>
                            <div class="col-sm-12 col-xs-12 fre-input-field">
								<?php $installmentPlan = isset( $profile->installmentPlan ) ? (int) $profile->installmentPlan : 1; ?>
                                <label class="checkline" for="installmentPlan">
                                    <input id="installmentPlan" type="checkbox"
                                           name="installmentPlan"
                                           value="1" <?php checked( $installmentPlan, 1 ); ?> >
                                    <span class="<?php echo( $installmentPlan ? 'active' : '' ) ?>"><?php _e( 'Trusted Partner program participation', ET_DOMAIN ) ?></span>
                                </label>
                            </div>
							<?php if ( $personal_cover ) { ?>
                                <div class="col-sm-12 col-xs-12 text-center">
                                    <label class="fre-field-title"><?php _e( 'Personal cover', ET_DOMAIN ); ?>
                                        <span><?php _e( '(Max upload file size 2MB, allowed file types png, jpg)', ET_DOMAIN ); ?></span>
                                    </label>
                                    <div class="box_upload_img">
                                        <ul id="listImgPreviews"
                                            class="portfolio-thumbs-list row image">
											<?
											$attachment = $wpdb->get_row( "SELECT ID, guid FROM {$wpdb->prefix}posts WHERE post_parent = {$profile->ID} AND post_type='attachment'" );
											if ( ! empty( $attachment ) ) {
												?>
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
											<?php } ?>
                                        </ul>
                                        <div class="upfiles-container">
                                            <div class="fre-upload-file">
                                                Upload Files
                                                <input id="upfiles" type="file" multiple=""
                                                       accept="image/jpeg,image/gif,image/png,application/pdf,application/doc,application/exel">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="itemPreviewTemplate" style="display: none;">
                                    <li class="col-sm-3 col-xs-12 item">
                                        <div class="portfolio-thumbs-wrap">
                                            <div class="portfolio-thumbs img-wrap">
                                                <div class="portfolio-thumbs_file-name"></div>
                                                <img src="">
                                            </div>
                                            <div class="portfolio-thumbs-action delete-file">
                                                <i class="fa fa-trash-o"></i>Remove
                                            </div>
                                        </div>
                                    </li>
                                </div>
								<?php wp_enqueue_script( 'ad-freelancer', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true ); ?>
							<?php } ?>

                            <div class="col-sm-12 col-xs-12 employer-info-save btn-update-profile">
                                <input type="submit"
                                       class="btn-left fre-submit-btn btn-submit"
                                       value="<?php _e( 'Save', ET_DOMAIN ) ?>">
                                <span class="employer-info-cancel-btn fre-cancel-btn"
                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>