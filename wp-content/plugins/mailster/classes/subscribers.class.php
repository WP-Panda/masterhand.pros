<?php

class MailsterSubscribers {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'mailster_cron_worker', array( &$this, 'send_confirmations' ) );
		add_action( 'mailster_subscriber_subscribed', array( &$this, 'remove_pending_confirmations' ) );

		add_action( 'mailster_subscriber_notification', array( &$this, 'subscriber_delayed_notification' ) );
		add_action( 'mailster_unsubscribe_notification', array( &$this, 'subscriber_delayed_unsubscribe_notification' ) );

		add_action( 'user_register', array( &$this, 'wp_id' ) );
		add_action( 'profile_update', array( &$this, 'sync_wp_user' ) );
		add_action( 'update_user_meta', array( &$this, 'sync_wp_user_meta' ), 10, 4 );

		add_action( 'user_register', array( &$this, 'user_register' ) );
		add_action( 'register_form', array( &$this, 'register_form' ) );
		add_action( 'deleted_user', array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );
		add_action( 'remove_user_from_blog', array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );

		add_action( 'comment_form_logged_in_after', array( &$this, 'comment_form_checkbox' ) );
		add_action( 'comment_form_after_fields', array( &$this, 'comment_form_checkbox' ) );
		add_action( 'comment_post', array( &$this, 'comment_post' ), 10, 2 );
		add_action( 'wp_set_comment_status', array( &$this, 'wp_set_comment_status' ), 10, 2 );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );

		add_action( 'mailster_update_rating', array( &$this, 'update_rating' ) );

		if ( is_admin() ) {

			add_filter( 'set-screen-option', array( &$this, 'save_screen_options' ), 10, 3 );
			add_action( 'show_user_profile', array( &$this, 'edit_user_profile' ), 9, 1 );
			add_action( 'edit_user_profile', array( &$this, 'edit_user_profile' ), 9, 1 );

		} else {

		}

	}


	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Subscribers', 'mailster' ), esc_html__( 'Subscribers', 'mailster' ), 'mailster_edit_subscribers', 'mailster_subscribers', array( &$this, 'view_subscribers' ) );

		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			add_action( 'load-' . $page, array( &$this, 'edit_entry' ), 99 );

		else :

			add_action( 'load-' . $page, array( &$this, 'bulk_actions' ), 99 );
			add_action( 'load-' . $page, array( &$this, 'screen_options' ) );
			add_filter( 'manage_' . $page . '_columns', array( &$this, 'get_columns' ) );

		endif;

	}


	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			global $wp_locale;

			wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_style( 'jquery-ui-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_style( 'mailster-flags', MAILSTER_URI . 'assets/css/flags' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_style( 'mailster-subscriber-detail', MAILSTER_URI . 'assets/css/subscriber-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-subscriber-detail', MAILSTER_URI . 'assets/js/subscriber-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

			mailster_localize_script(
				'subscribers',
				array(
					'next'          => esc_html__( 'next', 'mailster' ),
					'prev'          => esc_html__( 'prev', 'mailster' ),
					'start_of_week' => get_option( 'start_of_week' ),
					'day_names'     => $wp_locale->weekday,
					'day_names_min' => array_values( $wp_locale->weekday_abbrev ),
					'month_names'   => array_values( $wp_locale->month ),
					'invalid_email' => esc_html__( 'This isn\'t a valid email address!', 'mailster' ),
					'email_exists'  => esc_html__( 'This email address already exists!', 'mailster' ),
				)
			);

		else :

			wp_enqueue_style( 'mailster-subscribers-table', MAILSTER_URI . 'assets/css/subscribers-table-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-subscribers-table', MAILSTER_URI . 'assets/js/subscribers-table-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
			mailster_localize_script(
				'subscribers',
				array(
					'onbeforeunload' => esc_html__( 'Bulk process in progress!', 'mailster' ),
					'initprogess'    => sprintf( esc_html__( 'processing page %d', 'mailster' ), 1 ),
				)
			);

		endif;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {

		$columns       = array(
			'cb'   => '<input type="checkbox" />',
			'name' => esc_html__( 'Name', 'mailster' ),
		);
		$custom_fields = mailster()->get_custom_fields();
		foreach ( $custom_fields as $key => $field ) {
			$columns[ $key ] = strip_tags( $field['name'] );
		}

		$columns['lists']  = esc_html__( 'Lists', 'mailster' );
		$columns['emails'] = esc_html__( 'Emails', 'mailster' );
		$columns['status'] = esc_html__( 'Status', 'mailster' );
		$columns['signup'] = esc_html__( 'Subscribed', 'mailster' );

		return $columns;
	}


	public function bulk_actions() {

		if ( empty( $_POST ) ) {
			return;
		}

		if ( isset( $_POST['post_data'] ) ) {
			$is_ajax = true;
			$page    = isset( $_POST['page'] ) ? (int) $_POST['page'] : 0;
			$limit   = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 40;
			$total   = isset( $_POST['count'] ) ? (int) $_POST['count'] : false;
			parse_str( $_POST['post_data'], $_POST );
		}

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) {
			$action = $_POST['action'];
		}

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) {
			$action = $_POST['action2'];
		}

		if ( isset( $_GET['action'] ) ) {
			$action = $_GET['action'];
		}

		$redirect        = add_query_arg( $_GET );
		$success_message = '';
		$error_message   = '';
		$message_postfix = '';

		if ( isset( $_POST['all_subscribers'] ) && $_POST['all_subscribers'] ) {
			$args = $_GET;

			$status  = isset( $_GET['status'] ) ? (int) $_GET['status'] : false;
			$orderby = ! empty( $_GET['orderby'] ) ? esc_sql( $_GET['orderby'] ) : 'ID';
			$order   = ! empty( $_GET['order'] ) ? esc_sql( $_GET['order'] ) : 'DESC';
			$offset  = $page * $limit;

			if ( in_array( $action, array( 'subscribed', 'unsubscribed', 'pending' ) ) ) {
				$offset = 0;
				if ( ! $status ) {
					$args['status__not_in'] = $this->get_status_by_name( $action );
				}
			} elseif ( 'delete' == $action ) {
				$offset = 0;
			}

			$subscriber_ids = mailster( 'subscribers' )->query(
				wp_parse_args(
					$args,
					array(
						'status'     => $status,
						'limit'      => $limit,
						'offset'     => $offset,
						'orderby'    => $orderby,
						'order'      => $order,
						'return_ids' => true,
					)
				)
			);

			$page++;
			$finished = ( $page == ceil( $total / $limit ) );

			$message_postfix = ' [' . sprintf( '%s/%s', number_format_i18n( $page ), number_format_i18n( ceil( $total / $limit ) ) ) . ']';

		} else {
			if ( empty( $_POST['subscribers'] ) ) {
				return;
			}
			$subscriber_ids = array_filter( $_POST['subscribers'], 'is_numeric' );
		}

		switch ( $action ) {

			case 'delete':
			case 'delete_actions':
				if ( current_user_can( 'mailster_delete_subscribers' ) ) {

					$remove_actions = 'delete_actions' == $action;

					$success = $this->remove( $subscriber_ids, null, $remove_actions );
					if ( is_wp_error( $success ) ) {
						$error_message = sprintf( esc_html__( 'There was an error while deleting subscribers: %s', 'mailster' ), $success->get_error_message() );

					} elseif ( $success ) {
						$count = count( $subscriber_ids );
						if ( $remove_actions ) {
							$error_message = sprintf( esc_html__( _n( '%d subscriber has been removed!', '%d subscribers have been removed!', $count, 'mailster' ) ), $count );
						} else {
							$error_message = sprintf( esc_html__( _n( '%d subscriber has been removed!', '%d subscribers have been removed!', $count, 'mailster' ) ), $count );
						}
					}
				}
				break;

			case 'subscribed':
				if ( $count = $this->change_status( $subscriber_ids, 1 ) ) {
					$success_message = sprintf( esc_html__( '%d Subscribers have been subscribed', 'mailster' ), $count );
				}
				break;

			case 'unsubscribed':
				if ( $count = $this->change_status( $subscriber_ids, 2 ) ) {
					$success_message = sprintf( esc_html__( '%d Subscribers have been unsubscribed', 'mailster' ), $count );
				}
				break;

			case 'pending':
				if ( $count = $this->change_status( $subscriber_ids, 0 ) ) {
					$success_message = sprintf( esc_html__( '%d Subscribers have been set to pending', 'mailster' ), $count );
				}
				break;

			case 'send_campaign':
				$listid = mailster( 'lists' )->add_segment();

				if ( $this->assign_lists( $subscriber_ids, $listid, false, true ) ) {
					$count           = count( $subscriber_ids );
					$success_message = sprintf( esc_html__( '%d Subscribers have been assigned to a new list', 'mailster' ), $coun );
				}
				break;

			case 'confirmation':
				if ( $count = $this->send_confirmations( $subscriber_ids, true, true ) ) {
					$success_message = sprintf( esc_html__( 'Confirmations sent to %d pending subscribers', 'mailster' ), $count );
				}
				break;

			case 'verify':
				$verfied = $unverfied = 0;

				foreach ( $subscriber_ids as $subscriber_id ) {
					$subscriber = $this->get( $subscriber_id, true );
					$subscriber = $this->verify( $subscriber );
					if ( is_wp_error( $subscriber ) ) {
						$this->change_status( $subscriber_id, $this->get_status_by_name( 'error' ) );
						$this->update_meta( $subscriber_id, 0, 'error', $subscriber->get_error_message() );
						$unverfied++;
					} else {
						if ( 4 == $subscriber['status'] ) {
							$subscriber['status'] = 1;
						}

						$this->update( $subscriber );
						$verfied++;
					}
				}

				if ( $unverfied ) {
					$error_message = sprintf( esc_html__( _n( '%s subscriber has not been verified!', '%s subscribers have not been verified!', $unverfied, 'mailster' ) ), $unverfied );
				}

				if ( $verfied ) {
					$success_message = sprintf( esc_html__( _n( '%s subscriber has been verified!', '%s subscribers have been verified!', $verfied, 'mailster' ) ), $verfied );
				}

				break;

			default:
				if ( preg_match( '#^add_list_(\w+)#', $action, $match ) ) {
					$ids = 'all' == $match[1] ? null : (int) $match[1];
					if ( $list = mailster( 'lists' )->get( $ids ) ) {
						$this->assign_lists( $subscriber_ids, $list->ID, false, true );
						$success_message = sprintf( esc_html__( '%1$d Subscribers added to list %2$s', 'mailster' ), count( $subscriber_ids ), '"<a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a>"' );
					}
				} elseif ( preg_match( '#^remove_list_(\w+)#', $action, $match ) ) {
					$ids = 'all' == $match[1] ? null : (int) $match[1];
					if ( $list = mailster( 'lists' )->get( $ids ) ) {
						$this->unassign_lists( $subscriber_ids, $list->ID, false, true );
						$success_message = sprintf( esc_html__( '%1$d Subscribers removed from list %2$s', 'mailster' ), count( $subscriber_ids ), '"<a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a>"' );
					}
				} elseif ( preg_match( '#^confirm_list_(\w+)#', $action, $match ) ) {
					$ids = 'all' == $match[1] ? null : (int) $match[1];
					if ( $list = mailster( 'lists' )->get( $ids ) ) {
						mailster( 'lists' )->confirm_subscribers( $list->ID, $subscriber_ids );
						$success_message = sprintf( esc_html__( '%1$d Subscribers confirmed to %2$s lists', 'mailster' ), count( $subscriber_ids ), count( $list ) );
					}
				} elseif ( preg_match( '#^unconfirm_list_(\w+)#', $action, $match ) ) {
					$ids = 'all' == $match[1] ? null : (int) $match[1];
					if ( $list = mailster( 'lists' )->get( $ids ) ) {
						mailster( 'lists' )->unconfirm_subscribers( $list->ID, $subscriber_ids );
						$success_message = sprintf( esc_html__( '%1$d Subscribers unconfirmed from %2$s lists', 'mailster' ), count( $subscriber_ids ), count( $list ) );
					}
				}

				break;

		}
		if ( $success_message ) {
			mailster_notice( $success_message . $message_postfix, 'success', true, 'subscriber_bulk_success', true, null, true );
		}

		if ( $error_message ) {
			mailster_notice( $error_message . $message_postfix, 'error', true, 'subscriber_bulk_error', true, null, true );
		}

		if ( isset( $is_ajax ) ) {

			wp_send_json(
				array(
					'finished'        => $finished,
					'total'           => $total,
					'page'            => $page,
					'message'         => $finished ? '<span>' . esc_html__( 'Finished!', 'mailster' ) . '</span>' : '<span title="' . esc_html__( 'Check the browser console for more info!', 'mailster' ) . '">' . sprintf( esc_html__( 'processing page %1$s of %2$s', 'mailster' ), number_format_i18n( $page + 1 ), number_format_i18n( ceil( $total / $limit ) ) ) . '&hellip;</span>',
					'success_message' => $success_message,
					'error_message'   => $error_message,
					'delay'           => 30,
				)
			);

		} else {

			wp_redirect( $redirect );
			exit;

		}

	}


	public function edit_entry() {

		if ( isset( $_GET['new'] ) && isset( $_GET['wp_user'] ) ) {

			$user_id = (int) $_GET['wp_user'];

			$subscriber_id = $this->add_from_wp_user(
				$user_id,
				array(
					'status'  => 1,
					'referer' => 'wpuser',
				)
			);

			if ( is_wp_error( $subscriber_id ) ) {

				mailster_notice( __( $subscriber_id->get_error_message(), 'mailster' ), 'error', true );
				wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers' );

			} else {

				mailster_notice( esc_html__( 'Subscriber added', 'mailster' ), 'success', true );
				do_action( 'mailster_subscriber_save', $subscriber_id );
				wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber_id );
				exit;

			}
		}
		if ( isset( $_POST['mailster_data'] ) ) {

			if ( isset( $_POST['save'] ) ) :

				parse_str( $_POST['_wp_http_referer'], $urlparams );

				$is_new = isset( $urlparams['new'] );

				$old_subscriber_data = $this->get( (int) $_POST['mailster_data']['ID'], true );

				if ( $is_new && ! current_user_can( 'mailster_add_subscribers' ) ) {
					wp_die( esc_html__( 'You are not allowed to add subscribers!', 'mailster' ) );
				}

				if ( ! $is_new && ! current_user_can( 'mailster_edit_subscribers' ) ) {
					wp_die( esc_html__( 'You are not allowed to edit subscribers!', 'mailster' ) );
				}

				$entry = (object) stripslashes_deep( $_POST['mailster_data'] );

				if ( $is_new ) {
					$entry->referer = 'backend';
					$entry->confirm = 0;
				}

				// maybe send confirmation if status wasn't pending
				if ( $old_subscriber_data && $old_subscriber_data->status != 0 ) {
					$entry->confirmation = 0;
				}

				$subscriber_id = $is_new
				? $this->add( $entry )
				: $this->update( $entry );

				if ( is_wp_error( $subscriber_id ) ) {

					switch ( $subscriber_id->get_error_code() ) {
						case 'email_exists':
							$subscriber = $this->get_by_mail( $entry->email );

							$msg = sprintf( esc_html__( '%1$s already exists. %2$s', 'mailster' ), '<strong>&quot;' . $subscriber->email . '&quot;</strong>', '<a href="edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID . '">' . esc_html__( 'Edit this user', 'mailster' ) . '</a>' );
							break;
						default:
							$msg = $subscriber_id->get_error_message();
					}

					mailster_notice( $msg, 'error', true );

				} else {

					$subscriber = $this->get( $subscriber_id, true );

					if ( isset( $_POST['mailster_lists'] ) ) {
						$lists = array_filter( $_POST['mailster_lists'], 'is_numeric' );
					} else {
						$lists = array();
					}
					$current_lists = $this->get_lists( $subscriber->ID, true );

					if ( $unasssign = array_diff( $current_lists, $lists ) ) {
						$this->unassign_lists( $subscriber->ID, $unasssign );
					}
					if ( $assign = array_diff( $lists, $current_lists ) ) {
						$this->assign_lists( $subscriber->ID, $assign, false, true );
					}

					if ( ! $old_subscriber_data || $subscriber->status != $old_subscriber_data->status ) {
						if ( mailster_option( 'list_based_opt_in' ) ) {
							if ( 1 == $subscriber->status ) {
								mailster( 'lists' )->confirm_subscribers( null, $subscriber->ID );
							} elseif ( 0 == $subscriber->status ) {
								mailster( 'lists' )->unconfirm_subscribers( null, $subscriber->ID );
							}
						}
					}

					mailster_notice( $is_new ? esc_html__( 'Subscriber added', 'mailster' ) : esc_html__( 'Subscriber saved', 'mailster' ), 'success', true );
					do_action( 'mailster_subscriber_save', $subscriber->ID );
					wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );
					exit;

				} elseif ( isset( $_POST['delete'] ) || isset( $_POST['delete_actions'] ) ) :

					if ( ! current_user_can( 'mailster_delete_subscribers' ) ) {
						wp_die( esc_html__( 'You are not allowed to delete subscribers!', 'mailster' ) );
					}

					$remove_actions = isset( $_POST['delete_actions'] );

					if ( $subscriber = $this->get( (int) $_POST['mailster_data']['ID'], true ) ) {
						$success = $this->remove( $subscriber->ID, null, $remove_actions );
						if ( ! $success ) {
							mailster_notice( esc_html__( 'There was an error while deleting subscribers!', 'mailster' ), 'error', true );

						} else {
							mailster_notice( sprintf( esc_html__( 'Subscriber %s has been removed', 'mailster' ), '<strong>&quot;' . $subscriber->email . '&quot;</strong>' ), 'error', true, true );
							do_action( 'mailster_subscriber_delete', $subscriber->ID, $subscriber->email );
						}

						wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers' );
						exit;

					};

			elseif ( isset( $_POST['confirmation'] ) ) :

				if ( $subscriber = $this->get( (int) $_POST['mailster_data']['ID'], true ) ) {
					if ( $this->send_confirmations( $subscriber->ID, true, true ) ) {
						mailster_notice( esc_html__( 'Confirmation has been sent', 'mailster' ), 'success', true );
					}
					wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );
					exit;
				};

			endif;

		}

		if ( isset( $_GET['resendcampaign'] ) ) {
			if ( ! current_user_can( 'publish_newsletters' ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'mailster-resend-campaign' ) ) {
				wp_die( esc_html__( 'You are not allowed to publish campaigns!', 'mailster' ) );
			}

			$subscriber_id = isset( $_GET['ID'] ) ? (int) $_GET['ID'] : null;
			$campaign_id   = isset( $_GET['campaign_id'] ) ? (int) $_GET['campaign_id'] : null;

			if ( $subscriber_id && $campaign_id && $subscriber = $this->get( $subscriber_id, true ) ) {

				$campaign = mailster( 'campaigns' )->get( $campaign_id );

				if ( $campaign && mailster( 'campaigns' )->send( $campaign_id, $subscriber_id, true, true ) ) {
					mailster_notice( sprintf( esc_html__( 'Campaign %s has been sent', 'mailster' ), '<strong>&quot;' . $campaign->post_title . '&quot;</strong>' ), 'success', true );
					wp_redirect( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber_id );
					exit;
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $limit  (optional)
	 * @param unknown $offset (optional)
	 * @return unknown
	 */
	public function sync_all_subscriber( $limit = null, $offset = 0 ) {

		global $wpdb;

		$this->wp_id();

		$sql = "SELECT ID FROM {$wpdb->prefix}mailster_subscribers WHERE wp_id != 0";

		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
		}

		$subscriber_ids = $wpdb->get_col( $sql );

		foreach ( $subscriber_ids as $subscriber_id ) {
			$this->sync_subscriber( $subscriber_id );
		}

		return count( $subscriber_ids );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @return unknown
	 */
	public function sync_subscriber( $subscriber_id ) {

		// Subscriber => WP User
		if ( ! mailster_option( 'sync' ) ) {
			return;
		}

		$synclist = mailster_option( 'synclist', array() );

		if ( empty( $synclist ) ) {
			return;
		}

		// delete cache
		mailster_cache_delete( 'get_custom_fields_' . $subscriber_id );
		$subscriber = $this->get( $subscriber_id, true );

		if ( ! $subscriber->wp_id ) {
			return;
		}

		$usertable_fields = array( 'user_login', 'user_nicename', 'user_email', 'user_url', 'display_name' );

		$userdata = array();
		$usermeta = array();

		foreach ( $synclist as $field => $meta ) {

			if ( in_array( $meta, $usertable_fields ) ) {
				$userdata[ $meta ] = $subscriber->{$field};
			} else {
				$usermeta[ $meta ] = $subscriber->{$field};
			}
		}

		remove_action( 'profile_update', array( &$this, 'sync_wp_user' ) );
		remove_action( 'update_user_meta', array( &$this, 'sync_wp_user_meta' ), 10, 4 );

		if ( ! empty( $userdata ) ) {
			wp_update_user( wp_parse_args( array( 'ID' => $subscriber->wp_id ), $userdata ) );
		}
		if ( ! empty( $usermeta ) ) {
			foreach ( $usermeta as $key => $value ) {
				update_user_meta( $subscriber->wp_id, $key, $value );
			}
		}

		add_action( 'profile_update', array( &$this, 'sync_wp_user' ) );
		add_action( 'update_user_meta', array( &$this, 'sync_wp_user_meta' ), 10, 4 );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $limit  (optional)
	 * @param unknown $offset (optional)
	 * @return unknown
	 */
	public function sync_all_wp_user( $limit = null, $offset = 0 ) {

		global $wpdb;

		$this->wp_id();

		$sql = "SELECT wp_id AS ID FROM {$wpdb->prefix}mailster_subscribers WHERE wp_id != 0";
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
		}

		$user_ids = $wpdb->get_col( $sql );

		foreach ( $user_ids as $user_id ) {
			$this->sync_wp_user( $user_id );
		}

		return count( $user_ids );

	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 * @return unknown
	 */
	public function sync_wp_user( $user_id ) {

		// WP User => Subscriber
		if ( ! mailster_option( 'sync' ) ) {
			return;
		}

		$synclist = mailster_option( 'synclist', array() );

		if ( empty( $synclist ) ) {
			return;
		}

		$user = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			return;
		}

		$subscriber = $this->get_by_wpid( $user_id );

		if ( ! $subscriber ) {
			return;
		}

		$userdata = array();

		foreach ( $synclist as $field => $meta ) {
			if ( isset( $user->data->{$meta} ) ) {
				$userdata[ $field ] = $user->data->{$meta};
			} else {
				$userdata[ $field ] = get_user_meta( $user_id, $meta, true );
			}
		}

		return $this->update( wp_parse_args( array( 'ID' => $subscriber->ID ), $userdata ), true, true );

	}


	/**
	 *
	 *
	 * @param unknown $meta_id
	 * @param unknown $user_id
	 * @param unknown $meta_key
	 * @param unknown $meta_value
	 */
	public function sync_wp_user_meta( $meta_id, $user_id, $meta_key, $meta_value ) {

		if ( ! mailster_option( 'sync' ) ) {
			return;
		}

		$synclist = mailster_option( 'synclist', array() );

		if ( ! in_array( $meta_key, $synclist ) ) {
			return;
		}

		$subscriber = $this->get_by_wpid( $user_id );

		if ( ! $subscriber ) {
			return;
		}

		$key = array_search( $meta_key, $synclist );

		if ( is_array( $meta_value ) ) {
			$meta_value = end( $meta_value );
		}

		$this->add_custom_value( $subscriber->ID, $key, (string) $meta_value );

	}


	public function view_subscribers() {

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			include MAILSTER_DIR . 'views/subscribers/detail.php';

		else :

			include MAILSTER_DIR . 'views/subscribers/overview.php';

		endif;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function output_referer( $id ) {

		$subscriber = $this->get( $id );
		if ( ! $subscriber ) {
			return;
		}

		$meta       = (object) $this->meta( $subscriber->ID );
		$timeformat = mailster( 'helper' )->timeformat();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		if ( isset( $meta->referer ) ) :

			switch ( $meta->referer ) {
				case 'import':
					?>

					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( 'import on %s', 'mailster' ), date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
							<?php
					break;
				case 'wpuser':
				case '/wp-admin/user-new.php':
				case '/wp-login.php?action=register':
					?>
					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( 'WP user on %s', 'mailster' ), date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
									<?php
					break;
				case 'backend':
					?>
					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( 'Backend on %s', 'mailster' ), date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
							<?php
					break;
				case 'extern':
					?>
					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( 'an extern form on %s', 'mailster' ), date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
							<?php
					break;
				case '/':
					?>
					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( 'Homepage on %s', 'mailster' ), date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
						<?php
					break;
				default:
					if ( preg_match( '#^wpcomment_(\d+)#', $meta->referer, $match ) ) :
						$comment = get_comment( $match[1] );
						?>

						<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <span><?php printf( esc_html__( '%1$s on %2$s', 'mailster' ), '<a href="' . get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID . '">' . esc_html__( 'Comment', 'mailster' ) . '</a>', date( $timeformat, $subscriber->added + $timeoffset ) ); ?></span>
					<?php elseif ( preg_match( '#^https?://#', $meta->referer, $match ) ) : ?>

					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <a href="<?php echo $meta->referer; ?>"><?php echo $meta->referer; ?></a>
					<?php else : ?>

					<strong><?php esc_html_e( 'via', 'mailster' ); ?></strong> <?php echo $meta->referer; ?>

					<?php endif; ?>

					<?php
					break;

			}
			if ( isset( $meta->form ) ) :
				if ( $form = mailster( 'forms' )->get( $meta->form, false, false ) ) :
					?>
			<br><strong><?php esc_html_e( 'Form', 'mailster' ); ?> #<?php echo $form->ID; ?>:</strong> <a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_forms&ID=' . $form->ID ); ?>"><?php echo esc_html( $form->name ); ?></a>
					<?php
			endif;
			endif;

		endif;

	}


	/**
	 *
	 *
	 * @param unknown $user
	 */
	public function edit_user_profile( $user ) {

		include MAILSTER_DIR . 'views/subscribers/user_edit.php';
	}


	public function screen_options() {

		require_once MAILSTER_DIR . 'classes/subscribers.table.class.php';

		$screen = get_current_screen();

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Subscribers', 'mailster' ),
				'default' => 50,
				'option'  => 'mailster_subscribers_per_page',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $option
	 * @param unknown $value
	 * @return unknown
	 */
	public function save_screen_options( $status, $option, $value ) {

		if ( 'mailster_subscribers_per_page' == $option ) {
			update_user_option( get_current_user_id(), 'mailster_subscribers_per_page', (int) $value );
			return $value;
		}

		return $status;

	}


	public function admin_notices() {

		if ( isset( $_GET['post_type'] ) && 'subscriber' == $_GET['post_type'] && isset( $_GET['post_status'] ) && 'error' == $_GET['post_status'] ) {

			echo '<div class="error"><p><strong><a href="' . add_query_arg(
				array(
					'convert_errors' => 1,
					'post_status'    => 'subscribed',
					'_wpnonce'       => wp_create_nonce( 'mailster_nonce' ),
				)
			) . '">' . esc_html__( 'convert all subscribers with error status back to subscribed', 'mailster' ) . '</a></strong></p></div>';
		}
	}


	public function remove_unassigned_meta() {

		global $wpdb;

		$wpdb->query( "DELETE a FROM {$wpdb->prefix}mailster_subscriber_fields AS a {$wpdb->prefix}mailster_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL" );
		$wpdb->query( "DELETE a FROM {$wpdb->prefix}mailster_subscriber_meta AS a {$wpdb->prefix}mailster_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL" );
		$wpdb->query( "DELETE a FROM {$wpdb->prefix}mailster_queue AS a {$wpdb->prefix}mailster_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL" );
		$wpdb->query( "DELETE a FROM {$wpdb->prefix}mailster_lists_subscribers AS a {$wpdb->prefix}mailster_subscribers AS s ON a.subscriber_id = s.ID WHERE s.ID IS NULL" );

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function verify( $entry ) {

		if ( is_numeric( $entry ) ) {
			$entry = $this->get( $entry, true );
		}

		return apply_filters( 'mymail_verify_subscriber', apply_filters( 'mailster_verify_subscriber', (array) $entry ) );
	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @param unknown $overwrite               (optional)
	 * @param unknown $merge                   (optional)
	 * @param unknown $subscriber_notification (optional)
	 * @return unknown
	 */
	public function update( $entry, $overwrite = true, $merge = false, $subscriber_notification = false ) {

		global $wpdb;

		$entry = (array) $entry;

		if ( isset( $entry['id'] ) ) {
			$entry['ID'] = $entry['id'];
			unset( $entry['id'] );
		}
		if ( isset( $entry['email'] ) ) {
			if ( ! mailster_is_email( $entry['email'] ) ) {
				return new WP_Error( 'invalid_email', esc_html__( 'invalid email address', 'mailster' ) );
			}
			// local part must be case sensitive while domain must be lowercase (RFC 5321)
			$emailparts     = explode( '@', $entry['email'] );
			$entry['email'] = trim( $emailparts[0] . '@' . strtolower( $emailparts[1] ) );
		}

		// fix typos from third party
		if ( isset( $entry['referrer'] ) ) {
			$entry['referer'] = $entry['referrer'];
		}

		$field_names = array(
			'ID'         => '%d',
			'hash'       => '%s',
			'email'      => '%s',
			'status'     => '%d',
			'added'      => '%d',
			'signup'     => '%d',
			'confirm'    => '%d',
			'updated'    => '%d',
			'ip_signup'  => '%s',
			'ip_confirm' => '%s',
			'wp_id'      => '%d',
			'rating'     => '%f',
		);

		$now = time();

		$data          = array();
		$meta          = array();
		$customfields  = array();
		$lists         = null;
		$subscriber_id = null;
		$meta_keys     = $this->get_meta_keys( true );

		$entry = $this->verify( $entry );
		if ( is_wp_error( $entry ) ) {
			return $entry;
		} elseif ( $entry === false ) {
			return new WP_Error( 'not_verified', esc_html__( 'Subscriber failed verification', 'mailster' ) );
		}

		if ( isset( $entry['_lists'] ) ) {
			$lists = $entry['_lists'];
			unset( $entry['_lists'] );
		}

		if ( isset( $entry['ID'] ) ) {
			if ( ! empty( $entry['ID'] ) ) {
				$subscriber_id = (int) $entry['ID'];
			} else {
				unset( $entry['ID'] );
			}
		}

		foreach ( $entry as $key => $value ) {
			if ( isset( $field_names[ $key ] ) ) {
				if ( $key == 'status' && $value == '-1' ) {
					continue;
				}

				$data[ $key ] = $value;
			} elseif ( in_array( $key, $meta_keys ) ) {
				$meta[ $key ] = $value;
			} else {
				$customfields[ $key ] = $value;
			}
		}

		ksort( $data );
		ksort( $customfields );
		ksort( $field_names );

		$usedfields = array_keys( $data );

		$sql = "INSERT INTO {$wpdb->prefix}mailster_subscribers (" . implode( ', ', $usedfields ) . ')';

		$sql .= ' VALUES (' . implode( ', ', array_values( array_intersect_key( $field_names, array_flip( $usedfields ) ) ) ) . ')';

		if ( $overwrite ) {
			$sql .= " ON DUPLICATE KEY UPDATE updated = $now";
			foreach ( $usedfields as $field ) {
				$sql .= ", $field = values($field)";
			}
		}

		$wpdb->suppress_errors();

		if ( false !== $wpdb->query( $wpdb->prepare( $sql, $data ) ) ) {

			if ( ! empty( $wpdb->insert_id ) ) {
				$subscriber_id = $wpdb->insert_id;
			}
			$bulkimport = defined( 'MAILSTER_DO_BULKIMPORT' ) && MAILSTER_DO_BULKIMPORT;

			if ( ! $bulkimport ) {
				mailster_cache_delete( 'subscriber_' . $subscriber_id );
				mailster_cache_delete( 'get_custom_fields_' . $subscriber_id );
				mailster_cache_delete( 'subscriber_meta_' . $subscriber_id . '0' );
			}

			if ( isset( $meta['ip'] ) && $meta['ip'] && 'unknown' !== ( $geo = mailster_ip2City( $meta['ip'] ) ) ) {

				$meta['geo'] = $geo->country_code . '|' . $geo->city;
				if ( $geo->city ) {
					$meta['coords']     = (float) $geo->latitude . ',' . (float) $geo->longitude;
					$meta['timeoffset'] = (int) $geo->timeoffset;
				}
			}
			if ( isset( $meta['form'] ) ) {
				$form = mailster( 'forms' )->get( $meta['form'], false );
				// if form exists and is not a user choice and has lists
				if ( $form && ! $form->userschoice && ! empty( $form->lists ) ) {
					$this->assign_lists( $subscriber_id, $form->lists, false, $data['status'] == 0 ? false : true );
				}
			}
			if ( $lists ) {
				$this->assign_lists( $subscriber_id, $lists, false, $data['status'] == 0 ? false : true );
			}

			$this->add_custom_value( $subscriber_id, $customfields, null, ! $merge );
			$this->update_meta( $subscriber_id, 0, $meta );

			// not on bulk import
			if ( ! $bulkimport ) {

				if ( isset( $data['wp_id'] ) ) {
					$this->sync_wp_user( $data['wp_id'] );
				} else {
					$this->sync_subscriber( $subscriber_id );
					$this->wp_id( $subscriber_id );
				}

				if ( isset( $data['status'] ) ) {
					if ( $data['status'] == 0 ) {
						$this->send_confirmations( $subscriber_id, $subscriber_notification, true );
					}
					if ( $data['status'] == 1 && $subscriber_notification ) {
						$this->subscriber_notification( $subscriber_id );
					}
				}
			}

			do_action( 'mailster_update_subscriber', $subscriber_id );

			return $subscriber_id;

		} else {

			$mysql_errno = 2006;

			if ( isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli && $wpdb->dbh instanceof mysqli ) {
				$mysql_errno = mysqli_errno( $wpdb->dbh );
			}

			if ( $mysql_errno == 1062 ) {
				return new WP_Error( 'email_exists', sprintf( esc_html__( 'The email "%s" already exists.', 'mailster' ), $entry['email'] ) );
			}
			return new WP_Error( $mysql_errno, $wpdb->last_error );
		}

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @param unknown $overwrite               (optional)
	 * @param unknown $merge                   (optional)
	 * @param unknown $subscriber_notification (optional)
	 * @return unknown
	 */
	public function add( $entry, $overwrite = false, $merge = false, $subscriber_notification = true ) {

		$now = time();

		$entry = is_string( $entry ) ? array( 'email' => $entry ) : (array) $entry;

		if ( ! isset( $entry['email'] ) ) {
			return new WP_Error( 'email_missing', esc_html__( 'You must define an email address.', 'mailster' ) );
		}
		$entry = wp_parse_args(
			$entry,
			array(
				'hash'    => $this->hash( $entry['email'] ),
				'added'   => $now,
				'signup'  => $now,
				'updated' => $now,
				'referer' => mailster_get_referer(),
			)
		);

		if ( isset( $entry['status'] ) && $entry['status'] == -1 ) {
			unset( $entry['status'] );
		} elseif ( ! isset( $entry['status'] ) ) {
			$entry['status'] = 1;
		}

		if ( ! isset( $entry['confirm'] ) ) {
			$entry['confirm'] = ( isset( $entry['status'] ) && $entry['status'] == 1 ) ? $now : null;
		}

		if ( ! is_admin() && mailster_option( 'track_users' ) ) {

			$ip = mailster_get_ip();

			$entry = wp_parse_args(
				$entry,
				array(
					'ip'         => $ip,
					'ip_signup'  => $ip,
					'ip_confirm' => ( isset( $entry['status'] ) && $entry['status'] == 1 ) ? $ip : null,
				)
			);

		}

		$subscriber_id = $this->update( $entry, $overwrite, $merge, $subscriber_notification );

		if ( ! is_wp_error( $subscriber_id ) ) {
			do_action( 'mailster_add_subscriber', $subscriber_id );
		}
		return $subscriber_id;

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function merge( $entry ) {

		$subscriber_id = $this->add( $entry );

		// user exists
		if ( is_wp_error( $subscriber_id ) ) {

			if ( $subscriber = $this->get_by_mail( $entry['email'] ) ) {
				$entry['ID'] = $subscriber->ID;
			}

			$subscriber_id = $this->update( $entry, true, true );

		}

		return $subscriber_id;

	}


	/**
	 *
	 *
	 * @param unknown $user_id                 (optional)
	 * @param unknown $userdata                (optional)
	 * @param unknown $merge                   (optional)
	 * @param unknown $subscriber_notification (optional)
	 * @return unknown
	 */
	public function add_from_wp_user( $user_id = null, $userdata = array(), $merge = false, $subscriber_notification = true ) {

		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$user = get_userdata( $user_id );
		if ( empty( $user ) ) {
			return new WP_Error( 'no_user', esc_html__( 'User doesn\'t exist!', 'mailster' ) );
		}

		$email = $user->data->user_email;

		$subscriber_exists = $this->get_by_mail( $email );

		if ( ! $merge && $subscriber_exists ) {
			return new WP_Error( 'subscriber_exists', esc_html__( 'Subscriber already exists', 'mailster' ) );
		}

		$first_name = get_user_meta( $user_id, 'first_name', true );
		$last_name  = get_user_meta( $user_id, 'last_name', true );

		if ( ! $first_name ) {
			$first_name = $user->data->display_name;
		}

		if ( ! isset( $userdata['status'] ) ) {
			$form = mailster( 'forms' )->get( 1 );

			$userdata['status'] = ( $form->doubleoptin && mailster_option( 'register_other_confirmation' ) ) ? 0 : 1;
		}

		$userdata = wp_parse_args(
			$userdata,
			array(
				'email'     => $email,
				'wp_id'     => $user_id,
				'firstname' => $first_name,
				'lastname'  => $last_name,
			)
		);

		$subscriber_id = $this->add( $userdata, true, $merge, $subscriber_notification );

		return $subscriber_id;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $key
	 * @param unknown $value         (optional)
	 * @param unknown $clear         (optional)
	 * @return unknown
	 */
	public function add_custom_value( $subscriber_id, $key, $value = null, $clear = false ) {

		global $wpdb;

		$fields = ! is_array( $key ) ? array( $key => $value ) : $key;

		if ( ! ( $count = count( $fields ) ) ) {
			return true;
		}

		if ( $clear ) {
			$this->remove_custom_value( $subscriber_id );
		}

		$sql = "INSERT INTO {$wpdb->prefix}mailster_subscriber_fields
        (subscriber_id, meta_key, meta_value) VALUES ";

		$inserts             = array();
		$british_date_format = mailster( 'helper' )->dateformat() == 'd/m/Y';

		$customfields = mailster()->get_custom_fields();

		foreach ( $fields as $key => $value ) {
			if ( isset( $customfields[ $key ] ) && $customfields[ $key ]['type'] == 'date' && $value ) {
				if ( $british_date_format ) {
					if ( preg_match( '#(\d{1,2})/(\d{1,2})/(\d{2,4})#', $value, $d ) ) {
						$value = $d[3] . '-' . $d[2] . '-' . $d[1];
					}
				}
				$value = mailster( 'helper' )->do_timestamp( $value, 'Y-m-d' );
			}

			$inserts[] = $wpdb->prepare( '(%d, %s, %s)', $subscriber_id, $key, $value );
		}

		if ( empty( $inserts ) ) {
			return true;
		}

		$sql .= implode( ', ', $inserts );

		$sql .= ' ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id), meta_key = values(meta_key), meta_value = values(meta_value)';

		return false !== $wpdb->query( $sql );

	}

	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $key           (optional)
	 * @return unknown
	 */
	public function remove_custom_value( $subscriber_id, $key = null ) {

		global $wpdb;

		$sql = "DELETE FROM {$wpdb->prefix}mailster_subscriber_fields WHERE subscriber_id = %d";
		if ( ! is_null( $key ) ) {
			$sql .= $wpdb->prepare( ' AND meta_key = %s', (string) $key );
		}

		return false !== $wpdb->query( $wpdb->prepare( $sql, $subscriber_id ) );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $lists
	 * @param unknown $remove_old     (optional)
	 * @param unknown $added          (optional)
	 * @return unknown
	 */
	public function assign_lists( $subscriber_ids, $lists, $remove_old = false, $added = null ) {

		$subscriber_ids = ! is_array( $subscriber_ids ) ? array( (int) $subscriber_ids ) : array_filter( $subscriber_ids, 'is_numeric' );
		if ( ! is_array( $lists ) ) {
			$lists = array( (int) $lists );
		}

		if ( $remove_old ) {
			$this->unassign_lists( $subscriber_ids, null, $lists );
		}

		return mailster( 'lists' )->assign_subscribers( $lists, $subscriber_ids, $remove_old, $added );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $lists          (optional)
	 * @param unknown $not_list       (optional)
	 * @return unknown
	 */
	public function unassign_lists( $subscriber_ids, $lists = null, $not_list = null ) {

		global $wpdb;

		$subscriber_ids = ! is_array( $subscriber_ids ) ? array( (int) $subscriber_ids ) : array_filter( $subscriber_ids, 'is_numeric' );

		$sql = "DELETE FROM {$wpdb->prefix}mailster_lists_subscribers WHERE subscriber_id IN (" . implode( ', ', $subscriber_ids ) . ')';

		if ( ! is_null( $lists ) && ! empty( $lists ) ) {
			if ( ! is_array( $lists ) ) {
				$lists = array( $lists );
			}

			$sql .= ' AND list_id IN (' . implode( ', ', array_filter( $lists, 'is_numeric' ) ) . ')';
		}
		if ( ! is_null( $not_list ) && ! empty( $not_list ) ) {
			if ( ! is_array( $not_list ) ) {
				$not_list = array( $not_list );
			}

			$sql .= ' AND list_id NOT IN (' . implode( ', ', array_filter( $not_list, 'is_numeric' ) ) . ')';
		}

		if ( false !== $wpdb->query( $sql ) ) {
			do_action( 'mailster_unassign_lists', $subscriber_ids, $lists, $not_list );

			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $status         (optional)
	 * @param unknown $remove_actions (optional)
	 * @param unknown $remove_meta    (optional)
	 * @return unknown
	 */
	public function remove( $subscriber_ids, $status = null, $remove_actions = false, $remove_meta = true ) {

		global $wpdb;

		$subscriber_ids = ! is_array( $subscriber_ids ) ? array( (int) $subscriber_ids ) : array_filter( $subscriber_ids, 'is_numeric' );
		$statuses       = ! is_null( $status ) ? ( ! is_array( $status ) ? array( (int) $status ) : array_filter( $status, 'is_numeric' ) ) : null;

		// reduce subscriber_ids if status is set
		if ( $statuses ) {
			$sql            = "SELECT subscribers.ID FROM {$wpdb->prefix}mailster_subscribers AS subscribers WHERE subscribers.ID IN (0," . implode( ',', $subscriber_ids ) . ')';
			$sql           .= ' AND subscribers.status IN (' . implode( ',', $statuses ) . ')';
			$subscriber_ids = $wpdb->get_col( $sql );
		}

		if ( empty( $subscriber_ids ) ) {
			return true;
		}

		$subscriber_ids_concat = implode( ',', $subscriber_ids );

		if ( $delete_wp_user = ( mailster_option( 'delete_wp_user' ) && current_user_can( 'delete_users' ) ) ) {

			// get all wp users except the current user
			$current_user_id = get_current_user_id();
			$sql             = "SELECT wp_id FROM {$wpdb->prefix}mailster_subscribers AS subscribers WHERE subscribers.wp_id != 0 AND subscribers.wp_id != %d AND subscribers.ID IN (" . $subscriber_ids_concat . ')';

			$wp_ids_to_delete = $wpdb->get_col( $wpdb->prepare( $sql, $current_user_id ) );

		}

		// delete from subscribers, lists_subscribers, subscriber_fields, subscriber_meta, queue
		$sql = 'DELETE subscribers,lists_subscribers,subscriber_fields,' . ( $remove_actions ? 'actions,' : '' ) . "subscriber_meta,queue FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON ( subscribers.ID = lists_subscribers.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS subscriber_fields ON ( subscribers.ID = subscriber_fields.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_actions AS actions ON ( subscribers.ID = actions.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS subscriber_meta ON ( subscribers.ID = subscriber_meta.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON ( subscribers.ID = queue.subscriber_id ) WHERE subscribers.ID IN (" . $subscriber_ids_concat . ' )';

		$sql = 'DELETE subscribers,lists_subscribers,subscriber_fields,' . ( $remove_actions ? 'actions,' : '' ) . ( $remove_meta ? 'subscriber_meta,' : '' ) . "queue FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON ( subscribers.ID = lists_subscribers.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS subscriber_fields ON ( subscribers.ID = subscriber_fields.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_actions AS actions ON ( subscribers.ID = actions.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS subscriber_meta ON ( subscribers.ID = subscriber_meta.subscriber_id ) LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON ( subscribers.ID = queue.subscriber_id ) WHERE subscribers.ID IN (" . $subscriber_ids_concat . ' )';

		if ( $success = ( false !== $wpdb->query( $sql ) ) ) {
			if ( $wpdb->last_error ) {
				return new WP_Error( 'db_error', $wpdb->last_error );
			}

			// anonymize action data
			if ( ! $remove_actions ) {
				$sql = "UPDATE {$wpdb->prefix}mailster_actions AS actions SET subscriber_id = NULL WHERE actions.subscriber_id IN (" . $subscriber_ids_concat . ')';

				$wpdb->query( $sql );
			}
			// anonymize subscriber_meta data
			if ( ! $remove_meta ) {
				$sql = "UPDATE {$wpdb->prefix}mailster_subscriber_meta AS subscriber_meta SET subscriber_id = NULL WHERE subscriber_meta.subscriber_id IN (" . $subscriber_ids_concat . ')';

				$wpdb->query( $sql );
			}

			if ( $delete_wp_user ) {

				remove_action( 'deleted_user', array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );

				foreach ( $wp_ids_to_delete as $wp_id ) {
					$user = new WP_User( $wp_id );
					if ( ! in_array( $user->roles[0], array( 'administrator' ) ) ) {
						wp_delete_user( $wp_id );
					}
				}

				add_action( 'deleted_user', array( &$this, 'delete_subscriber_from_wpuser' ), 10, 2 );
			}
		}

		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 */
	public function delete_subscriber_from_wpuser( $user_id ) {

		$subscriber = $this->get_by_wpid( $user_id );
		if ( ! $subscriber ) {
			return;
		}

		if ( mailster_option( 'delete_wp_subscriber' ) ) {
			$this->remove( $subscriber->ID );
		} else {
			$this->update(
				array(
					'ID'    => $subscriber->ID,
					'wp_id' => 0,
				)
			);
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_meta_keys( $keys_only = false ) {
		$meta_keys = array(
			'bounce'        => esc_html__( 'Bounce', 'mailster' ),
			'geo'           => esc_html__( 'Location', 'mailster' ),
			'coords'        => esc_html__( 'Coordinates', 'mailster' ),
			'client'        => esc_html__( 'Client', 'mailster' ),
			'clienttype'    => esc_html__( 'Type of Client', 'mailster' ),
			'clientversion' => esc_html__( 'Clientversion', 'mailster' ),
			'lang'          => esc_html__( 'Language', 'mailster' ),
			'ip'            => esc_html__( 'IP address', 'mailster' ),
			'confirmation'  => esc_html__( 'Confirmation', 'mailster' ),
			'error'         => esc_html__( 'Error', 'mailster' ),
			'referer'       => esc_html__( 'Referer', 'mailster' ),
			'timeoffset'    => esc_html__( 'Timeoffset to UTC', 'mailster' ),
			'form'          => esc_html__( 'Form', 'mailster' ),
			'unsubscribe'   => esc_html__( 'Unsubscribe', 'mailster' ),
			'gdpr'          => esc_html__( 'GDPR Timestamp', 'mailster' ),
			'tags'          => esc_html__( 'Tags', 'mailster' ),
			'formkey'       => esc_html__( 'Form Key', 'mailster' ),
		);
		return $keys_only ? array_keys( $meta_keys ) : $meta_keys;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_single_meta_keys() {
		return array( 'ip', 'lang', 'timeoffset', 'form' );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key         (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function meta( $id, $key = null, $campaign_id = 0 ) {

		global $wpdb;

		$default = array();

		if ( false === ( $meta = mailster_cache_get( 'subscriber_meta_' . $id . $campaign_id ) ) ) {

			$default = array_fill_keys( $this->get_meta_keys( true ), null );

			$sql = "SELECT a.* FROM {$wpdb->prefix}mailster_subscriber_meta AS a WHERE a.subscriber_id = %d AND a.campaign_id = %d";

			$result = $wpdb->get_results( $wpdb->prepare( $sql, $id, $campaign_id ) );
			$meta   = array();

			foreach ( $result as $row ) {
				if ( ! isset( $meta[ $row->subscriber_id ] ) ) {
					$meta[ $row->subscriber_id ] = $default;
				}
				if ( 'tags' == $row->meta_key && $row->meta_value ) {
					$row->meta_value = maybe_unserialize( $row->meta_value );
				}

				$meta[ $row->subscriber_id ][ $row->meta_key ] = $row->meta_value;
			}

			mailster_cache_add( 'subscriber_meta_' . $id . $campaign_id, $meta );

		}

		if ( is_null( $key ) ) {
			return isset( $meta[ $id ] ) ? $meta[ $id ] : $default;
		}

		return isset( $meta[ $id ] ) && isset( $meta[ $id ][ $key ] ) ? $meta[ $id ][ $key ] : ( isset( $default[ $key ] ) ? $default[ $key ] : null );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $campaign_id (optional)
	 * @param unknown $key
	 * @param unknown $value       (optional)
	 * @return unknown
	 */
	public function update_meta( $id, $campaign_id = 0, $key = null, $value = null ) {

		global $wpdb;

		$meta = is_array( $key ) ? (array) $key : array( $key => $value );

		if ( empty( $meta ) ) {
			return true;
		}

		$oldmeta          = $this->meta( $id );
		$meta_keys        = $this->get_meta_keys( true );
		$single_meta_keys = $this->get_single_meta_keys();

		$insert = array_intersect_key( (array) $meta, array_flip( $meta_keys ) );

		$sql = "INSERT INTO {$wpdb->prefix}mailster_subscriber_meta (subscriber_id, campaign_id, meta_key, meta_value)";

		$sql .= ' VALUES ';

		$inserts = array();

		foreach ( $insert as $key => $value ) {

			if ( 'tags' == $key ) {
				$value = maybe_serialize( $value );
			}

			// new value is empty and old value is NOT empty
			if ( ! $value && isset( $oldmeta[ $key ] ) ) {
				// delete that row
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mailster_subscriber_meta WHERE subscriber_id = %d AND meta_key = %s", $id, $key ) );
				continue;
			}
			$single_meta = in_array( $key, $single_meta_keys );
			if ( ! $single_meta ) {
				$inserts[] = $wpdb->prepare( '(%d, %d, %s, %s)', $id, $campaign_id, $key, $value );
			}

			if ( ( $campaign_id || $single_meta ) && 'tags' != $key ) {
				$inserts[] = $wpdb->prepare( '(%d, %d, %s, %s)', $id, 0, $key, $value );
			}

			$oldmeta[ $id ][ $key ] = $value;
		}

		$sql .= implode( ', ', $inserts );

		$sql .= $wpdb->prepare( ' ON DUPLICATE KEY UPDATE subscriber_id = %d, campaign_id = values(campaign_id), meta_key = values(meta_key), meta_value = values(meta_value)', $id );

		if ( empty( $inserts ) || false !== $wpdb->query( $sql ) ) {

			mailster_cache_delete( 'subscriber_meta_' . $id . $campaign_id );

			return true;

		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $ids
	 */
	public function update_rating( $ids ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			$actions = mailster( 'actions' )->get_by_subscriber( $id, null, false, true );
			$rating  = 0.25;
			if ( $this->get_sent( $id ) ) {
				$openrate   = $this->get_open_rate( $id );
				$aclickrate = $this->get_adjusted_click_rate( $id );

				$rating = max( $rating, ( $openrate + $aclickrate ) / 2 );

				if ( $actions['softbounces'] ) {
					$rating -= 0.01;
				}

				if ( $actions['bounces'] ) {
					$rating -= 0.2;
				}

				if ( $actions['unsubscribes'] ) {
					$rating -= 0.3;
				}
			}

			$rating = (float) apply_filters( 'mailster_subscriber_rating', $rating, $id );
			$rating = (float) apply_filters( 'mailster_subscriber_rating_' . $id, $rating );
			$rating = max( 0.1, min( $rating, 1 ) );

			$sql = "UPDATE {$wpdb->prefix}mailster_subscribers AS a SET a.rating = %f WHERE a.ID = %d AND a.rating != %f";
			$wpdb->query( $wpdb->prepare( $sql, $rating, $id, $rating ) );

		}

		return;

	}


	/**
	 *
	 *
	 * @param unknown $ids (optional)
	 * @return unknown
	 */
	public function wp_id( $ids = null ) {

		global $wpdb;

		if ( ! is_null( $ids ) && ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$wpdb->suppress_errors();

		if ( is_array( $ids ) ) {

			$affected = 0;

			foreach ( $ids as $id ) {
				$sql = "UPDATE {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->users} AS b ON a.email = b.user_email SET a.wp_id = b.ID WHERE a.ID = %d";

				if ( false !== $wpdb->query( $wpdb->prepare( $sql, $id ) ) ) {
					$affected += $wpdb->rows_affected;
				}
			}

			return $affected;

		}

		$sql = "UPDATE {$wpdb->prefix}mailster_subscribers AS a INNER JOIN {$wpdb->users} AS b ON a.email = b.user_email SET a.wp_id = b.ID";
		if ( false !== $wpdb->query( $sql ) ) {
			return $wpdb->rows_affected;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $formatted (optional)
	 * @param unknown $round     (optional)
	 * @param unknown $status    (optional)
	 * @return unknown
	 */
	public function get_count( $formatted = false, $round = 1, $status = 1 ) {

		$count = $this->get_count_by_status( $status );

		if ( $round > 1 ) {
			$count = ceil( $count / $round ) * $round;
		}

		if ( 'kilo' === $formatted ) {
			if ( $count >= 1000000 ) {
				$count = round( $count / 1000000, 1 ) . 'M';
			} elseif ( $count >= 10000 ) {
				$count = round( $count / 1000, 1 ) . 'K';
			} else {
				$count = number_format_i18n( $count );
			}
		} elseif ( $formatted ) {
			$count = number_format_i18n( $count );
		}

		return $count;

	}


	/**
	 *
	 *
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function get_count_by_status( $status = null ) {

		global $wpdb;

		if ( false === ( $counts = mailster_cache_get( 'get_count_by_status' ) ) ) {

			$sql = "SELECT status, COUNT( a.ID ) AS count FROM {$wpdb->prefix}mailster_subscribers AS a GROUP BY status";

			$sql = apply_filters( 'mailster_subscribers_get_count_by_status', $sql );

			$result = $wpdb->get_results( $sql );

			$counts = array();

			foreach ( $result as $row ) {
				$counts[ $row->status ] = $row->count;
			}

			mailster_cache_add( 'get_count_by_status', $counts );

		}

		if ( is_string( $status ) ) {
			$status = $this->get_status_by_name( $status );
		}

		// return all
		return ( is_null( $status ) ) ? $counts :
		// only defined ones (array)
		( is_array( $status ) ? array_intersect_key( $counts, array_flip( $status ) ) :
			// only a specific if set
			( isset( $counts[ $status ] ) ? $counts[ $status ] : 0 ) );

	}


	/**
	 *
	 *
	 * @param unknown $status (optional)
	 * @return unknown
	 */
	public function get_totals( $status = null ) {

		$statuses = ! is_null( $status ) ? ( ! is_array( $status ) ? array( $status ) : $status ) : null;

		$counts = $this->get_count( false, 1, $statuses );

		return is_array( $counts ) ? array_sum( $counts ) : $counts;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_sent( $id, $total = false ) {

		return mailster( 'actions' )->get_by_subscriber( $id, 'sent' . ( $total ? '_total' : '' ), true );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_sent_campaigns( $id, $ids_only = false ) {
		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND a.type = 1";

		$campaigns = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_opens( $id, $total = false ) {

		return mailster( 'actions' )->get_by_subscriber( $id, 'opens' . ( $total ? '_total' : '' ), true );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_opened_campaigns( $id, $ids_only = false ) {

		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->postmeta} AS c ON c.post_id = a.campaign_id AND c.meta_key = '_mailster_autoresponder' LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND c.meta_value = '' AND a.type = 2";

		$campaigns = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

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

		$opens = $this->get_opens( $id );

		return min( 1, ( $opens / $sent ) );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_clicks( $id, $total = false ) {

		return mailster( 'actions' )->get_by_subscriber( $id, 'clicks' . ( $total ? '_total' : '' ), true );

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
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_clicked_campaigns( $id, $ids_only = false ) {

		global $wpdb;

		$sql = "SELECT p.post_title AS campaign_title, a.* FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->postmeta} AS c ON c.post_id = a.campaign_id AND c.meta_key = '_mailster_autoresponder' LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.campaign_id WHERE a.subscriber_id = %d AND c.meta_value = '' AND a.type = 3";

		$campaigns = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		return $ids_only ? wp_list_pluck( $campaigns, 'campaign_id' ) : $campaigns;

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

		return mailster( 'actions' )->get_activity( null, $id, $limit, $exclude );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_clients( $id ) {

		global $wpdb;

		$sql = "SELECT COUNT(a.meta_value) AS count, a.meta_value AS name, b.meta_value AS type FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.meta_key = 'client' AND b.meta_key = 'clienttype' AND a.subscriber_id = %d AND a.campaign_id != 0 GROUP BY a.meta_value ORDER BY count DESC";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		$total = ! empty( $result ) ? array_sum( wp_list_pluck( $result, 'count' ) ) : 0;

		foreach ( $result as $i => $row ) {
			$result[ $i ] = array(
				'name'       => $row->name,
				'type'       => $row->type,
				'count'      => $row->count,
				'percentage' => $row->count / $total,
			);
		}

		return $result;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function open_time( $id ) {
		return $this->compare( $id, 1, 2 );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $since_open (optional)
	 * @return unknown
	 */
	public function click_time( $id, $since_open = true ) {
		return $this->compare( $id, $since_open ? 2 : 1, 3 );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $actionA
	 * @param unknown $actionB
	 * @return unknown
	 */
	public function compare( $id, $actionA, $actionB ) {

		global $wpdb;

		$sql = "SELECT (b.timestamp - a.timestamp) AS time FROM {$wpdb->prefix}mailster_actions AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id WHERE a.type = %d AND b.type = %d AND a.subscriber_id = %d GROUP BY a.subscriber_id, a.campaign_id ORDER BY a.timestamp ASC, b.timestamp ASC";

		$times = $wpdb->get_col( $wpdb->prepare( $sql, $actionA, $actionB, $id ) );
		if ( empty( $times ) ) {
			return false;
		}

		$average = array_sum( $times ) / count( $times );

		return $average;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_lists( $id, $ids_only = false ) {

		global $wpdb;

		$cache = mailster_cache_get( 'subscribers_lists' );
		if ( isset( $cache[ $id ] ) ) {
			return $cache[ $id ];
		}

		$sql = "SELECT a.*, ab.added AS confirmed FROM {$wpdb->prefix}mailster_lists AS a LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON a.ID = ab.list_id WHERE ab.subscriber_id = %d";

		$lists = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		return $ids_only ? wp_list_pluck( $lists, 'ID' ) : $lists;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $campaign_id (optional)
	 * @param unknown $status      (optional)
	 * @return unknown
	 */
	public function unsubscribe( $id, $campaign_id = null, $status = null ) {

		return $this->unsubscribe_by_type( 'id', $id, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $hash
	 * @param unknown $campaign_id (optional)
	 * @param unknown $status      (optional)
	 * @return unknown
	 */
	public function unsubscribe_by_hash( $hash, $campaign_id = null, $status = null ) {

		return $this->unsubscribe_by_type( 'hash', $hash, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $md5
	 * @param unknown $campaign_id (optional)
	 * @param unknown $status      (optional)
	 * @return unknown
	 */
	public function unsubscribe_by_md5( $md5, $campaign_id = null, $status = null ) {

		return $this->unsubscribe_by_type( 'md5', $email, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @param unknown $campaign_id (optional)
	 * @param unknown $status      (optional)
	 * @return unknown
	 */
	public function unsubscribe_by_mail( $email, $campaign_id = null, $status = null ) {

		return $this->unsubscribe_by_type( 'email', $email, $campaign_id, $status );

	}


	/**
	 *
	 *
	 * @param unknown $type
	 * @param unknown $value
	 * @param unknown $campaign_id (optional)
	 * @param unknown $status      (optional)
	 * @return unknown
	 */
	private function unsubscribe_by_type( $type, $value, $campaign_id = null, $status = null ) {

		switch ( $type ) {
			case 'id':
				$subscriber = $this->get( (int) $value, false );
				break;
			case 'hash':
				$subscriber = $this->get_by_hash( $value, false );
				break;
			case 'md5':
				$subscriber = $this->get_by_md5( $value, false );
				break;
			case 'email':
				$subscriber = $this->get_by_mail( $value, false );
				break;
		}

		if ( ! $subscriber ) {
			return false;
		}

		if ( mailster( 'campaigns' )->list_based_opt_out( $campaign_id ) ) {

			$lists = mailster( 'campaigns' )->get_lists( $campaign_id, true );
			if ( $this->unassign_lists( $subscriber->ID, $lists ) ) {
				$status .= '_list';

				if ( $status ) {
					$this->update_meta( $subscriber->ID, $campaign_id, 'unsubscribe', $status );
				}
				do_action( 'mailster_list_unsubscribe', $subscriber->ID, $campaign_id, $lists, $status );
				$this->subscriber_unsubscribe_notification( $subscriber->ID, null, $lists );
				return true;
			}

			return false;

		}

		if ( $subscriber->status == 2 ) {
			return true;
		}

		if ( $this->change_status( $subscriber->ID, 2 ) ) {

			if ( $status ) {
				$this->update_meta( $subscriber->ID, $campaign_id, 'unsubscribe', $status );
			}
			do_action( 'mailster_unsubscribe', $subscriber->ID, $campaign_id, $status );

			$this->subscriber_unsubscribe_notification( $subscriber->ID );
			return true;

		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @return unknown
	 */
	public function subscriber_notification( $id, $timestamp = null ) {

		if ( defined( 'MAILSTER_DO_BULKIMPORT' ) && MAILSTER_DO_BULKIMPORT ) {
			return false;
		}

		if ( ! mailster_option( 'subscriber_notification' ) || ! mailster_option( 'subscriber_notification_receviers' ) ) {
			return false;
		}

		if ( $delay = mailster_option( 'subscriber_notification_delay' ) ) {

			$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay );
			$timestamp = apply_filters( 'mymail_subscriber_notification_delay', apply_filters( 'mailster_subscriber_notification_delay', $timestamp ) );

			return ( ! wp_next_scheduled( 'mailster_subscriber_notification' ) )
				? wp_schedule_single_event( $timestamp, 'mailster_subscriber_notification' )
				: false;

		} else {

			$subscriber = $this->get( $id );
			if ( ! $subscriber ) {
				return;
			}

			if ( ! $timestamp ) {
				$timestamp = time();
			}

			return mailster( 'notification' )->add(
				$timestamp,
				array(
					'template'      => 'new_subscriber',
					'subscriber_id' => $subscriber->ID,
				)
			);

		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function subscriber_delayed_notification() {

		if ( ! mailster_option( 'subscriber_notification' ) || ! mailster_option( 'subscriber_notification_receviers' ) || ! mailster_option( 'subscriber_notification_delay' ) ) {
			return false;
		}

		return mailster( 'notification' )->add(
			time(),
			array(
				'template' => 'new_subscriber_delayed',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @param unknown $lists     (optional)
	 * @return unknown
	 */
	public function subscriber_unsubscribe_notification( $id, $timestamp = null, $lists = null ) {

		if ( defined( 'MAILSTER_DO_BULKIMPORT' ) && MAILSTER_DO_BULKIMPORT ) {
			return false;
		}

		if ( ! mailster_option( 'unsubscribe_notification' ) || ! mailster_option( 'unsubscribe_notification_receviers' ) ) {
			return false;
		}

		if ( $delay = mailster_option( 'unsubscribe_notification_delay' ) ) {

			$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay );
			$timestamp = apply_filters( 'mymail_subscriber_unsubscribe_notification_delay', apply_filters( 'mailster_subscriber_unsubscribe_notification_delay', $timestamp ) );

			return ( ! wp_next_scheduled( 'mailster_unsubscribe_notification' ) )
				? wp_schedule_single_event( $timestamp, 'mailster_unsubscribe_notification' )
				: false;

		} else {

			$subscriber = $this->get( $id );
			if ( ! $subscriber ) {
				return;
			}

			if ( ! $timestamp ) {
				$timestamp = time();
			}

			return mailster( 'notification' )->add(
				$timestamp,
				array(
					'template'      => 'unsubscribe',
					'subscriber_id' => $subscriber->ID,
				)
			);

		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function subscriber_delayed_unsubscribe_notification() {

		if ( ! mailster_option( 'unsubscribe_notification' ) || ! mailster_option( 'unsubscribe_notification_receviers' ) || ! mailster_option( 'unsubscribe_notification_delay' ) ) {
			return false;
		}

		return mailster( 'notification' )->add(
			time(),
			array(
				'template' => 'unsubscribe_delayed',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $ids   (optional)
	 * @param unknown $force (optional)
	 * @param unknown $now   (optional)
	 * @param unknown $user_form_id   (optional)
	 * @return unknown
	 */
	public function send_confirmations( $ids = null, $force = false, $now = false, $user_form_id = null ) {

		global $wpdb;

		// get the very first form ID
		$fallback_form_id = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->prefix}mailster_forms ORDER BY ID ASC LIMIT 1" );

		// get all pending subscribers which are not queued already
		$sql  = "SELECT subscribers.ID, subscribers.signup, IFNULL( confirmation.meta_value, 0 ) AS try, forms.resend, forms.resend_count, forms.resend_time, IFNULL( forms.ID, $fallback_form_id ) AS form_id, lists_subscribers.list_id FROM {$wpdb->prefix}mailster_subscribers AS subscribers";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON subscribers.ID = lists_subscribers.subscriber_id AND lists_subscribers.added = 0";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS confirmation ON subscribers.ID = confirmation.subscriber_id AND confirmation.meta_key = 'confirmation'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS form ON subscribers.ID = form.subscriber_id AND form.meta_key = 'form'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON subscribers.ID = queue.subscriber_id AND queue.campaign_id = 0";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_forms AS forms ON forms.ID = IFNULL( form.meta_value, $fallback_form_id )";

		$sql .= ' WHERE 1=1';

		// status is either pending or list assignment is pending
		$sql .= ' AND (subscribers.status = 0 OR lists_subscribers.added = 0)';
		// queue doesn"t exist or has been sent already (and not removed from queue)
		$sql .= ' AND (queue.subscriber_id IS NULL OR queue.sent != 0)';
		// try is less den the count from the form settings
		if ( ! $force ) {
			$sql .= ' AND (IFNULL( confirmation.meta_value, 0 ) <= forms.resend_count)';
			$sql .= ' AND (forms.resend = 1 OR IFNULL( confirmation.meta_value, 0 ) = 0)';
		}
		// resend is enabled or it's the first try
		if ( ! $force ) {
		}

		if ( ! is_null( $ids ) ) {
			$ids = ! is_array( $ids ) ? array( (int) $ids ) : array_filter( $ids, 'is_numeric' );
			$ids = array_filter( $ids );
			if ( ! empty( $ids ) ) {
				$sql .= ' AND subscribers.ID IN (' . implode( ',', $ids ) . ')';
			}
		}

		$sql .= ' GROUP BY subscribers.ID, lists_subscribers.list_id';

		$entries = $wpdb->get_results( $sql );

		$subscribers = array();

		foreach ( $entries as $entry ) {
			if ( ! isset( $subscribers[ $entry->ID ] ) ) {
				$subscribers[ $entry->ID ]           = $entry;
				$subscribers[ $entry->ID ]->list_ids = array();
			}
			$subscribers[ $entry->ID ]->list_ids[] = $entry->list_id;
		}

		$count = 0;

		foreach ( $subscribers as $subscriber ) {

			$timestamp = $now ? time() : max( time(), $subscriber->signup ) + ( $subscriber->resend_time * 3600 * $subscriber->try );

			if ( mailster( 'notification' )->add(
				$timestamp,
				array(
					'subscriber_id' => $subscriber->ID,
					'template'      => 'confirmation',
					'form'          => ! is_null( $user_form_id ) ? (int) $user_form_id : $subscriber->form_id,
					'list_ids'      => $subscriber->list_ids,
				)
			) ) {
				$this->update_meta( $subscriber->ID, 0, 'confirmation', ++$subscriber->try );
				$count++;
			}
		}

		return $count;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id (optional)
	 * @return unknown
	 */
	public function remove_pending_confirmations( $subscriber_id = null ) {

		global $wpdb;

		// delete confirmation option and all pending confirmations
		$sql = "DELETE a,b FROM {$wpdb->prefix}mailster_queue AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND b.meta_key = 'confirmation' WHERE a.campaign_id = 0 AND a.options LIKE '%s:8:\"template\";s:12:\"confirmation\";%'";

		if ( ! is_null( $subscriber_id ) ) {
			$sql .= ' AND a.subscriber_id = ' . (int) $subscriber_id;
		}

		return false !== $wpdb->query( $sql );
	}


	/**
	 *
	 *
	 * @param unknown $args        (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function query( $args = array(), $campaign_id = null ) {

		require_once MAILSTER_DIR . 'classes/subscriber.query.class.php';
		$query = MailsterSubscriberQuery::get_instance();
		return $query->run( $args, $campaign_id );

	}


	/**
	 *
	 *
	 * @param unknown $ID            (optional)
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get( $ID, $custom_fields = false ) {

		global $wpdb;

		if ( is_numeric( $ID ) ) {
			return $this->get_by_type( 'ID', $ID, $custom_fields );
		}

	}


	/**
	 *
	 *
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_current_user( $custom_fields = true ) {

		$subscriber = false;

		if ( isset( $_COOKIE ) && isset( $_COOKIE['mailster'] ) ) {
			$hash       = $_COOKIE['mailster'];
			$subscriber = mailster( 'subscribers' )->get_by_hash( $hash, $custom_fields );
		}

		if ( empty( $subscriber ) ) {
			$subscriber = $this->get_by_wpid( null, $custom_fields );
		}

		return $subscriber;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_current_user_id() {

		if ( $subscriber = $this->get_current_user( false ) ) {
			return $subscriber->ID;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $mail
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_by_mail( $mail, $custom_fields = false ) {

		return $this->get_by_type( 'email', $mail, $custom_fields );

	}


	/**
	 *
	 *
	 * @param unknown $hash
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_by_hash( $hash, $custom_fields = false ) {

		return $this->get_by_type( 'hash', $hash, $custom_fields );
	}


	/**
	 *
	 *
	 * @param unknown $md5
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_by_md5( $md5, $custom_fields = false ) {

		return $this->get_by_type( 'md5', $md5, $custom_fields );
	}


	/**
	 *
	 *
	 * @param unknown $wpid          (optional)
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_by_wpid( $wpid = null, $custom_fields = false ) {

		if ( is_null( $wpid ) ) {
			$wpid = get_current_user_id();
		}

		if ( ! $wpid ) {
			return false;
		}

		return $this->get_by_type( 'wp_id', $wpid, $custom_fields );
	}


	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $limit         (optional)
	 * @param unknown $offset        (optional)
	 * @param unknown $not_in_status (optional)
	 * @return unknown
	 */
	public function get_ids( $status, $limit = null, $offset = 0, $not_in_status = false ) {

		global $wpdb;

		if ( is_null( $status ) ) {
			$status = array( 1 );
		} elseif ( $status === false ) {
			$status = array( 0, 1, 2, 3, 4, 5, 6 );
		}
		$statuses = ! is_array( $status ) ? array( $status ) : $status;
		$statuses = array_filter( $statuses, 'is_numeric' );

		$sql  = 'SELECT a.ID';
		$sql .= " FROM {$wpdb->prefix}mailster_subscribers AS a";
		$sql .= ' WHERE status ' . ( $not_in_status ? 'NOT IN' : 'IN' ) . ' (' . implode( ', ', $statuses ) . ')';

		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
		}

		return $wpdb->get_col( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $type
	 * @param unknown $value
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	private function get_by_type( $type, $value, $custom_fields = false ) {

		global $wpdb;

		if ( 'ID' == $type ) {
			$value = (int) $value;
			if ( $subscriber = mailster_cache_get( 'subscriber_' . $value ) ) {
				return $subscriber;
			}
			$type = esc_sql( $type );
		} elseif ( 'md5' == $type ) {
			$type = 'md5(`email`)';
		} else {
			$type = esc_sql( $type );
		}

		$sql = "SELECT * FROM {$wpdb->prefix}mailster_subscribers WHERE " . $type . " = '" . esc_sql( $value ) . "' LIMIT 1";

		$sql = apply_filters( 'mailster_subscribers_get_by_type_sql', $sql, $type, $value );

		if ( ! ( $subscriber = $wpdb->get_row( $sql ) ) ) {
			return false;
		}

		if ( $custom_fields ) {

			$fields = $this->get_custom_fields( $subscriber->ID );

			$subscriber = (object) wp_parse_args( $fields, (array) $subscriber );
		}

		$subscriber->ID      = (int) $subscriber->ID;
		$subscriber->wp_id   = (int) $subscriber->wp_id;
		$subscriber->status  = (int) $subscriber->status;
		$subscriber->added   = (int) $subscriber->added;
		$subscriber->updated = (int) $subscriber->updated;
		$subscriber->signup  = (int) $subscriber->signup;
		$subscriber->confirm = (int) $subscriber->confirm;
		$subscriber->rating  = (float) $subscriber->rating;

		if ( $custom_fields ) {
			mailster_cache_set( 'subscriber_' . $subscriber->ID, $subscriber );
		}

		return $subscriber;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $field         (optional)
	 * @return unknown
	 */
	public function get_custom_fields( $subscriber_id, $field = null ) {

		global $wpdb;

		if ( false === ( $custom_fields = mailster_cache_get( 'get_custom_fields_' . $subscriber_id ) ) ) {

			$custom_field_names = mailster()->get_custom_fields( true );

			$custom_fields              = array_fill_keys( $custom_field_names, null );
			$custom_fields['firstname'] = '';
			$custom_fields['lastname']  = '';
			$custom_fields['fullname']  = '';

			$sql = $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->prefix}mailster_subscriber_fields WHERE subscriber_id = %d", $subscriber_id );

			$meta_data = $wpdb->get_results( $sql );

			foreach ( $meta_data as $i => $data ) {
				$custom_fields[ $data->meta_key ] = $data->meta_value;
			}

			$custom_fields['fullname'] = trim(
				mailster_option( 'name_order' )
				? $custom_fields['lastname'] . ' ' . $custom_fields['firstname']
				: $custom_fields['firstname'] . ' ' . $custom_fields['lastname']
			);

			if ( is_null( $field ) ) {
				mailster_cache_set( 'get_custom_fields_' . $subscriber_id, $custom_fields );
			}
		}
		if ( is_null( $field ) ) {
			return $custom_fields;
		}

		return isset( $custom_fields[ $field ] ) ? $custom_fields[ $field ] : null;

	}


	/**
	 *
	 *
	 * @param unknown $custom_fields (optional)
	 * @return unknown
	 */
	public function get_empty( $custom_fields = false ) {

		global $wpdb;

		$fields = wp_parse_args(
			array(
				'firstname',
				'lastname',
				'fullname',
			),
			$wpdb->get_col( "DESCRIBE {$wpdb->prefix}mailster_subscribers" )
		);

		if ( ! $custom_fields ) {
			$fields = wp_parse_args( mailster()->get_custom_fields( true ), $fields );
		}

		$subscriber = (object) array_fill_keys( array_values( $fields ), null );

		$subscriber->status = 1;

		return $subscriber;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $form_id (optional)
	 * @param unknown $list_id (optional)
	 * @return unknown
	 */
	public function get_confirm_link( $id, $form_id = null, $list_ids = null ) {

		$subscriber = $this->get( $id );
		if ( ! $subscriber ) {
			return '';
		}

		if ( is_null( $form_id ) ) {
			$form_id = '';
		}

		if ( ! is_null( $list_ids ) && ! is_array( $list_ids ) ) {
			$list_ids = array( $list_ids );
		}

		$baselink = home_url();

		if ( $query = parse_url( $baselink, PHP_URL_QUERY ) ) {
			$baselink = strtok( $baselink, '?' );
			wp_parse_str( $query, $query );
		}

		$slugs = mailster_option( 'slugs' );
		$slug  = isset( $slugs['confirm'] ) ? $slugs['confirm'] : 'confirm';
		$lists = $list_ids ? '/' . implode( '/', $list_ids ) : '';

		$link = ( mailster( 'helper' )->using_permalinks() )
			? trailingslashit( $baselink ) . trailingslashit( 'mailster/' . $slug . '/' . $subscriber->hash . '/' . $form_id . $lists )
			: add_query_arg(
				array(
					'mailster_confirm' => '',
					'k'                => $subscriber->hash,
					'f'                => $form_id,
					'l'                => $list_ids,
				),
				$baselink
			);

		if ( ! empty( $query ) ) {
			$link = add_query_arg( $query, $link );
		}

		return $link;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_recipient_detail( $id, $campaign_id ) {

		$subscriber = $this->get( $id, true );

		$timeformat = mailster( 'helper' )->timeformat();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$actions = (object) mailster( 'actions' )->get_campaign_actions( $campaign_id, $id, null, false );

		$meta = $this->meta( $id, null, $campaign_id );

		$return['success'] = true;

		$avatar = get_option( 'show_avatars' );

		$html = '';

		if ( $avatar ) :
			$html .= '<div class="user_image" title="' . esc_html__( 'Source', 'mailster' ) . ': Gravatar.com" data-email="' . $subscriber->email . '" style="background-image:url(' . $this->get_gravatar_uri( $subscriber->email, 240 ) . ')"></div>';
		endif;

		$html .= '<div class="receiver-detail-data ' . ( $avatar ? 'has-avatar' : '' ) . '">';
		$html .= '<h4>' . esc_html( $subscriber->fullname ? $subscriber->fullname : $subscriber->email ) . ' <a href="edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID . '">' . esc_html__( 'edit', 'mailster' ) . '</a></h4>';
		$html .= '<ul>';

		$html .= '<li><label>' . esc_html__( 'sent', 'mailster' ) . ':</label> ' . ( $actions->sent ? date( $timeformat, $actions->sent + $timeoffset ) . ', ' . sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $actions->sent ) ) . ( $actions->sent_total > 1 ? ' <span class="count">(' . sprintf( esc_html__( '%d times', 'mailster' ), $actions->sent_total ) . ')</span>' : '' ) : esc_html__( 'not yet', 'mailster' ) );
		$html .= '<li><label>' . esc_html__( 'opens', 'mailster' ) . ':</label> ' . ( $actions->opens ? date( $timeformat, $actions->opens + $timeoffset ) . ', ' . sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $actions->opens ) ) . ( $actions->opens_total > 1 ? ' <span class="count">(' . sprintf( esc_html__( '%d times', 'mailster' ), $actions->opens_total ) . ')</span>' : '' ) : esc_html__( 'not yet', 'mailster' ) );

		$html .= '<p class="meta"><label>&nbsp;</label>';
		if ( $meta['client'] ) {
			$html .= sprintf( esc_html__( 'with %s', 'mailster' ), '<strong>' . $meta['client'] . '</strong>' );
		}

		if ( $meta['geo'] ) {
			$geo = explode( '|', $meta['geo'] );
			if ( $geo[1] ) {
				$html .= ' ' . sprintf( esc_html_x( 'in %1$s, %2$s', 'in [city], [country]', 'mailster' ), '<strong>' . $geo[1] . '</strong>', '<span class="mailster-flag flag-' . strtolower( $geo[0] ) . '"></span> <strong>' . $geo[0] . '</strong>' );
			}
		}
		$html .= '</p>';

		$html .= '</li>';

		if ( $actions->clicks ) {
			$html .= '<li><ul>';
			foreach ( $actions->clicks as $link => $count ) {
				$html .= '<li class=""><a href="' . $link . '" class="external clicked-link">' . $link . '</a> <span class="count">(' . sprintf( esc_html__( _n( '%s click', '%s clicks', (int) $count, 'mailster' ) ), $count ) . ')</span></li>';
			}
			$html .= '</ul></li>';
		}

		if ( $actions->unsubscribes ) {
			$message = mailster( 'helper' )->get_unsubscribe_message( $this->meta( $id, 'unsubscribe', $campaign_id ) );
			$html   .= '<li>' . sprintf( esc_html__( 'Unsubscribes on %s', 'mailster' ), date( $timeformat, $actions->unsubscribes + $timeoffset ) . ', ' . sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $actions->unsubscribes ) ) ) . '<br>' . esc_html( $message ) . '</li>';
		}

		if ( $actions->bounces ) {
			$message = mailster( 'helper' )->get_bounce_message( $this->meta( $id, 'bounce', $campaign_id ) );
			$html   .= '<li>';
			if ( $actions->softbounces_total ) :
				$html .= '<label class="red">' . sprintf( esc_html__( _n( '%s soft bounce', '%s soft bounces', $actions->softbounces_total, 'mailster' ) ), $actions->softbounces_total ) . '</label>';
			endif;
			$html .= '<strong class="red">' . sprintf( esc_html__( 'Hard bounced at %s', 'mailster' ), date( $timeformat, $actions->bounces + $timeoffset ) . ', ' . sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $actions->bounces ) ) ) . '</strong><br>' . esc_html( $message );
			$html .= '</li>';
		} elseif ( $actions->softbounces ) {
			$message = mailster( 'helper' )->get_bounce_message( $this->meta( $id, 'bounce', $campaign_id ) );
			$html   .= '<li><label class="red">' . sprintf( esc_html__( _n( '%s soft bounce', '%s soft bounces', $actions->softbounces_total, 'mailster' ) ), $actions->softbounces_total ) . '</label><br>' . esc_html( $message ) . '</li>';
		}

		$html .= '</ul>';
		$html .= '</div>';

		return $html;

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		if ( $new ) {
			$subscriber_id = $this->add_from_wp_user(
				get_current_user_id(),
				array(
					'status'  => 1,
					'referer' => 'backend',
				),
				false,
				false
			);
		}

	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 * @return unknown
	 */
	public function user_register( $user_id ) {

		// for third party plugins
		if ( ! apply_filters( 'mymail_user_register', apply_filters( 'mailster_user_register', true ) ) ) {
			return;
		}

		$is_register = isset( $_POST['wp-submit'] );

		if ( $is_register ) {

			if ( ! mailster_option( 'register_signup' ) ) {
				return false;
			}

			// stop if not option
			if ( ! isset( $_POST['mailster_user_newsletter_signup'] ) ) {
				return false;
			}

			$status = mailster_option( 'register_signup_confirmation' ) ? 0 : 1;

			$referer = 'wp-login.php';

		} else {

			if ( ! mailster_option( 'register_other' ) ) {
				return false;
			}

			$status = mailster_option( 'register_other_confirmation' ) ? 0 : 1;

			$roles = mailster_option( 'register_other_roles', array() );

			$pass = false;

			foreach ( $roles as $role ) {
				if ( user_can( $user_id, $role ) ) {
					$pass = true;
					break;
				}
			}

			if ( ! $pass ) {
				return;
			}

			$referer = 'other';

		}

		$lists = $is_register ? mailster_option( 'register_signup_lists', array() ) : mailster_option( 'register_other_lists', array() );

		$subscriber_id = $this->add_from_wp_user(
			$user_id,
			array(
				'status'  => $status,
				'referer' => apply_filters( 'mymail_user_register_referer', apply_filters( 'mailster_user_register_referer', $referer ) ),
				'_lists'  => $lists,
			),
			true
		);

		if ( is_wp_error( $subscriber_id ) ) {

			return false;

		} else {

			return true;
		}

	}


	public function register_form() {

		if ( ! mailster_option( 'register_signup' ) ) {
			return;
		}

		$output = '<p><label for="mailster_user_newsletter_signup"><input name="mailster_user_newsletter_signup" type="checkbox" id="mailster_user_newsletter_signup" value="1" ' . checked( mailster_option( 'register_signup_checked' ), true, false ) . ' />' . mailster_text( 'newsletter_signup' ) . '</label></p>';

		echo apply_filters( 'mailster_register_form_signup_field', $output ) . "\n";

	}


	public function comment_form_checkbox() {

		if ( ! mailster_option( 'register_comment_form' ) ) {
			return;
		}

		$commenter = wp_get_current_commenter();

		if ( ! empty( $commenter['comment_author_email'] ) && $this->get_by_mail( $commenter['comment_author_email'] ) ) {
			return;
		}

		if ( is_user_logged_in() && $this->get_by_wpid( get_current_user_id() ) ) {
			return;
		}

		$field  = '<p class="comment-form-newsletter-signup">';
		$field .= '<label for="mailster_newsletter_signup"><input name="newsletter_signup" type="checkbox" id="mailster_newsletter_signup" value="1" ' . checked( mailster_option( 'register_comment_form_checked' ), true, false ) . '/> ' . mailster_text( 'newsletter_signup' ) . '</label>';
		$field .= '</p>';

		echo apply_filters( 'comment_form_field_newsletter_signup', $field ) . "\n";

	}


	/**
	 *
	 *
	 * @param unknown $comment_id
	 * @param unknown $comment_approved
	 * @return unknown
	 */
	public function comment_post( $comment_id, $comment_approved ) {

		if ( ! mailster_option( 'register_comment_form' ) ) {
			return false;
		}

		if ( isset( $_POST['newsletter_signup'] ) ) {

			if ( in_array( $comment_approved . '', mailster_option( 'register_comment_form_status', array() ) ) ) {

				$comment = get_comment( $comment_id );

				if ( $comment && ! $this->get_by_mail( $comment->comment_author_email ) ) {

					$lists = mailster_option( 'register_comment_form_lists', array() );
					$lists = apply_filters( 'mymail_comment_post_lists', apply_filters( 'mailster_comment_post_lists', $lists, $comment, $comment_approved ), $comment, $comment_approved );

					$status = mailster_option( 'register_comment_form_confirmation' ) ? 0 : 1;

					$userdata = array(
						'email'     => $comment->comment_author_email,
						'status'    => $status,
						'firstname' => $comment->comment_author,
						'referer'   => 'wpcomment_' . $comment->comment_ID,
					);
					$userdata = apply_filters( 'mymail_comment_post_userdata', apply_filters( 'mailster_comment_post_userdata', $userdata, $comment, $comment_approved ), $comment, $comment_approved );

					$subscriber_id = $this->add( $userdata );

					if ( $subscriber_id && ! is_wp_error( $subscriber_id ) && ! empty( $lists ) ) {
						$this->assign_lists( $subscriber_id, $lists, false, $status );
					}
				}
			} elseif ( ! in_array( $comment_approved . '', array( '1', 'approve' ) ) ) {
				add_comment_meta( $comment_id, 'newsletter_signup', true, true );
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $comment_id
	 * @param unknown $comment_status
	 * @return unknown
	 */
	public function wp_set_comment_status( $comment_id, $comment_status ) {

		if ( ! mailster_option( 'register_comment_form' ) || ! in_array( $comment_status . '', array( '1', 'approve' ) ) ) {
			return false;
		}

		if ( get_comment_meta( $comment_id, 'newsletter_signup', true ) ) {

			$comment = get_comment( $comment_id );

			if ( ! $this->get_by_mail( $comment->comment_author_email ) ) {

				$lists = mailster_option( 'register_comment_form_lists', array() );
				$lists = apply_filters( 'mymail_comment_post_lists', apply_filters( 'mailster_comment_post_lists', $lists, $comment, $comment_approved ), $comment, $comment_approved );

				$status = mailster_option( 'register_comment_form_confirmation' ) ? 0 : 1;

				$userdata = array(
					'email'     => $comment->comment_author_email,
					'status'    => $status,
					'firstname' => $comment->comment_author,
					'referer'   => 'wpcomment_' . $comment->comment_ID,
					'signup'    => strtotime( $comment->comment_date_gmt ),
				);
				$userdata = apply_filters( 'mymail_comment_post_userdata', apply_filters( 'mailster_comment_post_userdata', $userdata, $comment, $comment_approved ), $comment, $comment_approved );

				$subscriber_id = $this->add( $userdata );

				if ( $subscriber_id && ! is_wp_error( $subscriber_id ) && ! empty( $lists ) ) {
					$this->assign_lists( $subscriber_id, $lists, false, $status );
				}

				delete_comment_meta( $comment_id, 'newsletter_signup' );

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $campaign_id
	 * @param unknown $hard          (optional)
	 * @param unknown $status        (optional)
	 * @return unknown
	 */
	public function bounce( $subscriber_id, $campaign_id, $hard = false, $status = null ) {

		global $wpdb;

		$subscriber = $this->get( $subscriber_id, false );
		if ( ! $subscriber ) {
			return false;
		}

		$campaign = mailster( 'campaigns' )->get( $campaign_id );
		if ( ! $campaign ) {
			$campaign_id = 0;
		}

		if ( $hard ) {

			if ( $this->change_status( $subscriber->ID, $this->get_status_by_name( 'hardbounced' ) ) ) {
				do_action( 'mailster_bounce', $subscriber->ID, $campaign_id, true, $status );
				if ( $status ) {
					$this->update_meta( $subscriber->ID, $campaign_id, 'bounce', $status );
				}

				return true;
			}

			return false;
		}

		// soft bounce
		$bounce_attempts = mailster_option( 'bounce_attempts' );

		// check if bounce limit has been reached => hardbounce
		if ( $bounce_attempts == 1 || $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailster_actions WHERE type = 5 AND subscriber_id = %d AND campaign_id = %d AND count >= %d LIMIT 1", $subscriber->ID, $campaign_id, $bounce_attempts ) ) ) {

			return $this->bounce( $subscriber->ID, $campaign_id, true, $status );

		}

		// softbounce
		do_action( 'mailster_bounce', $subscriber->ID, $campaign_id, false, $status );
		if ( $status ) {
			$this->update_meta( $subscriber->ID, $campaign_id, 'bounce', $status );
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_sent_mails( $id ) {

		global $wpdb;

		if ( false === ( $counts = mailster_cache_get( 'get_sent_mails' ) ) ) {

			$sql = "SELECT status, COUNT( * ) AS count FROM {$wpdb->prefix}mailster_subscribers GROUP BY status";

			$result = $wpdb->get_results( $sql );
			$counts = array();

			foreach ( $result as $row ) {
				$counts[ $row->status ] = $row->count;
			}

			mailster_cache_add( 'get_sent_mails', $counts );
		}

		return ( is_null( $id ) ) ? $counts : ( isset( $counts[ $id ] ) ? $counts[ $id ] : 0 );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $size (optional)
	 * @return unknown
	 */
	public function get_gravatar( $id, $size = 120 ) {

		$subscriber = $this->get( $id );
		return $this->get_gravatar_uri( $subscriber->email, $size );

	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @param unknown $size  (optional)
	 * @return unknown
	 */
	public function get_gravatar_uri( $email, $size = 120 ) {

		$email = strtolower( trim( $email ) );
		$hash  = md5( $email );
		// create a number from 01 to 09 based on the email address
		$id      = '0' . ( round( abs( crc32( $hash ) ) % 9 ) + 1 );
		$default = 'https://static.mailster.co/user/user' . $id . '.gif';

		$image = get_avatar( $email, $size, $default );

		if ( preg_match( '/src=["\'](.*)["\']/Ui', $image, $match ) ) {
			$url = htmlspecialchars_decode( $match[1] );
		} else {
			$url = 'https://' . ( $id % 3 ) . '.gravatar.com/avatar/' . $hash . '?d=' . urlencode( $default ) . '&s=' . $size;
		}

		return $url;

	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function get_status_by_name( $name ) {

		$statuses = $this->get_status();
		$found    = array_search( $name, $statuses );

		if ( $found === false ) {
			$statuses = $this->get_status( null, true );
			$found    = array_search( $name, $statuses );
		}

		return $found;
	}


	/**
	 *
	 *
	 * @param unknown $id   (optional)
	 * @param unknown $nice (optional)
	 * @return unknown
	 */
	public function get_status( $id = null, $nice = false ) {

		if ( $nice ) {
			$statuses = array(
				esc_html__( 'Pending', 'mailster' ),
				esc_html__( 'Subscribed', 'mailster' ),
				esc_html__( 'Unsubscribed', 'mailster' ),
				esc_html__( 'Hardbounced', 'mailster' ),
				esc_html__( 'Error', 'mailster' ),
			);

		} else {
			$statuses = array(
				'pending',
				'subscribed',
				'unsubscribed',
				'hardbounced',
				'error',
			);
		}

		return is_null( $id ) ? $statuses : ( isset( $statuses[ $id ] ) ? $statuses[ $id ] : false );
	}


	/**
	 *
	 *
	 * @param unknown $data
	 * @return unknown
	 */
	public function get_userdata( $data ) {

		$custom_field_names = mailster()->get_custom_fields( true );

		$userdatafields = wp_parse_args( (array) $custom_field_names, array( 'firstname', 'lastname', 'fullname' ) );

		return (object) array_intersect_key( (array) $data, array_flip( $userdatafields ) );

	}


	/**
	 *
	 *
	 * @param unknown $data
	 * @param unknown $userdata
	 * @return unknown
	 */
	public function get_metadata( $data, $userdata ) {

		return (object) array_intersect_key( (array) $data, array_flip( array_keys( array_diff_key( (array) $data, (array) $userdata ) ) ) );

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $timestamps     (optional)
	 * @return unknown
	 */
	public function get_timeoffset_timestamps( $subscriber_ids, $timestamps = null ) {

		global $wpdb;

		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = array( $subscriber_ids );
		}

		if ( is_null( $timestamps ) ) {
			$timestamps = time();
		}

		$subscriber_ids = array_filter( $subscriber_ids, 'is_numeric' );
		$timeoffset     = mailster( 'helper' )->gmt_offset( true );

		if ( empty( $subscriber_ids ) ) {
			return array();
		}

		if ( is_array( $timestamps ) ) {
			$sql = "SELECT a.ID, IF(b.meta_value IS NULL,0,(-b.meta_value*3600+$timeoffset)) AS offset FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.ID = b.subscriber_id AND b.meta_key = 'timeoffset' WHERE a.ID IN (" . implode( ',', $subscriber_ids ) . ') ORDER BY a.ID ASC';

			$result = $wpdb->get_results( $sql );

			$return = array();

			foreach ( $subscriber_ids as $i => $subscriber ) {
				if ( $result[ $i ]->ID == $subscriber ) {
					$return[ $i ] = $timestamps[ $i ] + $result[ $i ]->offset;
				} else {
					$return[ $i ] = $timestamps[ $i ];
				}
			}
		} elseif ( is_numeric( $timestamps ) ) {

			$chunks = array_chunk( $subscriber_ids, 2000 );
			$return = array();

			foreach ( $chunks as $subscriber_id_chunk ) {

				$sql = "SELECT IF(b.meta_value IS NULL,0,(-b.meta_value*3600+$timeoffset))+$timestamps AS timestamps FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.ID = b.subscriber_id AND b.meta_key = 'timeoffset' WHERE a.ID IN (" . implode( ',', $subscriber_id_chunk ) . ') ORDER BY a.ID ASC';

				$result = $wpdb->get_col( $sql );

				$return = array_merge( $return, $result );

			}
		} else {
			return array();
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $subscriber_ids
	 * @param unknown $new_status
	 * @param unknown $silent         (optional)
	 * @return unknown
	 */
	public function change_status( $subscriber_ids, $new_status, $silent = false ) {

		global $wpdb;

		$subscriber_ids = ! is_array( $subscriber_ids ) ? array( (int) $subscriber_ids ) : array_filter( $subscriber_ids, 'is_numeric' );

		$count = 0;

		foreach ( $subscriber_ids as $subscriber_id ) {

			$subscriber = $this->get( $subscriber_id );

			if ( ! is_numeric( $new_status ) ) {
				$new_status = $this->get_status_by_name( $new_status );
			}

			if ( $subscriber->status == $new_status ) {
				$count++;
				continue;
			}

			$old_status = $subscriber->status;

			if ( false !== $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_subscribers SET status = %d, updated = %d WHERE ID = %d", $new_status, time(), $subscriber->ID ) ) ) {
				if ( ! $silent ) {
					do_action( 'mailster_subscriber_change_status', $new_status, $old_status, $subscriber );
				}

				if ( mailster_option( 'list_based_opt_in' ) ) {
					if ( 1 == $new_status ) {
						mailster( 'lists' )->confirm_subscribers( null, $subscriber_id );
					} elseif ( 0 == $new_status ) {
						mailster( 'lists' )->unconfirm_subscribers( null, $subscriber_id );
					}
				}

				$count++;
				continue;
			}
		}

		return $count;

	}


	/**
	 *
	 *
	 * @param unknown $email
	 * @return unknown
	 */
	private function hash( $email ) {

		$org_email = $email;

		for ( $i = 0; $i < 10; $i++ ) {
			$email = sha1( $email );
		}

		$hash = md5( $email . mailster_option( 'ID', '' ) );
		return apply_filters( 'mailster_subscriber_hash', $hash, $org_email );

	}


}
