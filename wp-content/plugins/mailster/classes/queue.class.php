<?php

class MailsterQueue {

	private $max_retry_after_error = 3;

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		add_action( 'mailster_cron', array( &$this, 'update_status' ), 10 );
		add_action( 'mailster_cron', array( &$this, 'update' ), 20 );
		add_action( 'mailster_cron_cleanup', array( &$this, 'cleanup' ), 50 );

		add_action( 'mailster_cron_worker', array( &$this, 'update_status' ), 10 );
		add_action( 'mailster_cron_worker', array( &$this, 'update' ), 20 );
		add_action( 'mailster_cron_worker', array( &$this, 'progress' ), 50 );
		add_action( 'mailster_cron_worker', array( &$this, 'finish_campaigns' ), 100 );

		add_action( 'mailster_cron_autoresponder', array( &$this, 'autoresponder_timebased' ), 30 );
		add_action( 'mailster_cron_autoresponder', array( &$this, 'autoresponder_usertime' ), 30 );
		add_action( 'mailster_cron_autoresponder', array( &$this, 'autoresponder' ), 30 );

		add_action( 'mailster_update_queue', array( &$this, 'autoresponder' ), 30 );
		add_action( 'mailster_update_queue', array( &$this, 'update_status' ), 30 );
		add_action( 'mailster_update_queue', array( &$this, 'update' ), 30 );

		add_action( 'mailster_bounce', array( &$this, 'update_after_bounce' ), 10, 3 );

