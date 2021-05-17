<?php
class ME_Shortcodes_Auth {
    public static function init_shortcodes() {
        add_shortcode('me_user_account', array(__CLASS__, 'marketengine_user_account'));
        add_shortcode('me_user_register', array(__CLASS__, 'marketengine_register_form'));
        add_shortcode('me_user_login', array(__CLASS__, 'marketengine_login_form'));
    }
    public static function marketengine_user_account() {
        global $wp;
        if (is_user_logged_in()) {
            return self::logged_in_template();
        } else {
            return self::authentication_template();
        }
    }
    public static function logged_in_template() {
        global $wp;
        if (isset($wp->query_vars['edit-profile']) && marketengine_is_activated_user() ) {
            return self::marketengine_user_edit_profile();
        } elseif (isset($wp->query_vars['change-password']) && marketengine_is_activated_user() ) {
            return self::marketengine_change_password();
        } elseif (isset($wp->query_vars['listings'])) {
            return self::marketengine_user_listings();
        }elseif (isset($wp->query_vars['orders'])) {
            return self::marketengine_user_orders();
        }elseif (isset($wp->query_vars['purchases'])) {
            return self::marketengine_user_purchases();
        }elseif (isset($wp->query_vars['listing-id'])) {
            return self::marketengine_user_edit_listing();
        }elseif (isset($wp->query_vars['resolution-center'])) {
            return self::marketengine_resolution_center();
        }
        return self::marketengine_user_profile();
    }

    public static function authentication_template() {
        global $wp;
        if (isset($wp->query_vars['forgot-password'])) {
            return self::forgot_password_form();
        } elseif (isset($wp->query_vars['reset-password'])) {
            return self::marketengine_resetpass_form();
        } elseif (isset($wp->query_vars['register'])) { 
            return self::marketengine_register_form();
        }
        return self::marketengine_login_form();
    }
    public static function marketengine_user_profile() {
        if(!marketengine_is_activated_user() && !marketengine_get_notices()) {
            $message = "<div class='me-authen-inactive'>";
            $message .= "<p>" . __("Thank you! Please <span>check your mailbox</span> to activate your account.", "enginethemes") . "</p>";
            $message .= "<p>" . __("Inactive account cannot do following actions:", "enginethemes") . "</p>";
            $message .= '<ul>';
            $message .= "<li>" . __("- Edit user profile", "enginethemes") . "</li>";
            $message .= "<li>" . __("- Post listings", "enginethemes") . "</li>";
            $message .= "<li>" . __("- Order listings", "enginethemes") . "</li>";
            $message .= "</ul>";
            $message .= "</div>";

            marketengine_add_notice( $message );
        }
        ob_start();
        marketengine_get_template('account/user-profile');
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_user_edit_profile() {
        ob_start();
        marketengine_get_template('account/edit-profile');
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_change_password() {
        $user = ME()->get_current_user();
        ob_start();
        marketengine_get_template('account/change-password', array('user' => $user));
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_user_listings() {
        ob_start();
        marketengine_get_template('account/my-listings');
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_user_edit_listing() {
        ob_start();
        $listing_id = get_query_var('listing-id');
        $listing = marketengine_get_listing($listing_id);

        marketengine_get_template('post-listing/edit-listing', array('listing' => $listing));
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_user_orders() {
        ob_start();
        marketengine_get_template('account/my-orders');
        $content = ob_get_clean();
        return $content;
    }
    public static function marketengine_user_purchases() {
        ob_start();
        marketengine_get_template('account/my-purchases');
        $content = ob_get_clean();
        return $content;
    }

    public static function marketengine_login_form() {
        ob_start();
        marketengine_get_template('account/form-login');
        $content = ob_get_clean();
        return $content;
    }
    public static function marketengine_register_form() {
        ob_start();
        marketengine_get_template('account/form-register');
        $content = ob_get_clean();
        return $content;
    }

    public static function forgot_password_form() {
        ob_start();
        marketengine_get_template('account/forgot-password');
        $content = ob_get_clean();
        return $content;
    }
    public static function marketengine_resetpass_form() {
        ob_start();
        marketengine_get_template('account/reset-pass');
        $content = ob_get_clean();
        return $content;
    }
    public static function marketengine_confirm_email() {
        ob_start();
        marketengine_get_template('account/confirm-email');
        $content = ob_get_clean();
        return $content;
    }
    public static function marketengine_resolution_center() {
        ob_start();
        marketengine_get_template('resolution/cases');
        $content = ob_get_clean();
        return $content;
    }
}
ME_Shortcodes_Auth::init_shortcodes();