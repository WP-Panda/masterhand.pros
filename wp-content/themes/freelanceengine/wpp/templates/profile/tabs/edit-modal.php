<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
$class           = fre_share_role() || wpp_fre_is_freelancer() ? 6 : 10;
$confirmed_email = ! empty( $user_confirm_email ) ? sprintf( ' <span>%s</span>', __( '(Confirmed email address)', WPP_TEXT_DOMAIN ) ) : '';
$confirmed_phone = ! empty( $user_phone ) ? sprintf( ' <span>%s</span>', __( '(Confirmed by sms)', WPP_TEXT_DOMAIN ) ) : '';
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

                            <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 employer-info-avatar avatar-profile-page">
                                <span class="employer-avatar img-avatar image"><?php echo get_avatar( $user_ID, 125 ) ?></span>
                                <a href="#" id="user_avatar_browse_button">
									<?php _e( 'Change Photo', WPP_TEXT_DOMAIN ) ?>
                                </a>
                            </div>

							<?php
							$default = [
								[
									'id'          => 'post_content',
									'label'       => __( 'About me', WPP_TEXT_DOMAIN ),
									'value'       => $about ?? '',
									'placeholder' => __( 'About me', WPP_TEXT_DOMAIN ),
									'wrap_class'  => sprintf( 'col-md-%1$s col-lg-%1$s col-sm-12 col-xs-12 fre-input-field', $class ),
									'type'        => 'editor'
								]
							];

							if ( fre_share_role() || wpp_fre_is_freelancer() ) :
								$default[] = [
									'id'          => 'hour_rate',
									'label'       => __( 'Rate', WPP_TEXT_DOMAIN ),
									'value'       => $hour_rate ?? '',
									'placeholder' => __( 'Your rate', WPP_TEXT_DOMAIN ),
									'wrap_class'  => 'col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-hourly-field',
									'label_class' => 'fre-field-title ratelbl',
									'type'        => 'number',
									'conditional' => [
										'compare' => 'or',
										'for'     => [
											'role',
											'freelancer'
										]
									]
								];
							endif;

							$default[] = [
								'id'         => 'clear_1',
								'type'       => 'clear',
								'wrap_class' => 'clearfix'
							];

							$default[] = [
								'id'          => 'display_name',
								'label'       => __( 'Name', WPP_TEXT_DOMAIN ),
								'value'       => $display_name ?? '',
								'placeholder' => __( 'Your name', WPP_TEXT_DOMAIN ),
							];

							$default[] = [
								'id'          => 'user_email',
								'label'       => __( 'Email', WPP_TEXT_DOMAIN ) . $confirmed_email,
								'value'       => $user_data->user_email ?? '',
								'placeholder' => __( 'Your email', WPP_TEXT_DOMAIN ),
							];


							$data = new WPP_Form_Constructor( $default );
							$data->parse_data();
							?>


                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                <label class="fre-field-title"><?php _e( 'Phone', ET_DOMAIN ); ?>
                                    <span><?php if ( $user_phone ) {
											_e( '(Confirmed by sms)', ET_DOMAIN );
										} ?></span></label>
                                <a href="#modal_change_phone" data-toggle="modal"
                                   data-dismiss="modal" class="change-phone"
                                   data-ctn_edit="ctn-edit-account" id="btn_edit">
									<?php echo ! empty( $user_phone_code . $user_phone ) ? $user_phone_code . $user_phone : _e( 'Edit phone', ET_DOMAIN ); ?>
                                </a>
                            </div>

                            <!--new start-->
							<?php wpp_get_template_part( 'inc/select-location-profile-edit', [ 'location' => $location ] ); ?>
                            <!--new end-->

                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field default-currency-wrap">

                                <label class="fre-field-title">
									<?php _e( 'Currency', ET_DOMAIN ); ?>
                                </label>

                                <select name="project_currency">

									<?php $selected_currency = get_user_meta( $user_ID, 'currency', true );

									foreach ( get_currency() as $key => $data ) {
										$is_selected  = '';
										$user_country = get_user_country();
										wpp_dump( $user_country );
										$user_country = $user_country['name'];

										if ( empty( $selected_currency ) ) {
											if ( $user_country == $data['country'] ) {
												$is_selected = 'selected';
											}
										} else {
											if ( $selected_currency == $data['code'] ) {
												$is_selected = 'selected';
											}
										} ?>
                                        <option data-icon="<?php echo $data['flag'] ?>" <?php echo $is_selected ?>>
											<?php echo $data['code'] ?>
                                        </option>
									<?php } ?>

                                </select>
                            </div>

							<?php if ( fre_share_role() || wpp_fre_is_freelancer() ) { ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-experience-field">
                                    <label class="fre-field-title"><?php _e( 'Years experience', ET_DOMAIN ); ?></label>
                                    <input type="number" value="<?php echo $experience; ?>"
                                           name="et_experience" id="et_experience" min="0"
                                           placeholder="<?php _e( 'Total', ET_DOMAIN ) ?>">
                                </div>
							<?php } ?>

                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                <label class="fre-field-title"><?php _e( 'Password', ET_DOMAIN ); ?></label>
                                <a href="#" class="change-password">
									<?php _e( '******', ET_DOMAIN ); ?>
                                </a>

								<?php if ( function_exists( 'fre_credit_add_request_secure_code' ) ) {
									$fre_credit_secure_code = ae_get_option( 'fre_credit_secure_code' );
									if ( ! empty( $fre_credit_secure_code ) ) {
										?>
                                        <ul class="fre-secure-code">
                                            <li>
                                                <span><?php _e( "Secure code", ET_DOMAIN ) ?></span>
                                            </li>
											<?php do_action( 'fre-profile-after-list-setting' ); ?>
                                        </ul>
									<?php }
								} ?>
                            </div>

							<?php if ( use_paypal_to_escrow() ) { ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                    <label class="fre-field-title"><?php _e( 'Paypal account', ET_DOMAIN ) ?></label>
                                    <input type="text"
                                           value="<?php echo $user_data->paypal ?>"
                                           name="user_paypal" id="user_paypal"
                                           placeholder="<?php _e( 'Your paypal login', ET_DOMAIN ) ?>">
                                </div>
							<?php }


							/**
							 * Вывод полей социальные сети
							 */
							$soc_data = apply_filters( 'wpp_social_fields_array', [] );
							$default  = [];

							if ( ! empty( $soc_data ) ) :
								foreach ( $soc_data as $one_field ) {
									$default[] = [
										'id'          => $one_field['id'],
										'label'       => $one_field['label'],
										'value'       => $user_data->{$one_field['id']} ?? '',
										'placeholder' => $one_field['placeholder']
									];
								}
							endif;


							if ( ! empty( $default ) ) :
								$data = new WPP_Form_Constructor( $default );
								$data->parse_data();
							endif;

							do_action( 'ae_edit_post_form', PROFILE, $profile );
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
                <!--edit--form-->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>