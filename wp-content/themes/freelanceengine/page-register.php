<?php
	/**
	 * Template Name: Register Page Template
	 */
	global $post;
	get_header();
	$code = ! empty( $_REQUEST[ 'code' ] ) ? $_REQUEST[ 'code' ] : null;
	if ( ! isset( $_REQUEST[ 'role' ] ) ) {
		?>
        <div class="fre-page-wrapper">
            <div class="fre-page-section">
                <div class="container">
                    <div class="fre-authen-wrapper">
                        <div class="fre-register-default">
                            <h1>
								<?php _e( 'Sign Up Free Account', ET_DOMAIN ) ?>
                            </h1>
                            <div class="fre-register-wrap">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12">
                                        <h3>
											<?php _e( 'Client', ET_DOMAIN ); ?>
                                        </h3>
                                        <p>
											<?php _e( 'Post project, find professionals and hire favorite to work.', ET_DOMAIN ); ?>
                                        </p>
                                        <a class="fre-submit-btn"
                                           href="<?php echo bloginfo( 'site_url' ); ?>/register/?role=client<?= ! empty( $code ) ? '&code=' . $code : '' ?>">
											<?php _e( 'Sign Up', ET_DOMAIN ); ?>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <h3>
											<?php _e( 'Professional', ET_DOMAIN ); ?>
                                        </h3>
                                        <p>
											<?php _e( 'Create professional profile and find jobs to work.', ET_DOMAIN ); ?>
                                        </p>
                                        <a class="fre-submit-btn"
                                           href="<?php echo bloginfo( 'site_url' ); ?>/register/?role=professional<?= ! empty( $code ) ? '&code=' . $code : '' ?>">
											<?php _e( 'Sign Up', ET_DOMAIN ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<?php
								if ( fre_check_register() && function_exists( 'ae_render_social_button' ) ) { ?>
                                    <!--<div class="fre-authen-footer">
                                <?php $before_string = __( "You can use social account to login", ET_DOMAIN );
										ae_render_social_button( [], [], $before_string ); ?>
                            </div>-->
								<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	} else {
		if ( $_REQUEST[ 'role' ] == 'client' ) {
			$role = EMPLOYER;
		}
		if ( $_REQUEST[ 'role' ] == 'professional' ) {
			$role = FREELANCER;
		}
		if ( ! empty( $_REQUEST[ 'code' ] ) ) {
			$code = $_REQUEST[ 'code' ];
		}
		$re_url = '';
		if ( isset( $_GET[ 'ae_redirect_url' ] ) ) {
			$re_url = $_GET[ 'ae_redirect_url' ];
		}
		?>
        <div class="fre-page-wrapper">
            <div class="fre-page-section">
                <div class="container">
                    <div class="fre-authen-wrapper">
                        <div class="fre-authen-register">
                            <h1>
								<?php if ( $role == EMPLOYER ) {
									_e( 'Sign Up Client Account', ET_DOMAIN );
								} else {
									_e( 'Sign Up Professional Account', ET_DOMAIN );
								} ?>
                            </h1>
                            <form role="form" id="signup_form" class="signup_form">
                                <input type="hidden" name="ae_redirect_url" value="<?php echo $re_url ?>"/>
                                <input type="hidden" name="role" id="role" value="<?php echo $role; ?>"/>
                                <input type="hidden" name="code" id="code" value="<?php echo $code; ?>"/>
                                <div class="fre-input-field prof_type">
                                    <label><?php _e( '', ET_DOMAIN ) ?></label>
                                    <span><input type="radio" name="type_prof" value="profession"
                                                 checked>Individual Name</span>
                                    <span><input type="radio" name="type_prof" value="company">Company Name</span>
                                </div>
                                <div class="fre-input-field" style="display: none">
                                    <label><?php _e( 'Company Name', ET_DOMAIN ) ?></label>
                                    <input type="text" name="company_name" class="need_valid" id="company_name"
                                           placeholder="<?php _e( 'Company Name', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'First Name', ET_DOMAIN ) ?></label>
                                    <input type="text" name="first_name" class="need_valid" id="first_name"
                                           placeholder="<?php _e( 'First Name', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Last Name', ET_DOMAIN ) ?></label>
                                    <input type="text" name="last_name" id="last_name" class="need_valid"
                                           placeholder="<?php _e( 'Last Name', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Email', ET_DOMAIN ) ?></label>
                                    <input type="text" name="user_email" id="user_email" class="need_valid"
                                           placeholder="<?php _e( 'Email', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Username', ET_DOMAIN ) ?></label>
                                    <input type="text" name="user_login" id="user_login" class="need_valid"
                                           placeholder="<?php _e( 'Username', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Password', ET_DOMAIN ) ?></label>
                                    <input type="password" name="user_pass" id="user_pass" class="need_valid"
                                           placeholder="<?php _e( 'Password', ET_DOMAIN ); ?>">
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Confirm Your Password', ET_DOMAIN ) ?></label>
                                    <input type="password" name="repeat_pass" id="repeat_pass" class="need_valid"
                                           placeholder="<?php _e( 'Confirm Your Password', ET_DOMAIN ); ?>">
                                </div>

								<?php
									global $wpdb;
									$results = $wpdb->get_results( "SELECT `id`, `name` FROM {$wpdb->prefix}location_countries ORDER BY `name`", OBJECT );
								?>

                                <div class="fre-input-field select">
                                    <label><?php _e( 'Select Country', ET_DOMAIN ) ?></label>
                                    <select class="sel" name="country" id="country" required="required">
                                        <option value="">Select Country</option>
										<?php
											if ( $results ) {
												foreach ( $results as $result ) {
													echo "<option value='$result->id'>$result->name</option>";
												}
											} else {
												echo '<option value="">Country not available</option>';
											} ?>
                                    </select>
                                </div>

                                <div class="fre-input-field select disabled">
                                    <label><?php _e( 'Select State', ET_DOMAIN ) ?></label>
                                    <select name="state" id="state" required="required" disabled>
                                        <option value="">Select country first</option>
                                    </select>
                                </div>

                                <div class="fre-input-field select disabled">
                                    <label><?php _e( 'Select City', ET_DOMAIN ) ?></label>
                                    <select name="city" id="city" required="required" disabled>
                                        <option value="">Select state first</option>
                                    </select>
                                </div>
                                <div class="fre-input-field">
                                    <label><?php _e( 'Referral code (optional)', ET_DOMAIN ) ?></label>
                                    <input type="number" value="<?= $code ?>"
                                           class="input-item text-field is_number"
                                           pattern="-?(\d+|\d+.\d+|.\d+)([eE][-+]?\d+)?"
                                           onkeydown="if (event.keyCode == 16 || event.keyCode == 69 || event.keyCode == 189) return false"
                                           min="1" name="referral-code" id="refferal-code"
                                           placeholder="<?php _e( 'Referral code', ET_DOMAIN ); ?>">
                                    <div class="quantity-up"></div>
                                    <div class="quantity-down"></div>
                                </div>
								<?php ae_gg_recaptcha( $container = 'fre-input-field' ); ?>
                                <button class="fre-submit-btn btn-submit signup_btn"><?php _e( 'Sign Up', ET_DOMAIN ); ?></button>
                                <div class="checkline login-remember accept fre-input-field">
                                    <input id="remember" name="remember" type="checkbox"/>
                                    <span>
                                            <?php $tos   = et_get_page_link( 'tos', [], false );
	                                            $url_tos = '<a href="' . et_get_page_link( 'tos' ) . '" rel="noopener noreferrer" target="_blank">' . __( 'Terms of Service and Privacy Policy', ET_DOMAIN ) . '</a>';
	                                            if ( $tos ) {
		                                            printf( __( 'I accept the %s', ET_DOMAIN ), $url_tos );
	                                            } ?></span>
                                </div>
                            </form>
                            <div class="already-register">
                                <p>
									<?php _e( 'Already have an account?', ET_DOMAIN ); ?>
                                    <a href="<?php echo bloginfo( 'url' ) ?>/login/">
										<?php _e( 'Log In', ET_DOMAIN ); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<?php
	}
	get_footer();