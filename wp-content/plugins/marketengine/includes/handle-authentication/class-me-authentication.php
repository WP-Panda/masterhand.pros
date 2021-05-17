<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME_Authentication
 *
 * Handling visitor authentication behavior
 *
 * @class       ME_Authentication
 * @version     1.0
 * @package     Includes/Authentication
 * @author      EngineThemesTeam
 * @category    Class
 */
class ME_Authentication
{
    /**
     * Login
     *
     * Signon a user into system
     *
     * @since 1.0
     *
     * @see wp_signon
     * @param array $user_data
     *     @type string      $user_pass            The plain-text user password.
     *     @type string      $user_login           The user's login username.
     *
     * @return WP_User|WP_Error True: WP_User finish. WP_Error on error
     */
    public static function login($user_data)
    {
        $user_login = $user_data['user_login'];
        $user_pass  = $user_data['user_password'];

        $error = new WP_Error();
        if (empty($user_login)) {
            $error->add('username_required', __('The field username is required.', 'enginethemes'));
        }
        if (empty($user_pass)) {
            $error->add('password_required', __('The field password is required.', 'enginethemes'));
        }
        if ($error->get_error_messages()) {
            return $error;
        }

        $user = get_user_by('login', $user_login);
        if (!$user && strpos($user_login, '@')) {
            $user = get_user_by('email', $user_login);
        }

        if ($user) {
            $user_login = $user->user_login;
        }

        $creds                  = array();
        $creds['user_login']    = $user_login;
        $creds['user_password'] = $user_pass;
        $creds['remember']      = isset($user_data['rememberme']);
        $secure                 = is_ssl() ? true : false;
        /**
         * filter the login credentials
         * @param Array $creds
         * @since 1.0
         */
        $creds = apply_filters('marketengine_login_credentials', $creds);
        $user  = wp_signon($creds, $secure);

        return $user;
    }
    /**
     * Register new user
     *
     * Add new user to the blog
     *
     * @since 1.0
     *
     * @see wp_insert_user
     * @param Array $user_data The user info
     *     @type string      $user_pass            The plain-text user password.
     *     @type string      $user_login           The user's login username.
     *     @type email       $user_email           The user's email.
     *
     * @return WP_User|WP_Error True: WP_User finish. WP_Error on error
     */
    public static function register($user_data)
    {
        $rules = array(
            'user_login'     => 'required',
            'user_pass'      => 'required',
            'first_name'     => 'required',
            'last_name'      => 'required',
            'confirm_pass'   => 'required|same:user_pass',
            'user_email'     => 'required|email',
            'agree_with_tos' => 'required',
        );

        $custom_attributes = array(
            'user_login'     => __("user login", "enginethemes"),
            'user_pass'      => __("user password", "enginethemes"),
            'first_name'     => __("first name", "enginethemes"),
            'last_name'      => __("last name", "enginethemes"),
            'confirm_pass'   => __("confirm password", "enginethemes"),
            'user_email'     => __("user email", "enginethemes"),
            'agree_with_tos' => __("agree with term of use", "enginethemes"),
        );
        /**
         * Filter register data validate rules
         *
         * @param Array $rules
         * @param Array $user_data
         *
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_register_form_rules', $rules, $user_data);
        $is_valid = marketengine_validate($user_data, $rules, $custom_attributes);

        $errors = new WP_Error();
        if (!$is_valid) {
            $invalid_data = marketengine_get_invalid_message($user_data, $rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        extract($user_data);
        $sanitized_user_login = sanitize_user($user_login);
        /**
         * Filter the email address of a user being registered.
         *
         * @since 1.0
         *
         * @param string $user_email The email address of the new user.
         */
        $user_email = apply_filters('user_registration_email', $user_email);

        // Check the username
        if ($sanitized_user_login == '') {
            $errors->add('empty_username', __("Please enter a username.", "enginethemes"));
        } elseif (!validate_username($user_login) || preg_match('/[^a-z0-9]/', $user_data['user_login'])) {
            $errors->add('invalid_username', __("Usernames can only contain letters (a-z), numbers (0-9), and underscores (_).", "enginethemes"));
            $sanitized_user_login = '';
        } else {
            /** This filter is documented in wp-includes/user.php */
            $illegal_user_logins = array_map('strtolower', (array) apply_filters('illegal_user_logins', array()));
            if (in_array(strtolower($sanitized_user_login), $illegal_user_logins)) {
                $errors->add('invalid_username', __("Sorry, that username is not allowed.", "enginethemes"));
            }
        }

        // Check the email address
        if ($user_email == '') {
            $errors->add('empty_email', __("Please type your email address.", "enginethemes"));
        } elseif (!is_email($user_email)) {
            $errors->add('invalid_email', __("The email address isn&#8217;t correct.", "enginethemes"));
            $user_email = '';
        }

        /**
         * Fires when submitting registration form data, before the user is created.
         *
         * @since 1.0
         *
         * @param string   $sanitized_user_login The submitted username after being sanitized.
         * @param string   $user_email           The submitted email.
         * @param WP_Error $errors               Contains any errors with submitted username and email,
         *                                       e.g., an empty field, an invalid username or email,
         *                                       or an existing username or email.
         */
        do_action('register_post', $sanitized_user_login, $user_email, $errors);

        /**
         * Filter the errors encountered when a new user is being registered.
         *
         * The filtered WP_Error object may, for example, contain errors for an invalid
         * or existing username or email address. A WP_Error object should always returned,
         * but may or may not contain errors.
         *
         * If any errors are present in $errors, this will abort the user's registration.
         *
         * @since 1.0
         *
         * @param WP_Error $errors               A WP_Error object containing any errors encountered
         *                                       during registration.
         * @param string   $sanitized_user_login User's username after it has been sanitized.
         * @param string   $user_email           User's email.
         */
        $errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

        if ($errors->get_error_code()) {
            return $errors;
        }

        /**
         * do action before add new user
         *
         * @param Array $user_data The data user submit
         *
         * @since 1.0
         */
        do_action('marketengine_before_user_register', $user_data);

        $user_data['role'] = apply_filters('marketengine_user_register_role', 'author');
        $user_id           = wp_insert_user($user_data);
        if (is_wp_error($user_id)) {
            return $user_id;
        }

        $user                           = new WP_User($user_id);
        $is_required_email_confirmation = marketengine_option('user-email-confirmation') ? true : false;

        if ($is_required_email_confirmation) {
            // generate the activation key
            $activate_email_key = wp_hash(md5($user_data['user_email'] . time()));
            // store the activation key to user meta data
            update_user_meta($user->ID, 'activate_email_key', $activate_email_key);
            // send email
            self::send_activation_email($user);
        } else {
            self::send_registration_success_email($user);
        }
        /**
         * Do action marketengine_user_register
         *
         * @param Object $user WP_User
         * @param Array $user_data
         *
         * @since 1.0
         *
         */
        do_action('marketengine_user_register', $user, $user_data);
        return $user;
    }

