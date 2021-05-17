<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME Authentication Form
 *
 * Class control user data in authentication form
 *
 * @version     1.0
 * @package     Includes/Authentication
 * @author      Dakachi
 * @category    Class
 */
class ME_Auth_Form extends ME_Form
{

    public static function init_hooks()
    {
        add_action('wp_loaded', array(__CLASS__, 'process_login'));
        add_action('wp_loaded', array(__CLASS__, 'process_register'));
        add_action('wp_loaded', array(__CLASS__, 'process_forgot_pass'));
        add_action('wp_loaded', array(__CLASS__, 'process_reset_pass'));
        add_action('wp_loaded', array(__CLASS__, 'process_confirm_email'));
        add_action('wp_loaded', array(__CLASS__, 'process_resend_confirm_email'));

        add_action('wp_loaded', array(__CLASS__, 'process_change_password'));
        add_filter('password_change_email', array(__CLASS__, 'password_change_email'));

        add_action('wp_loaded', array(__CLASS__, 'update_user_profile'));
    }

    public static function get_redirect_link()
    {
        if (isset($_POST['redirect'])) {
            $redirect = esc_url( $_POST['redirect'] );
        } elseif (wp_get_referer()) {
            $redirect = wp_get_referer();
        } else {
            $redirect = marketengine_get_page_permalink('user_account');
        }
        return $redirect;
    }

