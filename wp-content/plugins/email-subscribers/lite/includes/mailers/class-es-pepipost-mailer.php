<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ES_Pepipost_Mailer' ) ) {
	/**
	 * Class ES_Pepipost_Mailer
	 *
	 * @since 4.2.1
	 * @since 4.3.2 Modified response
	 */
	class ES_Pepipost_Mailer extends ES_Base_Mailer {
		/**
		 * Pepipost API Url
		 *
		 * @since 4.3.2
		 * @var string
		 *
		 */
		public $api_url = 'https://api.pepipost.com/v2/sendEmail';
		/**
		 * API Key
		 *
		 * @since 4.3.2
		 * @var string
		 *
		 */
		public $api_key = '';

		/**
		 * ES_Pepipost_Mailer constructor.
		 *
		 * @since 4.3.2
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Send Email
		 *
		 * @param ES_Message $message
		 *
		 * @return bool|WP_Error
		 *
		 * @since 4.2.1
		 * @since 4.3.2 Modified Response
		 */
		public function send( ES_Message $message ) {

			ES()->logger->info( 'Start Sending Email Using Pepipost', $this->logger_context );

			$ig_es_mailer_settings = get_option( 'ig_es_mailer_settings', array() );

			if ( ES()->is_const_defined( 'pepipost', 'api_key' ) ) {
				$this->api_key = ES()->get_const_value( 'pepipost', 'api_key' );
			} else {
				$this->api_key = ! empty( $ig_es_mailer_settings['pepipost']['api_key'] ) ? $ig_es_mailer_settings['pepipost']['api_key'] : '';
			}

			if ( empty( $this->api_key ) ) {
				return $this->do_response( 'error', 'API Key is empty' );
			}

			$params = array();

			// We are decoding HTML entities i.e. converting &#8220; to “ to fix garbage characters issue in CZech languange
			// We need to decode entities only in case of Pepipost. For other mailers it is working as expected.
			$message->body = ES_Common::decode_entities( $message->body );

			$list_unsubscribe_header = ES()->mailer->get_list_unsubscribe_header( $message->to );
			
			if ( ! empty( $list_unsubscribe_header ) ) {
				$params['personalizations'][] = array(
					'recipient'               => $message->to,
					'X-List-Unsubscribe'      => $list_unsubscribe_header,
					'X-List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
				);
			} else {
				$params['personalizations'][]['recipient'] = $message->to;
			}
			
			$params['from']['fromEmail'] = $message->from;
			$params['from']['fromName']  = $message->from_name;
			$params['subject']           = $message->subject;
			$params['content']           = $message->body;
			$params['replyToId']         = $message->reply_to_email;
			
			$tracking_settings = array(
				'opentrack'  => ES()->mailer->can_track_open() ? 1 : 0,
				'clicktrack' => ES()->mailer->can_track_clicks() ? 1: 0,
			);

			$params['settings'] = $tracking_settings;

			
			if ( ! empty( $list_unsubscribe_header ) ) {
				$params['x-headers'] = array(
					'List-Unsubscribe'      => $list_unsubscribe_header,
					'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
				);
			}

			$attachments = $message->attachments;
			if ( ! empty( $attachments ) ) {
				foreach ( $attachments as $attachment_name => $attachment_path ) {
					if ( is_file( $attachment_path ) ) {
						$attachment_content      = file_get_contents( $attachment_path );
						$encoded_content         = base64_encode( $attachment_content );
						$params['attachments'][] = array(
							'fileContent' => $encoded_content,
							'fileName' => $attachment_name,
						);
					}
				}
			}

			$headers = array(
				'user-agent'   => 'APIMATIC 2.0',
				'Accept'       => 'application/json',
				'content-type' => 'application/json; charset=utf-8',
				'api_key'      => $this->api_key
			);

			if ( ! empty( $list_unsubscribe_header ) ) {
				$headers['X-List-Unsubscribe'] = $list_unsubscribe_header;
				$headers['X-List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
			}

			$headers = ! empty( $message->headers ) ? array_merge( $headers, $message->headers ) : $headers;
			$method  = 'POST';
			$qs      = json_encode( $params );

			
			$options = array(
				'timeout' => 15,
				'method'  => $method,
				'headers' => $headers
			);

			
			if ( 'POST' == $method ) {
				$options['body'] = $qs;
			}

			$response = wp_remote_request( $this->api_url, $options );
			if ( ! is_wp_error( $response ) ) {
				$body = ! empty( $response['body'] ) ? json_decode( $response['body'], true ) : '';

				if ( ! empty( $body ) ) {
					if ( 'Success' === $body['message'] ) {
						return $this->do_response( 'success' );
					} elseif ( ! empty( $body['error_info'] ) ) {
						return $this->do_response( 'error', $body['error_info']['error_message'] );
					}
				} else {
					$this->do_response( 'error', wp_remote_retrieve_response_message( $response ) );
				}
			}

			ES()->logger->info( 'Email Sent Successfully Using Pepipost', $this->logger_context );

			return $this->do_response( 'success' );
		}

	}

}
