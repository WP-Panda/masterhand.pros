<?php

/**
 *
 *
 * @param unknown $headline
 * @param unknown $content
 * @param unknown $to            (optional)
 * @param unknown $replace       (optional)
 * @param unknown $attachments   (optional)
 * @param unknown $template_file (optional)
 * @param unknown $headers       (optional)
 * @return unknown
 */
function mailster_send( $headline, $content, $to = '', $replace = array(), $attachments = array(), $template_file = 'notification.html', $headers = null ) {

	_deprecated_function( __FUNCTION__, '2.0', 'mailster(\'notification\')->send($args)' );

	if ( empty( $to ) ) {
		$current_user = wp_get_current_user();
		$to           = $current_user->user_email;
	}

	$defaults = array( 'notification' => '' );

	$replace = apply_filters( 'mymail_send_replace', apply_filters( 'mailster_send_replace', wp_parse_args( $replace, $defaults ), $defaults ) );

	$mail = mailster( 'mail' );

	// extract the header if it's already Mime encoded
	if ( ! empty( $headers ) ) {
		if ( is_string( $headers ) ) {
			$headerlines = explode( "\n", trim( $headers ) );
			foreach ( $headerlines as $header ) {
				$parts = explode( ':', $header, 2 );
				$key   = trim( $parts[0] );
				$value = trim( $parts[1] );

				// if fom is set, use it!
				if ( 'from' == strtolower( $key ) ) {
					if ( preg_match( '#(.*)?<([^>]+)>#', $value, $matches ) ) {
						$mail->from      = trim( $matches[2] );
						$mail->from_name = trim( $matches[1] );
					} else {
						$mail->from      = $value;
						$mail->from_name = '';
					}
				} elseif ( ! in_array( strtolower( $key ), array( 'content-type' ) ) ) {
					$mail->headers[ $key ] = trim( $value );
				}
			}
		} elseif ( is_array( $headers ) ) {
			foreach ( $headers as $key => $value ) {
				$mail->mailer->addCustomHeader( $key, $value );
			}
		}
	}

	$mail->to          = $to;
	$mail->subject     = $headline;
	$mail->attachments = $attachments;

	return $mail->send_notification( $content, $headline, $replace, false, $template_file );
}


/**
 *
 *
 * @param unknown $to
 * @param unknown $subject
 * @param unknown $message
 * @param unknown $headers       (optional)
 * @param unknown $attachments   (optional)
 * @param unknown $template_file (optional)
 * @return unknown
 */
function mailster_wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $template_file = 'notification.html' ) {
	_deprecated_function( __FUNCTION__, '2.3', 'mailster()->wp_mail' );
	return mailster()->wp_mail( $to, $subject, $message, $headers, $attachments = array(), $template_file );
}


/**
 * deprecated
 *
 * @param unknown $campaign
 * @param unknown $subscriber
 * @param unknown $track      (optional)
 * @param unknown $forcesend  (optional)
 * @param unknown $force      (optional)
 * @return unknown
 */
function mailster_send_campaign_to_subscriber( $campaign, $subscriber, $track = false, $forcesend = false, $force = false ) {

	_deprecated_function( __FUNCTION__, '2.3', 'mailster(\'campaigns\')->send' );

	$campaign_id   = is_numeric( $campaign ) ? $campaign : $campaign->ID;
	$subscriber_id = is_numeric( $subscriber ) ? $subscriber : $subscriber->ID;

	$result = mailster( 'campaigns' )->send( $campaign_id, $subscriber_id, $track, $forcesend || $force, false );

	if ( is_wp_error( $result ) ) {
		return false;
	}

	return $result;

}