    /**
     * This function copy from wordpress wp-login.php
     *
     * Handles sending password retrieval email to user.
     *
     * @global wpdb         $wpdb      WordPress database abstraction object.
     * @global PasswordHash $wp_hasher Portable PHP password hashing framework.
     *
     * @return bool|WP_Error True: when finish. WP_Error on error
     */
    public static function retrieve_password($user)
    {
        global $wpdb, $wp_hasher;

        $errors = new WP_Error();

        $rules = array(
            'user_email' => 'required|email',
        );

        $custom_attributes = array(
            'user_email' => __("user email", "enginethemes"),
        );
        /**
         * Filter register data validate rules
         *
         * @param Array $rules
         * @param Array $user
         *
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_forgot_password_form_rules', $rules, $user);
        $is_valid = marketengine_validate($user, $rules, $custom_attributes);

        if (!$is_valid) {
            $invalid_data = marketengine_get_invalid_message($user, $rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        $user_data = get_user_by('email', sanitize_email($user['user_email']));
        if (empty($user_data)) {
            $errors->add('invalid_email', __("<strong>ERROR</strong>: There is no user registered with that email address.", "enginethemes"));
        }

        /**
         * Fires before errors are returned from a password reset request.
         *
         * @since 2.1.0
         * @since 4.4.0 Added the `$errors` parameter.
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action('lostpassword_post', $errors);

        if ($errors->get_error_code()) {
            return $errors;
        }

        if (!$user_data) {
            $errors->add('invalidcombo', __("<strong>ERROR</strong>: Invalid email.", "enginethemes"));
            return $errors;
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key        = get_password_reset_key($user_data);

        if (is_wp_error($key)) {
            return $key;
        }

        $profile_link    = marketengine_get_page_permalink('user_account');
        $reset_pass_link = add_query_arg(array(
            'key'   => $key,
            'login' => rawurlencode($user_login),
        ), marketengine_get_endpoint_url('reset-password', '', $profile_link));

        $reset_pass_link = apply_filters('marketengine_resert_password_link', $reset_pass_link, $user_data, $key);

        if (is_multisite()) {
            $blogname = $GLOBALS['current_site']->site_name;
        } else
        /*
         * The blogname option is escaped with esc_html on the way into the database
         * in sanitize_option we want to reverse this for the plain text arena of emails.
         */
        {
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        $recover_url = '<a href="'.$reset_pass_link.'">'.$reset_pass_link.'</a>';
        $mail_args = array(
            'recover_url' => $recover_url,
            'blogname' => $blogname,
            'display_name' => get_the_author_meta( 'display_name', $user_data->ID )
        );
        ob_start();
        marketengine_get_template('emails/reset-password', $mail_args);
        $message = ob_get_clean();

        

        $title = sprintf(__("Password Reset", "enginethemes"), $blogname);

        /**
         * Filter user reset password email subject
         *
         * @param String $mail_subject
         * @param Object $user_data
         *
         * @since 1.0
         */
        $title = apply_filters('marketengine_reset_password_mail_subject', $title, $user_data);
        /**
         * Filter user reset password email content
         *
         * @param String $mail_content
         * @param Object $user_data
         *
         * @since 1.0
         */
        $message = apply_filters('marketengine_reset_password_mail_content', $message, $user_data);

        if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message)) {
            return new WP_Error('system_error', __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.', "enginethemes"));
        }

        return true;
    }

