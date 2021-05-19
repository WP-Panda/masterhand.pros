<?php
/**
 * IHS_Send_Programmable_SMS Class to send programmable SMS
 *
 * Require the bundled autoload file - the path may need to change
 * based on where you downloaded and unzipped the SDK
 *
 * @package Orion SMS OTP Verification.
 */
require 'autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class IHS_Send_Programmable_SMS {

	/**
	 * Sends the Message using Twilio Api
	 *
	 * @param {string} $sid Your Account SID from twilio.com/console.
	 * @param {string} $token Auth Token from twilio.com/console.
	 * @param {string} $mob_no_with_country_code The number you'd like to send the message to.
	 * @param {string} $twilio_mob_no A Twilio phone number you purchased at twilio.com/console.
	 * @param {string} $message The body of the text message you'd like to send.
	 */
	public function ihs_send_msg_using_twilio( $sid, $token, $mob_no_with_country_code, $twilio_mob_no, $message ) {

		$sid = trim( $sid );
		$token = trim( $token );
		$mob_no_with_country_code = trim( $mob_no_with_country_code );
		$twilio_mob_no = trim( $twilio_mob_no );
		$message = trim( $message );

		$client = new Client( $sid, $token );
		
		$response = $client->messages->create(
			$mob_no_with_country_code,
			array(
				'from' => $twilio_mob_no,
				'body' => $message
			)
		);
		echo "--";
		print_r( $response );
		
	}
}