if ( ! function_exists( 'mymail' ) && mailster_option( 'legacy_hooks' ) ) :

	require_once MAILSTER_DIR . 'includes/deprecated_actions.php';

	// deprecated stuff
	if ( ! defined( 'MYMAIL_VERSION' ) ) {
		define( 'MYMAIL_VERSION', MAILSTER_VERSION );
	}

	if ( ! defined( 'DOING_AJAX' ) ) {
		add_action(
			'mymail_form_header',
			function() {

				global $pagenow;

				if ( strpos( $_SERVER['REQUEST_URI'], 'myMail/form.php' ) !== false && isset( $_SERVER['HTTP_REFERER'] ) && 'form.php' == $pagenow ) {

					$referer = '<a href="' . esc_url_raw( $_SERVER['HTTP_REFERER'] ) . '" target="_blank" rel="noopener">' . esc_url_raw( $_SERVER['HTTP_REFERER'] ) . '</a>';
					if ( isset( $_GET['button'] ) ) {
						$msg = 'A deprecated Subscriber Button for Mailster has been found at %1$s. Please update the HTML following %2$s.';
					} else {
						$msg = 'An deprecated external form for Mailster has been found at %1$s. Please update the HTML following %2$s.';
					}

					mailster_notice( sprintf( $msg, $referer, '<a href="https://kb.mailster.co/updating-mymail-to-mailster/" target="_blank" rel="noopener">this guide</a>' ), 'error', 3600, 'oldsubscriberbtn' );
				}
			}
		);

		add_action(
			'mymail_cron_worker',
			function() {

				global $pagenow;

				if ( strpos( $_SERVER['REQUEST_URI'], 'myMail/cron.php' ) !== false && isset( $_SERVER['HTTP_REFERER'] ) && 'cron.php' == $pagenow ) {

					$referer = '<a href="' . esc_url_raw( $_SERVER['HTTP_REFERER'] ) . '" target="_blank" rel="noopener">' . esc_url_raw( $_SERVER['HTTP_REFERER'] ) . '</a>';

					$msg = 'The URL to the cron has changed but still get triggered! Please update your cron service to the new URL.</strong></p><a class="button button-primary" href="edit.php?post_type=newsletter&page=mailster_settings#cron">Get the new URL</a>';

					mailster_notice( $msg, 'error', 3600, 'oldcronurl' );
				}
			}
		);
	}

	/**
	 *
	 *
	 * @param unknown $subclass (optional)
	 * @return unknown
	 */
	function mymail( $subclass = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster' );
		return mailster( $subclass );
	}


	/**
	 *
	 *
	 * @param unknown $option
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	function mymail_option( $option, $fallback = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_option' );
		return mailster_option( $option, $fallback );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_options() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_options' );
		return mailster_options();
	}



	/**
	 *
	 *
	 * @param unknown $key
	 * @param unknown $data
	 * @param unknown $expire (optional)
	 * @return unknown
	 */
	function mymail_cache_add( $key, $data, $expire = 0 ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_cache_add' );
		return mailster_cache_add( $key, $data, $expire );
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @param unknown $data
	 * @param unknown $expire (optional)
	 * @return unknown
	 */
	function mymail_cache_set( $key, $data, $expire = 0 ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_cache_set' );
		return mailster_cache_set( $key, $data, $expire );
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @param unknown $force (optional)
	 * @param unknown $found (optional, reference)
	 * @return unknown
	 */
	function mymail_cache_get( $key, $force = false, &$found = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_cache_get' );
		return mailster_cache_get( $key, $force, $found );
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @return unknown
	 */
	function mymail_cache_delete( $key ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_cache_delete' );
		return mailster_cache_delete( $key );
	}


	/**
	 *
	 *
	 * @param unknown $option
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	function mymail_text( $option, $fallback = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_text' );
		return mailster_text( $option, $fallback );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_texts() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_texts' );
		return mailster_texts();
	}


	/**
	 *
	 *
	 * @param unknown $option
	 * @param unknown $value
	 * @param unknown $temp   (optional)
	 * @return unknown
	 */
	function mymail_update_option( $option, $value, $temp = false ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_update_option' );
		return mailster_update_option( $option, $value, $temp );
	}


	/**
	 *
	 *
	 * @param unknown $headline
	 * @param unknown $content
	 * @param unknown $to          (optional)
	 * @param unknown $replace     (optional)
	 * @param unknown $attachments (optional)
	 * @param unknown $template    (optional)
	 * @param unknown $headers     (optional)
	 * @return unknown
	 */
	function mymail_send( $headline, $content, $to = '', $replace = array(), $attachments = array(), $template = 'notification.html', $headers = array() ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_send' );
		return mailster_send( $headline, $content, $to, $replace, $atachments, $template, $headers );

	}


	/**
	 *
	 *
	 * @param unknown $to
	 * @param unknown $subject
	 * @param unknown $message
	 * @param unknown $headers     (optional)
	 * @param unknown $attachments (optional)
	 * @param unknown $template    (optional)
	 * @return unknown
	 */
	function mymail_wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $template = 'notification.html' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster()->wp_mail' );
		return mailster()->wp_mail( $to, $subject, $message, $headers, $attachments = array(), $template );
	}


	/**
	 * depreciated
	 *
	 * @param unknown $campaign
	 * @param unknown $subscriber
	 * @param unknown $track      (optional)
	 * @param unknown $forcesend  (optional)
	 * @param unknown $force      (optional)
	 * @return unknown
	 */
	function mymail_send_campaign_to_subscriber( $campaign, $subscriber, $track = false, $forcesend = false, $force = false ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_send_campaign_to_subscriber' );
		return mailster_send_campaign_to_subscriber( $campaign, $subscriber, $track, $forcesend, $force );

	}


	/**
	 *
	 *
	 * @param unknown $id          (optional)
	 * @param unknown $echo        (optional)
	 * @param unknown $classes     (optional)
	 * @param unknown $depreciated (optional)
	 * @return unknown
	 */
	function mymail_form( $id = 0, $echo = true, $classes = '', $depreciated = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_form' );
		return mailster_form( $id, $echo, $classes, $depreciated );

	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_active_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_active_campaigns' );
		return mailster_get_active_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_paused_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_paused_campaigns' );
		return mailster_get_paused_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_queued_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_queued_campaigns' );
		return mailster_get_queued_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_draft_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_draft_campaigns' );
		return mailster_get_draft_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_finished_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_finished_campaigns' );
		return mailster_get_finished_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_pending_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_pending_campaigns' );
		return mailster_get_pending_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_autoresponder_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_autoresponder_campaigns' );
		return mailster_get_autoresponder_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_get_campaigns( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_campaigns' );
		return mailster_get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	function mymail_list_newsletter( $args = '' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_list_newsletter' );
		return mailster_list_newsletter( $args );
	}


	/**
	 *
	 *
	 * @param unknown $ip  (optional)
	 * @param unknown $get (optional)
	 * @return unknown
	 */
	function mymail_ip2Country( $ip = '', $get = 'code' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_ip2Country' );
		return mailster_ip2Country( $ip, $get );
	}


	/**
	 *
	 *
	 * @param unknown $ip  (optional)
	 * @param unknown $get (optional)
	 * @return unknown
	 */
	function mymail_ip2City( $ip = '', $get = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_ip2City' );
		return mailster_ip2City( $ip, $get );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_get_ip() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_ip' );
		return mailster_get_ip();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_is_local() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_is_local' );
		return mailster_is_local();
	}


	/**
	 *
	 *
	 * @param unknown $ip
	 * @return unknown
	 */
	function mymail_validate_ip( $ip ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_validate_ip' );
		return mailster_validate_ip( $ip );
	}


	/**
	 *
	 *
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	function mymail_get_lang( $fallback = false ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_lang' );
		return mailster_get_lang( $fallback );
	}


	/**
	 *
	 *
	 * @param unknown $string (optional)
	 * @return unknown
	 */
	function mymail_get_user_client( $string = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_user_client' );
		return mailster_get_user_client( $string );
	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @param unknown $userdata      (optional)
	 * @param unknown $lists         (optional)
	 * @param unknown $double_opt_in (optional)
	 * @param unknown $overwrite     (optional)
	 * @param unknown $mergelists    (optional)
	 * @param unknown $template      (optional)
	 * @return unknown
	 */
	function mymail_subscribe( $email, $userdata = array(), $lists = array(), $double_opt_in = null, $overwrite = true, $mergelists = null, $template = 'notification.html' ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_subscribe' );
		return mailster_subscribe( $email, $userdata, $lists, $double_op_in, $overwrite, $mergelists, $template );

	}


	/**
	 * depreciated
	 *
	 * @param unknown $email_hash_id
	 * @param unknown $campaign_id   (optional)
	 * @param unknown $logit         (optional)
	 * @return unknown
	 */
	function mymail_unsubscribe( $email_hash_id, $campaign_id = null, $logit = true ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_unsubscribe' );
		return mailster_unsubscribe( $email_hash_id, $campaign_id, $logit );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_get_subscribed_subscribers() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_subscribed_subscribers' );
		return mailster_get_subscribed_subscribers();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_get_unsubscribed_subscribers() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_unsubscribed_subscribers' );
		return mailster_get_unsubscribed_subscribers();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function mymail_get_hardbounced_subscribers() {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_hardbounced_subscribers' );
		return mailster_get_hardbounced_subscribers();
	}


	/**
	 *
	 *
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	function mymail_get_subscribers( $status = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_subscribers' );
		return mailster_get_subscribers( $status );
	}


	/**
	 *
	 *
	 * @param unknown $part       (optional)
	 * @param unknown $deprecated (optional)
	 * @return unknown
	 */
	function mymail_clear_cache( $part = '', $deprecated = false ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_clear_cache' );
		return mailster_clear_cache( $part, $deprecated );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $type       (optional)
	 * @param unknown $once       (optional)
	 * @param unknown $key        (optional)
	 * @param unknown $capability (optional)
	 * @return unknown
	 */
	function mymail_notice( $args, $type = '', $once = false, $key = null, $capability = true ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_notice' );
		return mailster_notice( $args, $type, $once, $key, $capability );
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @return unknown
	 */
	function mymail_remove_notice( $key ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_remove_notice' );
		return mailster_remove_notice( $key );
	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @return unknown
	 */
	function mymail_is_email( $email ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_is_email' );
		return mailster_is_email( $email );
	}


	/**
	 *
	 *
	 * @param unknown $id_email_or_hash
	 * @param unknown $type             (optional)
	 * @return unknown
	 */
	function mymail_get_subscriber( $id_email_or_hash, $type = null ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_get_subscriber' );
		return mailster_get_subscriber( $id_email_or_hash, $type );
	}


	/**
	 *
	 *
	 * @param unknown $tag
	 * @param unknown $callbackfunction
	 * @return unknown
	 */
	function mymail_add_tag( $tag, $callbackfunction ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_add_tag' );
		return mailster_add_tag( $tag, $callbackfunction );
	}


	/**
	 *
	 *
	 * @param unknown $tag
	 * @return unknown
	 */
	function mymail_remove_tag( $tag ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_remove_tag' );
		return mailster_remove_tag( $tag );
	}


	/**
	 *
	 *
	 * @param unknown $callbackfunction
	 * @return unknown
	 */
	function mymail_add_style( $callbackfunction ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_add_style' );
		return mailster_add_style( $callbackfunction );
	}


	/**
	 *
	 *
	 * @param unknown $text
	 * @return unknown
	 */
	function mymail_update_notice( $text ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_update_notice' );
		return mailster_update_notice( $text );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function is_mymail_newsletter_homepage() {
		_deprecated_function( __FUNCTION__, '2.3', 'is_mailster_newsletter_homepage' );
		return is_mailster_newsletter_homepage();
	}


	/**
	 *
	 *
	 * @param unknown $redirect (optional)
	 * @param unknown $method   (optional)
	 * @param unknown $showform (optional)
	 * @return unknown
	 */
	function mymail_require_filesystem( $redirect = '', $method = '', $showform = true ) {
		_deprecated_function( __FUNCTION__, '2.3', 'mailster_require_filesystem' );
		return mailster_require_filesystem( $redirect, $method, $showform );
	}

endif;
