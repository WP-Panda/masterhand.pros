<?php
	get_header();
?>
    <div class="fre-page-wrapper">
        <div class="fre-page-section">
            <div class="container">
                <div class="fre-authen-wrapper">
                    <div class="fre-authen-lost-pass">
                        <h1>
							<?php _e( 'Reset Your Password', ET_DOMAIN ); ?>
                        </h1>
                        <p>
							<?php _e( "Enter your email address below. We'll look for your account and send you a password reset email.", ET_DOMAIN ); ?>
                        </p>
                        <form role="form" id="forgot_form" class="auth-form forgot_form">
                            <div class="fre-input-field">
                                <label><?php _e( 'Email', ET_DOMAIN ); ?></label>
                                <input type="text" id="user_email" name="user_email" class="need_valid"
                                       placeholder="<?php _e( 'Your email address', ET_DOMAIN ); ?>">
                            </div>
                            <button class="fre-submit-btn btn-submit"><?php _e( 'Reset Password', ET_DOMAIN ); ?></button>
                        </form>
                        <div class="fre-authen-footer">
							<?php _e( 'Already have an account?', ET_DOMAIN ); ?>
                            <a href="<?php echo bloginfo( 'url' ) ?>/login/">
								<?php _e( 'Log In', ET_DOMAIN ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer();