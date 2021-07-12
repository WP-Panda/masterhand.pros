<?php
/**
 * Plugin name: Ajax check register
 */
add_action('wp_ajax_check_register','check_register');
add_action('wp_ajax_nopriv_check_register','check_register');
    function check_register()
    {
        if ($_POST['name']) {
            if (username_exists($_POST['name'])) {
                echo 'true';
            } else {
                echo 'false';
            }
            wp_die();
        }
        if ($_POST['email']){
            if (email_exists($_POST['email'])) {
                echo 'true';
            } else {
                echo 'false';
            }
            wp_die();
        }
        if ($_POST['referral_code']){
            if (get_user_by_referral_code($_POST['referral_code']) == false) {
                if ($_POST['type_prof'] == 'company' && !empty(check_ref_code_by_company($_POST['referral_code']))) {
                    echo 'true';
                }
                echo 'false';
            } else {
                echo 'true';
            }
            wp_die();
        }
    }

add_action('print_footer_scripts', 'check_register_js', 99);

function check_register_js()
{
    ?>
    <script>
        jQuery('#user_login').focusout(function ($) {
            var data = {
                action: 'check_register',
                name: jQuery("#user_login").val()
            };
            jQuery.post('/wp-admin/admin-ajax.php', data, function (response) {
                if (response == 'true') {
                    jQuery("#user_login").parent().addClass('fre-input-field error');
                    jQuery("#user_login").parent().append("<div for='user_login' class='message'>Sorry, that username already exists!</div>");
                }
                if (response == 'false') jQuery("#user_login").parent().addClass('fre-input-field');
            });
        });
        jQuery('#refferal-code').focusout(function ($) {
            var data = {
                action: 'check_register',
                type_prof: jQuery('input[name=type_prof]:checked').val(),
                referral_code: jQuery("#refferal-code").val()
            };
            jQuery.post('/wp-admin/admin-ajax.php', data, function (response) {
                if (response == 'false') {
                    jQuery("#refferal-code").parent().addClass('fre-input-field error');
                    jQuery("#refferal-code").parent().append("<div for='user_login' class='message'>Sorry, such a referral code does not exist!</div>");
                }
                if (response == 'true') jQuery("#user_login").parent().addClass('fre-input-field');
            });
        });
        jQuery('#user_email').focusout(function($) {
            var data = {
                action: 'check_register',
                email: jQuery("#user_email").val()
            };
            jQuery.post('/wp-admin/admin-ajax.php', data, function (response) {
                if (response == 'true') {
                    jQuery("#user_email").parent().addClass('fre-input-field error');
                    jQuery("#user_email").parent().append("<div for='user_login' class='message'>Sorry, that email address is already used!</div>");
                }
                if (response == 'false') jQuery("#user_email").parent().addClass('fre-input-field');
            });
        });
    </script>
    <?php
}