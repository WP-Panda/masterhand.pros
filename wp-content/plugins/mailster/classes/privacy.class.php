<?php

class MailsterPrivacy {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'save_post', array( &$this, 'maybe_update_privacy_link' ), 10, 3 );

	}


	public function init() {

		add_action( 'wp_privacy_personal_data_exporters', array( &$this, 'register_exporter' ) );
		add_action( 'wp_privacy_personal_data_erasers', array( &$this, 'register_eraser' ) );
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( 'Mailster', $this->privacy_content() );
		}

	}

	public function privacy_content() {

		$content  =
			'<h2 class="privacy-policy-tutorial">' . esc_html__( 'What data Mailster collects from your subscribers', 'mailster' ) . '</h2>';
		$content .=
			'<h3 class="wp-policy-help">' . esc_html__( 'Newsletter', 'mailster' ) . '</h3>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'If you have signed up for our newsletter you may receive emails from us. This includes but not limited to transactional emails and marketing emails.', 'mailster' ) . '</p>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'We\'ll only send emails which you have explicitly or implicitly (registration, product purchase etc.) signed up to.', 'mailster' ) . '</p>';

		$tracked_fields = array(
			esc_html__( 'your email address', 'mailster' ),
			esc_html__( 'your name', 'mailster' ),
		);

		if ( $custom_fields = mailster()->get_custom_fields() ) {
			$custom_fields  = wp_list_pluck( $custom_fields, 'name' );
			$tracked_fields = array_merge( $tracked_fields, $custom_fields );
		}

		if ( mailster_option( 'track_location' ) ) {
			$tracked_fields[] = esc_html__( 'your current location', 'mailster' );
		}
		if ( mailster_option( 'track_users' ) ) {
			$tracked_fields[] = esc_html__( 'your current IP address and timestamp of signup', 'mailster' );
			$tracked_fields[] = esc_html__( 'your IP address and timestamp when you have confirmed your subscription', 'mailster' );
		}

		$content .=
			'<p class="wp-policy-help">' . sprintf( esc_html__( 'On signup we collect %s and the current web address were you sign up.', 'mailster' ), implode( ', ', $tracked_fields ) ) . '</p>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'We send our emails via', 'mailster' ) . ' ';

		if ( 'simple' == ( $deliverymethod = mailster_option( 'deliverymethod' ) ) ) {
			$content .= esc_html__( 'our own server.', 'mailster' );
		} elseif ( 'smtp' == $deliverymethod ) {
			$content .= sprintf( esc_html__( 'via SMTP host %s', 'mailster' ), mailster_option( 'smtp_host' ) );
		} elseif ( 'gmail' == $deliverymethod ) {
			$content .= sprintf( esc_html__( 'a service called %s', 'mailster' ), 'Gmail by Google' );
		} else {
			$content .= sprintf( esc_html__( 'a service called %s', 'mailster' ), $deliverymethod );
		}
		$content .=
			'</p>';

		$tracking = array();

		if ( mailster_option( 'track_opens' ) ) {
			$tracking[] = esc_html__( 'if you open the email in your email client', 'mailster' );
		}
		if ( mailster_option( 'track_clicks' ) ) {
			$tracking[] = esc_html__( 'if you click a link in the email', 'mailster' );
		}
		if ( mailster_option( 'track_location' ) ) {
			$tracking[] = esc_html__( 'your current location', 'mailster' );
		}
		if ( mailster_option( 'track_users' ) ) {
			$tracking[] = esc_html__( 'your current IP address', 'mailster' );
		}

		if ( ! empty( $tracking ) ) {
			$content .=
				'<p class="wp-policy-help">' . sprintf( esc_html__( 'Once you get an email from us we track %s.', 'mailster' ), implode( ', ', $tracking ) ) . '</p>';
		}

		if ( mailster_option( 'do_not_track' ) ) {
			$content .= '<p class="wp-policy-help">' . esc_html__( 'We respect your browsers "Do Not Track" feature which means we do not track your interaction with our emails.', 'mailster' ) . '</p>';
		}

		return apply_filters( 'mailster_privacy_content', $content );
	}

	public function register_exporter( $exporters ) {
		$exporters['mailster-exporter'] = array(
			'exporter_friendly_name' => esc_html__( 'Mailster Data', 'mailster' ),
			'callback'               => array( &$this, 'data_export' ),
		);
		return $exporters;
	}

	public function register_eraser( $eraser ) {
		$eraser['mailster-eraser'] = array(
			'eraser_friendly_name' => esc_html__( 'Mailster Data', 'mailster' ),
			'callback'             => array( &$this, 'data_erase' ),
		);
		return $eraser;
	}

	public function data_export( $email_address, $page = 1 ) {

		$export_items = array();

		if ( $subscriber = mailster( 'subscribers' )->get_by_mail( $email_address, true ) ) {

			$meta = mailster( 'subscribers' )->meta( $subscriber->ID );

			$data = array();

			// general data
			foreach ( $subscriber as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				if ( in_array( $key, array( 'added', 'updated', 'signup', 'confirm' ) ) ) {
					$value = mailster( 'helper' )->do_timestamp( $value );
				}
				$data[] = array(
					'name'  => $key,
					'value' => $value,
				);
			}

			// meta data
			foreach ( $meta as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				$data[] = array(
					'name'  => $key,
					'value' => $value,
				);
			}

			$export_items[] = array(
				'group_id'    => 'mailster',
				'group_label' => 'Mailster',
				'item_id'     => 'mailster-' . $subscriber->ID,
				'data'        => $data,
			);

			if ( $lists = mailster( 'subscribers' )->get_lists( $subscriber->ID ) ) {

				$data = array();
				// lists
				foreach ( $lists as $key => $value ) {
					$data[] = array(
						'name'  => esc_html__( 'List Name', 'mailster' ),
						'value' => $value->name,
					);
					$data[] = array(
						'name'  => esc_html__( 'Description', 'mailster' ),
						'value' => $value->description,
					);
					$data[] = array(
						'name'  => esc_html__( 'Added', 'mailster' ),
						'value' => mailster( 'helper' )->do_timestamp( $value->added ),
					);
					$data[] = array(
						'name'  => esc_html__( 'Confirmed', 'mailster' ),
						'value' => mailster( 'helper' )->do_timestamp( $value->confirmed ),
					);
				}

				$export_items[] = array(
					'group_id'    => 'mailster_lists',
					'group_label' => 'Mailster Lists',
					'item_id'     => 'mailster-lists-' . $subscriber->ID,
					'data'        => $data,
				);

			}

			if ( $activity = mailster( 'actions' )->get_activity( null, $subscriber->ID ) ) {
				$data      = array();
				$campaigns = array();

				// activity
				foreach ( $activity as $key => $value ) {

					if ( ! isset( $campaigns[ $value->campaign_id ] ) ) {
						$campaigns[ $value->campaign_id ] = array(
							'group_id'    => 'mailster_campaign_' . $value->campaign_id,
							'group_label' => 'Mailster Campaign "' . $value->campaign_title . '"',
							'item_id'     => 'mailster-campaign-' . $value->campaign_id,
							'data'        => array(),
						);
					}

					switch ( $value->type ) {
						case 1: // sent
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Sent', 'mailster' ),
								'value' => mailster( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;
						case 2: // opened
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Opened', 'mailster' ),
								'value' => mailster( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;
						case 3:  // clicked
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Clicked', 'mailster' ),
								'value' => mailster( 'helper' )->do_timestamp( $value->timestamp ) . ' (' . $value->link . ')',
							);
							break;
						case 4:  // clicked
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Unsubscribe', 'mailster' ),
								'value' => mailster( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;

					}
				}

				$export_items = array_merge( $export_items, array_values( $campaigns ) );

			}
		}

		return array(
			'data' => $export_items,
			'done' => true,
		);

	}

	public function data_erase( $email_address, $page = 1 ) {

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$subscriber = mailster( 'subscribers' )->get_by_mail( $email_address );

		if ( ! $subscriber ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$messages       = array();
		$items_removed  = false;
		$items_retained = false;

		if ( mailster( 'subscribers' )->remove( $subscriber->ID ) ) {
			$items_removed = true;
		} else {
			$items_retained = false;
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	public function maybe_update_privacy_link( $post_id, $post, $update = null ) {

		// only on update
		if ( ! $update || ! $post_id || ! $post ) {
			return;
		}

		if ( $privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' ) ) {
			if ( $post_id == $privacy_policy_page_id ) {
				$link      = get_permalink( $post_id );
				$gdpr_link = mailster_option( 'gdpr_link' );
				if ( $gdpr_link && $link != $gdpr_link ) {
					if ( mailster_update_option( 'gdpr_link', $link ) && mailster_option( 'gdpr_forms' ) ) {
						mailster_notice( '[Mailster] ' . sprintf( esc_html__( 'The Privacy page link has been changed to %s', 'mailster' ), '<em>' . $link . '</em>' ), 'info', true );
					}
				}
			}
		}

	}

}
