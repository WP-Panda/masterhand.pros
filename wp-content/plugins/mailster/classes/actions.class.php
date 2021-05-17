<?php

class MailsterActions {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		add_action( 'mailster_send', array( &$this, 'send' ), 10, 2 );
		add_action( 'mailster_open', array( &$this, 'open' ), 10, 3 );
		add_action( 'mailster_click', array( &$this, 'click' ), 10, 4 );
		add_action( 'mailster_unsubscribe', array( &$this, 'unsubscribe' ), 10, 3 );
		add_action( 'mailster_list_unsubscribe', array( &$this, 'list_unsubscribe' ), 10, 4 );
		add_action( 'mailster_bounce', array( &$this, 'bounce' ), 10, 3 );
		add_action( 'mailster_subscriber_error', array( &$this, 'error' ), 10, 3 );
		add_action( 'mailster_cron_cleanup', array( &$this, 'cleanup' ) );

	}


	/**
	 *
	 *
	 * @param unknown $fields (optional)
	 * @param unknown $where  (optional)
	 * @return unknown
	 */
	public function get_fields( $fields = null, $where = null ) {

		global $wpdb;

		$fields = esc_sql( is_null( $fields ) ? '*' : ( is_array( $fields ) ? implode( ', ', $fields ) : $fields ) );

		$sql = "SELECT $fields FROM {$wpdb->prefix}mailster_actions WHERE 1=1";
		if ( is_array( $where ) ) {
			foreach ( $where as $key => $value ) {
				$sql .= ', ' . esc_sql( $key ) . " = '" . esc_sql( $value ) . "'";
			}
		}

		return $wpdb->get_results( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function send( $subscriber_id, $campaign_id ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 1,
			),
			true
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $explicit      (optional)
	 * @return unknown
	 */
	public function open( $subscriber_id, $campaign_id, $explicit = true ) {

		return $this->add_subscriber_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 2,
			),
			$explicit
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $link
	 * @param unknown $index         (optional)
	 * @param unknown $explicit      (optional)
	 * @return unknown
	 */
	public function click( $subscriber_id, $campaign_id, $link, $index = 0, $explicit = true ) {

		$this->open( $subscriber_id, $campaign_id, false );

		$link_id = $this->get_link_id( $link, $index );

		return $this->add_subscriber_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 3,
				'link_id'       => $link_id,
			),
			$explicit
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function unsubscribe( $subscriber_id, $campaign_id, $status = null ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 4,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $lists
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function list_unsubscribe( $subscriber_id, $campaign_id, $lists, $status = null ) {

		return $this->unsubscribe( $subscriber_id, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $hard          (optional)
	 * @return unknown
	 */
	public function bounce( $subscriber_id, $campaign_id, $hard = false ) {

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => $hard ? 6 : 5,
				'count'         => 1,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $error         (optional)
	 * @return unknown
	 */
	public function error( $subscriber_id, $campaign_id, $error = '' ) {

		mailster( 'subscribers' )->update_meta( $subscriber_id, $campaign_id, 'error', $error );

		return $this->add_action(
			array(
				'subscriber_id' => $subscriber_id,
				'campaign_id'   => $campaign_id,
				'type'          => 7,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 */
	private function add_subscriber_action( $args, $explicit = true ) {

		if ( mailster_option( 'do_not_track' ) && isset( $_SERVER['HTTP_DNT'] ) && $_SERVER['HTTP_DNT'] == 1 ) {
			return;
		}

		$user_meta = array(
			'ip' => mailster_get_ip(),
		);

		if ( 'unknown' !== ( $geo = mailster_ip2City() ) ) {

			$user_meta['geo'] = $geo->country_code . '|' . $geo->city;
			if ( $geo->city ) {
				$user_meta['coords']     = (float) $geo->latitude . ',' . (float) $geo->longitude;
				$user_meta['timeoffset'] = (int) $geo->timeoffset;
			}
		}

		// only explicitly opened
		if ( $args['type'] == 2 && $explicit ) {

			if ( $client = mailster_get_user_client() ) {

				// remove meta info if client is Gmail (GoogleImageProxyy)
				if ( 'Gmail' == $client->client ) {
					$user_meta = array();
					// Gmail downloads images as soon as recevied
					if ( 'http://mail.google.com/' == wp_get_raw_referer() ) {
						return;
					}
				}
				if ( 'Yahoo' == $client->client ) {
					$user_meta = array();
				}

				$user_meta['client']        = $client->client;
				$user_meta['clientversion'] = $client->version;
				$user_meta['clienttype']    = $client->type;
			}
		}

		mailster( 'subscribers' )->update_meta( $args['subscriber_id'], $args['campaign_id'], $user_meta );

		$this->add( $args, $explicit );

	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 */
	private function add_action( $args, $explicit = true ) {

		$this->add( $args, $explicit );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $explicit (optional)
	 * @return unknown
	 */
	private function add( $args, $explicit = true ) {

		global $wpdb;

		$now = time();

		$args = wp_parse_args(
			$args,
			array(
				'timestamp' => $now,
				'count'     => 1,
			)
		);

		$sql = "INSERT INTO {$wpdb->prefix}mailster_actions (" . implode( ', ', array_keys( $args ) ) . ')';

		$sql .= " VALUES ('" . implode( "','", array_values( $args ) ) . "') ON DUPLICATE KEY UPDATE";

		$sql .= ( $explicit ) ? ' timestamp = timestamp, count = count+1' : ' count = values(count)';

		$sql = apply_filters( 'mailster_actions_add_sql', $sql, $args, $explicit );

		if ( false !== $wpdb->query( $sql ) ) {
			if ( $args['type'] != 1 && $explicit && isset( $args['subscriber_id'] ) ) {
				wp_schedule_single_event( time() + 120, 'mailster_update_rating', array( $args['subscriber_id'] ) );
			}

			return true;
		}

		return false;

	}


	/**
	 * clear queue with all subscribers in $campaign_id but NOT in subscribers
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscribers
	 * @return unknown
	 */
	public function clear( $campaign_id, $subscribers ) {

		global $wpdb;

		$campaign_id = (int) $campaign_id;
		$subscribers = array_filter( $subscribers, 'is_numeric' );

		if ( empty( $subscribers ) ) {
			return true;
		}

		$chunks = array_chunk( $subscribers, 200 );

		$success = true;

		foreach ( $chunks as $subscriber_chunk ) {

			$sql = "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE a.campaign_id = %d AND a.sent = 0 AND a.subscriber_id NOT IN (" . implode( ',', $subscriber_chunk ) . ')';

			$success = $success && $wpdb->query( $wpdb->prepare( $sql, $campaign_id ) );

		}

		return $success;

	}


	public function cleanup() {

		global $wpdb;

		// delete all softbounces where a hardbounce exists
		$wpdb->query( $wpdb->prepare( "DELETE b FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.campaign_id = b.campaign_id AND a.subscriber_id = b.subscriber_id AND a.link_id = b.link_id WHERE a.type = %d AND b.type = %d", 6, 5 ) );

		// remove actions where's either a subscriber nor a campaign assigned
		$wpdb->query( "DELETE actions FROM {$wpdb->prefix}mailster_actions AS actions WHERE actions.subscriber_id IS NULL AND actions.campaign_id IS NULL" );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $action      (optional)
	 * @param unknown $strict      (optional)
	 * @return unknown
	 */
	public function get_by_campaign( $campaign_id = null, $action = null, $strict = false ) {

		global $wpdb;

		$cache_key = 'action_counts_by_campaign';

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $campaign_id ) ) {

			if ( isset( $action_counts[ $campaign_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $campaign_id ];
				}

				return isset( $action_counts[ $campaign_id ][ $action ] ) ? $action_counts[ $campaign_id ][ $action ] : null;
			}

			$campaign_ids = array( $campaign_id );

		} elseif ( is_array( $campaign_id ) ) {

			$campaign_ids = $campaign_id;

		}

		$default = $this->get_default_action_counts();

		$sql = "SELECT a.post_id AS ID, a.meta_value AS parent_id FROM {$wpdb->postmeta} AS a WHERE a.meta_key = '_mailster_parent_id'";

		if ( isset( $campaign_ids ) ) {
			$sql .= ' AND a.meta_value IN (' . implode( ',', $campaign_ids ) . ')';
		}

		$parent_ids = array();
		$parents    = $wpdb->get_results( $sql );
		foreach ( $parents as $parent ) {
			$parent_ids[ $parent->ID ] = $parent->parent_id;
		}

		// $sql = "SELECT a.campaign_id AS ID, type, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a";
		$sql = "SELECT a.campaign_id AS ID, type, COUNT( DISTINCT COALESCE( a.subscriber_id, 1) ) AS count, COUNT(DISTINCT a.subscriber_id) AS count_cleard, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a";

		if ( isset( $campaign_ids ) ) {
			$sql .= ' WHERE a.campaign_id IN (' . implode( ',', $campaign_ids ) . ')';
		}

		if ( ! empty( $parent_ids ) ) {
			$sql .= ' OR a.campaign_id IN (' . implode( ',', array_keys( $parent_ids ) ) . ')';
		}

		$sql .= ' GROUP BY a.type, a.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $campaign_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->ID ] ) ) {
				$action_counts[ $row->ID ] = $default;
			}

			if ( ( $hasparent = isset( $parent_ids[ $row->ID ] ) ) && ! isset( $action_counts[ $parent_ids[ $row->ID ] ] ) ) {
				$action_counts[ $parent_ids[ $row->ID ] ] = $default;
			}

			// sent
			if ( 1 == $row->type ) {
				$action_counts[ $row->ID ]['sent']         = (int) $row->count;
				$action_counts[ $row->ID ]['sent_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['sent_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['sent']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['sent_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['sent_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // opens
			elseif ( 2 == $row->type ) {
				$action_counts[ $row->ID ]['opens']         = (int) $row->count;
				$action_counts[ $row->ID ]['opens_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['opens_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['opens']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['opens_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['opens_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // clicks
			elseif ( 3 == $row->type ) {
				$action_counts[ $row->ID ]['clicks']         = (int) $row->count;
				$action_counts[ $row->ID ]['clicks_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['clicks_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['clicks_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // unsubscribes
			elseif ( 4 == $row->type ) {
				$action_counts[ $row->ID ]['unsubscribes']         = (int) $row->count;
				$action_counts[ $row->ID ]['unsubscribes_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['unsubscribes']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['unsubscribes_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // softbounces
			elseif ( 5 == $row->type ) {
				$action_counts[ $row->ID ]['softbounces']         = (int) $row->count;
				$action_counts[ $row->ID ]['softbounces_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['softbounces']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['softbounces_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			} // bounces
			elseif ( 6 == $row->type ) {
				$action_counts[ $row->ID ]['bounces']         = (int) $row->count;
				$action_counts[ $row->ID ]['bounces_deleted'] = (int) $row->count - (int) $row->count_cleard;
				$action_counts[ $row->ID ]['sent']           -= (int) $row->count;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['bounces']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['bounces_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
					$action_counts[ $parent_ids[ $row->ID ] ]['sent']            -= (int) $row->count;
				}
			} // error
			elseif ( 7 == $row->type ) {
				$action_counts[ $row->ID ]['errors']         = (int) $row->count;
				$action_counts[ $row->ID ]['errors_total']   = (int) $row->total;
				$action_counts[ $row->ID ]['errors_deleted'] = (int) $row->count - (int) $row->count_cleard;
				if ( $hasparent ) {
					$action_counts[ $parent_ids[ $row->ID ] ]['errors']         += (int) $row->count;
					$action_counts[ $parent_ids[ $row->ID ] ]['errors_total']   += (int) $row->total;
					$action_counts[ $parent_ids[ $row->ID ] ]['errors_deleted'] += ( (int) $row->count - (int) $row->count_cleard );
				}
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $campaign_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_array( $campaign_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $campaign_id ] ) ? $action_counts[ $campaign_id ] : $default;
		}

		return isset( $action_counts[ $campaign_id ] ) && isset( $action_counts[ $campaign_id ][ $action ] ) ? $action_counts[ $campaign_id ][ $action ] : 0;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $action        (optional)
	 * @param unknown $strict        (optional)
	 * @return unknown
	 */
	public function get_by_subscriber( $subscriber_id = null, $action = null, $strict = false ) {

		global $wpdb;

		$cache_key      = 'action_counts_by_subscriber';
		$subscriber_ids = array();

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $subscriber_id ) ) {

			if ( isset( $action_counts[ $subscriber_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $subscriber_id ];
				}

				return isset( $action_counts[ $subscriber_id ][ $action ] ) ? $action_counts[ $subscriber_id ][ $action ] : null;
			}

			$subscriber_ids = array( $subscriber_id );

		} elseif ( is_array( $subscriber_id ) ) {

			$subscriber_ids = array_filter( $subscriber_id, 'is_numeric' );

		}

		$default = $this->get_default_action_counts();

		$sql = "SELECT a.campaign_id, a.subscriber_id AS ID, type, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a";

		if ( ! empty( $subscriber_ids ) ) {
			$sql .= ' WHERE a.subscriber_id IN (' . implode( ',', $subscriber_ids ) . ')';
		}

		$sql .= ' GROUP BY a.type, a.subscriber_id, a.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $subscriber_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->ID ] ) ) {
				$action_counts[ $row->ID ] = $default;
			}

			// sent
			if ( 1 == $row->type ) {
				$action_counts[ $row->ID ]['sent']       += (int) $row->count;
				$action_counts[ $row->ID ]['sent_total'] += (int) $row->total;
			} // opens
			elseif ( 2 == $row->type ) {
				$action_counts[ $row->ID ]['opens']       += (int) $row->count;
				$action_counts[ $row->ID ]['opens_total'] += (int) $row->total;
			} // clicks
			elseif ( 3 == $row->type ) {
				$action_counts[ $row->ID ]['clicks']       += (int) $row->count;
				$action_counts[ $row->ID ]['clicks_total'] += (int) $row->total;
			} // unsubscribes
			elseif ( 4 == $row->type ) {
				$action_counts[ $row->ID ]['unsubscribes'] += (int) $row->count;
			} // softbounces
			elseif ( 5 == $row->type ) {
				$action_counts[ $row->ID ]['softbounces'] += (int) $row->count;
			} // bounces
			elseif ( 6 == $row->type ) {
				$action_counts[ $row->ID ]['bounces'] += (int) $row->count;
			} // error
			elseif ( 7 == $row->type ) {
				$action_counts[ $row->ID ]['errors']       += floor( $row->count );
				$action_counts[ $row->ID ]['errors_total'] += floor( $row->total );
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $subscriber_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_array( $subscriber_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $subscriber_id ] ) ? $action_counts[ $subscriber_id ] : $default;
		}

		if ( isset( $action_counts[ $subscriber_id ] ) && isset( $action_counts[ $subscriber_id ][ $action ] ) ) {
			return $action_counts[ $subscriber_id ][ $action ];
		}

		return 0;

	}


	/**
	 *
	 *
	 * @param unknown $list_id (optional)
	 * @param unknown $action  (optional)
	 * @param unknown $strict  (optional)
	 * @return unknown
	 */
	public function get_by_list( $list_id = null, $action = null, $strict = false ) {

		global $wpdb;

		$cache_key = 'action_counts_by_lists';

		$action_counts = mailster_cache_get( $cache_key );
		if ( ! $action_counts ) {
			$action_counts = array();
		}

		if ( is_numeric( $list_id ) ) {

			if ( isset( $action_counts[ $list_id ] ) ) {
				if ( is_null( $action ) ) {
					return $action_counts[ $list_id ];
				}

				return isset( $action_counts[ $list_id ][ $action ] ) ? $action_counts[ $list_id ][ $action ] : null;
			}

			$list_ids = array( $list_id );

		} elseif ( is_array( $list_id ) ) {

			$list_ids = $list_id;

		}

		$default = $this->get_default_action_counts();

		$sql = "SELECT b.list_id AS ID, type, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS b ON a.subscriber_id = b.subscriber_id WHERE a.campaign_id != 0";

		if ( $strict ) {
			$sql .= ' AND b.list_id = ' . (int) $list_id;
		}

		$sql .= ' GROUP BY b.list_id, a.type, a.campaign_id';

		$result = $wpdb->get_results( $sql );

		foreach ( $list_ids as $id ) {
			if ( ! isset( $action_counts[ $id ] ) ) {
				$action_counts[ $id ] = $default;
			}
		}

		foreach ( $result as $row ) {

			if ( ! isset( $action_counts[ $row->ID ] ) ) {
				$action_counts[ $row->ID ] = $default;
			}

			// sent
			if ( 1 == $row->type ) {
				$action_counts[ $row->ID ]['sent']       += (int) $row->count;
				$action_counts[ $row->ID ]['sent_total'] += (int) $row->total;
			} // opens
			elseif ( 2 == $row->type ) {
					$action_counts[ $row->ID ]['opens']       += (int) $row->count;
					$action_counts[ $row->ID ]['opens_total'] += (int) $row->total;
			} // clicks
			elseif ( 3 == $row->type ) {
					$action_counts[ $row->ID ]['clicks']       += (int) $row->count;
					$action_counts[ $row->ID ]['clicks_total'] += (int) $row->total;
			} // unsubscribes
			elseif ( 4 == $row->type ) {
					$action_counts[ $row->ID ]['unsubscribes'] += (int) $row->count;
			} // softbounces
			elseif ( 5 == $row->type ) {
					$action_counts[ $row->ID ]['softbounces'] += (int) $row->count;
			} // bounces
			elseif ( 6 == $row->type ) {
					$action_counts[ $row->ID ]['bounces'] += (int) $row->count;
			} // error
			elseif ( 7 == $row->type ) {
					$action_counts[ $row->ID ]['errors']       += floor( $row->count );
					$action_counts[ $row->ID ]['errors_total'] += floor( $row->total );
			}
		}

		mailster_cache_set( $cache_key, $action_counts );

		if ( is_null( $list_id ) && is_null( $action ) ) {
			return $action_counts;
		}

		if ( is_null( $action ) ) {
			return isset( $action_counts[ $list_id ] ) ? $action_counts[ $list_id ] : $default;
		}

		return isset( $action_counts[ $list_id ] ) && isset( $action_counts[ $list_id ][ $action ] ) ? $action_counts[ $list_id ][ $action ] : 0;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_default_action_counts() {
		return array(
			'sent'                 => 0,
			'sent_total'           => 0,
			'sent_deleted'         => 0,
			'opens'                => 0,
			'opens_total'          => 0,
			'opens_deleted'        => 0,
			'clicks'               => 0,
			'clicks_total'         => 0,
			'clicks_deleted'       => 0,
			'unsubscribes'         => 0,
			'unsubscribes_deleted' => 0,
			'softbounces'          => 0,
			'softbounces_deleted'  => 0,
			'bounces'              => 0,
			'bounces_deleted'      => 0,
			'errors'               => 0,
			'errors_total'         => 0,
			'errors_deleted'       => 0,
		);
	}


	/**
	 *
	 *
	 * @param unknown $scale  (optional)
	 * @param unknown $limit  (optional)
	 * @param unknown $offset (optional)
	 * @param unknown $sets   (optional)
	 * @return unknown
	 */
	public function get_dashboard_actions( $scale = 'days', $limit = 7, $offset = 0, $sets = null ) {

		global $wpdb, $wp_locale;

		if ( is_null( $sets ) ) {
			$sets = array( 'opens', 'clicks', 'unsubscribes', 'bounces' );
		}

		$timestring = array(
			'years'   => '%Y',
			'month'   => '%Y-%m',
			'days'    => '%Y-%m-%d',
			'hours'   => '%Y-%m-%d %h:00:00',
			'minutes' => '%Y-%m-%d %h:%s:00',
		);
		$times      = array(
			'days'    => 86400,
			'hours'   => 3600,
			'minutes' => 60,
		);

		$set_ids = array(
			'sent'         => 1,
			'opens'        => 2,
			'clicks'       => 3,
			'unsubscribes' => 4,
			'softbounces'  => 5,
			'bounces'      => 6,
			'errors'       => 7,
		);

		$labels = array();

		if ( $scale == 'days' ) {
			$startdate = strtotime( 'next ' . $scale . ' midnight', strtotime( '-' . $offset . ' ' . $scale ) ) - 1;
			$enddate   = strtotime( 'next ' . $scale . ' midnight', strtotime( '-' . ( $offset + $limit ) . ' ' . $scale ) );
			for ( $i = 0; $i < $limit; $i++ ) {
				$str                             = strtotime( '-' . $i . ' days', $startdate );
				$labels[ date( 'Y-m-d', $str ) ] = $wp_locale->weekday_abbrev[ $wp_locale->weekday[ date( 'w', $str ) ] ];
			}
		} elseif ( $scale == 'month' ) {
				$startdate = strtotime( 'first day of next month midnight', strtotime( '-' . $offset . ' ' . $scale ) ) - 1;
				$enddate   = strtotime( 'first day of this month midnight', strtotime( '-' . ( $offset + $limit ) . ' ' . $scale ) );
			for ( $i = 0; $i < $limit; $i++ ) {
				$str                           = strtotime( '-' . $i . ' month', $startdate );
				$labels[ date( 'Y-m', $str ) ] = $wp_locale->month_abbrev[ $wp_locale->month[ date( 'm', $str ) ] ];
			}
		} elseif ( $scale == 'years' ) {
			for ( $i = 0; $i < $limit; $i++ ) {
				$str                         = strtotime( '-' . $i . ' years', $startdate );
				$labels[ date( 'Y', $str ) ] = date( 'Y', $str );
			}
		}

		$timeoffset = mailster( 'helper' )->gmt_offset( true );
		$sql        = 'SELECT t1.date';

		foreach ( $sets as $i => $set ) {
			$sql .= ', COUNT(CASE WHEN a.type = ' . ( $set_ids[ $set ] ) . " THEN 1 ELSE NULL END) AS $set";
		}
		$sql .= ' FROM (SELECT a.date AS date FROM ( SELECT curdate() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS date FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c ) a WHERE a.date ';

		$sql .= $wpdb->prepare( " BETWEEN '%s' AND DATE_ADD('%s' ,INTERVAL %d DAY)", date( 'Y-m-d', $enddate + $timeoffset ), date( 'Y-m-d', $enddate + $timeoffset ), $limit );
		$sql .= ") t1 LEFT JOIN {$wpdb->prefix}mailster_actions AS a ON FROM_UNIXTIME(a.timestamp+$timeoffset, '%Y-%m-%d') = t1.date";

		$sql .= ' GROUP BY t1.date ORDER BY t1.date DESC';

		$result = $wpdb->get_results( $sql );

		$colorseed = uniqid();
		$datasets  = array();
		$return    = array(
			'colorseed' => $colorseed,
			'startdate' => date( 'Y-m-d H:i:s', $startdate ),
			'enddate'   => date( 'Y-m-d H:i:s', $enddate ),
			'labels'    => array_values( $labels ),
			'datasets'  => array(),
		);

		$empty    = array_fill_keys( array_keys( $labels ), 0 );
		$datasets = array_fill_keys( $sets, $empty );

		foreach ( $result as $row ) {
			foreach ( $sets as $set ) {
				$datasets[ $set ][ $row->date ] = (int) $row->{$set};
			}
		}

		$saturation = 150;
		$brightness = 10;

		foreach ( $datasets as $name => $data ) {
			$color                = $this->get_color_string( $name );
			$return['datasets'][] = array(
				'label'                => $name,
				'fillColor'            => 'rgba(' . $color . ',0.2)',
				'strokeColor'          => 'rgba(' . $color . ',1)',
				'pointColor'           => 'rgba(' . $color . ',1)',
				'pointStrokeColor'     => '#fff',
				'pointHighlightFill'   => '#fff',
				'pointHighlightStroke' => 'rgba(' . $color . ',1)',
				'data'                 => array_values( $data ),
			);
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	private function get_color_string( $name ) {

		switch ( $name ) {
			case 'sent':
				return '234,53,86';
			case 'opens':
				return '97,210,214';
			case 'clicks':
				return '255,228,77';
			case 'unsubscribes':
				return '181,225,86';
			case 'bounces':
				return '130,24,124';
			default:
				return '128,128,128';
		}

	}


	/**
	 *
	 *
	 * @param unknown $scale (optional)
	 * @param unknown $since (optional)
	 * @param unknown $desc  (optional)
	 * @return unknown
	 */
	public function get_chronological_actions( $scale = 'days', $since = null, $desc = true ) {

		global $wpdb;

		$timestring = array(
			'days'    => '%Y-%m-%d',
			'hours'   => '%Y-%m-%d %h:00:00',
			'minutes' => '%Y-%m-%d %h:%s:00',
		);
		$times      = array(
			'days'    => 86400,
			'hours'   => 3600,
			'minutes' => 60,
		);

		if ( ! isset( $timestring[ $scale ] ) ) {
			$scale = 'days';
		}

		if ( is_null( $since ) ) {
			$since = strtotime( '-1 ' . $scale );
		}

		if ( false === ( $actions = mailster_cache_get( 'chronological_actions_' . $scale . $since . $desc ) ) ) {

			$timeoffset = mailster( 'helper' )->gmt_offset( true );
			$default    = array(
				'sent'         => 0,
				'opens'        => 0,
				'clicks'       => 0,
				'unsubscribes' => 0,
				'softbounces'  => 0,
				'bounces'      => 0,
				'errors'       => 0,
				'signups'      => 0,
			);

			$actions = array();

			$sql = "SELECT FROM_UNIXTIME(a.timestamp+$timeoffset, '" . $timestring[ $scale ] . "') AS date, COUNT(FROM_UNIXTIME(a.timestamp+$timeoffset, '" . $timestring[ $scale ] . "')) AS count, a.type FROM {$wpdb->prefix}mailster_actions AS a";

			$sql .= $wpdb->prepare( ' WHERE a.timestamp > %d', $since + $timeoffset );

			$sql .= " GROUP BY FROM_UNIXTIME(a.timestamp+$timeoffset, '" . $timestring[ $scale ] . "'), a.type ORDER BY a.timestamp";

			$result = $wpdb->get_results( $sql );

			$start = strtotime( '00:00', $since );

			$timeframe = ceil( ( time() - $since ) / $times[ $scale ] );

			for ( $i = 1; $i <= $timeframe; $i++ ) {
				$s             = $start + ( $times[ $scale ] * $i );
				$actions[ $s ] = $default;
			}

			foreach ( $result as $row ) {

				$timestr = strtotime( $row->date );

				if ( ! isset( $actions[ $timestr ] ) ) {
					continue;
				}

				// sent
				if ( 1 == $row->type ) {
					$actions[ $timestr ]['sent'] = (int) $row->count;
				} // opens
				elseif ( 2 == $row->type ) {
						$actions[ $timestr ]['opens'] = (int) $row->count;
				} // clicks
				elseif ( 3 == $row->type ) {
						$actions[ $timestr ]['clicks'] = (int) $row->count;
				} // unsubscribes
				elseif ( 4 == $row->type ) {
						$actions[ $timestr ]['unsubscribes'] = (int) $row->count;
				} // softbounces
				elseif ( 5 == $row->type ) {
						$actions[ $timestr ]['softbounces'] = (int) $row->count;
				} // bounces
				elseif ( 6 == $row->type ) {
						$actions[ $timestr ]['bounces'] = (int) $row->count;
				} // error
				elseif ( 7 == $row->type ) {
						$actions[ $timestr ]['errors'] = floor( $row->count );
				}
			}

			$sql = "SELECT FROM_UNIXTIME(a.signup+$timeoffset, '" . $timestring[ $scale ] . "') AS date, COUNT(FROM_UNIXTIME(a.signup+$timeoffset, '" . $timestring[ $scale ] . "')) AS count FROM {$wpdb->prefix}mailster_subscribers AS a";

			$sql .= $wpdb->prepare( ' WHERE a.signup > %d AND a.status != 0', $since + $timeoffset );

			$sql .= " GROUP BY FROM_UNIXTIME(a.signup+$timeoffset, '" . $timestring[ $scale ] . "') ORDER BY a.signup";

			$result = $wpdb->get_results( $sql );
			foreach ( $result as $row ) {

				$timestr = strtotime( $row->date );
				if ( ! isset( $actions[ $timestr ] ) ) {
					continue;
				}

				$actions[ $timestr ]['signups'] = (int) $row->count;
			}

			if ( ! $desc ) {
				krsort( $actions );
			}

			mailster_cache_add( 'chronological_actions_' . $scale . $since . $desc, $actions );

		}

		return $actions;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $action        (optional)
	 * @param unknown $cache         (optional)
	 * @return unknown
	 */
	public function get_campaign_actions( $campaign_id, $subscriber_id = null, $action = null, $cache = true ) {

		global $wpdb;

		if ( false === ( $actions = mailster_cache_get( 'campaign_actions' ) ) ) {

			$default = array(
				'sent'              => 0,
				'sent_total'        => 0,
				'opens'             => 0,
				'opens_total'       => 0,
				'clicks'            => array(),
				'clicks_total'      => 0,
				'unsubscribes'      => 0,
				'softbounces'       => 0,
				'softbounces_total' => 0,
				'bounces'           => 0,
				'errors'            => 0,
				'errors_total'      => 0,
			);

			$actions = array();

			$sql = "SELECT a.subscriber_id AS ID, type, COUNT(DISTINCT a.subscriber_id) AS count, SUM(a.count) AS total, a.timestamp, a.link_id, b.link FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_links AS b ON b.ID = a.link_id WHERE a.campaign_id = %d";

			// if not cached just get from the current user
			if ( ! $cache && $subscriber_id ) {
				$sql .= ' AND a.subscriber_id = ' . (int) $subscriber_id;
			}

			$sql .= ' GROUP BY a.type, a.link_id, a.subscriber_id, a.campaign_id';

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			foreach ( $result as $row ) {

				if ( ! isset( $actions[ $row->ID ] ) ) {
					$actions[ $row->ID ] = $default;
				}

				// sent
				if ( 1 == $row->type ) {
					$actions[ $row->ID ]['sent']       = (int) $row->timestamp;
					$actions[ $row->ID ]['sent_total'] = (int) $row->total;
				} // opens
				elseif ( 2 == $row->type ) {
						$actions[ $row->ID ]['opens']       = (int) $row->timestamp;
						$actions[ $row->ID ]['opens_total'] = (int) $row->total;
				} // clicks
				elseif ( 3 == $row->type ) {
						$actions[ $row->ID ]['clicks'][ $row->link ] = (int) $row->total;
						$actions[ $row->ID ]['clicks_total']        += (int) $row->total;
				} // unsubscribes
				elseif ( 4 == $row->type ) {
						$actions[ $row->ID ]['unsubscribes'] = (int) $row->timestamp;
				} // softbounces
				elseif ( 5 == $row->type ) {
						$actions[ $row->ID ]['softbounces']        = (int) $row->timestamp;
						$actions[ $row->ID ]['softbounces_total'] += (int) $row->total;
				} // bounces
				elseif ( 6 == $row->type ) {
						$actions[ $row->ID ]['bounces'] = (int) $row->timestamp;
				} // error
				elseif ( 7 == $row->type ) {
						$actions[ $row->ID ]['errors']       = floor( $row->timestamp );
						$actions[ $row->ID ]['errors_total'] = floor( $row->total );
				}
			}

			if ( $cache ) {
				mailster_cache_add( 'campaign_actions', $actions );
			}
		}

		if ( is_null( $subscriber_id ) && is_null( $action ) ) {
			return $actions;
		}

		if ( is_null( $action ) ) {
			return isset( $actions[ $subscriber_id ] ) ? $actions[ $subscriber_id ] : $default;
		}

		return isset( $actions[ $subscriber_id ] ) && isset( $actions[ $subscriber_id ][ $action ] ) ? $actions[ $subscriber_id ][ $action ] : false;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_clicked_links( $campaign_id ) {

		global $wpdb;

		if ( false === ( $clicked_links = mailster_cache_get( 'clicked_links_' . $campaign_id ) ) ) {

			if ( $parent_id = get_post_meta( $campaign_id, '_mailster_parent_id', true ) ) {
				$sql = "SELECT c.link, c.i, COUNT(*) AS clicks, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->postmeta} AS b ON b.meta_key = '_mailster_parent_id' AND b.post_id = a.campaign_id LEFT JOIN {$wpdb->prefix}mailster_links AS c ON c.ID = a.link_id WHERE (a.campaign_id = %d OR b.meta_value = %d) AND a.type = 3 GROUP BY a.campaign_id, a.link_id ORDER BY c.i ASC, total DESC, clicks DESC";

				$sql = $wpdb->prepare( $sql, $campaign_id, $campaign_id );

			} else {
				$sql = "SELECT c.link, c.i, COUNT(*) AS clicks, SUM(a.count) AS total FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_links AS c ON c.ID = a.link_id WHERE a.campaign_id = %d AND a.type = 3 GROUP BY a.campaign_id, a.link_id ORDER BY c.i ASC, total DESC, clicks DESC";

				$sql = $wpdb->prepare( $sql, $campaign_id );

			}

			$result = $wpdb->get_results( $sql );

			$clicked_links = array();

			foreach ( $result as $row ) {
				$clicked_links[ $row->link ][ $row->i ] = array(
					'clicks' => $row->clicks,
					'total'  => $row->total,
				);
			}

			mailster_cache_add( 'clicked_links_' . $campaign_id, $clicked_links );

		}

		return $clicked_links;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_clients( $campaign_id ) {

		global $wpdb;

		if ( false === ( $clients = mailster_cache_get( 'clients_' . $campaign_id ) ) ) {

			$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS name, b.meta_value AS type, c.meta_value AS version FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS c ON a.subscriber_id = c.subscriber_id AND a.campaign_id = c.campaign_id WHERE a.meta_key = 'client' AND b.meta_key = 'clienttype' AND c.meta_key = 'clientversion' AND a.campaign_id = %d GROUP BY a.meta_value, c.meta_value ORDER BY count DESC";

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			$total = ! empty( $result ) ? array_sum( wp_list_pluck( $result, 'count' ) ) : 0;

			$clients = array();

			foreach ( $result as $row ) {
				$clients[] = array(
					'name'       => $row->name,
					'type'       => $row->type,
					'version'    => $row->version,
					'count'      => $row->count,
					'percentage' => $row->count / $total,
				);
			}

			mailster_cache_add( 'clients_' . $campaign_id, $clients );

		}

		return $clients;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_environment( $campaign_id ) {

		global $wpdb;

		if ( false === ( $environment = mailster_cache_get( 'environment_' . $campaign_id ) ) ) {

			$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS type FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.meta_key = 'clienttype' AND a.campaign_id = %d AND b.type = 2 GROUP BY a.meta_value ORDER BY count DESC";

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			$total = ! empty( $result ) ? array_sum( wp_list_pluck( $result, 'count' ) ) : 0;

			$environment = array();

			foreach ( $result as $row ) {
				$environment[ $row->type ] = array(
					'count'      => $row->count,
					'percentage' => $row->count / $total,
				);
			}

			mailster_cache_add( 'environment_' . $campaign_id, $environment );

		}

		return $environment;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_error_list( $campaign_id ) {

		global $wpdb;

		if ( false === ( $error_list = mailster_cache_get( 'error_list_' . $campaign_id ) ) ) {

			$sql = "SELECT s.ID, s.email, a.timestamp, a.count, b.meta_value AS errormsg FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id LEFT JOIN {$wpdb->prefix}mailster_subscribers AS s ON s.ID = a.subscriber_id WHERE a.campaign_id = %d AND a.type = 7 AND b.meta_key = 'error' ORDER BY a.timestamp DESC";

			$error_list = $wpdb->get_results( $wpdb->prepare( $sql, $campaign_id ) );

			mailster_cache_add( 'error_list_' . $campaign_id, $error_list );

		}

		return $error_list;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id   (optional)
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $limit         (optional)
	 * @param unknown $exclude       (optional)
	 * @return unknown
	 */
	public function get_activity( $campaign_id = null, $subscriber_id = null, $limit = null, $exclude = null ) {

		global $wpdb;

		$exclude = ( ! is_null( $exclude ) && ! is_array( $exclude ) ? array( $exclude ) : $exclude );

		$sql = "SELECT p.post_title AS campaign_title, p.post_status AS campaign_status, a.*, b.link, error.meta_value AS error FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_links AS b ON b.ID = a.link_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS error ON error.subscriber_id = a.subscriber_id AND error.campaign_id = a.campaign_id AND error.meta_key = 'error' LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE 1";

		if ( ! is_null( $campaign_id ) ) {
			$sql .= ' AND a.campaign_id = ' . (int) $campaign_id;
		}

		if ( ! is_null( $subscriber_id ) ) {
			$sql .= ' AND a.subscriber_id = ' . (int) $subscriber_id;
		}

		if ( ! is_null( $exclude ) ) {
			$sql .= ' AND a.type NOT IN (' . implode( ',', array_filter( $exclude, 'is_numeric' ) ) . ')';
		}

		$sql .= ' ORDER BY a.timestamp DESC, a.type DESC';

		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $limit;
		}

		$actions = $wpdb->get_results( $sql );

		return $actions;

	}


	/**
	 *
	 *
	 * @param unknown $list_id (optional)
	 * @param unknown $limit   (optional)
	 * @param unknown $exclude (optional)
	 * @return unknown
	 */
	public function get_list_activity( $list_id = null, $limit = null, $exclude = null ) {

		global $wpdb;

		$exclude = ( ! is_null( $exclude ) && ! is_array( $exclude ) ? array( $exclude ) : $exclude );

		$sql = "SELECT p.post_title AS campaign_title, a.*, b.link FROM {$wpdb->prefix}mailster_actions AS a INNER JOIN (SELECT min(timestamp) as max_ts, type FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON a.subscriber_id = ab.subscriber_id WHERE 1";

		if ( ! is_null( $list_id ) ) {
			$sql .= ' AND ab.list_id = ' . (int) $list_id;
		}

		$sql .= " GROUP BY type, link_id) AS a2 ON a.timestamp = a2.max_ts and a.type = a2.type LEFT JOIN {$wpdb->prefix}mailster_links AS b ON b.ID = a.link_id LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON a.subscriber_id = ab.subscriber_id WHERE 1";

		if ( ! is_null( $list_id ) ) {
			$sql .= ' AND ab.list_id = ' . (int) $list_id;
		}

		if ( ! is_null( $exclude ) ) {
			$sql .= ' AND a.type NOT IN (' . implode( ',', array_filter( $exclude, 'is_numeric' ) ) . ')';
		}

		$sql .= ' GROUP BY a.type, a.link_id ORDER BY a.timestamp DESC, a.type DESC';

		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $limit;
		}

		$actions = $wpdb->get_results( $sql );

		return $actions;

	}


	/**
	 *
	 *
	 * @param unknown $link
	 * @param unknown $index (optional)
	 * @return unknown
	 */
	public function get_link_id( $link, $index = 0 ) {

		global $wpdb;

		if ( $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}mailster_links WHERE `link` = %s AND `i` = %d LIMIT 1", $link, (int) $index ) ) ) {

			return (int) $id;

		} elseif ( $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mailster_links (`link`, `i`) VALUES (%s, %d)", $link, $index ) ) ) {

			return (int) $wpdb->insert_id;

		}

		return null;

	}


}
