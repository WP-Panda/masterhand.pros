<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
global $wpdb;
$message = false;

if ( ! empty( $_POST['g-recaptcha-response'] ) ) {
	$secret         = '6LdPT2sUAAAAAHKHRm9X8VgPeNGjlqc-EfGAJyf5';
	$verifyResponse = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response'] );
	$responseData   = json_decode( $verifyResponse );
	$message        = ! empty( $responseData->success ) ? 'success' : 'error';
}

if ( 'success' === $message ) {
	header( 'Content-type: application/json' );
	$user_email  = $_POST['user_email'];
	$user_answer = $_POST['user_answer'];

	$date = new DateTime();

	if ( empty( $user_email ) || empty( $user_answer ) ) {

		$response = [
			'response'   => 'error',
			'error_type' => 'user_error',
			'text'       => __( 'email or answer is empty or not correct', WPP_TEXT_DOMAIN )
		];

		echo json_encode( $response );
		die();
	} else {
		$results = $wpdb->get_results( "SELECT * FROM wp_unsubscribe_users WHERE user_email = '$user_email'" );

		if ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) ) { //validate email
			if ( ! $results ) {
				$wpdb->insert( 'wp_unsubscribe_users', [
					'user_email'  => strtolower( $user_email ),
					'user_answer' => $user_answer,
					'date'        => $date->format( "Y-m-d h:i:s" )
				] );
			}

			$response = [
				'response' => 'success',
				'text'     => __( 'You have unsubscribed successfully.', WPP_TEXT_DOMAIN )
			];
			echo json_encode( $response );

		} else {

			$response = [
				'response'   => 'error',
				'error_type' => 'email',
				'text'       => 'email is empty or not correct'
			];

			echo json_encode( $response );
		}

		die();
	}

} else {
	header( 'Content-type: application/json' );
	$response = [ 'response' => 'error', 'error_type' => 'user_error', 'text' => 'captcha error' ];
	echo json_encode( $response );
	die();
}