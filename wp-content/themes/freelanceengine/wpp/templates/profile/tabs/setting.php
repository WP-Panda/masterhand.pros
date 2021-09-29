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

						<?php if ( $location && ! empty( $location['country'] ) ) {
							$str = [];
							foreach ( $location as $key => $item ) {
								if ( ! empty( $item['name'] ) ) {
									$str[] = $item['name'];
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
                                if (!empty($type)) {
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
						if ( ! empty( $escrow_stripe_api['use_stripe_escrow'] ) ) { ?>
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


        </div>

		<?php
		wpp_get_template_part( 'wpp/templates/profile/tabs/edit-modal',
			$args ); ?>

    </div>

	<?php
	if ( wpp_fre_is_freelancer() && ! empty( $profile_id ) ) { ?>
        <div class="skills skills2 fre-profile-box">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="freelance-portfolio-title">
						<?php echo __( "Specializations:", ET_DOMAIN ); ?>
                    </div>
                    <div class="skill-list">
						<?php if ( isset( $profile->tax_input['project_category'] ) && $profile->tax_input['project_category'] ) {
							echo baskserg_profile_categories4( $profile->tax_input['project_category'] );
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