    /**
     * Reset user password
     *
     * Check the activation key and reset user pass
     *
     * @since 1.0
     *
     * @see reset_password()
     * @param Array  $user_data The user reset pass data
     * @return WP_User| WP_Error WP_User: when finish. WP_Error on error
     */
    public static function reset_pass($user_data)
    {
        $rules = array(
            'user_login'   => 'required',
            'new_pass'     => 'required',
            'confirm_pass' => 'required|same:new_pass',
            'key'          => 'required',
        );

        $custom_attributes = array(
            'user_login'   => __("user login", "enginethemes"),
            'new_pass'     => __("new password", "enginethemes"),
            'confirm_pass' => __("confirm password", "enginethemes"),
            'key'          => __("reset password key", "enginethemes"),
        );
        /**
         * filter reset pass data validate rules
         *
         * @param Array $rules
         * @param Array $user_data
         *
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_reset_password_form_rules', $rules, $user_data);
        $is_valid = marketengine_validate($user_data, $rules, $custom_attributes);
        if (!$is_valid) {
            $errors       = new WP_Error();
            $invalid_data = marketengine_get_invalid_message($user_data, $rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        $user = check_password_reset_key( sanitize_text_field( $user_data['key'] ) , sanitize_user( $user_data['user_login'] ));
        if (is_wp_error($user)) {
            return $user;
        } else {
            do_action('password_reset', $user, $user_data['new_pass']);
            wp_set_password($user_data['new_pass'], $user->ID);

            $mail_title = __("You have reset password successfull.", "enginethemes");
            /**
             * Filter user reset password email subject
             *
             * @param String $mail_content
             * @param Object $user_data
             *
             * @since 1.0
             */
            $mail_title = apply_filters('marketengine_reset_password_success_mail_subject', $mail_title, $user);

            $site_url = '<a href="'.get_bloginfo('url').'">'.get_bloginfo('url').'</a>';
            $mail_args = array(
                'site_url' => $site_url,
                'blogname' => get_bloginfo('blogname'),
                'display_name' => get_the_author_meta( 'display_name', $user->ID )
            );
            // get mail content
            ob_start();
            marketengine_get_template('emails/reset-password-success', $mail_args);
            $mail_content = ob_get_clean();
            /**
             * Filter user reset password success email content
             *
             * @param String $mail_content
             * @param Object $user_data
             *
             * @since 1.0
             */
            $mail_content = apply_filters('marketengine_reset_password_success_mail_content', $mail_content, $user);
            wp_mail($user->user_email, wp_specialchars_decode($mail_title), $mail_content);

            return $user;
        }
    }
    /**
     * User Confirm Email
     *
     * Check the confirm key and set the user account is confirmed
     *
     * @since 1.0
     *
     * @param Array $user_data The confirm info
     *         - Email user_email  : the email need to confirm
     *         - String key:    the secure key
     * @return WP_Error| WP_User object
     */
    public static function confirm_email($user_data)
    {
        $rules = array(
            'user_email' => 'required|email',
            'key'        => 'required',
        );

        $custom_attributes = array(
            'user_email' => __("user email", "enginethemes"),
            'key'        => __("reset password key", "enginethemes"),
        );
        /**
         * filter confirm email data validate rules
         *
         * @param Array $rules
         * @param Array $user_data
         *
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_confirm_mail_rules', $rules, $user_data);
        $is_valid = marketengine_validate($user_data, $rules, $custom_attributes);
        if (!$is_valid) {
            $errors       = new WP_Error();
            $invalid_data = marketengine_get_invalid_message($user_data, $rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        $user = get_user_by('email', sanitize_email( $user_data['user_email'] ));
        if (!$user) {
            return new WP_Error('email_not_exists', __("The email is not exists.", "enginethemes"));
        }

        $activate_email_key = get_user_meta($user->ID, 'activate_email_key', true);
        if ($activate_email_key && $activate_email_key !== $user_data['key']) {
            return new WP_Error('invalid_key', __("Invalid key.", "enginethemes"));
        }
        delete_user_meta($user->ID, 'activate_email_key');
        /**
         * Do action after user confirmed email
         *
         * @param Object $user
         *
         * @since 1.0
         */
        do_action('marketengine_user_confirm_email', $user);
        return $user;
    }

    /**
     * Send Activation Email
     *
     * Send activation email to user with activation link
     *
     * @since 1.0
     *
     * @param WP_User $user
     *
     * @return bool | WP_Error
     */
    public static function send_activation_email($user)
    {
        $user_activate_email_key = get_user_meta($user->ID, 'activate_email_key', true);
        if ($user_activate_email_key) {
            /**
             * Filter user activation email subject
             *
             * @param String $mail_subject
             * @param Object $user
             *
             * @since 1.0
             */
            $activation_mail_subject = apply_filters('marketengine_activation_mail_subject', __("Activate Email", "enginethemes"), $user);
            $profile_link            = marketengine_get_page_permalink('user-profile');
            $activate_email_link     = add_query_arg(array(
                'key'        => $user_activate_email_key,
                'user_email' => $user->user_email,
                'action'     => 'confirm-email',
            ), $profile_link);

            $activate_email_link = '<a href="' . $activate_email_link . '" >' . $activate_email_link . '</a>';

            $args = array(
                'display_name'        => get_the_author_meta('display_name', $user->ID),
                'blogname'            => get_bloginfo('blogname'),
                'user_login'          => $user->user_login,
                'user_email'          => $user->user_email,
                'activate_email_link' => $activate_email_link,

            );
            // get activation mail content from template
            ob_start();
            marketengine_get_template('emails/activation', $args);
            $activation_mail_content = ob_get_clean();

            /**
             * Filter user activation email content
             *
             * @param String $mail_content
             * @param Object $user
             *
             * @since 1.0
             */
            $activation_mail_content = apply_filters('marketengine_activation_mail_content', $activation_mail_content, $user);

            return wp_mail($user->user_email, $activation_mail_subject, $activation_mail_content);
        } else {
            return new WP_Error('already_confirmed', __("Your email is already confirmed.", "enginethemes"));
        }
    }

    /**
     * Send Registration Success Email
     *
     * @since 1.0
     *
     * @param WP_User $user
     *
     * @return bool
     */
    public static function send_registration_success_email($user)
    {
        $args = array(
            'display_name' => get_the_author_meta('display_name', $user->ID),
            'blogname'     => get_bloginfo('blogname'),
            'user_login'   => $user->user_login,
            'user_email'   => $user->user_email,
        );
        // get registration success mail content from template
        ob_start();
        marketengine_get_template('emails/registration-success', $args);
        $registration_success_mail_content = ob_get_clean();
        /**
         * Filter user registration success email subject
         *
         * @param String $registration_success_mail_content
         * @param WP_User $user
         *
         * @since 1.0
         */
        $registration_success_mail_subject = apply_filters('marketengine_registration_success_email_subject', __("Registration Success Email", "enginethemes"), $user);
        /**
         * Filter user registration success email content
         *
         * @param String $registration_success_mail_subject
         * @param WP_User $user
         *
         * @since 1.0
         */
        $registration_success_mail_content = apply_filters('marketengine_registration_success_mail_content', $registration_success_mail_content, $user);

        return wp_mail($user->user_email, $registration_success_mail_subject, $registration_success_mail_content);
    }

    /**
     * Update user profile info
     *
     * @since 1.0
     *
     * @see wp_insert_user() More complete way to create a new user
     *
     * @param Array $user_data
     *
     * @return Int | WP_Error
     */
    public static function update_profile($user_data)
    {
        global $user_ID;
        $user_id = $user_ID;

        if (current_user_can('edit_users') && isset($user_data['ID'])) {
            $user_id = $user_data['ID'];
        }

        $user_data['ID'] = $user_id;
        $errors          = new WP_Error();
        if (isset($_POST['first_name']) && $_POST['first_name'] == '') {
            $errors->add('first_name', __("The first name cannot be empty .", "enginethemes"));
        }
        if (isset($_POST['last_name']) && $_POST['last_name'] == '') {
            $errors->add('first_name', __("The last name cannot be empty.", "enginethemes"));
        }

        if (empty($_POST['paypal_email'])) {
            $errors->add('paypal_email_empty', __("The paypal email field cannot be empty.", "enginethemes"));
        }

        if (!empty($_POST['paypal_email']) && !is_email($_POST['paypal_email'])) {
            $errors->add('first_name', __("The paypal email is incorrect.", "enginethemes"));
        }

        if ($errors->get_error_code()) {
            return $errors;
        }
        /**
         * Filter list fields user can not change
         *
         * @param Array
         *
         * @since 1.0
         */
        $non_editable_fields = apply_filters('marketengine_profile_non_editable_fields', array(
            'user_login' => __("User Login", "enginethemes"),
            'user_email' => __("User email", "enginethemes"),
        )
        );
        $user_data = array_diff_key($user_data, $non_editable_fields);
        return wp_update_user($user_data);
    }

    /**
     * User change password
     *
     * @since 1.0
     *
     * @see wp_update_user()
     *
     * @param Array $user_data
     *          - old_password
     *          - new_password
     *          - confirm_password
     *
     * @return Int | WP_Error
     */
    public static function change_password($user_data)
    {
        $rules = array(
            'current_password' => 'required',
            'new_password'     => 'required',
            'confirm_password' => 'required|same:new_password',
        );
        $errors = new WP_Error();

        $custom_attributes = array(
            'current_password' => __("current password", "enginethemes"),
            'new_password'     => __("new password", "enginethemes"),
            'confirm_password' => __("confirm password", "enginethemes"),
        );

        /**
         * filter change password data validate rules
         *
         * @param Array $rules
         * @param Array $user_data
         *
         * @since 1.0
         */
        $rules    = apply_filters('marketengine_change_password_rules', $rules, $user_data);
        $is_valid = marketengine_validate($user_data, $rules, $custom_attributes);
        if (!$is_valid) {
            $invalid_data = marketengine_get_invalid_message($user_data, $rules, $custom_attributes);
            foreach ($invalid_data as $key => $message) {
                $errors->add($key, $message);
            }
            return $errors;
        }

        $user = ME()->get_current_user();
        if (!$user->id || !wp_check_password($user_data['current_password'], $user->data->user_pass, $user->ID)) {
            $errors->add('current_password_invalid', __("The current password you enter is not correct.", "enginethemes"));
            return $errors;
        }
        // user have to activate email first
        if (get_option('is_required_email_confirmation') && get_user_meta($user->ID, 'confirm_key', true)) {
            $errors->add('inactive_account', __("Please confirm your email first.", "enginethemes"));
            return $errors;
        }

        wp_update_user(array('ID' => $user->ID, 'user_pass' => $user_data['new_password']));
        /**
         * do action change password
         *
         * @param Array $user
         *
         * @since 1.0
         */
        do_action('marketengine_user_change_password', $user);
        return $user->ID;
    }
}