		// hooks to remove subscriber from the queue
		if ( ! defined( 'MAILSTER_DO_BULKIMPORT' ) ) {
			add_action( 'mailster_subscriber_change_status', array( &$this, 'subscriber_change_status' ), 10, 3 );
			add_action( 'mailster_unassign_lists', array( &$this, 'unassign_lists' ), 10, 3 );
			add_action( 'mailster_update_subscriber', array( &$this, 'update_subscriber' ), 10, 3 );
		}

	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @return unknown
	 */
	public function add( $args ) {

		global $wpdb;

		$now = time();

		$args = wp_parse_args(
			$args,
			array(
				'added'     => $now,
				'timestamp' => $now,
				'priority'  => 10,
				'count'     => 1,
				'sent'      => 0,
			)
		);

		if ( isset( $args['options'] ) ) {
			$args['options'] = esc_sql( maybe_serialize( $args['options'] ) );
		}

		$sql = "INSERT INTO {$wpdb->prefix}mailster_queue (" . implode( ', ', array_keys( $args ) ) . ')';

		$sql .= " VALUES ('" . implode( "','", array_values( $args ) ) . "')";

		$sql .= ' ON DUPLICATE KEY UPDATE count = count+1, timestamp = values(timestamp), sent = values(sent), priority = values(priority), tags = values(tags)';

		return false !== $wpdb->query( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $hard
	 * @return unknown
	 */
	public function update_after_bounce( $subscriber_id, $campaign_id, $hard ) {

		// only softbounce
		if ( $hard ) {
			// remove all from queue
			$this->remove_subscribers( $subscriber_id );
			return;
		}

		$now   = time();
		$delay = mailster_option( 'bounce_delay', 60 ) * 60;

		return $this->add(
			array(
				'campaign_id'   => $campaign_id,
				'subscriber_id' => $subscriber_id,
				'timestamp'     => $now + $delay,
				'priority'      => 15,
				'count'         => 2,
				'requeued'      => 1,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscribers
	 * @param unknown $timestamp     (optional)
	 * @param unknown $priority      (optional)
	 * @param unknown $clear         (optional)
	 * @param unknown $ignore_status (optional)
	 * @param unknown $reset         (optional)
	 * @param unknown $options       (optional)
	 * @param unknown $tags          (optional)
	 * @return unknown
	 */
	public function bulk_add( $campaign_id, $subscribers, $timestamp = null, $priority = 10, $clear = false, $ignore_status = false, $reset = false, $options = false, $tags = false ) {

		global $wpdb;

		if ( $clear ) {
			$this->clear( $campaign_id, $subscribers );
		}

		if ( empty( $subscribers ) ) {
			return;
		}

		if ( is_null( $timestamp ) ) {
			$timestamp = time();
		}

		$timestamps = ! is_array( $timestamp )
			? array_fill( 0, count( $subscribers ), $timestamp )
			: $timestamp;

		$now = time();

		$campaign_id = (int) $campaign_id;
		$subscribers = array_filter( $subscribers, 'is_numeric' );

		if ( ! empty( $tags ) ) {
			$tags = maybe_serialize( $tags );
		} else {
			$tags = '';
		}
		if ( $options ) {
			$options = maybe_serialize( $options );
		}

		if ( empty( $subscribers ) ) {
			return true;
		}

		$inserts = array();

		foreach ( $subscribers as $i => $subscriber_id ) {
			$inserts[] = "($subscriber_id,$campaign_id,$now," . $timestamps[ $i ] . ",$priority,1,'$ignore_status','$options','$tags')";
		}

		$chunks = array_chunk( $inserts, 2000 );

		$success = true;

		foreach ( $chunks as $insert ) {
			$sql = "INSERT INTO {$wpdb->prefix}mailster_queue (subscriber_id, campaign_id, added, timestamp, priority, count, ignore_status, options, tags) VALUES";

			$sql .= ' ' . implode( ',', $insert );

			$sql .= ' ON DUPLICATE KEY UPDATE timestamp = values(timestamp), ignore_status = values(ignore_status)';
			if ( $reset ) {
				$sql .= ', sent = 0';
			}
			if ( $options ) {
				$sql .= sprintf( ", options = '%s'", $options );
			}
			if ( $tags ) {
				$sql .= sprintf( ", tags = '%s'", $tags );
			}

			$success = $success && false !== $wpdb->query( $sql );

		}

		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $subscribers (optional)
	 * @param unknown $requeued    (optional)
	 * @return unknown
	 */
	public function remove( $campaign_id = null, $subscribers = null, $requeued = false ) {

		global $wpdb;

		$sql = "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE 1";
		if ( ! is_null( $campaign_id ) ) {
			$sql .= $wpdb->prepare( ' AND a.campaign_id = %d', $campaign_id );
		}

		if ( ! $requeued ) {
			$sql .= ' AND a.requeued = 0';
		}

		if ( ! is_null( $subscribers ) ) {
			if ( ! is_array( $subscribers ) ) {
				$subscriber = array( $subscribers );
			}

			$subscribers = array_filter( $subscribers, 'is_numeric' );
			if ( empty( $subscribers ) ) {
				$subscribers = array( -1 );
			}

			$sql .= ' AND a.subscriber_id NOT IN (' . implode( ',', $subscribers ) . ')';

		}

		return false !== $wpdb->query( $sql );

	}

	/**
	 *
	 *
	 * @param unknown $subscribers (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function remove_subscribers( $subscribers, $campaign_id = null ) {

		global $wpdb;

		$sql = "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE 1";
		if ( ! is_null( $campaign_id ) ) {
			$sql .= $wpdb->prepare( ' AND a.campaign_id = %d', $campaign_id );
		}

		if ( ! is_array( $subscribers ) ) {
			$subscribers = array( (int) $subscribers );
		}

		$subscribers = array_filter( $subscribers, 'is_numeric' );
		if ( empty( $subscribers ) ) {
			$subscribers = array( -1 );
		}

		$sql .= ' AND a.subscriber_id IN (' . implode( ',', $subscribers ) . ')';

		return false !== $wpdb->query( $sql );

	}


	/**
	 * clear queue from subscribers who where previously in the queue but no longer assigned to the campaign
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $subscribers (optional)
	 * @return unknown
	 */
	public function clear( $campaign_id = null, $subscribers = array() ) {

		global $wpdb;

		$campaign_id = (int) $campaign_id;
		$subscribers = array_filter( $subscribers, 'is_numeric' );

		if ( empty( $subscribers ) ) {
			$subscribers = array( -1 );
		}

		$sql = "DELETE queue FROM {$wpdb->prefix}mailster_queue AS queue WHERE queue.sent = 0 AND queue.subscriber_id NOT IN (" . implode( ',', $subscribers ) . ')';
		if ( ! is_null( $campaign_id ) ) {
			$sql .= $wpdb->prepare( ' AND queue.campaign_id = %d', $campaign_id );
		}

		return false !== $wpdb->query( $sql );

	}


	public function cleanup() {

		global $wpdb;

		// remove all entries from the queue where subscribers are hardbounced
		$wpdb->query( "DELETE queue FROM {$wpdb->prefix}mailster_queue AS queue LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON queue.subscriber_id = b.subscriber_id AND queue.campaign_id = b.campaign_id WHERE b.type = 5 AND queue.requeued = 1 AND queue.sent != 0" );

		// remove all entries from the queue where subscribers got queue certain autoresponder and are sent already.
		$wpdb->query( "DELETE queue FROM {$wpdb->prefix}mailster_queue AS queue LEFT JOIN {$wpdb->posts} AS p ON p.ID = queue.campaign_id AND p.post_status = 'autoresponder' WHERE sent != 0 AND sent < " . ( time() ) );

		// remove all entries from the queue where campaign has been removed
		$wpdb->query( "DELETE queue FROM {$wpdb->prefix}mailster_queue AS queue LEFT JOIN {$wpdb->posts} AS p ON p.ID = queue.campaign_id AND p.post_type = 'newsletter' WHERE p.ID IS NULL AND queue.campaign_id != 0" );

	}


	public function update_status() {

		$campaigns = mailster( 'campaigns' )->get_campaigns( array( 'post_status' => array( 'queued' ) ) );

		$now = time();

		foreach ( $campaigns as $campaign ) {

			if ( $campaign->post_status != 'queued' ) {
				continue;
			}

			$timestamp = mailster( 'campaigns' )->meta( $campaign->ID, 'timestamp' );
			$timezone  = mailster( 'campaigns' )->meta( $campaign->ID, 'timezone' );

			// change status to active 24h if user based timezone is enabled
			if ( $timestamp - $now <= ( $timezone ? 86400 : 0 ) ) {
				mailster( 'campaigns' )->change_status( $campaign, 'active' );
			}
		}
	}


	public function update() {

		global $wpdb;
		// update the regular queue
		$campaigns = mailster( 'campaigns' )->get_campaigns( array( 'post_status' => array( 'active' ) ) );

		$now = time();

		foreach ( $campaigns as $campaign ) {

			if ( $campaign->post_status != 'active' ) {
				continue;
			}

			// check if subscribers have to get queue
			if ( ! mailster( 'campaigns' )->get_unsent_subscribers( $campaign->ID, array( 1 ), true, true ) ) {
				continue;
			}

			$timestamp = mailster( 'campaigns' )->meta( $campaign->ID, 'timestamp' );
			$timezone  = mailster( 'campaigns' )->meta( $campaign->ID, 'timezone' );

			$offset = 0;
			$limit  = 100000;

			// as long we have subscribers
			while ( count( $subscribers = mailster( 'campaigns' )->get_unsent_subscribers( $campaign->ID, array( 1 ), true, true, $limit, $offset ) ) ) {

				// get users timeoffsets
				if ( $timezone ) {
					$timestamp = mailster( 'subscribers' )->get_timeoffset_timestamps( $subscribers, $timestamp );
				}

				$this->bulk_add( $campaign->ID, $subscribers, $timestamp, 10, false );

				$offset += $limit;
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $force       (optional)
	 */
	public function autoresponder( $campaign_id = null, $force = false ) {

		global $wpdb;
		static $mailster_autoresponder;
		if ( ! isset( $mailster_autoresponder ) ) {
			$mailster_autoresponder = array();
		}

		$campaigns = empty( $campaign_id ) ? mailster( 'campaigns' )->get_autoresponder() : array( mailster( 'campaigns' )->get( $campaign_id ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		$now        = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		foreach ( $campaigns as $campaign ) {

			if ( $campaign->post_status != 'autoresponder' ) {
				continue;
			}

			if ( in_array( $campaign->ID, $mailster_autoresponder ) && ! $force ) {
				continue;
			}

			$mailster_autoresponder[] = $campaign->ID;

			$meta = mailster( 'campaigns' )->meta( $campaign->ID );

			if ( ! $meta['active'] ) {

				$this->remove( $campaign->ID );
				continue;
			}

			$autoresponder_meta = $meta['autoresponder'];

			if ( is_numeric( $autoresponder_meta['unit'] ) ) {

				mailster_notice( sprintf( 'Auto responder campaign %s has been deactivated caused by an old timeformat. Please update your campaign!', '<strong>"<a href="post.php?post=' . $campaign->ID . '&action=edit">' . $campaign->post_title . '</a>"</strong>' ), 'error', false, 'camp_error_' . $campaign->ID, $campaign->post_author );
				mailster( 'campaigns' )->update_meta( $campaign->ID, 'active', false );
				continue;
			}

			// time when user no longer get the campaign
			$grace_period  = apply_filters( 'mailster_autoresponder_grace_period', WEEK_IN_SECONDS, $campaign );
			$queue_upfront = 3600;

			if ( 'mailster_subscriber_insert' == $autoresponder_meta['action'] ) {

				$offset     = (int) $autoresponder_meta['amount'] . ' ' . strtoupper( $autoresponder_meta['unit'] );
				$list_based = mailster( 'campaigns' )->list_based_opt_out( $campaign->ID );

				$ignore_lists = $meta['ignore_lists'];
				$lists        = ! $ignore_lists ? (array) $meta['lists'] : true;

				$conditions = ! empty( $meta['list_conditions'] ) ? $meta['list_conditions'] : null;

				$query_args = array(
					'select'        => array(
						'subscribers.ID',
						"UNIX_TIMESTAMP ( FROM_UNIXTIME( IFNULL(lists_subscribers.added, IF(subscribers.confirm, subscribers.confirm, subscribers.signup)) ) + INTERVAL $offset ) AS autoresponder_timestamp",
					),
					'sent__not_in'  => $campaign->ID,
					'queue__not_in' => $campaign->ID,
					'lists'         => $lists,
					'conditions'    => $conditions,
					'where'         => array(
						'(subscribers.confirm != 0 OR subscribers.signup != 0)',
					),
					'having'        => array( 'autoresponder_timestamp <= ' . ( $now + $queue_upfront ) ),
					'orderby'       => 'autoresponder_timestamp',
				);

				if ( $grace_period ) {
					$query_args['having'][] = 'autoresponder_timestamp >= ' . ( $now - $grace_period );
				}

				if ( $ignore_lists ) {
					$query_args['where'][] = '(subscribers.signup >= ' . (int) $meta['timestamp'] . ')';
				} else {
					$query_args['where'][] = '(subscribers.signup >= ' . (int) $meta['timestamp'] . ' OR lists_subscribers.added >= ' . (int) $meta['timestamp'] . ')';
					$query_args['where'][] = 'lists_subscribers.added != 0';
				}

				$query_args = apply_filters( 'mailster_autoresponder_hook_args', $query_args, $campaign->ID );

				$subscribers = mailster( 'subscribers' )->query( $query_args, $campaign->ID );

				if ( ! empty( $subscribers ) ) {

					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps     = wp_list_pluck( $subscribers, 'autoresponder_timestamp' );

					$this->bulk_add( $campaign->ID, $subscriber_ids, $timestamps, 15 );

					$timestamp = min( $timestamps );

					// handle instant delivery
					if ( $timestamp - time() <= 0 ) {
						wp_schedule_single_event( $timestamp, 'mailster_cron_worker', array( $campaign->ID ) );
					}
				}
			} elseif ( 'mailster_subscriber_unsubscribed' == $autoresponder_meta['action'] ) {

				$offset = (int) $autoresponder_meta['amount'] . ' ' . strtoupper( $autoresponder_meta['unit'] );

				$conditions = ! empty( $meta['list_conditions'] ) ? $meta['list_conditions'] : null;

				$query_args = array(
					'select'        => array( 'subscribers.ID', "UNIX_TIMESTAMP ( FROM_UNIXTIME( actions_unsubscribe.timestamp ) + INTERVAL $offset ) AS autoresponder_timestamp" ),
					'status'        => array( 1, 2 ),
					'unsubscribe'   => -1,
					'sent__not_in'  => $campaign->ID,
					'queue__not_in' => $campaign->ID,
					'lists'         => ( empty( $meta['ignore_lists'] ) && ! empty( $meta['lists'] ) ) ? $meta['lists'] : false,
					'conditions'    => $conditions,
					'having'        => array( 'autoresponder_timestamp <= ' . ( $now + $queue_upfront ) ),
					'orderby'       => 'autoresponder_timestamp',
				);

				if ( $grace_period ) {
					$query_args['having'][] = 'autoresponder_timestamp >= ' . ( $now - $grace_period );
				}

				$query_args = apply_filters( 'mailster_autoresponder_hook_args', $query_args, $campaign->ID );

				$subscribers = mailster( 'subscribers' )->query( $query_args, $campaign->ID );

				if ( ! empty( $subscribers ) ) {

					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps     = wp_list_pluck( $subscribers, 'autoresponder_timestamp' );

					$this->bulk_add( $campaign->ID, $subscriber_ids, $timestamps, 15, false, true );

					$timestamp = min( $timestamps );

					// handle instant delivery
					if ( $timestamp - time() <= 0 ) {
						wp_schedule_single_event( $timestamp, 'mailster_cron_worker', array( $campaign->ID ) );
					}
				}
			} elseif ( 'mailster_autoresponder_followup' == $autoresponder_meta['action'] && $campaign->post_parent ) {

				$offset = (int) $autoresponder_meta['amount'] . ' ' . strtoupper( $autoresponder_meta['unit'] );

				$conditions = ! empty( $meta['list_conditions'] ) ? $meta['list_conditions'] : null;

				$query_args = array(
					'select'        => array( 'subscribers.ID' ),
					'sent__not_in'  => $campaign->ID,
					'queue__not_in' => $campaign->ID,
					'lists'         => ( empty( $meta['ignore_lists'] ) && ! empty( $meta['lists'] ) ) ? $meta['lists'] : false,
					'conditions'    => $conditions,
					'having'        => array( 'autoresponder_timestamp <= ' . ( $now + $queue_upfront ) ),
					'orderby'       => 'autoresponder_timestamp',
				);

				switch ( $autoresponder_meta['followup_action'] ) {
					case 1:
						$query_args['select'][] = "UNIX_TIMESTAMP( FROM_UNIXTIME ( actions_sent_1_0.timestamp) + INTERVAL $offset ) AS autoresponder_timestamp";
						$query_args['sent']     = $campaign->post_parent;
						break;
					case 2:
						$query_args['select'][] = "UNIX_TIMESTAMP( FROM_UNIXTIME ( actions_open_0_0.timestamp) + INTERVAL $offset ) AS autoresponder_timestamp";
						$query_args['open']     = $campaign->post_parent;
						break;
					case 3:
						$query_args['select'][] = "UNIX_TIMESTAMP( FROM_UNIXTIME ( actions_click_0_0.timestamp) + INTERVAL $offset ) AS autoresponder_timestamp";
						$query_args['click']    = $campaign->post_parent;
						break;
				}

				if ( $grace_period ) {
					$query_args['having'][] = 'autoresponder_timestamp >= ' . ( $now - $grace_period );
				}

				$query_args = apply_filters( 'mailster_autoresponder_hook_args', $query_args, $campaign->ID );

				$subscribers = mailster( 'subscribers' )->query( $query_args, $campaign->ID );

				if ( ! empty( $subscribers ) ) {

					$subscriber_ids = wp_list_pluck( $subscribers, 'ID' );
					$timestamps     = wp_list_pluck( $subscribers, 'autoresponder_timestamp' );

					$this->bulk_add( $campaign->ID, $subscriber_ids, $timestamps, 15, false );

					$timestamp = min( $timestamps );

					// handle instant delivery
					if ( $timestamp - time() <= 0 ) {
						wp_schedule_single_event( $timestamp, 'mailster_cron_worker', array( $campaign->ID ) );
					}
				}
			} elseif ( 'mailster_post_published' == $autoresponder_meta['action'] && $autoresponder_meta['post_type'] == 'rss' ) {

				if ( preg_match_all( '#<module[^>]*?data-rss="(.*?)".*?</module>#ms', $campaign->post_content, $hits ) ) {
					$feed_urls = array_unique( $hits[1] );

					foreach ( $feed_urls as $feed_url ) {
						$feeds = mailster( 'helper' )->get_feed_since( $autoresponder_meta['since'], $feed_url );

						if ( is_wp_error( $feeds ) || empty( $feeds ) ) {
							continue;
						}

						$count = count( $feeds );
						$last  = strtotime( $feeds[0]->post_date_gmt );

						$created = 0;

						foreach ( $feeds as $i => $feed ) {

							$autoresponder_meta['post_count_status']++;

							if ( ! ( $autoresponder_meta['post_count_status'] % ( $autoresponder_meta['post_count'] + 1 ) ) ) {

								$integer = floor( $autoresponder_meta['amount'] );
								$decimal = $autoresponder_meta['amount'] - $integer;

								$send_offset = ( strtotime( '+' . $integer . ' ' . $autoresponder_meta['unit'], 0 ) + ( strtotime( '+1 ' . $autoresponder_meta['unit'], 0 ) * $decimal ) );

								// multiply the offset with the number of created campaigns
								$send_offset = $send_offset * ( $created + 1 );

								// define an offset to move the "pointer" in the feed
								$index_offset = $count - $i - 1;

								// sleep one second if multiples are created to prevent the same timestamps
								if ( $created ) {
									sleep( 1 );
								}

								if ( $new_id = mailster( 'campaigns' )->autoresponder_to_campaign( $campaign->ID, $send_offset, $autoresponder_meta['issue'], false, $index_offset ) ) {

									$created++;
									$new_campaign = mailster( 'campaigns' )->get( $new_id );
									$autoresponder_meta['issue']++;

									mailster_notice( sprintf( __( 'New campaign %1$s has been created and is going to be sent on %2$s.', 'mailster' ), '<strong>"<a href="post.php?post=' . esc_attr( $new_campaign->ID ) . '&action=edit">' . esc_html( $new_campaign->post_title ) . '</a>"</strong>', '<strong>' . date( mailster( 'helper' )->timeformat(), $now + $send_offset + $timeoffset ) . '</strong>' ), 'info', true );

									do_action( 'mailster_autoresponder_post_published', $campaign->ID, $new_id );

								}
							}
						}

						if ( $count ) {

							$autoresponder_meta['since'] = $last;
							// do not create more than one campaign here.
							break;
						}
					}

					mailster( 'campaigns' )->update_meta( $campaign->ID, 'autoresponder', $autoresponder_meta );
				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $force       (optional)
	 */
	public function autoresponder_timebased( $campaign_id = null, $force = false ) {

		global $wpdb;
		static $mailster_autoresponder;
		if ( ! isset( $mailster_autoresponder ) ) {
			$mailster_autoresponder = array();
		}

		$campaigns = empty( $campaign_id ) ? mailster( 'campaigns' )->get_autoresponder() : array( mailster( 'campaigns' )->get( $campaign_id ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		$now        = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		foreach ( $campaigns as $campaign ) {

			if ( $campaign->post_status != 'autoresponder' ) {
				continue;
			}

			if ( in_array( $campaign->ID, $mailster_autoresponder ) && ! $force ) {
				continue;
			}

			$mailster_autoresponder[] = $campaign->ID;

			$meta = mailster( 'campaigns' )->meta( $campaign->ID );

			$autoresponder_meta = $meta['autoresponder'];

			if ( 'mailster_autoresponder_timebased' != $autoresponder_meta['action'] ) {
				continue;
			}

			if ( ! $meta['active'] ) {

				$this->remove( $campaign->ID );
				continue;
			}

			$time_conditions   = isset( $autoresponder_meta['time_conditions'] );
			$new_content_since = isset( $autoresponder_meta['since'] ) ? (int) $autoresponder_meta['since'] : false;
			$starttime         = $meta['timestamp'];
			$delay             = $starttime - $now;

			// check if endtime is passed.
			if ( isset( $autoresponder_meta['endschedule'] ) && $autoresponder_meta['endtimestamp'] && $autoresponder_meta['endtimestamp'] - $now < 0 ) {
				// disable this campaign
				mailster( 'campaigns' )->update_meta( $campaign->ID, 'active', false );
				mailster_notice( sprintf( esc_html__( 'Auto responder campaign %s has been finished and is deactivated!', 'mailster' ), '<strong>"<a href="post.php?post=' . $campaign->ID . '&action=edit">' . $campaign->post_title . '</a>"</strong>' ), 'success', false, 'autoresponder_' . $campaign_id, $campaign->post_author );
				continue;
			}

			// seconds the campaign should created before the actual send time.
			$time_created_before = 3600;

			// add a day if timezone based sending is enabled
			if ( $meta['timezone'] ) {
				// $time_created_before += DAY_IN_SECONDS;
			}

			// do it if time is over.
			$doit = $delay <= $time_created_before;
			// do not schedule new by default.
			$schedule_new = false;

			// check for conditions "only if [time_post_count] [post_type] have been published."
			if ( $doit && $time_conditions ) {

				if ( 'rss' == $autoresponder_meta['time_post_type'] ) {

					if ( preg_match_all( '#<module[^>]*?data-rss="(.*?)".*?</module>#ms', $campaign->post_content, $hits ) ) {
						$feed_urls = array_unique( $hits[1] );

						foreach ( $feed_urls as $feed_url ) {
							if ( $posts = mailster( 'helper' )->get_feed_since( $new_content_since, $feed_url ) ) {
								if ( $autoresponder_meta['post_count_status'] = count( $posts ) ) {
									mailster( 'campaigns' )->update_meta( $campaign->ID, 'autoresponder', $autoresponder_meta );
									break;
								}
							}
						}
					}
				} else {

				}

				// if post count is reached
				if ( $autoresponder_meta['post_count_status'] >= $autoresponder_meta['time_post_count'] ) {

					// reduce counter with required posts counter
					$autoresponder_meta['post_count_status'] = $autoresponder_meta['post_count_status'] - $autoresponder_meta['time_post_count'];

				} else {

					// schedule new event if it's in the past
					$schedule_new = $delay < 0;
					$doit         = false;

				}
			}

			// check if modules with content exist | "only if new content is available."
			if ( $doit && $new_content_since ) {

				$placeholder = mailster( 'placeholder', $campaign->post_content );
				$placeholder->set_campaign( $campaign->ID );
				$placeholder->rss_since( $new_content_since );

				if ( $placeholder->has_content( true ) ) {
					// has content.
				} else {
					// schedule new event if it's in the past.
					$schedule_new = $delay < 0;
					$doit         = false;
				}
			}

			if ( $doit && $new_id = mailster( 'campaigns' )->autoresponder_to_campaign( $campaign->ID, $delay, $autoresponder_meta['issue']++ ) ) {

				$newCamp = mailster( 'campaigns' )->get( $new_id );
				mailster_notice( sprintf( esc_html__( 'New campaign %s has been created!', 'mailster' ), '<strong>"<a href="post.php?post=' . $newCamp->ID . '&action=edit">' . $newCamp->post_title . '</a>"</strong>' ), 'info', true, 'autoresponder_' . $campaign->ID, $campaign->post_author );

				$schedule_new = true;
				if ( $new_content_since ) {
					$autoresponder_meta['since'] = $now;
				}

				do_action( 'mailster_autoresponder_timebased', $campaign->ID, $new_id );

				mailster( 'campaigns' )->update_meta( $campaign->ID, 'autoresponder', $autoresponder_meta );

			}

			if ( $schedule_new ) {
				$nextdate = mailster( 'helper' )->get_next_date_in_future( $starttime, $autoresponder_meta['interval'], $autoresponder_meta['time_frame'], $autoresponder_meta['weekdays'] );

				mailster( 'campaigns' )->update_meta( $campaign->ID, 'timestamp', $nextdate );

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $force       (optional)
	 */
	public function autoresponder_usertime( $campaign_id = null, $force = false ) {

		global $wpdb;
		static $mailster_autoresponder;
		if ( ! isset( $mailster_autoresponder ) ) {
			$mailster_autoresponder = array();
		}

		$campaigns = empty( $campaign_id ) ? mailster( 'campaigns' )->get_autoresponder() : array( mailster( 'campaigns' )->get( $campaign_id ) );

		$now        = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		foreach ( $campaigns as $campaign ) {

			if ( $campaign->post_status != 'autoresponder' ) {
				continue;
			}

			if ( in_array( $campaign->ID, $mailster_autoresponder ) && ! $force ) {
				continue;
			}

			$mailster_autoresponder[] = $campaign->ID;

			$meta = mailster( 'campaigns' )->meta( $campaign->ID );

			$autoresponder_meta = $meta['autoresponder'];

			if ( 'mailster_autoresponder_usertime' != $autoresponder_meta['action'] ) {
				continue;
			}

			if ( ! $meta['active'] ) {

				$this->remove( $campaign->ID );
				continue;
			}

			$timezone_based = $meta['timezone'];

			$date_fields = mailster()->get_custom_date_fields( true );

			if ( ! in_array( $autoresponder_meta['uservalue'], $date_fields ) ) {
				mailster_notice( sprintf( 'Auto responder campaign %s has been deactivated caused by a missing date field. Please update your campaign!', '<strong>"<a href="post.php?post=' . $campaign->ID . '&action=edit">' . $campaign->post_title . '</a>"</strong>' ), 'error', false, 'camp_error_' . $campaign->ID, $campaign->post_author );
				mailster( 'campaigns' )->update_meta( $campaign->ID, 'active', false );
				$this->remove( $campaign->ID );
				continue;
			}

			$integer     = floor( $autoresponder_meta['amount'] );
			$decimal     = $autoresponder_meta['amount'] - $integer;
			$once        = isset( $autoresponder_meta['once'] ) && $autoresponder_meta['once'];
			$exact_date  = isset( $autoresponder_meta['userexactdate'] ) && $autoresponder_meta['userexactdate'];
			$send_offset = ( strtotime( '+' . $integer . ' ' . $autoresponder_meta['unit'], 0 ) + ( strtotime( '+1 ' . $autoresponder_meta['unit'], 0 ) * $decimal ) ) * $autoresponder_meta['before_after'];

			$subscriber_ids  = array();
			$timestamps      = array();
			$offsettimestamp = strtotime( '+' . ( -1 * $send_offset ) . ' seconds', strtotime( 'tomorrow midnight' ) ) + $timeoffset;

			if ( $exact_date ) {

				$cond = array(
					'field'    => $autoresponder_meta['uservalue'],
					'operator' => '=',
					'value'    => date( 'Y-m-d', $offsettimestamp ),
				);

			} else {

				switch ( $autoresponder_meta['userunit'] ) {
					case 'year':
						$cond = array(
							'field'    => $autoresponder_meta['uservalue'],
							'operator' => '$',
							'value'    => date( '-m-d', $offsettimestamp ),
						);
						break;
					case 'month':
						$cond = array(
							'field'    => $autoresponder_meta['uservalue'],
							'operator' => '$',
							'value'    => date( '-d', $offsettimestamp ),
						);
						break;
					default:
						$cond = array(
							'field'    => $autoresponder_meta['uservalue'],
							'operator' => '!=',
							'value'    => '',
						);
						break;
				}
			}

			if ( $meta['ignore_lists'] ) {
				$lists = false;
			} else {
				$lists = $meta['lists'];
			}

			$conditions = ! empty( $meta['list_conditions'] ) ? $meta['list_conditions'] : array();

			$conditions[] = array( $cond );

			$subscribers = mailster( 'subscribers' )->query(
				array(
					'fields'        => array( 'ID', $autoresponder_meta['uservalue'] ),
					'lists'         => $lists,
					'conditions'    => $conditions,
					'sent__not_in'  => $once ? $campaign->ID : false,
					'queue__not_in' => $campaign->ID,
					'orderby'       => $autoresponder_meta['uservalue'],
				),
				$campaign->ID
			);

			foreach ( $subscribers as $subscriber ) {

				$nextdate = strtotime( $subscriber->{$autoresponder_meta['uservalue']} ) + $send_offset - $timeoffset;

				// in the past already so get next date in future
				if ( $nextdate - $now < 0 && ! $exact_date ) {
					$nextdate = mailster( 'helper' )->get_next_date_in_future( $nextdate, $autoresponder_meta['useramount'], $autoresponder_meta['userunit'] );
				}

				$timedelay = $nextdate - $now;

				if ( $timedelay < ( $timezone_based ? 86400 : 3600 ) && $timedelay >= 0 ) {
					$subscriber_ids[] = $subscriber->ID;
					$timestamps[]     = $nextdate;
				}
			}

			if ( ! empty( $subscriber_ids ) ) {
				if ( $timezone_based ) {
					$timestamps = mailster( 'subscribers' )->get_timeoffset_timestamps( $subscriber_ids, $timestamps );
				}

				$this->bulk_add( $campaign->ID, $subscriber_ids, $timestamps, 15 );

				do_action( 'mailster_autoresponder_usertime', $campaign->ID, $subscriber_ids );
			}
		}
	}


	public function finish_campaigns() {

		global $wpdb;

		// remove not sent queues which have a wrong status
		$wpdb->query( "DELETE a FROM {$wpdb->prefix}mailster_queue AS a LEFT JOIN {$wpdb->prefix}mailster_subscribers AS b ON a.subscriber_id = b.ID WHERE (a.sent = 0 OR a.ignore_status = 1) AND b.status != 1 AND a.campaign_id != 0" );

		// select all active campaigns
		$sql = "SELECT posts.ID, queue.sent FROM {$wpdb->prefix}posts AS posts LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON posts.ID = queue.campaign_id LEFT JOIN {$wpdb->prefix}mailster_actions AS actions ON actions.subscriber_id = queue.subscriber_id AND actions.campaign_id = queue.campaign_id AND actions.type = 1 WHERE posts.post_status IN ('active') AND posts.post_type = 'newsletter' AND queue.requeued = 0 GROUP BY posts.ID HAVING SUM(queue.sent = 0) = 0 OR queue.sent IS NULL";

		$ids = $wpdb->get_col( $sql );

		foreach ( $ids as $id ) {

			$totals = mailster( 'campaigns' )->get_totals( $id );
			$sent   = mailster( 'campaigns' )->get_sent( $id );
			if ( ! $totals || ! $sent ) {
				continue;
			}

			mailster( 'campaigns' )->finish( $id );

		}

		// remove notifications which are sent over an hour ago
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mailster_queue WHERE sent != 0 AND campaign_id = 0 AND sent < %d", ( time() - 3600 ) ) );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id  (optional)
	 * @param unknown $process_id (optional)
	 * @return unknown
	 */
	public function progress( $campaign_id = null, $process_id = 0 ) {

		global $wpdb;

		$last_hit = get_option( 'mailster_cron_lasthit' );

		if ( ( $pid = mailster( 'cron' )->lock( $process_id ) ) !== true ) {

			echo '<h2>' . esc_html__( 'Cron Lock Enabled!', 'mailster' ) . '</h2>';
			$sec = isset( $last_hit['timestamp'] ) ? ( round( time() - $last_hit['timestamp'] ) ) : 0;

			if ( is_user_logged_in() ) {
				echo '<p>' . esc_html__( 'Another process is currently running the cron process and you have been temporary blocked to prevent duplicate emails getting sent out.', 'mailster' ) . '</p>';

				echo '<p>' . sprintf( esc_html__( 'Read more about Cron Locks %s.', 'mailster' ), '<a href="https://kb.mailster.co/what-is-a-cron-lock/">' . esc_html__( 'here', 'mailster' ) . '</a>' );

				if ( $last_hit ) {
					echo '<p>' . sprintf(
						esc_html__( 'Cron Lock requested %s ago from:', 'mailster' ),
						'<strong>' . ( $sec > 60 ? human_time_diff( time() + $sec ) : sprintf( esc_html__( _n( '%d second', '%d seconds', $sec, 'mailster' ) ), $sec ) ) . '</strong>'
					) . '</p>';

					echo '<p><strong>IP: ' . $last_hit['ip'] . '<br>PID: ' . $pid . '<br>' . $last_hit['user'] . '</strong></p>';
				}
			}

			// unlock?
			$unlock = apply_filters( 'mymail_unlock_cron', apply_filters( 'mailster_unlock_cron', false ) );
			if ( $unlock && $sec > max( 600, $unlock ) ) {
				// unlock automatically but with a minimum of 10 minutes
				mailster( 'cron' )->unlock( $process_id );
				echo '<h2>' . esc_html__( 'Cron Lock has been released!', 'mailster' ) . '</h2>';
			} else {
				return false;
			}
		}

		if ( $campaign_id && ! is_array( $campaign_id ) ) {
			$campaign_id = array( $campaign_id );
		}

		$microtime  = microtime( true );
		$globaltime = isset( $GLOBALS['time_start'] ) ? $GLOBALS['time_start'] : $microtime;

		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		if ( empty( $last_hit ) ) {
			$last_hit = array(
				'timestamp' => $microtime,
				'time'      => 0,
				'timemax'   => 0,
				'mail'      => 0,
			);
		}

		$last_hit = array(
			'ip'           => mailster_get_ip(),
			'timestamp'    => $microtime,
			'user'         => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown',
			'oldtimestamp' => $last_hit['timestamp'],
			'time'         => $last_hit['timemax'],
			'timemax'      => $last_hit['timemax'],
			'mail'         => $last_hit['mail'],
		);

		update_option( 'mailster_cron_lasthit', $last_hit );

		$memory_limit           = ini_get( 'memory_limit' );
		$max_execution_time_ini = ini_get( 'max_execution_time' );

		ignore_user_abort( true );
		set_time_limit( 0 );
		$send_at_once       = mailster_option( 'send_at_once' );
		$max_bounces        = mailster_option( 'bounce_attempts' );
		$max_execution_time = mailster_option( 'max_execution_time', 0 );

		$sent_this_turn       = 0;
		$send_delay           = mailster_option( 'send_delay', 0 ) / 1000;
		$mail_send_time       = 0;
		$MID                  = mailster_option( 'ID' );
		$unsubscribe_homepage = apply_filters( 'mymail_unsubscribe_link', apply_filters( 'mailster_unsubscribe_link', ( get_page( mailster_option( 'homepage' ) ) ? get_permalink( mailster_option( 'homepage' ) ) : get_bloginfo( 'url' ) ) ) );

		$campaign_errors = array();

		$to_send = $this->size( $microtime, $campaign_id );

		$queue_update_sql = "UPDATE {$wpdb->prefix}mailster_queue SET sent = %d, error = %d, priority = %d, count = %d WHERE subscriber_id = %d AND campaign_id = %d AND requeued = %d AND options = %s LIMIT 1";

		$this->cron_log( 'UTC', '<strong>' . date( 'Y-m-d H:i:s' ) . ' - ' . time() . '</strong>' );
		$this->cron_log( 'Local Time', '<strong>' . date( 'Y-m-d H:i:s', time() + $timeoffset ) . '</strong>' );

		if ( $memory_limit ) {
			$this->cron_log( 'memory limit', '<strong>' . (int) $memory_limit . ' MB</strong>' );
		}

		$this->cron_log( 'max_execution_time', '<strong>' . $max_execution_time_ini . ' seconds</strong>' );
		$this->cron_log( 'queue size', '<strong>' . number_format_i18n( $to_send ) . ' mails</strong>' );
		$this->cron_log( 'send max at once', '<strong>' . number_format_i18n( $send_at_once ) . '</strong>' );

		if ( $to_send ) {

			$sql = 'SELECT queue.campaign_id, queue.count AS _count, queue.requeued AS _requeued, queue.options AS _options, queue.tags AS _tags, queue.priority AS _priority, subscribers.ID AS subscriber_id, subscribers.status, subscribers.email, subscribers.rating';

			$sql .= " FROM {$wpdb->prefix}mailster_queue AS queue";
			$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = queue.campaign_id";
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON subscribers.ID = queue.subscriber_id";
			// time is in the past and errors are within the range
			$sql .= ' WHERE queue.timestamp <= ' . (int) $microtime . " AND queue.sent = 0 AND queue.error < {$this->max_retry_after_error}";

			// post status is important or is '0' (transactional email)
			$sql .= " AND (posts.post_status IN ('finished', 'active', 'queued', 'autoresponder') OR queue.campaign_id = 0)";

			// subscriber status is 1 (subscribed) or ignore_status
			$sql .= ' AND (subscribers.status = 1 OR queue.ignore_status = 1)';

			// subscriber exists or is not subscriber_id
			$sql .= ' AND (subscribers.ID IS NOT NULL OR queue.subscriber_id = 0)';

			if ( $campaign_id ) {
				$campaign_id = array_filter( $campaign_id, 'is_numeric' );
				$sql        .= ' AND queue.campaign_id IN (' . implode( ', ', $campaign_id ) . ')';
			}

			$sql .= ' ORDER BY queue.priority DESC, subscribers.rating DESC';

			$sql .= ! mailster_option( 'split_campaigns' ) ? ', queue.campaign_id ASC' : '';

			$sql .= " LIMIT $send_at_once";

			$result = $wpdb->get_results( $sql );

			if ( $wpdb->last_error ) {
				$this->cron_log( 'DB Error', '&nbsp;<span class="error">' . $wpdb->last_error . '</span>' );
			}

			$this->cron_log( 'subscribers found', '<strong>' . number_format_i18n( count( $result ) ) . '</strong>' );

			$this->cron_log();

			$this->cron_log( '#', 'email', 'campaign', 'try', 'time (sec.)' );

			foreach ( $result as $i => $data ) {

				if ( connection_aborted() ) {
					break;
				}

				if ( $max_execution_time && microtime( true ) - $globaltime > $max_execution_time - 1 ) {
					$this->cron_log( '', '&nbsp;<span class="error">' . esc_html__( 'timeout reached', 'mailster' ) . '</span>', '', '', '' );
					if ( ! $send_this_turn ) {
						mailster_notice( sprintf( esc_html__( 'Mailster is not able to send your campaign cause of a server timeout. Please increase the %1$s on the %2$s', 'mailster' ), '<strong>&quot;' . esc_html__( 'Max. Execution Time', 'mailster' ) . '&quot;</strong>', '<a href="edit.php?post_type=newsletter&page=mailster_settings&mailster_remove_notice=max_execution_time#delivery">' . esc_html__( 'settings page', 'mailster' ) . '</a>' ), 'error', false, 'max_execution_time' );
					}

					break;
				}

				$send_start_time = microtime( true );

				$data = apply_filters( 'mailster_queue_campaign_subscriber_data', $data );

				if ( $data->campaign_id ) {

					if ( ! $data->_requeued ) {
						// prevent to send duplicates within one minute
						if ( $duplicate = $wpdb->get_results( $wpdb->prepare( "SELECT subscriber_id FROM {$wpdb->prefix}mailster_actions WHERE campaign_id = %d AND subscriber_id = %d AND type = %d && `timestamp` > %d", $data->campaign_id, $data->subscriber_id, 1, time() - 60 ) ) ) {
							$this->cron_log( '', '&nbsp;<span class="error">' . $data->subscriber_id . ' ' . $data->email . '<br>' . esc_html__( 'Prevent to send duplicate within one minute.', 'mailster' ) . '</span>', $data->campaign_id, $data->_count, '' );
							continue;
						}
					}

					if ( in_array( $data->campaign_id, $campaign_errors ) ) {
						continue;
					}

					$tags = ! empty( $data->_tags ) ? @unserialize( $data->_tags ) : array();

					// regular campaign
					$result = mailster( 'campaigns' )->send( $data->campaign_id, $data->subscriber_id, null, false, true, $tags );

					$options = false;

				} elseif ( $data->_options ) {

					if ( $options = @unserialize( $data->_options ) ) {
						$result = mailster( 'notification' )->send( $data->subscriber_id, $options );
					} else {
						continue;
					}
				} else {

					continue;

				}

				$took = microtime( true ) - $send_start_time;

				// success
				if ( ! is_wp_error( $result ) ) {

					$mail_send_time += $took;

					$wpdb->query( $wpdb->prepare( $queue_update_sql, time(), 0, $data->_priority, $data->_count, $data->subscriber_id, $data->campaign_id, $data->_requeued, $data->_options ) );

					if ( ! $options ) {
						$this->cron_log( $i + 1, $data->subscriber_id . ' ' . $data->email, $data->campaign_id, $data->_count, $took > 2 ? '<span class="error">' . $took . '</span>' : $took );

					} else {

						$this->cron_log( $i + 1, print_r( $options, true ), $options['template'], $data->_count, $took > 2 ? '<span class="error">' . $took . '</span>' : $took );

					}

					$sent_this_turn++;

					// error
				} else {

					$this->cron_log( $i + 1, '<span class="error">' . $data->subscriber_id . ' ' . $data->email . '</span>', $data->campaign_id ? $data->campaign_id : $options['template'], $data->_count, $took > 2 ? '<span class="error">' . $took . '</span>' : $took );
					$this->cron_log( '', '&nbsp;<span class="error">[' . $result->get_error_code() . '] ' . $result->get_error_message() . '</span>', '', '', '' );

						// user_error
					if ( $result->get_error_code() == 'user_error' ) {

						$error = $data->_count >= $this->max_retry_after_error;

						$wpdb->query( $wpdb->prepare( $queue_update_sql, 0, $data->_count, 15, $data->_count + 1, $data->subscriber_id, $data->campaign_id, $data->_requeued, $data->_options ) );

						if ( $error ) {
							do_action( 'mailster_subscriber_error', $data->subscriber_id, $data->campaign_id, $result->get_error_message() );

							mailster( 'subscribers' )->change_status( $data->subscriber_id, 4 );
						}

						// notification_error
					} elseif ( $result->get_error_code() == 'notification_error' ) {

							$error = $data->_count >= $this->max_retry_after_error;

							$wpdb->query( $wpdb->prepare( $queue_update_sql, 0, $data->_count, 15, $data->_count + 1, $data->subscriber_id, $data->campaign_id, $data->_requeued, $data->_options ) );

						if ( $error ) {
							if ( isset( $options['template'] ) && $options['template'] ) {
								mailster_notice( sprintf( esc_html__( 'Notification %1$s has thrown an error: %2$s', 'mailster' ), '<strong>&quot;' . $options['template'] . '&quot;</strong>', '<strong>' . implode( '', $result->get_error_messages() ) ) . '</strong>', 'error', 7200, 'notification_error_' . $options['template'] );
							}

							do_action( 'mailster_notification_error', $data->subscriber_id, $result->get_error_message() );
						}

						// campaign_error
					} elseif ( $result->get_error_code() == 'error' ) {

						$campaign = mailster( 'campaigns' )->get( $data->campaign_id );

						if ( $campaign->post_status == 'autoresponder' ) {
							mailster_notice( sprintf( esc_html__( 'Autoresponder %1$s has caused sending error: %2$s', 'mailster' ), '<a href="post.php?post=' . $campaign->ID . '&action=edit"><strong>' . $campaign->post_title . '</strong></a>', '<strong>' . implode( '', $result->get_error_messages() ) ) . '</strong>', 'success', true, 'camp_error_' . $campaign->ID, $campaign->post_author );

						} else {

							if ( mailster_option( 'pause_campaigns' ) ) {
								mailster( 'campaigns' )->change_status( $campaign, 'paused' );
								mailster_notice( sprintf( esc_html__( 'Campaign %1$s has been paused cause of a sending error: %2$s', 'mailster' ), '<a href="post.php?post=' . $campaign->ID . '&action=edit"><strong>' . $campaign->post_title . '</strong></a>', '<strong>' . implode( '', $result->get_error_messages() ) ) . '</strong>', 'error', 7200, 'camp_error_' . $campaign->ID, $campaign->post_author );

							} else {
								mailster_notice( sprintf( esc_html__( 'Campaign %1$s has some delay cause of a sending error: %2$s', 'mailster' ), '<a href="post.php?post=' . $campaign->ID . '&action=edit"><strong>' . $campaign->post_title . '</strong></a>', '<strong>' . implode( '', $result->get_error_messages() ) ) . '</strong>', 'success', true, 'camp_error_' . $campaign->ID, $campaign->post_author );

							}
						}

						array_push( $campaign_errors, $data->campaign_id );
						do_action( 'mailster_campaign_error', $data->subscriber_id, $data->campaign_id, $result->get_error_message() );

						// system_error
					} elseif ( $result->get_error_code() == 'system_error' ) {

						array_push( $campaign_errors, $data->campaign_id );
						do_action( 'mailster_system_error', $data->subscriber_id, $data->campaign_id, $result->get_error_message() );

					}
				}

				// pause between mails
				if ( $send_delay ) {
					usleep( max( 1, round( ( $send_delay - ( microtime( true ) - $send_start_time ) ), 3 ) * 1000000 ) );
				}
			}
		}
		$this->cron_log();

		$max_memory_usage = memory_get_peak_usage( true );

		if ( $max_memory_usage ) {
			$this->cron_log( 'max. memory usage', '<strong>' . size_format( $max_memory_usage, 2 ) . '</strong>' );
		}

		$this->cron_log( 'sent this turn', $sent_this_turn );

		$took = ( microtime( true ) - $microtime );
		if ( $sent_this_turn ) {
			$mailtook = round( $took / $sent_this_turn, 4 );
			$this->cron_log( 'time', round( $took, 2 ) . ' sec., (' . $mailtook . ' sec./mail)' );
			mailster_remove_notice( 'max_execution_time' );
			$last_hit['timemax'] = max( $last_hit['timemax'], $took );
			$last_hit['mail']    = $mailtook;
		}

		if ( is_user_logged_in() ) {
			$this->show_cron_log();
		}

		mailster( 'cron' )->unlock( $process_id );

		$last_hit['time'] = $took;
		update_option( 'mailster_cron_lasthit', $last_hit );

		do_action( 'mailster_cron_finished' );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $microtime (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function size( $microtime = null, $campaign_id = null ) {

		global $wpdb;

		if ( is_null( $microtime ) ) {
			$microtime = microtime( true );
		}

		if ( $campaign_id && ! is_array( $campaign_id ) ) {
			$campaign_id = array( $campaign_id );
		}

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_queue AS queue LEFT JOIN {$wpdb->prefix}mailster_subscribers AS subscribers ON subscribers.ID = queue.subscriber_id LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = queue.campaign_id WHERE queue.timestamp <= " . (int) $microtime . " AND queue.sent = 0 AND queue.error < {$this->max_retry_after_error} AND (posts.post_status IN ('finished', 'active', 'queued', 'autoresponder') OR queue.campaign_id = 0) AND (subscribers.status = 1 OR queue.ignore_status = 1) AND (subscribers.ID IS NOT NULL OR queue.subscriber_id = 0)";

		if ( $campaign_id ) {
			$campaign_id = array_filter( $campaign_id, 'is_numeric' );
			$sql        .= ' AND queue.campaign_id IN (' . implode( ', ', $campaign_id ) . ')';
		}

		return (int) $wpdb->get_var( $sql );
	}


	public function cron_log() {

		global $mailster_cron_log, $mailster_cron_log_max_fields;

		if ( ! $mailster_cron_log ) {
			$mailster_cron_log = array();
		}

		if ( $a = func_get_args() ) {
			array_unshift( $a, microtime( true ) );
			$mailster_cron_log[]          = $a;
			$mailster_cron_log_max_fields = max( $mailster_cron_log_max_fields || 0, count( $a ) );
		} else {
			$mailster_cron_log_max_fields = 0;
			$mailster_cron_log[]          = array();
		}

	}


	public function show_cron_log() {

		global $mailster_cron_log, $mailster_cron_log_max_fields;

		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$html = '<table cellpadding="0" cellspacing="0" width="100%">';
		$i    = 1;
		foreach ( $mailster_cron_log as $logs ) {
			if ( empty( $logs ) ) {
				$i     = 1;
				$html .= '</table><table cellpadding="0" cellspacing="0" width="100%">';
				continue;
			}
			$time  = array_shift( $logs );
			$html .= '<tr class="' . ( $i % 2 ? 'odd' : 'even' ) . '">';
			foreach ( $logs as $j => $log ) {
				$html .= '<td>' . $log . '</td>';
			}
			$html .= str_repeat( '<td>&nbsp;</td>', max( 0, ( $mailster_cron_log_max_fields + 2 ) - $j - 4 ) );
			$html .= '<td width="50">' . date( 'H:i:s', $time + $timeoffset ) . ':' . round( ( $time - floor( $time ) ) * 10000 ) . '</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</table>';
		echo $html;
	}


	/**
	 *
	 *
	 * @param unknown $new_status
	 * @param unknown $old_status
	 * @param unknown $subscriber
	 */
	public function subscriber_change_status( $new_status, $old_status, $subscriber ) {
		if ( $new_status != 1 ) {
			$this->remove_subscriber( $subscriber->ID );
		}
	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $lists
	 * @param unknown $not_list
	 */
	public function unassign_lists( $subscriber_ids, $lists, $not_list ) {
		$this->remove_subscriber( $subscriber_ids );
	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 */
	public function update_subscriber( $subscriber_id ) {
		$this->remove_subscriber( $subscriber_id );
	}


	/**
	 *
	 *
	 * @param unknown $subscribers
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function remove_subscriber( $subscribers, $campaign_id = null ) {

		global $wpdb;

		$sql = "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE 1";
		if ( ! is_null( $campaign_id ) ) {
			$sql .= $wpdb->prepare( ' AND a.campaign_id = %d', $campaign_id );
		}

		if ( ! is_array( $subscribers ) ) {
			$subscribers = array( $subscribers );
		}

		$subscribers = array_filter( $subscribers, 'is_numeric' );

		$sql .= ' AND a.subscriber_id IN (' . implode( ',', $subscribers ) . ')';

		return false !== $wpdb->query( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $timestamp   (optional)
	 * @return unknown
	 */
	public function get_job_count( $campaign_id = null, $timestamp = null ) {

		global $wpdb;

		if ( is_null( $timestamp ) ) {
			$timestamp = time();
		}

		if ( $timestamp === false ) {
			$timestamp = 0;
		}

		$sql = "SELECT COUNT(queue.subscriber_id) AS count FROM {$wpdb->prefix}mailster_queue AS queue WHERE queue.sent = 0 AND queue.timestamp > %d AND queue.campaign_id = %d";

		return $wpdb->get_var( $wpdb->prepare( $sql, $timestamp, $campaign_id ) );

	}


}
