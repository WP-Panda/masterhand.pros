<?php
/**
 * Template Name: Login Page
 */

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.8
 */

global $post;
if ( is_user_logged_in() ) {
	wp_redirect( home_url() );
	exit;
}
get_header();
// the_post();
// Redirect after login success
$re_url = '';
if ( isset( $_GET['ae_redirect_url'] ) ) {
	$re_url = $_GET['ae_redirect_url'];
} else {
	$re_url = home_url();
}
?>

    <div class="fre-page-wrapper">
        <div class="fre-page-section">
            <div class="container">
                <div class="fre-authen-wrapper">
                    <div class="fre-authen-login">
                        <h1>
							<?php _e( 'Log into Your Account', ET_DOMAIN ) ?>
                        </h1>
                        <form role="form" id="signin_form" class="">
                            <input type="hidden" value="<?php echo $re_url ?>" name="ae_redirect_url"/>
                            <div class="fre-input-field">
                                <label><?php _e( 'Username or Email', ET_DOMAIN ) ?></label>
                                <input type="text" name="user_login" class="need_valid"
                                       placeholder="<?php _e( 'Username or Email', ET_DOMAIN ) ?>">
                            </div>
                            <div class="fre-input-field">
                                <label><?php _e( 'Password', ET_DOMAIN ) ?></label>
                                <input type="password" name="user_pass" class="need_valid"
                                       placeholder="<?php _e( 'Password', ET_DOMAIN ) ?>">
                            </div>
                            <div class="checkline login-remember">
                                <input id="remember" name="remember" type="checkbox">
                                <span><?php _e( 'Remember me', ET_DOMAIN ) ?></span>
                            </div>
							<?php //ae_gg_recaptcha( $container = 'fre-input-field' );?>
                            <button class="fre-submit-btn btn-submit"><?php _e( 'Log In', ET_DOMAIN ) ?></button>
                        </form>
                        <div class="fre-login-social">
							<?php
							if ( fre_check_register() && function_exists( 'ae_render_social_button' ) ) {
								$before_string = __( "You can use your social account to log in", ET_DOMAIN );
								ae_render_social_button( [], [], $before_string );
							}
							?>
                        </div>
						<?php if ( fre_check_register() ) { ?>
                            <div class="not-yet-register">
                                <a href="<?php echo bloginfo( 'url' ) ?>/register/" class="">
									<?php _e( 'Register here', ET_DOMAIN ) ?>
                                </a>
                            </div>
						<?php } ?>
                        <div class="forgot-password">
                            <a href="<?php echo bloginfo( 'url' ) ?>/forgot-password/" class="">
								<?php _e( 'Forgot password?', ET_DOMAIN ) ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();