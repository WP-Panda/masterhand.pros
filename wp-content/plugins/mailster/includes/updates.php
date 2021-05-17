<?php

/*
This runs if an update was done.
*/

global $wpdb;

$mailster_options = mailster_options();
$mailster_texts   = mailster_texts();

$new_version = MAILSTER_VERSION;

$texts              = isset( $mailster_options['text'] ) && ! empty( $mailster_options['text'] ) ? $mailster_options['text'] : $mailster_texts;
$show_update_notice = false;

$default_options = mailster( 'settings' )->get_defaults();
$default_texts   = mailster( 'settings' )->get_default_texts();

if ( $old_version ) {

	// remove any branch version from the string.
	$old_version_sanitized = preg_replace( '#^([^a-z]+)(\.|-)([a-z_]+)(.*?)$#i', '$1', $old_version );

	switch ( $old_version_sanitized ) {
		case '1.0':
		case '1.0.1':
			mailster_notice( '[1.1.0] Capabilities are now available. Please check the <a href="edit.php?post_type=newsletter&page=mailster_settings#capabilities">settings page</a>' );
			mailster_notice( '[1.1.0] Custom Fields now support dropbox and radio button. Please check the <a href="edit.php?post_type=newsletter&page=mailster_settings#subscribers">settings page</a>' );

			$texts['firstname'] = esc_html__( 'First Name', 'mailster' );
			$texts['lastname']  = esc_html__( 'Last Name', 'mailster' );

		case '1.1.0':
			$texts['email']             = esc_html__( 'Email', 'mailster' );
			$texts['submitbutton']      = esc_html__( 'Subscribe', 'mailster' );
			$texts['unsubscribebutton'] = esc_html__( 'Yes, unsubscribe me', 'mailster' );
			$texts['unsubscribelink']   = esc_html__( 'unsubscribe', 'mailster' );
			$texts['webversion']        = esc_html__( 'webversion', 'mailster' );

		case '1.1.1.1':
			$texts['lists'] = esc_html__( 'Lists', 'mailster' );

			mailster_notice( '[1.2.0] Auto responders are now available! Please set the <a href="edit.php?post_type=newsletter&page=mailster_settings#capabilities">capabilities</a> to get access' );

		case '1.2.0':
			$mailster_options['send_limit']  = 10000;
			$mailster_options['send_period'] = 24;
			$mailster_options['ajax_form']   = true;

			$texts['unsubscribeerror'] = esc_html__( 'An error occurred! Please try again later!', 'mailster' );

			mailster_notice( '[1.2.1] New capabilities available! Please update them in the <a href="edit.php?post_type=newsletter&page=mailster_settings#capabilities">settings</a>' );

		case '1.2.1':
		case '1.2.1.1':
		case '1.2.1.2':
		case '1.2.1.3':
		case '1.2.1.4':
			mailster_notice( '[1.2.2] New capability: "manage capabilities". Please check the <a href="edit.php?post_type=newsletter&page=mailster_settings#capabilities">settings page</a>' );
		case '1.2.2':
		case '1.2.2.1':
			$mailster_options['post_count'] = 30;
			mailster_notice( '[1.3.0] Track your visitors cities! Activate the option on the <a href="edit.php?post_type=newsletter&page=mailster_settings#general">settings page</a>' );

			$texts['forward'] = esc_html__( 'forward to a friend', 'mailster' );


		case '1.3.0':
			$mailster_options['frontpage_pagination'] = true;
			$mailster_options['basicmethod']          = 'sendmail';
			$mailster_options['deliverymethod']       = ( mailster_option( 'smtp' ) ) ? 'smtp' : 'simple';
			$mailster_options['bounce_active']        = ( mailster_option( 'bounce_server' ) && mailster_option( 'bounce_user' ) && mailster_option( 'bounce_pwd' ) );

			$mailster_options['spf_domain']   = $mailster_options['dkim_domain'];
			$mailster_options['send_offset']  = $mailster_options['send_delay'];
			$mailster_options['send_delay']   = 0;
			$mailster_options['smtp_timeout'] = 10;


			mailster_notice( '[1.3.1] DKIM is now better supported but you have to check  <a href="edit.php?post_type=newsletter&page=mailster_settings#general">settings page</a>' );

		case '1.3.1':
		case '1.3.1.1':
		case '1.3.1.2':
		case '1.3.1.3':
		case '1.3.2':
		case '1.3.2.1':
		case '1.3.2.2':
		case '1.3.2.3':
		case '1.3.2.4':
			delete_option( 'mailster_bulk_imports' );
			$forms                     = $mailster_options['forms'];
			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['prefill']             = true;
				$mailster_options['forms'][] = $form;
			}

			mailster_notice( '[1.3.3] New capability: "manage subscribers". Please check the <a href="edit.php?post_type=newsletter&page=mailster_settings#capabilities">capabilities settings page</a>' );
		case '1.3.3':
		case '1.3.3.1':
		case '1.3.3.2':
			$mailster_options['subscription_resend_count'] = 2;
			$mailster_options['subscription_resend_time']  = 48;


		case '1.3.4':
			$mailster_options['sendmail_path'] = '/usr/sbin/sendmail';
		case '1.3.4.1':
		case '1.3.4.2':
		case '1.3.4.3':
			$forms        = $mailster_options['forms'];
			$customfields = mailster_option( 'custom_field', array() );

			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				$order = array( 'email' );
				if ( isset( $mailster_options['firstname'] ) ) {
					$order[] = 'firstname';
				}
				if ( isset( $mailster_options['lastname'] ) ) {
					$order[] = 'lastname';
				}
				$required = array( 'email' );
				if ( isset( $mailster_options['require_firstname'] ) ) {
					$required[] = 'firstname';
				}
				if ( isset( $mailster_options['require_lastname'] ) ) {
					$required[] = 'lastname';
				}

				foreach ( $customfields as $field => $data ) {
					if ( isset( $data['ask'] ) ) {
						$order[] = $field;
					}
					if ( isset( $data['required'] ) ) {
						$required[] = $field;
					}
				}
				$form['order']               = $order;
				$form['required']            = $required;
				$mailster_options['forms'][] = $form;
			}

		case '1.3.4.4':
		case '1.3.4.5':
		case '1.3.5':
		case '1.3.6':
		case '1.3.6.1':
			add_action( 'shutdown', array( $mailster_templates, 'renew_default_template' ) );

		case '1.4.0':
		case '1.4.0.1':
			$lists                                    = isset( $mailster_options['newusers'] ) ? $mailster_options['newusers'] : array();
			$mailster_options['register_other_lists'] = $mailster_options['register_comment_form_lists'] = $mailster_options['register_signup_lists'] = $lists;
			$mailster_options['register_comment_form_status'] = array( '1', '0' );
			if ( ! empty( $lists ) ) {
				$mailster_options['register_other'] = true;
			}

			$texts['newsletter_signup'] = esc_html__( 'Sign up to our newsletter', 'mailster' );

			mailster_notice( '[1.4.1] New option for WordPress Users! Please <a href="edit.php?post_type=newsletter&page=mailster_settings#subscribers">update your settings</a>!' );
			mailster_notice( '[1.4.1] New text for newsletter sign up Please <a href="edit.php?post_type=newsletter&page=mailster_settings#texts">update your settings</a>!' );

		case '1.4.1':
		case '1.5.0':
		case '1.5.1':
		case '1.5.1.1':
		case '1.5.1.2':
			set_transient( 'mailster_dkim_records', array(), 1 );

			mailster_notice( '[1.5.2] Since Twitter dropped support for API 1.0 you have to create a new app if you would like to use the <code>{tweet:username}</code> tag. Enter your credentials <a href="edit.php?post_type=newsletter&page=mailster_settings#tags">here</a>!' );

		case '1.5.2':
			update_option( 'envato_plugins', '' );

		case '1.5.3':
		case '1.5.3.1':
		case '1.5.3.2':
			$mailster_options['charset']  = 'UTF-8';
			$mailster_options['encoding'] = '8bit';

			$forms = $mailster_options['forms'];

			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['asterisk']            = true;
				$mailster_options['forms'][] = $form;
			}

		case '1.5.4':
		case '1.5.4.1':
		case '1.5.5':
		case '1.5.5.1':
		case '1.5.6':
		case '1.5.7':
		case '1.5.7.1':
			$forms = $mailster_options['forms'];

			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['submitbutton']        = mailster_text( 'submitbutton' );
				$mailster_options['forms'][] = $form;
			}

		case '1.5.8':
			$forms = $mailster_options['forms'];

			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				if ( is_numeric( $form['submitbutton'] ) ) {
					$form['submitbutton'] = '';
				}
				$mailster_options['forms'][] = $form;
			}

		case '1.5.8.1':
		case '1.6.0':
			$mailster_options['slug'] = 'newsletter';

		case '1.6.1':
			if ( ! isset( $mailster_options['slug'] ) ) {
				$mailster_options['slug'] = 'newsletter';
			}


		case '1.6.2':
		case '1.6.2.1':
		case '1.6.2.2':
			// just a random ID for better bounces
			$mailster_options['ID']           = md5( uniqid() );
			$mailster_options['bounce_check'] = 5;
			$mailster_options['bounce_delay'] = 60;

		case '1.6.3':
		case '1.6.3.1':
		case '1.6.4':
		case '1.6.4.1':
		case '1.6.4.2':
			$forms = $mailster_options['forms'];

			$mailster_options['forms'] = array();
			foreach ( $forms as $form ) {
				if ( ! isset( $form['text'] ) ) {
					$form['precheck']                  = true;
					$form['double_opt_in']             = mailster_option( 'double_opt_in' );
					$form['text']                      = mailster_option( 'text' );
					$form['subscription_resend']       = mailster_option( 'subscription_resend' );
					$form['subscription_resend_count'] = mailster_option( 'subscription_resend_count' );
					$form['subscription_resend_time']  = mailster_option( 'subscription_resend_time' );
					$form['vcard']                     = mailster_option( 'vcard' );
					$form['vcard_filename']            = mailster_option( 'vcard_filename' );
					$form['vcard_content']             = mailster_option( 'vcard_content' );
				}
				$mailster_options['forms'][] = $form;
			}

			mailster_notice( '[1.6.5] Double-Opt-In options are now form specific. Please <a href="edit.php?post_type=newsletter&page=mailster_forms">check your forms</a> if everything has been converted correctly!', '', false, 'update165' );

		case '1.6.5':
		case '1.6.5.1':
		case '1.6.5.2':
		case '1.6.5.3':
		case '1.6.6':
		case '1.6.6.1':
		case '1.6.6.2':
		case '1.6.6.3':
		case '2.0 beta 1':
		case '2.0 beta 1.1':
			$campaigns = mailster( 'campaigns' )->get_autoresponder();

			foreach ( $campaigns as $campaign ) {

				$meta = mailster( 'campaigns' )->meta( $campaign->ID );

				if ( $meta['active'] ) {

					mailster( 'campaigns' )->update_meta( $campaign->ID, 'active', false );
					mailster_notice( 'Autoresponders have been disabled cause of some internal change. Please <a href="edit.php?post_status=autoresponder&post_type=newsletter&mailster_remove_notice=autorespondersdisabled">update them to reactivate them</a>', '', false, 'autorespondersdisabled' );

				}
			}



		case '2.0 beta 2':
		case '2.0 beta 2.1':
		case '2.0 beta 3':
			$mailster_options['autoupdate'] = 'minor';

		case '2.0RC 1':
		case '2.0RC 2':
			delete_option( 'envato_plugins' );
			delete_option( 'updatecenter_plugins' );

		case '2.0':
		case '2.0.1':
		case '2.0.2':
		case '2.0.3':
		case '2.0.4':
		case '2.0.5':
		case '2.0.6':
		case '2.0.7':
			$mailster_options['pause_campaigns'] = true;
		case '2.0.8':
		case '2.0.9':
			$mailster_options['slugs'] = array(
				'confirm'     => 'confirm',
				'subscribe'   => 'subscribe',
				'unsubscribe' => 'unsubscribe',
				'profile'     => 'profile',
			);

			$mailster_options['_flush_rewrite_rules'] = true;
		case '2.0.10':
		case '2.0.11':
		case '2.0.12':
			$mailster_options['_flush_rewrite_rules'] = true;
		case '2.0.13':
			$forms = $mailster_options['forms'];
			$optin = isset( $forms[0] ) && isset( $forms[0]['double_opt_in'] );
			$mailster_options['register_comment_form_confirmation'] = $optin;
			$mailster_options['register_signup_confirmation']       = $optin;

		case '2.0.14':
			global $wp_roles;

			if ( $wp_roles ) {
				$roles                                    = $wp_roles->get_names();
				$mailster_options['register_other_roles'] = array_keys( $roles );
			}

		case '2.0.15':
		case '2.0.16':
		case '2.0.17':
		case '2.0.18':
		case '2.0.19':
		case '2.0.20':
		case '2.0.21':
		case '2.0.22':
		case '2.0.23':
		case '2.0.24':
		case '2.0.25':
		case '2.0.26':
		case '2.0.27':
		case '2.0.28':
		case '2.0.29':
		case '2.0.30':
		case '2.0.31':
		case '2.0.32':
		case '2.0.33':
		case '2.0.34':
			mailster_notice( 'Please clear your cache if you are using page cache on your site', '', false, 'mailsterpagecache' );
			$mailster_options['welcome'] = true;

		case '2.1':
		case '2.1.1':
			if ( $mailster_options['php_mailer'] ) {
				$mailster_options['php_mailer'] = '5.2.14';
			}
			$mailster_options['archive_slug']         = $mailster_options['slug'];
			$mailster_options['archive_types']        = array( 'finished', 'active' );
			$mailster_options['module_thumbnails']    = true;
			$mailster_options['_flush_rewrite_rules'] = true;

		case '2.1.2':
		case '2.1.3':
		case '2.1.4':
		case '2.1.5':
		case '2.1.6':
			$mailster_options['got_url_rewrite'] = mailster( 'helper' )->got_url_rewrite();

		case '2.1.7':
		case '2.1.8':
			$mailster_options['_flush_rewrite_rules'] = true;

		case '2.1.9':
			$texts = wp_parse_args( $texts, $default_texts );

			$t = mailster( 'translations' )->get_translation_data();

			if ( ! empty( $t ) ) {
				mailster_notice( sprintf( 'An important change to localizations in Mailster has been made. <a href="%s">read more</a>', 'https://kb.mailster.co/translations-in-mailster/' ), '', false, 'mailstertranslation' );
			}

			unset( $mailster_options['texts'] );
			$show_update_notice = true;

		case '2.1.10':
		case '2.1.11':
		case '2.1.12':
		case '2.1.13':
		case '2.1.14':
		case '2.1.15':
		case '2.1.16':
		case '2.1.16.1':
		case '2.1.17':
		case '2.1.18':
			mailster( 'cron' )->unlock( 0 );

		case '2.1.19':
		case '2.1.20':
		case '2.1.21':
		case '2.1.22':
		case '2.1.23':
		case '2.1.24':
		case '2.1.25':
			if ( isset( $mailster_options['smtp_auth'] ) ) {
				$mailster_options['smtp_auth'] = 'LOGIN';
			}
			if ( $mailster_options['php_mailer'] == '5.2.7' ) {
				$mailster_options['php_mailer'] = false;
			}

		case '2.1.26':
		case '2.1.27':
		case '2.1.28':
			if ( isset( $mailster_options['dkim'] ) && isset( $mailster_options['dkim_private_key'] ) && empty( $mailster_options['dkim_private_hash'] ) ) {
				$mailster_options['dkim_private_hash'] = md5( $mailster_options['dkim_private_key'] );
			}

		case '2.1.29':
		case '2.1.30':
			if ( isset( $mailster_options['php_mailer'] ) && $mailster_options['php_mailer'] ) {
				mailster_notice( sprintf( 'PHPMailer has been updated to 5.2.21. <a href="%s">read more</a>', 'https://github.com/PHPMailer/PHPMailer/releases/tag/v5.2.20' ), '', false, 'phpmailer' );
				$mailster_options['php_mailer'] = 'latest';
			}

		case '2.1.31':
		case '2.1.32':
		case '2.1.33':
			$mailster_options['tags']['address'] = '';
			$mailster_options['high_dpi']        = true;
			update_option( 'mailster', time() );
			update_option( 'mailster_setup', time() );
			update_option( 'mailster_templates', '' );
			update_option( 'mailster_cron_lasthit', '' );
			delete_option( 'mailster_purchasecode_disabled' );
			$mailster_options['welcome']              = true;
			$mailster_options['legacy_hooks']         = true;
			$mailster_options['_flush_rewrite_rules'] = true;
			update_option( 'mailster_license', $mailster_options['purchasecode'] );

		case '2.2':
		case '2.2.1':
		case '2.2.2':
			$mailster_options['_flush_rewrite_rules'] = true;

		case '2.2.3':
		case '2.2.4':
			$wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'no' WHERE option_name IN ('mailster_templates', 'mailster_cron_lasthit')" );

		case '2.2.5':
		case '2.2.6':
			$wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'yes' WHERE option_name IN ('mailster_username', 'mailster_email')" );

		case '2.2.7':
		case '2.2.8':
		case '2.2.9':
		case '2.2.10':
			update_option( 'mailster_hooks', get_option( 'mailster_hooks', '' ) );

		case '2.2.11':
		case '2.2.12':
		case '2.2.13':
		case '2.2.14':
		case '2.2.15':
		case '2.2.16':
		case '2.2.17':
		case '2.2.18':
			// since 2.3
			$mailster_options['webversion_bar'] = true;
			$mailster_options['track_opens']    = true;
			$mailster_options['track_clicks']   = true;

			update_option( 'mailster_cron_lasthit', '' );

			// allow NULL values on two columns
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mailster_actions CHANGE `subscriber_id` `subscriber_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mailster_actions CHANGE `campaign_id` `campaign_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );

			$mailster_options['welcome']              = true;
			$mailster_options['_flush_rewrite_rules'] = true;
			$show_update_notice                       = true;

		case '2.3':
		case '2.3.1':
		case '2.3.2':
		case '2.3.3':
		case '2.3.4':
		case '2.3.5':
			$mailster_options['track_location'] = $mailster_options['trackcountries'];

		case '2.3.6':
			$mailster_options['gdpr_link']  = $default_options['gdpr_link'];
			$mailster_options['gdpr_text']  = $default_options['gdpr_text'];
			$mailster_options['gdpr_error'] = $default_options['gdpr_error'];

		case '2.3.7':
			mailster( 'helper' )->mkdir( '', true );
			mailster( 'helper' )->mkdir( 'templates', true );
			mailster( 'helper' )->mkdir( 'screenshots', true );
			mailster( 'helper' )->mkdir( 'backgrounds', true );

		case '2.3.8':
		case '2.3.9':
		case '2.3.10':
		case '2.3.11':
		case '2.3.12':
		case '2.3.13':
			// allow NULL values on one column
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mailster_subscriber_meta CHANGE `subscriber_id` `subscriber_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );
			$mailster_options['_flush_rewrite_rules'] = true;
		case '2.3.14':
			// remove entries caused by wrong tracking
			$wpdb->query( "DELETE FROM {$wpdb->prefix}mailster_actions WHERE subscriber_id = 0" );

		case '2.3.15':
		case '2.3.16':
			$mailster_options['ask_usage_tracking'] = true;

		case '2.3.17':
			if ( isset( $mailster_options['bounce_ssl'] ) && $mailster_options['bounce_ssl'] ) {
				$mailster_options['bounce_secure'] = 'ssl';
			}

		case '2.3.18':
		case '2.3.19':
			// no longer in use
			delete_option( 'mailster_template_licenses' );
			$mailster_options['welcome'] = true;

		case '2.4':
		case '2.4.1':
			// changes dummy image server
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s) WHERE post_type = 'newsletter'", '//dummy.newsletter-plugin.com/', '//dummy.mailster.co/' ) );

		case '2.4.2':
			$mailster_options['_flush_rewrite_rules'] = true;

		case '2.4.3':
			if ( get_option( 'mailster' ) && get_option( 'mailster' ) < strtotime( '2018-01-01 00:00' ) ) {
				$mailster_options['legacy_hooks'] = true;
			}
			// prefix mailster entries with "_"
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->usermeta} SET `meta_key` = replace(meta_key, %s, %s) WHERE meta_key LIKE 'mailster_%'", 'mailster_', '_mailster_' ) );

		case '2.4.4':
		case '2.4.5':
		case '2.4.5.1':
			// remove white space from these fields
			$wpdb->query( "UPDATE {$wpdb->prefix}mailster_forms SET `redirect` = TRIM(redirect)" );
			$wpdb->query( "UPDATE {$wpdb->prefix}mailster_forms SET `confirmredirect` = TRIM(confirmredirect)" );

		case '2.4.6':
		case '2.4.7':
		case '2.4.8':
			$texts['gdpr_text']  = $mailster_options['gdpr_text'];
			$texts['gdpr_error'] = $mailster_options['gdpr_error'];

			update_option( 'mailster_templates', '' );

		case '2.4.9':
			delete_option( 'mailster_recent_feeds' );

		case '2.4.10':
			$mailster_options['mail_opt_out'] = isset( $mailster_options['bounce'] ) && $mailster_options['bounce'];

			if ( ! is_plugin_active( 'mailster-gmail/mailster-gmail.php' ) && 'gmail' == $mailster_options['deliverymethod'] ) {

				if ( $mailster_option['gmail_user'] && $mailster_option['gmail_pwd'] ) {
					$mailster_options['smtp_host']    = 'smtp.googlemail.com';
					$mailster_options['smtp_port']    = 587;
					$mailster_options['smtp_timeout'] = 10;
					$mailster_options['smtp_auth']    = true;
					$mailster_options['smtp_user']    = mailster_option( 'gmail_user' );
					$mailster_options['smtp_pwd']     = mailster_option( 'gmail_pwd' );
					$mailster_options['smtp_secure']  = 'tls';
					$mailster_options['gmail_user']   = '';
					$mailster_options['gmail_pwd']    = '';

				}

				mailster_notice( sprintf( esc_html__( 'The Gmail Sending Method is deprecated and will soon not work anymore! Please update to the new plugin %1$s and follow our setup guide %2$s.', 'mailster-gmail' ), '<a href="' . admin_url( 'plugin-install.php?s=mailster-gmail+everpress&tab=search&type=term' ) . '">Mailster Gmail Integration</a>', '<a href="https://kb.mailster.co/send-your-newsletters-via-gmail/" class="external">' . esc_html__( 'here', 'mailster' ) . '</a>' ), 'error', false, 'gmail_deprecated' );
			}

		case '2.4.11':
		case '2.4.12':
			delete_transient( 'mailster_verified' );

		case '2.4.13':
		case '2.4.14':
		case '2.4.15':
		case '2.4.16':
			if ( $mailster_options['track_location'] && $mailster_options['track_location_update'] ) {
				mailster( 'geo' )->update();
			}

		default:
			// reset translations
			update_option( 'mailster_translation', '' );

			do_action( 'mailster_update', $old_version_sanitized, $new_version );
			do_action( 'mailster_update_' . $old_version_sanitized, $new_version );

	}

	update_option( 'mailster_version_old', $old_version );
	update_option( 'mailster_updated', time() );

}

// do stuff every update
$mailster_texts = $texts;

// update options
update_option( 'mailster_options', $mailster_options );
// update texts
update_option( 'mailster_texts', $mailster_texts );

// update caps
mailster( 'settings' )->update_capabilities();

// clear cache
mailster_clear_cache();

// delete plugin hash
delete_transient( 'mailster_hash' );


// mailster_update_option('welcome', true);
add_action( 'shutdown', array( 'UpdateCenterPlugin', 'clear_options' ) );

if ( $old_version && $show_update_notice ) {
	mailster_notice(
		array(
			'key' => 'update_info',
			'cb'  => 'mailster_update_notice',
		)
	);
}
