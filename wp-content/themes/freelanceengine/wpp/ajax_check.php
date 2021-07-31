<?php
/**
 * Plugin name: Ajax check register
 */
add_action( 'wp_ajax_check_register', 'check_register' );
add_action( 'wp_ajax_nopriv_check_register', 'check_register' );

function check_register() {

	$valid = [];

	if ( ! empty( $_POST['user_login'] ) && username_exists( $_POST['user_login'] ) ) {
		$valid[] = true;
	}

	if ( ! empty( $_POST['user_email'] ) && email_exists( $_POST['user_email'] ) ) {
		$valid[] = true;
	}


	if ( ! empty( $_POST['referral-code'] ) ) {

		if ( false === get_user_by_referral_code( $_POST['referral_code'] ) ) {

			if ( $_POST['type_prof'] == 'company' && ! empty( check_ref_code_by_company( $_POST['referral_code'] ) ) ) {
				$valid[] = true;
			}

		} else {
			$valid[] = true;
		}

	}

	if ( ! empty( $valid ) ) {
		wp_send_json_error();
	}

	wp_send_json_success();
}

add_action( 'print_footer_scripts', 'check_register_js', 99 );

function check_register_js() {
	?>
    <script>
        jQuery(function ($) {


            $('#user_login').focusout(function (e) {

                var $data = {
                    action: 'check_register',
                    user_login: $("#user_login").val()
                };

                $.post('/wp-admin/admin-ajax.php', $data, function ($response) {

                    if (!$response.success) {
                        $("#user_login").parent().addClass('fre-input-field error');
                        $("#user_login").parent().append("<div for='user_login' class='message'><?php _e( 'Sorry, that username already exists!', WPP_TEXT_DOMAIN );  ?></div>");
                    } else {
                        $("#user_login").parent().addClass('fre-input-field');
                    }

                });
            });

            $('#refferal-code').focusout(function (e) {

                var $data = {
                    action: 'check_register',
                    type_prof: $('input[name=type_prof]:checked').val(),
                    referral_code: $("#refferal-code").val()
                };

                $.post('/wp-admin/admin-ajax.php', $data, function ($response) {
                    if (!$response.success) {
                        $("#refferal-code").parent().addClass('fre-input-field error');
                        $("#refferal-code").parent().append("<div for='refferal-code' class='message'><?php _e( 'Sorry, such a referral code does not exist!', WPP_TEXT_DOMAIN );  ?></div>");
                    } else {
                        $("#refferal-code").parent().addClass('fre-input-field');
                    }
                });
            });

            $('#user_email').focusout(function (e) {
                var $data = {
                    action: 'check_register',
                    user_email: $("#user_email").val()
                };
                $.post('/wp-admin/admin-ajax.php', $data, function ($response) {
                    if (!$response.success) {
                        $("#user_email").parent().addClass('fre-input-field error');
                        $("#user_email").parent().append("<div for='user_email' class='message'><?php _e( 'Sorry, that email address is already used!', WPP_TEXT_DOMAIN );  ?></div>");
                    } else {
                        $("#user_email").parent().addClass('fre-input-field');
                    }
                });
            });

        });
    </script>
	<?php
}