<?php

class MailsterLists {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 30 );

	}


	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Lists', 'mailster' ), esc_html__( 'Lists', 'mailster' ), 'mailster_edit_lists', 'mailster_lists', array( &$this, 'view_lists' ) );

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			add_action( 'load-' . $page, array( &$this, 'edit_entry' ), 99 );

		else :

			add_action( 'load-' . $page, array( &$this, 'bulk_actions' ), 99 );
			add_filter( 'manage_' . $page . '_columns', array( &$this, 'get_columns' ) );

		endif;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'name'        => esc_html__( 'Name', 'mailster' ),
			'description' => esc_html__( 'Description', 'mailster' ),
			'subscribers' => esc_html__( 'Subscribers', 'mailster' ),
			'updated'     => esc_html__( 'Updated', 'mailster' ),
			'added'       => esc_html__( 'Added', 'mailster' ),

		);
		return $columns;
	}


	public function view_lists() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

			wp_enqueue_style( 'mailster-list-detail', MAILSTER_URI . 'assets/css/list-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-list-detail', MAILSTER_URI . 'assets/js/list-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

			mailster_localize_script(
				'lists',
				array(
					'next' => esc_html__( 'next', 'mailster' ),
					'prev' => esc_html__( 'prev', 'mailster' ),
				)
			);

			include MAILSTER_DIR . 'views/lists/detail.php';

		else :

			wp_enqueue_style( 'mailster-lists-table', MAILSTER_URI . 'assets/css/lists-table-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			include MAILSTER_DIR . 'views/lists/overview.php';

		endif;
	}


	public function bulk_actions() {

		if ( empty( $_POST ) ) {
			return;
		}

		if ( empty( $_POST['lists'] ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) {
			$action = $_POST['action'];
		}

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) {
			$action = $_POST['action2'];
		}

		$redirect = add_query_arg( $_GET );

		switch ( $action ) {

			case 'delete':
				if ( current_user_can( 'mailster_delete_lists' ) ) {

					if ( $this->remove( $_POST['lists'] ) ) {
						mailster_notice( sprintf( esc_html__( '%d Lists have been removed', 'mailster' ), count( $_POST['lists'] ) ), 'error', true );
					}

					wp_redirect( $redirect );
					exit;

				}
				break;
			case 'delete_subscribers':
				if ( current_user_can( 'mailster_delete_lists' ) && current_user_can( 'mailster_delete_subscribers' ) ) {

					if ( $this->remove( $_POST['lists'], true ) ) {
						mailster_notice( sprintf( esc_html__( '%d Lists with subscribers have been removed', 'mailster' ), count( $_POST['lists'] ) ), 'error', true );
					}

					wp_redirect( $redirect );
					exit;

				}
				break;
			case 'subscribe':
				if ( $count = $this->change_status( $_POST['lists'], 1 ) ) {

					mailster_notice( esc_html__( 'Subscribers have been subscribed', 'mailster' ), 'error', true );

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'unsubscribe':
				if ( $this->unsubscribe( $_POST['lists'] ) ) {

					mailster_notice( esc_html__( 'Subscribers have been unsubscribed', 'mailster' ), 'error', true );

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'merge':
				if ( count( $_POST['lists'] ) == 1 ) {

					mailster_notice( esc_html__( 'Please selected at least two lists!', 'mailster' ), 'error', true );

					wp_redirect( $redirect );
					exit;

				} elseif ( $this->merge( $_POST['lists'] ) ) {

					mailster_notice( sprintf( esc_html__( 'Lists have been merged. Please update your %s if necessary!', 'mailster' ), '<a href="edit.php?post_type=newsletter">' . esc_html__( 'campaigns', 'mailster' ) . '</a>' ), 'success', true );

					wp_redirect( $redirect );
					exit;
				}
				break;
			case 'send_campaign':
				$link = 'post-new.php?post_type=newsletter';
				$link = add_query_arg( array( 'lists' => $_POST['lists'] ), $link );

				wp_redirect( $link );
				exit;
			break;

		}

	}


	public function edit_entry() {

		if ( isset( $_POST['mailster_data'] ) ) {

			if ( isset( $_POST['save'] ) ) :

				parse_str( $_POST['_wp_http_referer'], $urlparams );

				$empty = $this->get_empty();

				// sanitize input;
				$entry   = (object) ( array_intersect_key( stripslashes_deep( $_POST['mailster_data'] ), (array) $empty ) );
				$list_id = isset( $urlparams['new'] ) ? $this->add( $entry ) : $this->update( $entry );

				if ( is_wp_error( $list_id ) ) {

					switch ( $list_id->get_error_code() ) {
						case 'email_exists':
							$subscriber = $this->get_by_mail( $entry->email );

							$msg = sprintf( esc_html__( '%1$s already exists. %2$s', 'mailster' ), '<strong>&quot;' . $subscriber->email . '&quot;</strong>', '<a href="edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID . '">' . esc_html__( 'Edit this user', 'mailster' ) . '</a>' );
							break;
						default:
							$msg = $list_id->get_error_message();
					}

					mailster_notice( $msg, 'error', true );

				} else {

					$list = $this->get( $list_id );

					mailster_notice( isset( $urlparams['new'] ) ? esc_html__( 'List added', 'mailster' ) : esc_html__( 'List saved', 'mailster' ), 'success', true );
					do_action( 'mailster_list_save', $list_id );
					wp_redirect( 'edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID );
					exit;
				} elseif ( $_POST['delete'] || $_POST['delete_subscribers'] ) :

					if ( $list = $this->get( (int) $_POST['mailster_data']['ID'], false ) ) {

						$delete_subscribers = isset( $_POST['delete_subscribers'] );

						if ( $this->remove( $list->ID, $delete_subscribers ) ) {
							mailster_notice( sprintf( esc_html__( 'List %s has been removed', 'mailster' ), '<strong>&quot;' . $list->name . '&quot;</strong>' ), 'error', true );
							do_action( 'mailster_list_delete', $list->ID );
							wp_redirect( 'edit.php?post_type=newsletter&page=mailster_lists' );
							exit;
						}
					}

			endif;

		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_empty() {
		return (object) array(
			'ID'          => 0,
			'parent_id'   => 0,
			'name'        => '',
			'slug'        => '',
			'description' => '',
			'added'       => 0,
			'updated'     => 0,
			'subscribers' => 0,
		);
	}


	/**
	 *
	 *
	 * @param unknown $entry          (optional)
	 * @param unknown $overwrite      (optional)
	 * @param unknown $subscriber_ids (optional)
	 * @return unknown
	 */
	public function add_segment( $entry = array(), $overwrite = false, $subscriber_ids = null ) {

		$id = get_option( 'mailster_list_segment_id', 1 );

		$segment = $this->get( $segment_id = get_option( 'mailster_list_segment_parent_id' ) );
		if ( ! $segment ) {
			$segment_id = $this->add(
				array(
					'name'        => esc_html__( 'Segments', 'mailster' ),
					'description' => esc_html__( 'contains all segments', 'mailster' ),
				),
				true
			);
			update_option( 'mailster_list_segment_parent_id', $segment_id );
		}

		$name = isset( $entry['name'] ) && ! empty( $entry['name'] ) ? $entry['name'] : sprintf( esc_html__( 'Segment #%d', 'mailster' ), $id++ );

		$current_user = wp_get_current_user();

		$list_id = $this->add(
			wp_parse_args(
				$entry,
				array(
					'name'        => $name,
					'parent_id'   => $segment_id,
					'slug'        => sanitize_title( $name ) . '-' . time(),
					'description' => sprintf( esc_html__( 'Segment created by %s', 'mailster' ), $current_user->display_name ),
				)
			),
			$overwrite,
			$subscriber_ids
		);

		if ( ! is_wp_error( $list_id ) ) {
			update_option( 'mailster_list_segment_id', $id );
			return $list_id;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @param unknown $overwrite      (optional)
	 * @param unknown $subscriber_ids (optional)
	 * @return unknown
	 */
	public function update( $entry, $overwrite = true, $subscriber_ids = null ) {

		global $wpdb;

		$entry = (array) $entry;
		if ( isset( $entry['id'] ) ) {
			$entry['ID'] = $entry['id'];
			unset( $entry['id'] );
		}

		$field_names = array(
			'ID'          => '%d',
			'parent_id'   => '%d',
			'name'        => '%s',
			'slug'        => '%s',
			'description' => '%d',
			'added'       => '%d',
			'updated'     => '%d',
		);

		$now = time();

		$data    = array();
		$list_id = null;

		$entry = apply_filters( 'mymail_verify_list', apply_filters( 'mailster_verify_list', $entry ) );
		if ( is_wp_error( $entry ) ) {
			return $entry;
		} elseif ( $entry === false ) {
			return new WP_Error( 'not_verified', esc_html__( 'List failed verification', 'mailster' ) );
		}

		if ( isset( $entry['ID'] ) ) {
			if ( ! empty( $entry['ID'] ) ) {
				$list_id = (int) $entry['ID'];
			} else {
				unset( $entry['ID'] );
			}
		}

		foreach ( $entry as $key => $value ) {
			if ( isset( $field_names[ $key ] ) ) {
				$data[ $key ] = $value;
			}
		}

		if ( isset( $data['name'] ) && empty( $data['name'] ) ) {
			$data['name'] = esc_html__( 'undefined', 'mailster' );
		}

		$sql = "INSERT INTO {$wpdb->prefix}mailster_lists (" . implode( ', ', array_keys( $data ) ) . ')';

		$sql .= " VALUES ('" . implode( "', '", array_map( 'esc_sql', array_values( $data ) ) ) . "')";

		if ( $overwrite ) {
			$sql .= " ON DUPLICATE KEY UPDATE updated = $now";
			foreach ( $data as $field => $value ) {
				$sql .= ", $field = values($field)";
			}
		}

		$wpdb->suppress_errors();

		if ( false !== $wpdb->query( $sql ) ) {

			if ( ! empty( $wpdb->insert_id ) ) {
				$list_id = $wpdb->insert_id;
			}

			if ( ! empty( $subscriber_ids ) ) {
				$this->assign_subscribers( $list_id, $subscriber_ids, false, true );
			}

			do_action( 'mailster_update_list', $list_id );

			return $list_id;

		} else {

			return new WP_Error( 'list_exists', $wpdb->last_error );
		}

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @param unknown $overwrite      (optional)
	 * @param unknown $subscriber_ids (optional)
	 * @return unknown
	 */
	public function add( $entry, $overwrite = false, $subscriber_ids = null ) {

		$now = time();

		$entry = is_string( $entry ) ? (object) array( 'name' => $entry ) : (object) $entry;

		$entry = (array) $entry;

		$entry = wp_parse_args(
			$entry,
			array(
				'parent_id'   => 0,
				'slug'        => sanitize_title( $entry['name'] ),
				'description' => '',
				'added'       => $now,
				'updated'     => $now,
			)
		);

		add_action( 'mailster_update_list', array( &$this, 'update_forms' ) );

		return $this->update( $entry, $overwrite, $subscriber_ids );

	}


	/**
	 *
	 *
	 * @param unknown $list_id
	 */
	public function update_forms( $list_id ) {

		$forms = mailster( 'forms' )->get_all();

		if ( empty( $forms ) ) {
			return;
		}

		foreach ( $forms as $form ) {
			if ( ! $form->addlists ) {
				continue;
			}

			mailster( 'forms' )->assign_lists( $form->ID, $list_id );
		}

	}


	/**
	 *
	 *
	 * @param unknown $list_id
	 */
	public function remove_from_forms( $list_id ) {

		$forms = mailster( 'forms' )->get_all();

		if ( empty( $forms ) ) {
			return;
		}

		foreach ( $forms as $form ) {
			mailster( 'forms' )->unassign_lists( $form->ID, $list_id );
		}

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscriber_ids
	 * @param unknown $force     (optional)
	 * @return unknown
	 */
	public function confirm_subscribers( $ids, $subscriber_ids, $force = false ) {

		global $wpdb;

		if ( ! is_null( $ids ) ) {
			if ( ! is_array( $ids ) ) {
				$ids = array( (int) $ids );
			}

			$ids = array_filter( $ids, 'is_numeric' );
			if ( empty( $ids ) ) {
				return true;
			}
		}
		if ( ! is_null( $subscriber_ids ) ) {
			if ( ! is_array( $subscriber_ids ) ) {
				$subscriber_ids = array( (int) $subscriber_ids );
			}

			$subscriber_ids = array_filter( $subscriber_ids, 'is_numeric' );
			if ( empty( $subscriber_ids ) ) {
				return true;
			}
		}

		$confirmed = time();

		$sql = "UPDATE {$wpdb->prefix}mailster_lists_subscribers SET added = %d WHERE 1=1";

		if ( ! is_null( $ids ) ) {
			$sql .= ' AND list_id IN (' . implode( ', ', $ids ) . ')';
		}
		if ( ! is_null( $subscriber_ids ) ) {
			$sql .= ' AND subscriber_id IN (' . implode( ', ', $subscriber_ids ) . ')';
		}
		if ( ! $force ) {
			$sql .= ' AND added = 0';
		}

		return false !== $wpdb->query( $wpdb->prepare( $sql, $confirmed ) );

	}

	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscriber_ids
	 * @param unknown $force     (optional)
	 * @return unknown
	 */
	public function unconfirm_subscribers( $ids, $subscriber_ids, $force = false ) {

		global $wpdb;

		if ( ! is_null( $ids ) ) {
			if ( ! is_array( $ids ) ) {
				$ids = array( (int) $ids );
			}

			if ( empty( $ids ) ) {
				return true;
			}
		}
		if ( ! is_null( $subscriber_ids ) ) {
			if ( ! is_array( $subscriber_ids ) ) {
				$subscriber_ids = array( (int) $subscriber_ids );
			}

			if ( empty( $subscriber_ids ) ) {
				return true;
			}
		}

		$confirmed = 0;

		$sql = "UPDATE {$wpdb->prefix}mailster_lists_subscribers SET added = %d WHERE 1=1";

		if ( ! is_null( $ids ) ) {
			$sql .= ' AND list_id IN (' . implode( ', ', $ids ) . ')';
		}
		if ( ! is_null( $subscriber_ids ) ) {
			$sql .= ' AND subscriber_id IN (' . implode( ', ', $subscriber_ids ) . ')';
		}
		if ( ! $force ) {
			$sql .= ' AND added != 0';
		}

		return false !== $wpdb->query( $wpdb->prepare( $sql, $confirmed ) );

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscriber_ids
	 * @param unknown $remove_old     (optional)
	 * @param unknown $added     (optional)
	 * @return unknown
	 */
	public function assign_subscribers( $ids, $subscriber_ids, $remove_old = false, $added = null ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {
			$ids = array( (int) $ids );
		}

		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = array( (int) $subscriber_ids );
		}

		if ( is_null( $added ) ) {
			$added = mailster_option( 'list_based_opt_in' ) ? 0 : time();
		} elseif ( true === $added || 1 == $added ) {
			$added = time();
		} elseif ( false === $added ) {
			$added = 0;
		}

		$ids            = array_filter( $ids );
		$subscriber_ids = array_filter( $subscriber_ids );

		if ( $remove_old ) {
			$this->unassign_subscribers( $ids, $subscriber_ids );
		}

		$inserts = array();
		foreach ( $ids as $list_id ) {
			foreach ( $subscriber_ids as $subscriber_id ) {
				$inserts[] = $wpdb->prepare( '(%d, %d, %d)', $list_id, $subscriber_id, $added );
			}
		}

		if ( empty( $inserts ) ) {
			return true;
		}

		$chunks = array_chunk( $inserts, 200 );

		$success = true;

		foreach ( $chunks as $insert ) {
			$sql = "INSERT INTO {$wpdb->prefix}mailster_lists_subscribers (list_id, subscriber_id, added) VALUES ";

			$sql .= ' ' . implode( ',', $insert );

			$sql .= ' ON DUPLICATE KEY UPDATE list_id = values(list_id), subscriber_id = values(subscriber_id)';

			$success = $success && ( false !== $wpdb->query( $sql ) );

		}

		// set the status for the list from the global status from the user
		$sql = "UPDATE {$wpdb->prefix}mailster_lists_subscribers AS l LEFT JOIN {$wpdb->prefix}mailster_subscribers AS s ON s.ID = l.subscriber_id SET l.added = s.confirm WHERE l.subscriber_id IN (" . implode( ', ', $subscriber_ids ) . ') AND l.added = 0 AND s.status != 0';

		$success = $success && ( false !== $wpdb->query( $sql ) );

		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscriber_ids
	 * @return unknown
	 */
	public function unassign_subscribers( $ids, $subscriber_ids ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {
			$ids = array( (int) $ids );
		}

		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = array( (int) $subscriber_ids );
		}

		$ids            = array_filter( $ids, 'is_numeric' );
		$subscriber_ids = array_filter( $subscriber_ids, 'is_numeric' );

		$chunks = array_chunk( $subscriber_ids, 200 );

		$success = true;

		foreach ( $chunks as $chunk ) {
			$sql = "DELETE FROM {$wpdb->prefix}mailster_lists_subscribers WHERE";

			$sql .= ' subscriber_id IN (' . implode( ',', $chunk ) . ')';

			if ( ! empty( $ids ) ) {
				$sql .= ' AND list_id IN (' . implode( ',', $ids ) . ')';
			};

			$success = $success && ( false !== $wpdb->query( $sql ) );

		}

		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $subscribers (optional)
	 * @return unknown
	 */
	public function remove( $ids, $subscribers = false ) {

		global $wpdb;

		$ids = is_numeric( $ids ) ? array( (int) $ids ) : array_filter( $ids, 'is_numeric' );

		if ( $subscribers ) {
			$sql = "DELETE a,b,c,d,e,f FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers b ON a.ID = b.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields c ON a.ID = c.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS d ON a.ID = d.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_actions AS e ON a.ID = e.subscriber_id LEFT JOIN {$wpdb->prefix}mailster_queue AS f ON a.ID = f.subscriber_id WHERE b.list_id IN (" . implode( ', ', $ids ) . ')';

			$wpdb->query( $sql );
		}

		$sql = "DELETE a,b FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers b ON a.ID = b.list_id WHERE a.ID IN (" . implode( ', ', $ids ) . ')';

		if ( false !== $wpdb->query( $sql ) ) {

			foreach ( $ids as $list_id ) {
				$this->remove_from_forms( $list_id );
			}

			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @return unknown
	 */
	public function subscribe( $ids ) {

		return $this->change_status( $ids, 1 );

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @return unknown
	 */
	public function unsubscribe( $ids ) {

		return $this->change_status( $ids, 2 );

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $name (optional)
	 * @return unknown
	 */
	public function merge( $ids, $name = null ) {

		global $wpdb;

		// need at least 2 lists
		if ( ! is_array( $ids ) || count( $ids ) < 2 ) {
			return false;
		}

		$now   = time();
		$ids   = is_numeric( $ids ) ? array( (int) $ids ) : array_filter( $ids, 'is_numeric' );
		$lists = $this->get( $ids );

		if ( empty( $lists ) ) {
			return false;
		}

		$list_names    = wp_list_pluck( $lists, 'name' );
		$segment_id    = get_option( 'mailster_list_segment_id', 1 );
		$merge_list_id = get_option( 'mailster_merged_list_id', 1 );

		if ( is_null( $name ) ) {
			$name = sprintf( esc_html__( 'Merged List #%d', 'mailster' ), $merge_list_id );
		}

		$new_id = $this->add(
			array(
				'name'        => $name,
				'slug'        => sanitize_title( $name ) . '-' . $now,
				'description' => esc_html__( 'A merged list of', 'mailster' ) . ":\n" . implode( ', ', $list_names ),
			)
		);

		if ( ! is_wp_error( $new_id ) ) {

			// move connections
			$sql = "UPDATE IGNORE {$wpdb->prefix}mailster_lists_subscribers SET list_id = %d, added = %d WHERE list_id IN (" . implode( ', ', $ids ) . ')';
			$wpdb->query( $wpdb->prepare( $sql, $new_id, $now ) );

			$this->remove( $ids, false );

			update_option( 'mailster_merged_list_id', ++$merge_list_id );

			return true;

		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 * @param unknown $new_status
	 * @return unknown
	 */
	public function change_status( $ids, $new_status ) {

		global $wpdb;

		$ids = is_numeric( $ids ) ? array( $ids ) : $ids;

		$sql = "UPDATE {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS b ON a.ID = b.subscriber_id  SET status = %d, updated = %d WHERE b.list_id IN (" . implode( ', ', array_filter( $ids, 'is_numeric' ) ) . ')';

		return false !== $wpdb->query( $wpdb->prepare( $sql, $new_status, time() ) );

	}


	/**
	 *
	 *
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $added (optional)
	 * @return unknown
	 */
	public function move_subscribers( $from, $to, $added = false ) {

		global $wpdb;

		$from = is_numeric( $from ) ? array( $from ) : $from;

		$sql = "UPDATE {$wpdb->prefix}mailster_lists_subscribers SET list_id = %d" . ( $added ? ', added = ' . time() : '' ) . " WHERE {$wpdb->prefix}mailster_lists_subscribers.list_id IN (" . implode( ', ', array_filter( $from, 'is_numeric' ) ) . ');';

		return false !== $wpdb->query( $wpdb->prepare( $sql, $to ) );

	}


	/**
	 *
	 *
	 * @param unknown $id     (optional)
	 * @param unknown $status (optional)
	 * @param unknown $counts (optional)
	 * @return unknown
	 */
	public function get( $id = null, $status = null, $counts = false ) {

		global $wpdb;

		$key = 'lists_' . md5( serialize( $id ) . serialize( $status ) . serialize( $counts ) );

		if ( false === ( $lists = mailster_cache_get( $key ) ) ) {

			if ( is_null( $status ) ) {
				$status = array( 1 );
			} elseif ( $status === false ) {
				$status = array( 0, 1, 2, 3, 4, 5, 6 );
			}
			$statuses = ! is_array( $status ) ? array( $status ) : $status;
			$statuses = array_filter( $statuses, 'is_numeric' );

			$lists = array();

			if ( is_null( $id ) ) {

				if ( $counts ) {
					$sql = "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers, CASE WHEN a.parent_id = 0 THEN a.ID*10 ELSE a.parent_id*10+1 END AS _sort FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN ( {$wpdb->prefix}mailster_subscribers AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(" . implode( ', ', $statuses ) . ')';
					if ( ! in_array( 0, $statuses ) ) {
						$sql .= ' AND ab.added != 0';
					}
					$sql .= ') ON a.ID = ab.list_id GROUP BY a.ID ORDER BY _sort ASC';
				} else {
					$sql = "SELECT a.*, CASE WHEN a.parent_id = 0 THEN a.ID*10 ELSE a.parent_id*10+1 END AS _sort FROM {$wpdb->prefix}mailster_lists AS a ORDER BY _sort ASC";
				}

				$sql = apply_filters( 'mailster_list_get_sql', $sql, null, $statuses, $counts );

				$lists = $wpdb->get_results( $sql );

			} elseif ( is_numeric( $id ) ) {

				if ( $counts ) {
					$sql = "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN ( {$wpdb->prefix}mailster_subscribers AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(" . implode( ', ', $statuses ) . ')';
					if ( ! in_array( 0, $statuses ) ) {
						$sql .= ' AND ab.added != 0';
					}
					$sql .= ') ON a.ID = ab.list_id WHERE a.ID = %d GROUP BY a.ID';
				} else {
					$sql = "SELECT a.* FROM {$wpdb->prefix}mailster_lists AS a WHERE a.ID = %d";
				}

				$sql = apply_filters( 'mailster_list_get_sql', $sql, $id, $statuses, $counts );

				$lists = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );

			} else {

				$ids = ! is_array( $id ) ? array( $id ) : $id;
				$ids = array_filter( $ids, 'is_numeric' );

				if ( ! empty( $ids ) ) {

					if ( $counts ) {
						$sql = "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN ( {$wpdb->prefix}mailster_subscribers AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(" . implode( ', ', $statuses ) . ')';
						if ( ! in_array( 0, $statuses ) ) {
							$sql .= ' AND ab.added != 0';
						}
						$sql .= ") ON a.ID = ab.list_id WHERE a.ID IN(' . implode( ', ', $ids ) . ') GROUP BY a.ID";
					} else {
						$sql = "SELECT a.* FROM {$wpdb->prefix}mailster_lists AS a WHERE a.ID IN(" . implode( ', ', $ids ) . ')';
					}

					$sql   = apply_filters( 'mailster_list_get_sql', $sql, $ids, $statuses, $counts );
					$lists = $wpdb->get_results( $sql );
				}
			}

			mailster_cache_add( $key, $lists );

		}

		return $lists;

	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @param unknown $field  (optional)
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function get_by_name( $name, $field = null, $status = 1 ) {

		global $wpdb;

		$key = 'lists_n_' . md5( serialize( $name ) . serialize( $field ) . serialize( $status ) );

		if ( false === ( $result = mailster_cache_get( $key ) ) ) {

			if ( ! is_null( $field ) && $field != 'subscribers' ) {

				$result = $wpdb->get_var( $wpdb->prepare( 'SELECT ' . esc_sql( $field ) . " FROM {$wpdb->prefix}mailster_lists WHERE (name = %s OR slug = %s) LIMIT 1", $name, $name ) );

			} else {

				$statuses = ! is_array( $status ) ? array( $status ) : $status;

				$statuses = array_filter( $statuses, 'is_numeric' );

				$sql = "SELECT a.*, COUNT(DISTINCT b.ID) AS subscribers FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN ( {$wpdb->prefix}mailster_subscribers AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND b.status IN(" . implode( ', ', $statuses ) . ')';
				if ( ! in_array( 0, $statuses ) ) {
					$sql .= ' AND ab.added != 0';
				}
				$sql .= ') ON a.ID = ab.list_id WHERE (a.name = %s OR a.slug = %s) GROUP BY a.ID';

				$result = $wpdb->get_row( $wpdb->prepare( $sql, $name, $name ) );

				if ( is_null( $field ) ) {
					$result = $result;
				} elseif ( isset( $result->{$field} ) ) {
					$result = $result->{$field};
				} else {
					$result = false;
				}
			}

			mailster_cache_add( $key, $result );

		}

		return $result;

	}


	/**
	 *
	 *
	 * @param unknown $lists    (optional)
	 * @param unknown $statuses (optional)
	 * @return unknown
	 */
	public function count( $lists = null, $statuses = null ) {

		global $wpdb;

		if ( $lists && ! is_array( $lists ) ) {
			$lists = array( $lists );
		}

		if ( ! is_null( $statuses ) && ! is_array( $statuses ) ) {
			$statuses = array( $statuses );
		}

		if ( is_array( $lists ) ) {
			$lists = array_filter( $lists, 'is_numeric' );
		}

		if ( is_array( $statuses ) ) {
			$statuses = array_filter( $statuses, 'is_numeric' );
		}

		$sql = "SELECT COUNT(DISTINCT a.ID) FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN ({$wpdb->prefix}mailster_lists AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.list_id) ON a.ID = ab.subscriber_id WHERE 1=1";

		$sql .= ( is_array( $lists ) )
			? ' AND b.ID IN (' . implode( ',', $lists ) . ')'
			: ( $lists === false ? ' AND b.ID IS NULL' : '' );

		if ( is_array( $statuses ) ) {
			$sql .= ' AND a.status IN (' . implode( ',', $statuses ) . ')';
		}

		$result = $wpdb->get_var( $sql );

		return $result ? (int) $result : 0;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_list_count() {

		global $wpdb;

		$sql = "SELECT COUNT( * ) AS count FROM {$wpdb->prefix}mailster_lists";

		return $wpdb->get_var( $sql );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $limit   (optional)
	 * @param unknown $exclude (optional)
	 * @return unknown
	 */
	public function get_activity( $id, $limit = null, $exclude = null ) {

		return mailster( 'actions' )->get_list_activity( $id, $limit, $exclude );

	}


	/**
	 *
	 *
	 * @param unknown $list_id  (optional)
	 * @param unknown $statuses (optional)
	 * @return unknown
	 */
	public function get_member_count( $list_id = null, $statuses = null ) {

		global $wpdb;

		$statuses = ! is_null( $statuses ) && ! is_array( $statuses ) ? array( $statuses ) : $statuses;
		$key      = is_array( $statuses ) ? 'list_counts_' . implode( '|', $statuses ) : 'list_counts';

		if ( false === ( $list_counts = mailster_cache_get( $key ) ) ) {

			$sql = "SELECT a.ID, a.parent_id, COUNT(DISTINCT ab.subscriber_id) AS count FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN ({$wpdb->prefix}mailster_subscribers AS b INNER JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON b.ID = ab.subscriber_id AND (ab.added != 0 OR b.status = 0)) ON a.ID = ab.list_id";

			if ( is_array( $statuses ) ) {
				$sql .= ' AND b.status IN (' . implode( ',', array_filter( $statuses, 'is_numeric' ) ) . ')';
			}

			$sql .= ' GROUP BY a.ID';

			$result = $wpdb->get_results( $sql );

			$list_counts = array();

			foreach ( $result as $list ) {
				if ( ! isset( $list_counts[ $list->ID ] ) ) {
					$list_counts[ $list->ID ] = 0;
				}

				$list_counts[ $list->ID ] += (int) $list->count;
				if ( $list->parent_id ) {
					$list_counts[ $list->parent_id ] += (int) $list->count;
				}
			}

			mailster_cache_add( $key, $list_counts );

		}

		if ( is_null( $list_id ) ) {
			return $list_counts;
		}

		return isset( $list_counts[ $list_id ] ) && isset( $list_counts[ $list_id ] ) ? (int) $list_counts[ $list_id ] : 0;

	}


	/**
	 *
	 *
	 * @param unknown $id         (optional)
	 * @param unknown $status     (optional)
	 * @param unknown $name       (optional)
	 * @param unknown $show_count (optional)
	 * @param unknown $checked    (optional)
	 */
	public function print_it( $id = null, $status = null, $name = 'mailster_lists', $show_count = true, $checked = array(), $type = 'checkbox' ) {

		if ( $lists = $this->get( $id, $status, ! ! $show_count ) ) {

			if ( ! is_array( $checked ) ) {
				$checked = array( $checked );
			}

			if ( $type == 'checkbox' ) {
				echo '<ul>';
				foreach ( $lists as $list ) {
					echo '<li><label title="' . ( $list->description ? $list->description : $list->name ) . '">' . ( $list->parent_id ? '&nbsp;&#x2517;&nbsp;' : '' ) . '<input type="checkbox" value="' . $list->ID . '" name="' . $name . '[]" ' . checked( in_array( $list->ID, $checked ), true, false ) . ' class="list' . ( $list->parent_id ? ' list-parent-' . $list->parent_id : '' ) . '"> ' . $list->name . '' . ( $show_count ? ' <span class="count">(' . number_format_i18n( $list->subscribers ) . ( is_string( $show_count ) ? ' ' . $show_count : '' ) . ')</span>' : '' ) . '</label></li>';
				}
				echo '</ul>';
			} else {
				echo '<select class="widefat" multiple name="' . $name . '">';
				foreach ( $lists as $list ) {
					echo '<option value="' . $list->ID . '" ' . selected( in_array( $list->ID, $checked ), true, false ) . '>' . ( $list->parent_id ? '&nbsp;&#x2517;&nbsp;' : '' ) . $list->name . '' . ( $show_count ? ' (' . number_format_i18n( $list->subscribers ) . ( is_string( $show_count ) ? ' ' . $show_count : '' ) . ')' : '' ) . '</option>';
				}
				echo '</select>';
			}
		} else {
			if ( is_admin() ) {
				echo '<ul><li>' . esc_html__( 'No Lists found!', 'mailster' ) . '</li><li><a href="edit.php?post_type=newsletter&page=mailster_lists&new">' . esc_html__( 'Create a List now', 'mailster' ) . '</a></li></ul>';
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_totals( $id = null, $total = false ) {

		return $this->get_action( 'total', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_sent( $id = null, $total = false ) {

		return $this->get_action( 'sent', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_sent_rate( $id = null, $total = false ) {

		$totals = $this->get_totals( $id, $total );
		if ( ! $totals ) {
			return 0;
		}

		$sent = $this->get_sent( $id, $total );

		return min( 1, ( $sent / $totals ) );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_errors( $id = null, $total = false ) {

		return $this->get_action( 'errors', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_error_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$errors = $this->get_errors( $id, $total );

		return min( 1, ( $errors / $sent ) );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_opens( $id = null, $total = false ) {

		return $this->get_action( 'opens', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_open_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$opens = $this->get_opens( $id, $total );

		return min( 1, ( $opens / $sent ) );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_clicks( $id = null, $total = false ) {

		return $this->get_action( 'clicks', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_click_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$clicks = $this->get_clicks( $id, $total );

		return min( 1, ( $clicks / $sent ) );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_adjusted_click_rate( $id = null, $total = false ) {

		$open = $this->get_opens( $id, $total );
		if ( ! $open ) {
			return 0;
		}

		$clicks = $this->get_clicks( $id, $total );

		return min( 1, ( $clicks / $open ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_unsubscribes( $id = null ) {

		return $this->get_action( 'unsubscribes', $id );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_unsubscribe_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$unsubscribes = $this->get_unsubscribes( $id, $total );

		return min( 1, ( $unsubscribes / $sent ) );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_adjusted_unsubscribe_rate( $id = null, $total = false ) {

		$open = $this->get_opens( $id, $total );
		if ( ! $open ) {
			return 0;
		}

		$unsubscribes = $this->get_unsubscribes( $id, $total );

		return min( 1, ( $unsubscribes / $open ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_bounces( $id = null ) {

		return $this->get_action( 'bounces', $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_bounce_rate( $id = null ) {

		$totals = $this->get_totals( $id );
		if ( ! $totals ) {
			return 0;
		}

		$bounces = $this->get_bounces( $id );

		return min( 1, ( $bounces / ( $totals + $bounces ) ) );

	}


	/**
	 *
	 *
	 * @param unknown $action
	 * @param unknown $id     (optional)
	 * @param unknown $total  (optional)
	 * @return unknown
	 */
	private function get_action( $action, $id = null, $total = false ) {

		return mailster( 'actions' )->get_by_list( $id, $action . ( $total ? '_total' : '' ) );

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		if ( $new ) {
			$this->add(
				array(
					'name' => esc_html__( 'Default List', 'mailster' ),
				),
				false,
				get_current_user_id()
			);
		}

	}


}