    public static function process_login()
    {
        if (!empty($_POST['login']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-login')) {
            $user = ME_Authentication::login($_POST);
            if (is_wp_error($user)) {
                marketengine_wp_error_to_notices($user);
            } else {
                // set the redirect link after login
                if (isset($_POST['redirect'])) {
                    $redirect = esc_url($_POST['redirect']);
                } elseif (wp_get_referer()) {
                    $redirect = wp_get_referer();
                } else {
                    $redirect = home_url();
                }
                /**
                 * action filter redirect link after user login
                 * @param String $redirect
                 * @param Object $user User object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_login_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }

    public static function process_register()
    {
        if (!empty($_POST['register']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-register')) {
            // Admin disable registration function
            if (!get_option('users_can_register')) {
                wp_redirect(site_url('wp-login.php?registration=disabled'));
                exit();
            }

            $user = ME_Authentication::register($_POST);
            if (is_wp_error($user)) {
                marketengine_wp_error_to_notices($user);
                return false;
            }

            $is_required_email_confirmation = marketengine_option('user-email-confirmation') ? true : false;
            if ($is_required_email_confirmation) {
                $message = "<div class='me-authen-inactive'>";
                $message .= "<p>" . __("Thank you! Please <span>check your mailbox</span> to activate your account.", "enginethemes") . "</p>";
                $message .= "<p>" . __("Inactive account cannot do following actions:", "enginethemes") . "</p>";
                $message .= "<p>" . __("- Edit user profile", "enginethemes") . "</p>";
                $message .= "<p>" . __("- Post listings", "enginethemes") . "</p>";
                $message .= "<p>" . __("- Order listings", "enginethemes") . "</p>";
                $message .= "</div>";

                marketengine_add_notice($message);
            } else {
                marketengine_add_notice(sprintf("<div class='me-authen-inactive'><p>" . __("Congratulation! You have successfully completed the registration process.", "enginethemes") . "</p></div>"));
            }
            // login in
            $_POST['user_password'] = $_POST['user_pass'];
            $user                   = ME_Authentication::login($_POST);
            // set the redirect link after register
            $redirect = self::get_redirect_link();
            /**
             * action filter redirect link after user login
             * @param String $redirect
             * @param Object $user User object
             * @since 1.0
             * @author EngineTeam
             */
            $redirect = apply_filters('marketengine_register_redirect', $redirect, $user);
            wp_redirect($redirect, 302);
            exit;
        }
    }

    public static function process_forgot_pass()
    {
        if (!empty($_POST['forgot_pass']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-forgot_pass')) {
            $password_retrieve = ME_Authentication::retrieve_password($_POST);
            if (!is_wp_error($password_retrieve)) {
                marketengine_add_notice(__("<div><p>The reset password email aldready send to your email account.</p></div>", "enginethemes"));
                // set the redirect link after forgot pass
                $redirect = self::get_redirect_link();
                /**
                 * action filter redirect link after user login
                 * @param String $redirect
                 * @param Object $user User object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_retrieve_password_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                marketengine_wp_error_to_notices($password_retrieve);
            }
        }
    }

    public static function process_reset_pass()
    {
        if (!empty($_POST['reset_password']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-reset_password')) {
            $user = ME_Authentication::reset_pass($_POST);
            if (!is_wp_error($user)) {
                marketengine_add_notice(__("<div><p>You have reset your password. Now you can login by your new password.</p></div>", "enginethemes"));
                // set the redirect link after reset pass
                $redirect = self::get_redirect_link();
                /**
                 * action filter redirect link after user login
                 * @param String $redirect
                 * @param Object $user User object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_reset_password_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                marketengine_wp_error_to_notices($user);
            }
        }
    }

    public static function process_confirm_email()
    {
        if (!empty($_GET['action']) && 'confirm-email' === $_GET['action']) {
            $user = ME_Authentication::confirm_email($_GET);
            if (!is_wp_error($user)) {
                marketengine_add_notice(__("<div><p>Your account has been confirmed successfully!.</p></div>", "enginethemes"));
                // set the redirect link after confirm email
                $redirect = self::get_redirect_link();
                /**
                 * action filter redirect link after user login
                 * @param String $redirect
                 * @param Object $user User object
                 * @since 1.0
                 * @author EngineTeam
                 */
                $redirect = apply_filters('marketengine_reset_password_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                $redirect = marketengine_get_page_permalink('user_account');
                marketengine_add_notice(__("<div><p>Invalid key. Please check your activation email again.</p></div>", "enginethemes"));
                wp_redirect($redirect, 302);
                exit;
            }
        }
    }

    public static function process_resend_confirm_email()
    {
        if (!empty($_GET['resend-confirmation-email']) && !empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'me-resend_confirmation_email')) {
            global $current_user;

            $activate_email_key = wp_hash(md5($current_user->user_email . time()));
            update_user_meta($current_user->ID, 'activate_email_key', $activate_email_key);

            $is_send_success = ME_Authentication::send_activation_email($current_user);
            if (!is_wp_error($is_send_success)) {
                marketengine_add_notice(__("<div><p>Please <span>check your mailbox</span> to activate your account.</p></div>", "enginethemes"));
                // set the redirect link after ask confirm email
                $redirect = self::get_redirect_link();
                $redirect = apply_filters('marketengine_resend_confirm_email_redirect', $redirect, $current_user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                marketengine_wp_error_to_notices($is_send_success);
            }
        }
    }

    public static function update_user_profile()
    {
        if (!empty($_POST['update_profile']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-update_profile')) {
            $user = ME_Authentication::update_profile($_POST);
            if (!is_wp_error($user)) {
                marketengine_add_notice(__("<div><p>Your profile has updated successfully.</p></div>", "enginethemes"));
                // set the redirect link after ask confirm email
                $redirect = self::get_redirect_link();
                $redirect = apply_filters('marketengine_update_profile_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                marketengine_wp_error_to_notices($user);
            }
        }
    }

    public static function process_change_password()
    {
        if (!empty($_POST['change_password']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'marketengine_change-password')) {
            $user = ME_Authentication::change_password($_POST);
            if (!is_wp_error($user)) {
                marketengine_add_notice(__("<div><p>Your password has been changed successfully.</p></div>", "enginethemes"));
                // set the redirect link after ask confirm email
                $redirect = self::get_redirect_link();
                $redirect = apply_filters('marketengine_update_profile_redirect', $redirect, $user);
                wp_redirect($redirect, 302);
                exit;
            } else {
                marketengine_wp_error_to_notices($user);
            }
        }
    }

    public static function password_change_email($pass_change_email)
    {
        /* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
        $pass_change_text = __('<p>Hi ###USERNAME###</p>,

<p>This notice confirms that your password was changed on ###SITENAME###.</p>

<p>If you did not change your password, please contact the Site Administrator at
###ADMIN_EMAIL### </p>

<p>This email has been sent to ###EMAIL###</p>
<p>
Regards, <br/>
All at ###SITENAME### <br/>
###SITEURL### </p>', 'enginethemes');

        $subject = __('Notice of Password Change', 'enginethemes');

        $pass_change_email['subject'] = $subject;
        $pass_change_email['message'] = $pass_change_text;
        return $pass_change_email;
    }
}

ME_Auth_Form::init_hooks();
