<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	extract( $args );
?>
<div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
    <div class="fre-profile-box">
        <div class="profile-freelance-info-wrap">
            <div class="profile-freelance-info row" id="cnt-profile-default">
                <div class="col-sm-2 col-xs-4 avatar_wp">
					<?php echo get_avatar( $user_data->ID, 145 ); ?>
                    <a href="#" id="user_avatar_browse_button" class="hidden-xs">
						<?php _e( 'Change Photo', ET_DOMAIN ) ?>
                    </a>
                </div>
                <div class="col-sm-6 col-md-7 col-lg-8 col-xs-8">
                    <div class="col-sm-8 col-xs-12 freelance-name">
						<?php echo $display_name ?>
                    </div>
                    <div class="col-sm-12 col-xs-12 freelance-profile-country">
						<?php if ( $location && ! empty( $location[ 'country' ] ) ) {
							$str = [];
							foreach ( $location as $key => $item ) {
								if ( ! empty( $item[ 'name' ] ) ) {
									$str[] = $item[ 'name' ];
								}
							}
							echo ! empty( $str ) ? implode( ' - ', $str ) : 'Error';
						} else {
							echo '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
						} ?>
                    </div>
                    <div class="col-sm-12 hidden-xs fre-jobs_txt">
						<?php $post = isset( $profile );
							if ( $post ) {
								setup_postdata( $profile );
								if ( ! empty( $profile_id ) ) {
									the_content();
								}
								wp_reset_postdata();
							} ?>
                    </div>
                </div>
                <div class="col-xs-12 visible-xs fre-jobs_txt">
					<?php if ( fre_share_role() || wpp_fre_is_freelancer() ) {
						if ( $hour_rate > 0 ) { ?>
                            <div class="rate visible-xs">
								<?php echo __( "Rate:", ET_DOMAIN ); ?>
                                <span><?php echo sprintf( __( '%s/hr ', ET_DOMAIN ), fre_price_format( $hour_rate ) ); ?></span>
                            </div>
							<?php
						}
					} ?>
                </div>
                <div class="col-sm-4 col-md-3 col-lg-2 col-xs-12">
                    <a href="#editprofile" data-toggle="modal"
                       class="fre-submit-btn employer-info-edit-btn btn-right">
						<?php _e( 'Edit', ET_DOMAIN ) ?>
                    </a>
					<?php if ( fre_share_role() || wpp_fre_is_freelancer() ) { ?>
						<?php if ( $hour_rate > 0 ) { ?>
                            <div class="rate hidden-xs">
								<?php echo __( "Rate:", ET_DOMAIN ); ?>
                                <span><?php echo sprintf( __( '%s/hr ', ET_DOMAIN ), fre_price_format( $hour_rate ) ); ?></span>
                            </div>
						<?php }
					} ?>
                </div>
            </div>

            <div class="row secure-bl">
                <div class="col-sm-12 col-md-6 col-lg-5 col-xs-12">
                    <div class="cnt-profile-hide" id="cnt-account-default" style="display: block">
                        <p>
							<?php _e( 'Email:', ET_DOMAIN ) ?>
                            <span><?php echo $user_data->user_email; ?></span></p>
						<?php if ( ( ! empty( $user_confirm_email ) && $user_confirm_email !== 'confirm' ) || ( empty( $user_confirm_email ) ) ) { ?>
                            <span class="not-confirm"><?php echo __( 'Not confirmed', ET_DOMAIN ); ?>
                                <i>X</i></span>
						<?php } else { ?>
                            <span class="confirm"><?php echo __( 'Confirmed', ET_DOMAIN ); ?> <i
                                        class="fa fa-check"></i></span>
						<?php } ?>
                        <script>
                            function confirm_email_again($type) {
                                if ($type) {
                                    document.getElementById('user_email').value = '<?php echo trim( $user_data->user_new_email ) ?>'
                                }
                                document.getElementById('account_form_submit').click()
                            }
                        </script>
                        <div>
							<?php
								if ( $user_data->user_new_email ) {
									printf( __( '<p class="noti-update">There is a pending change of the email to %1$s.</p>', ET_DOMAIN ), '<code>' . $user_data->user_new_email . '</code>', esc_url( et_get_page_link( "profile" ) . '?dismiss=new_email' ) );
								} ?>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_change_phone"
                         style="background:rgba(0,0,0,.45);">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close"
                                            data-dismiss="modal"></button>
                                    <div class="modal_t"><?php _e( "Update your phone number", ET_DOMAIN ) ?></div>
                                </div>
                                <div class="modal-body phone_form">
                                    <div class="profile-employer-secure-edit" id="ctn-edit-account"
                                         style="display:block;">
                                        <form role="form" id="account_form_phone"
                                              class="account_form fre-modal-form auth-form chane_phone_form">
                                            <div class="fre-input-field">
                                                <input type="number" class="user_phone"
                                                       id="user_phone"
                                                       name="user_phone"
                                                       value="<?php echo $user_phone ?>"
                                                       placeholder="<?php _e( 'XXXXXXXXXX', ET_DOMAIN ) ?>">

                                            </div>
                                            <div class="fre-form-btn">
                                                <input type="submit"
                                                       class="btn-left fre-submit-btn fre-btn save-btn phone_up"
                                                       id="account_form_submit"
                                                       value="<?php _e( 'SAVE', ET_DOMAIN ) ?>">
                                                <a href="#editprofile" data-toggle="modal"
                                                   class="fre-cancel-btn employer-info-cancel-btn"
                                                   data-dismiss="modal">
													<?php _e( 'Cancel', ET_DOMAIN ) ?>
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <script>
                        jQuery(function ($) {
                            var page = document.getElementsByClassName('page-id-9')
                            if (page.length !== 0) {
                                var phone_code = document.getElementsByName('ihs-country-code')
                                var phone = document.getElementById('user_phone')
                                phone_code[0].value = '<?=$user_phone_code?>'
                                phone_code[0].parentElement.style.width = "9rem"
                                phone.style.width = "calc(100% - 9rem)"
                            }
                            $('#ihs-mobile-otp1').attr('placeholder', 'Enter Verification Code')
                        })
                    </script>

                    <div class="phone-secure">
                        <p>
							<?php _e( "Phone", ET_DOMAIN );
								echo '<span>' . $user_phone_code . $user_phone . '</span>'; ?></p>
						<?php if ( $user_phone ) {
							echo '<span class="confirm">' . __( 'Confirmed', ET_DOMAIN ) . '<i class="fa fa-check"></i></span>'; ?>
						<?php } else {
							echo '<span class="not-confirm">' . __( 'Not confirmed', ET_DOMAIN ) . '<i>X</i></span>'; ?>
						<?php } ?>
                    </div>

					<?php
						$paypal_confirmation = get_user_meta( $user_ID, 'paypal_confirmation', true );
						$paypal              = get_user_meta( $user_ID, 'paypal', true );
						if ( use_paypal_to_escrow() ) { ?>
                            <p style="position: relative;">
								<?php _e( 'Paypal account email', ET_DOMAIN ) ?>

								<?php

									if ( ! empty( $paypal ) ) {
										echo '<span class="paypal_account_field"><span>' . $paypal . '</span></span>';
										?>
										<?php if ( $paypal_confirmation ) {
											echo '<span class="confirm">' . __( 'Confirmed', ET_DOMAIN ) . '<i class="fa fa-check"></i></span>'; ?>
										<?php } else {
											echo '<span class="not-confirm">' . __( 'Not confirmed', ET_DOMAIN ) . '<i>X</i></span>'; ?>
										<?php }
										if ( ! $paypal_confirmation ) {
											echo "<div style='color:red;'>The paypal account must be confirmed to make deals</div>";
											echo "<div style='color:red;'>To confirm your paypal account, we will get 1$ and then return it back</div>";
										}
									} else { ?>
                                        <span class="freelance-empty-info"><?php _e( 'Not updated', ET_DOMAIN ) ?></span>
									<?php } ?>
                            </p>
                            <style>
                                .paypal_account_field span {
                                    white-space: nowrap;
                                    width: 100px;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    display: inline-block;
                                    vertical-align: top;
                                    padding-left: 10px;
                                    padding-right: 0;
                                }
                            </style>
						<?php } ?>

                    <div class="confirm-btns">
						<?php if ( ! $paypal_confirmation && ! empty( $paypal ) ): ?>
                            <a href="javascript:;"
                               class="btn-left fre-submit-btn confrim_paypal_account">
								<?php _e( "Confirm paypal account", ET_DOMAIN ) ?>
                            </a>
						<?php endif; ?>
                        <a href="#modal_change_phone" data-toggle="modal"
                           class="btn-left fre-submit-btn change-phone">
							<?php _e( "Confirm by sms", ET_DOMAIN ) ?>
                        </a>

						<?php

						?>

						<?php if ( ! empty( $user_confirm_email ) && $user_confirm_email !== 'confirm' ) { // когда пустое поле мета ничего не происходит
							printf( __( '<a class="request-confirm fre-submit-btn btn-right">Confirm E-mail</a>', ET_DOMAIN ), '<code>' . esc_html( $user_data->user_new_email ) . '</code>' );
						} elseif ( ! $user_confirm_email ) {
							if ( ! $user_data->user_email ) {
								printf( __( '<p class="noti-update">You must add an email</p>', ET_DOMAIN ) );
							} else {
								printf( __( '<a class="request-confirm btn-right fre-submit-btn">Confirm E-mail</a>', ET_DOMAIN ), '<code>' . esc_html( $user_data->user_new_email ) . '</code>' );
							}
						} ?>
                    </div>

					<?php //stripe
						$escrow_stripe_api = ae_get_option( 'escrow_stripe_api', false );
						$use_escrow        = ae_get_option( 'use_escrow', false );
						if ( ! empty( $escrow_stripe_api ) && $use_escrow && function_exists( 'ae_stripe_recipient_field' ) ) {
							if ( ! empty( $escrow_stripe_api[ 'use_stripe_escrow' ] ) ) { ?>
                                <div class="stripe-connect__wrap">
                                    <p><?php _e( "Stripe account", ET_DOMAIN ) ?></p>

                                    <div class="stripe-connect__btns-wrap">
										<?php do_action( 'ae_escrow_stripe_user_field' ); ?>
                                    </div>
                                </div>
							<?php }
						} ?>


                </div>


				<?php wpp_get_template_part( 'wpp/templates/profile/social-links', [ 'profile_id' => $profile_id ] ); ?>
            </div>
            <!--soc inf + email verf -->

        </div>

        <!--edit-info--modal-->
        <div class="modal fade" id="editprofile" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
						<?php _e( 'My settings', ET_DOMAIN ) ?>
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="profile-employer-info-edit cnt-profile-hide"
                             id="ctn-edit-profile">
                            <div class="fre-employer-info-form" id="accordion" role="tablist"
                                 aria-multiselectable="true">
                                <form id="profile_form" class="row form-detail-profile-page"
                                      action=""
                                      method="post" novalidate>
                                    <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 employer-info-avatar avatar-profile-page">
                                        <span class="employer-avatar img-avatar image"><?php echo get_avatar( $user_ID, 125 ) ?></span>
                                        <a href="#" id="user_avatar_browse_button">
											<?php _e( 'Change Photo', ET_DOMAIN ) ?>
                                        </a>
                                    </div>
                                    <div class=" <?php if ( fre_share_role() || $user_role == FREELANCER ) { ?> col-md-8 col-lg-6 <?php } else { ?> col-md-10 col-lg-10 <?php } ?> col-sm-12 col-xs-12 fre-input-field">
                                        <label class="fre-field-title">About me</label>
										<?php

											wp_editor( $about, 'post_content', ae_editor_settings() );
										?>
                                    </div>
									<?php if ( fre_share_role() || $user_role == FREELANCER ) { ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-hourly-field">
                                            <label class="fre-field-title ratelbl"><?php _e( 'Rate', ET_DOMAIN ) ?></label>
                                            <input type="number" <?php if ( $hour_rate ) {
												echo "value= $hour_rate ";
											} ?> name="hour_rate" id="hour_rate" step="5" min="0"
                                                   placeholder="<?php _e( 'Your rate', ET_DOMAIN ) ?>">
                                        </div>
									<?php } ?>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                        <label class="fre-field-title"><?php _e( 'Name', ET_DOMAIN ); ?></label>
                                        <input type="text" value="<?php echo $display_name ?>"
                                               name="display_name" id="display_name"
                                               placeholder="<?php _e( 'Your name', ET_DOMAIN ) ?>">
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                        <label class="fre-field-title"><?php _e( 'Email', ET_DOMAIN ); ?>
                                            <span><?php if ( ! empty( $user_confirm_email ) ) {
													_e( '(Confirmed email address)', ET_DOMAIN );
												} ?></span></label>
                                        <input type="text"
                                               value="<?php echo $user_data->user_email ?>"
                                               name="user_email" id="user_email"
                                               placeholder="<?php _e( 'Your email', ET_DOMAIN ) ?>">
                                    </div>
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
									<?php include_once 'inc/select-location-profile-edit.php'; ?>
                                    <!--new end-->

                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field default-currency-wrap">
                                        <label class="fre-field-title">
											<?php _e( 'Currency', ET_DOMAIN ); ?>
                                        </label>

                                        <select name="project_currency">
											<?php
												$selected_currency = get_user_meta( $user_ID, 'currency', true );

												foreach ( get_currency() as $key => $data ) {
													$is_selected  = '';
													$user_country = get_user_country();
													$user_country = $user_country[ 'name' ];

													if ( empty( $selected_currency ) ) {
														if ( $user_country == $data[ 'country' ] ) {
															$is_selected = 'selected';
														}
													} else {
														if ( $selected_currency == $data[ 'code' ] ) {
															$is_selected = 'selected';
														}
													} ?>
                                                    <option data-icon="<?php echo $data[ 'flag' ] ?>" <?php echo $is_selected ?>>
														<?php echo $data[ 'code' ] ?>
                                                    </option>
												<?php }
											?>
                                        </select>
                                    </div>

									<?php if ( fre_share_role() || $user_role == FREELANCER ) { ?>
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
									<?php } ?>
									<?php do_action( 'ae_edit_post_form', PROFILE, $profile ); ?>
									<?php if ( $visualFlag ) { ?>
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
									<?php if ( $user_role == FREELANCER ) { ?>
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
        <!--edit-info--modal-->
    </div>
    <!--second--info-->
	<?php
		if ( wpp_fre_is_freelancer() && ! empty( $profile_id ) ) { ?>
            <div class="skills skills2 fre-profile-box">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                        <div class="freelance-portfolio-title">
							<?php echo __( "Specializations:", ET_DOMAIN ); ?>
                        </div>
                        <div class="skill-list">
							<?php if ( isset( $profile->tax_input[ 'project_category' ] ) && $profile->tax_input[ 'project_category' ] ) {
								echo baskserg_profile_categories4( $profile->tax_input[ 'project_category' ] );
							} else {
								echo '<span>No Specializations</span>';
							} ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <a href='#editcategory' data-toggle="modal"
                           class='fre-submit-btn btn-right'>
							<?php _e( 'Add category', ET_DOMAIN ) ?>
                        </a>
						<?php get_template_part( 'template-js/modal', 'profile-specialisation' ); ?>
                    </div>
                </div>
            </div>

			<?php
			wpp_get_template_part( 'wpp/templates/profile/lists/portfolios' );
			wpp_get_template_part( 'wpp/templates/profile/lists/documents' );
			wp_reset_query();
			if ( ! $is_company ) {
				?>
                <div class="fre-profile-box">
					<?php get_template_part( 'list', 'experiences' ); ?>
                </div>
			<?php }
			get_template_part( 'list', 'certifications' );
			if ( ! $is_company ) { ?>
                <div class="fre-profile-box">
					<?php get_template_part( 'list', 'educations' );
						wp_reset_query(); ?>
                </div>
			<?php }
		} ?>
</div><!--tab-settings-->
