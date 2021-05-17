<?php

class MailsterCampaigns {

	private $defaultTemplate = 'mailster';
	private $template;
	private $templatefile;
	private $templateobj;

	private $post_changed = array();

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_status' ) );

		if ( $hooks = get_option( 'mailster_hooks', false ) ) {

			foreach ( (array) $hooks as $campaign_id => $hook ) {
				if ( $hook ) {
					add_action( $hook, array( &$this, 'autoresponder_hook_' . $campaign_id ), 10, 5 );
				}
			}
		}

	}


	public function init() {

		add_action( 'transition_post_status', array( &$this, 'maybe_queue_post_changed' ), 10, 3 );

		add_action( 'mailster_finish_campaign', array( &$this, 'remove_revisions' ) );

		add_action( 'mailster_auto_post_thumbnail', array( &$this, 'get_post_thumbnail' ), 10, 1 );

		add_action( 'admin_menu', array( &$this, 'remove_meta_boxs' ) );
		add_action( 'admin_menu', array( &$this, 'autoresponder_menu' ), 20 );
		add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

		add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post_data' ), 1, 2 );
		add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

		add_action( 'before_delete_post', array( &$this, 'maybe_cleanup_after_delete' ) );

		add_filter( 'pre_post_content', array( &$this, 'remove_kses' ) );

		add_filter( 'heartbeat_received', array( &$this, 'heartbeat' ), 9, 2 );

		add_filter( 'admin_post_thumbnail_html', array( &$this, 'add_post_thumbnail_link' ), 10, 2 );
		add_filter( 'admin_post_thumbnail_size', array( &$this, 'admin_post_thumbnail_size' ), 10, 3 );

		add_action( 'wp_loaded', array( &$this, 'edit_hook' ) );
		add_action( 'get_the_excerpt', array( &$this, 'get_the_excerpt' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'assets' ) );
		add_filter( 'update_post_metadata', array( &$this, 'prevent_edit_lock' ), 10, 5 );

		add_filter( 'mailster_campaign_action', array( &$this, 'trigger_campaign_action' ), 10, 2 );

	}


	public function prevent_edit_lock( $bool, $object_id, $meta_key, $meta_value, $prev_value ) {

		if ( is_null( $bool ) && '_edit_lock' == $meta_key ) {
			$post = get_post( $object_id );
			if ( 'newsletter' == $post->post_type && in_array( $post->post_status, array( 'finished', 'active' ) ) ) {
				delete_post_meta( $object_id, '_edit_lock' );
				return false;
			}
		}
		return $bool;
	}


	/**
	 *
	 *
	 * @return unknown
	 * @param unknown $func
	 * @param unknown $org_args
	 */
	public function __call( $func, $org_args ) {

		if ( substr( $func, 0, 19 ) == 'autoresponder_hook_' ) {

			$campaign_id = (int) substr( $func, 19 );

			$subscribers = isset( $org_args[0] ) && $org_args[0] != '' ? $org_args[0] : null;
			$args        = isset( $org_args[1] ) && ! empty( $org_args[1] ) ? (array) $org_args[1] : null;

			$this->autoresponder_hook( $campaign_id, $subscribers, $args );

		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_ids (optional)
	 * @param unknown $args           (optional)
	 */
	public function autoresponder_hook( $campaign_id, $subscriber_ids = null, $args = array() ) {

		$meta = $this->meta( $campaign_id );

		if ( ! $meta['active'] || $meta['autoresponder']['action'] != 'mailster_autoresponder_hook' ) {
			return;
		}

		// prevent if empty or not null
		if ( empty( $subscriber_ids ) && ! is_null( $subscriber_ids ) ) {
			return;
		}

		$query_args = array(
			'lists'        => $meta['ignore_lists'] ? false : $meta['lists'],
			'conditions'   => $meta['list_conditions'],
			// 'queue__not_in' => $campaign_id,
			'sent__not_in' => isset( $meta['autoresponder']['once'] ) && $meta['autoresponder']['once'] ? $campaign_id : false,
			'include'      => $subscriber_ids,
		);

		$query_args = apply_filters( 'mailster_autoresponder_hook_args', $query_args, $campaign_id, $subscriber_ids, $args );
		$query_args = apply_filters( 'mailster_autoresponder_hook_args_' . current_filter(), $query_args, $campaign_id, $subscriber_ids, $args );

		$query_args['return_ids'] = true;

		$subscribers = mailster( 'subscribers' )->query( $query_args, $campaign_id );

		$timestamp = strtotime( '+ ' . $meta['autoresponder']['amount'] . ' ' . $meta['autoresponder']['unit'] );

		$priority      = $meta['autoresponder']['priority'];
		$clear         = false;
		$ignore_status = false;
		$reset         = true;
		$options       = isset( $meta['autoresponder']['multiple'] ) ? uniqid() : false;
		$tags          = $args;

		mailster( 'queue' )->bulk_add( $campaign_id, $subscribers, $timestamp, $priority, $clear, $ignore_status, $reset, $options, $tags );

		// handle instant delivery
		if ( $timestamp - time() <= 0 ) {
			wp_schedule_single_event( $timestamp, 'mailster_cron_worker', array( $campaign_id ) );
		}
	}


	public function register_post_type() {

		$is_autoresponder = is_admin() && isset( $_GET['post_status'] ) && $_GET['post_status'] == 'autoresponder';
		$single           = $is_autoresponder ? esc_html__( 'Autoresponder', 'mailster' ) : esc_html__( 'Campaign', 'mailster' );
		$plural           = $is_autoresponder ? esc_html__( 'Autoresponders', 'mailster' ) : esc_html__( 'Campaigns', 'mailster' );

		$color = '#a0a5aa';
		if ( is_admin() && ( isset( $_GET['post_type'] ) && 'newsletter' == $_GET['post_type'] || isset( $_GET['page'] ) && 'mailster_dashboard' == $_GET['page'] ) ) {
			$color = '#ffffff';
			// $menu_icon = '<svg fill="#fff" aria-hidden="true" focusable="false" data-prefix="fas" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" transform="scale(0.9)"><path d="M176 216h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16H176c-8.84 0-16 7.16-16 16v16c0 8.84 7.16 16 16 16zm-16 80c0 8.84 7.16 16 16 16h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16H176c-8.84 0-16 7.16-16 16v16zm96 121.13c-16.42 0-32.84-5.06-46.86-15.19L0 250.86V464c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V250.86L302.86 401.94c-14.02 10.12-30.44 15.19-46.86 15.19zm237.61-254.18c-8.85-6.94-17.24-13.47-29.61-22.81V96c0-26.51-21.49-48-48-48h-77.55c-3.04-2.2-5.87-4.26-9.04-6.56C312.6 29.17 279.2-.35 256 0c-23.2-.35-56.59 29.17-73.41 41.44-3.17 2.3-6 4.36-9.04 6.56H96c-26.51 0-48 21.49-48 48v44.14c-12.37 9.33-20.76 15.87-29.61 22.81A47.995 47.995 0 0 0 0 200.72v10.65l96 69.35V96h320v184.72l96-69.35v-10.65c0-14.74-6.78-28.67-18.39-37.77z"></path></svg>';
		}
		$menu_icon = '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" transform="scale(0.9)"><path fill="' . $color . '" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z" transform="scale(0.9)"></path></svg>';

		register_post_type(
			'newsletter',
			array(

				'labels'               => array(
					'name'                  => $plural,
					'singular_name'         => $single,
					'add_new'               => sprintf( esc_html__( 'New %s', 'mailster' ), $single ),
					'add_new_item'          => esc_html__( 'Create A New Campaign', 'mailster' ),
					'edit_item'             => sprintf( esc_html__( 'Edit %s', 'mailster' ), $single ),
					'new_item'              => sprintf( esc_html__( 'New %s', 'mailster' ), $single ),
					'all_items'             => esc_html__( 'All Campaigns', 'mailster' ),
					'view_item'             => esc_html__( 'View Newsletter', 'mailster' ),
					'search_items'          => sprintf( esc_html__( 'Search %s', 'mailster' ), $plural ),
					'not_found'             => sprintf( esc_html__( 'No %s found', 'mailster' ), $single ),
					'not_found_in_trash'    => sprintf( esc_html__( 'No %s found in Trash', 'mailster' ), $single ),
					'parent_item_colon'     => '',
					'menu_name'             => esc_html__( 'Newsletter', 'mailster' ),
					'filter_items_list'     => esc_html__( 'Filter Newsletter list', 'mailster' ),
					'items_list_navigation' => esc_html__( 'Newsletter list navigation', 'mailster' ),
					'items_list'            => esc_html__( 'Newsletter list', 'mailster' ),
				),

				'public'               => true,
				'can_export'           => true,
				'menu_icon'            => 'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
				'show_ui'              => true,
				'show_in_nav_menus'    => false,
				'show_in_menu'         => true,
				'show_in_admin_bar'    => true,
				'show_in_rest'         => false,
				'exclude_from_search'  => true,
				'capability_type'      => 'newsletter',
				'map_meta_cap'         => true,
				'has_archive'          => mailster_option( 'hasarchive', false ) ? mailster_option( 'archive_slug', false ) : false,
				'hierarchical'         => $is_autoresponder,
				'rewrite'              => array(
					'with_front' => false,
					'slug'       => mailster_option( 'slug', 'newsletter' ),
				),
				'supports'             => array(
					'title',
					'thumbnail',
					'revisions',
					'author',
				),
				'register_meta_box_cb' => array( &$this, 'meta_boxes' ),

			)
		);

	}


	public function register_post_status() {

		register_post_status(
			'paused',
			array(
				'label'       => esc_html__( 'Paused', 'mailster' ),
				'public'      => true,
				'label_count' => _n_noop( esc_html__( 'Paused', 'mailster' ) . ' <span class="count">(%s)</span>', esc_html__( 'Paused', 'mailster' ) . ' <span class="count">(%s)</span>' ),
			)
		);

		register_post_status(
			'active',
			array(
				'label'       => esc_html__( 'Active', 'mailster' ),
				'public'      => true,
				'label_count' => _n_noop( esc_html__( 'Active', 'mailster' ) . ' <span class="count">(%s)</span>', esc_html__( 'Active', 'mailster' ) . ' <span class="count">(%s)</span>' ),
			)
		);

		register_post_status(
			'queued',
			array(
				'label'       => esc_html__( 'Queued', 'mailster' ),
				'public'      => true,
				'label_count' => _n_noop( esc_html__( 'Queued', 'mailster' ) . ' <span class="count">(%s)</span>', esc_html__( 'Queued', 'mailster' ) . ' <span class="count">(%s)</span>' ),
			)
		);

		register_post_status(
			'finished',
			array(
				'label'       => esc_html__( 'Finished', 'mailster' ),
				'public'      => true,
				'label_count' => _n_noop( esc_html__( 'Finished', 'mailster' ) . ' <span class="count">(%s)</span>', esc_html__( 'Finished', 'mailster' ) . ' <span class="count">(%s)</span>' ),
			)
		);

		register_post_status(
			'autoresponder',
			array(
				'label'                  => esc_html__( 'Autoresponder', 'mailster' ),
				'public'                 => ! is_admin(),
				'exclude_from_search'    => true,
				'show_in_admin_all_list' => false,
				'label_count'            => _n_noop( esc_html__( 'Autoresponder', 'mailster' ) . ' <span class="count">(%s)</span>', esc_html__( 'Autoresponders', 'mailster' ) . ' <span class="count">(%s)</span>' ),
			)
		);

	}


	public function meta_boxes() {

		global $post;
		add_meta_box( 'mailster_details', esc_html__( 'Details', 'mailster' ), array( &$this, 'newsletter_details' ), 'newsletter', 'normal', 'high' );
		add_meta_box( 'mailster_template', ( ! in_array( $post->post_status, array( 'active', 'finished' ) ) && ! isset( $_GET['showstats'] ) ) ? esc_html__( 'Template', 'mailster' ) : esc_html__( 'Clickmap', 'mailster' ), array( &$this, 'newsletter_template' ), 'newsletter', 'normal', 'high' );
		add_meta_box( 'mailster_submitdiv', esc_html__( 'Save', 'mailster' ), array( &$this, 'newsletter_submit' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_delivery', esc_html__( 'Delivery', 'mailster' ), array( &$this, 'newsletter_delivery' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_receivers', esc_html__( 'Receivers', 'mailster' ), array( &$this, 'newsletter_receivers' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_options', esc_html__( 'Options', 'mailster' ), array( &$this, 'newsletter_options' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_attachments', esc_html__( 'Attachment', 'mailster' ), array( &$this, 'newsletter_attachment' ), 'newsletter', 'side', 'low' );

	}


	public function remove_meta_boxs() {
		remove_meta_box( 'submitdiv', 'newsletter', 'core' );
	}


	public function display_post_states( $post_states, $post ) {

		if ( $post->post_type == 'newsletter' ) {
			$post_states = array();
			if ( ! $this->meta( $post->ID, 'webversion' ) ) {
				$post_states['mailster_no_webversion'] = esc_html__( 'Private', 'mailster' );
			}
		}

		return $post_states;

	}

	public function autoresponder_menu() {

		global $submenu;

		if ( current_user_can( 'edit_newsletters' ) ) {
			$submenu['edit.php?post_type=newsletter'][] = array(
				esc_html__( 'Autoresponder', 'mailster' ),
				'mailster_edit_autoresponders',
				'edit.php?post_status=autoresponder&post_type=newsletter',
			);
		}

	}


	public function newsletter_details() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/details.php';
	}


	public function newsletter_template() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/template.php';
	}


	public function newsletter_delivery() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/delivery.php';
	}


	public function newsletter_receivers() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/receivers.php';
	}


	public function newsletter_options() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/options.php';
	}


	public function newsletter_attachment() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/attachment.php';
	}


	/**
	 *
	 *
	 * @param unknown $post
	 */
	public function newsletter_submit( $post ) {
		global $action;
		$post_type        = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish      = current_user_can( $post_type_object->cap->publish_posts );
		include MAILSTER_DIR . 'views/newsletter/submit.php';
	}


	public function get_the_excerpt( $excerpt ) {
		if ( isset( $_GET['post_type'] ) && 'newsletter' == $_GET['post_type'] ) {
			return '';
		}
		return $excerpt;
	}


	public function edit_hook() {

		if ( isset( $_GET['post_type'] ) && 'newsletter' == $_GET['post_type'] && ! isset( $_GET['page'] ) ) {

			// duplicate campaign
			if ( isset( $_GET['duplicate'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_duplicate_nonce' ) ) {
				$id   = (int) $_GET['duplicate'];
				$post = get_post( $id );
				if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() != $post->post_author ) && ! current_user_can( 'duplicate_others_newsletters' ) ) {
					wp_die( esc_html__( 'You are not allowed to duplicate this campaign.', 'mailster' ) );
				} else {
					if ( $new_id = $this->duplicate( $id ) ) {
						$id = $new_id;
					}
				}

				// pause campaign
			} elseif ( isset( $_GET['pause'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_pause_nonce' ) ) {
				$id = (int) $_GET['pause'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to pause this campaign.', 'mailster' ) );
				} else {
					$this->pause( $id );
				}

				// continue/start campaign
			} elseif ( isset( $_GET['start'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_start_nonce' ) ) {
				$id = (int) $_GET['start'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to start this campaign.', 'mailster' ) );
				} else {
					$this->start( $id );
				}

				// resume campaign
			} elseif ( isset( $_GET['resume'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_start_nonce' ) ) {
				$id = (int) $_GET['resume'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to resume this campaign.', 'mailster' ) );
				} else {
					$this->resume( $id );
				}

				// finish campaign
			} elseif ( isset( $_GET['finish'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_finish_nonce' ) ) {
				$id = (int) $_GET['finish'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to finish this campaign.', 'mailster' ) );
				} else {
					$this->finish( $id );
				}

				// activate autoresponder
			} elseif ( isset( $_GET['activate'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_activate_nonce' ) ) {
				$id = (int) $_GET['activate'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to activate this campaign.', 'mailster' ) );
				} else {
					$this->activate( $id );
				}

				// deactivate autoresponder
			} elseif ( isset( $_GET['deactivate'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_deactivate_nonce' ) ) {
				$id = (int) $_GET['deactivate'];
				if ( ! current_user_can( 'publish_newsletters', $id ) ) {
					wp_die( esc_html__( 'You are not allowed to deactivate this campaign.', 'mailster' ) );
				} else {
					$this->deactivate( $id );
				}
			}

			if ( isset( $id ) && ! ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) {
				$status = ( isset( $_GET['post_status'] ) ) ? '&post_status=' . $_GET['post_status'] : '';
				( isset( $_GET['edit'] ) )
					? wp_redirect( 'post.php?post=' . $id . '&action=edit' )
					: wp_redirect( 'edit.php?post_type=newsletter' . $status );
				exit;
			}

			add_filter( 'the_excerpt', '__return_false' );
			add_filter( 'post_row_actions', array( &$this, 'quick_edit_btns' ), 10, 2 );
			add_filter( 'page_row_actions', array( &$this, 'quick_edit_btns' ), 10, 2 );
			add_filter( 'bulk_actions-edit-newsletter', array( &$this, 'bulk_actions' ) );
			add_filter( 'manage_edit-newsletter_columns', array( &$this, 'columns' ) );
			add_filter( 'manage_newsletter_posts_custom_column', array( &$this, 'columns_content' ) );
			add_filter( 'manage_edit-newsletter_sortable_columns', array( &$this, 'columns_sortable' ) );
			add_filter( 'pre_get_posts', array( &$this, 'pre_get_posts' ) );

			$this->handle_bulk_actions();

		}

	}



	/**
	 *
	 *
	 * @return unknown
	 */
	public function notice() {

		global $post;

		switch ( $post->post_status ) {
			case 'finished':
				$timeformat = mailster( 'helper' )->timeformat();
				$timeoffset = mailster( 'helper' )->gmt_offset( true );
				$msg        = sprintf( esc_html__( 'This Campaign was sent on %s', 'mailster' ), '<span class="nowrap">' . date( $timeformat, $this->meta( $post->ID, 'finished' ) + $timeoffset ) . '</span>' );
				break;
			case 'queued':
				$msg = esc_html__( 'This Campaign is currently in the queue', 'mailster' );
				break;
			case 'active':
				$msg = esc_html__( 'This Campaign is currently progressing', 'mailster' );
				break;
			case 'paused':
				$msg = esc_html__( 'This Campaign has been paused', 'mailster' );
				break;
		}

		if ( ! isset( $msg ) ) {
			return false;
		}

		echo '<div class="updated inline"><p><strong>' . $msg . '</strong></p></div>';

	}


	/**
	 *
	 *
	 * @param unknown $messages
	 * @return unknown
	 */
	public function updated_messages( $messages ) {

		global $post_id, $post;

		if ( $post->post_type != 'newsletter' ) {
			return $messages;
		}

		$messages[] = 'No subject!';

		$messages['newsletter'] = array(
			0  => '',
			1  => sprintf( esc_html__( 'Campaign updated. %s', 'mailster' ), '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html__( 'View Newsletter', 'mailster' ) . '</a>' ),
			2  => sprintf( esc_html__( 'Template changed. %1$s', 'mailster' ), '<a href="' . remove_query_arg( 'message', mailster_get_referer() ) . '">' . esc_html__( 'Go back', 'mailster' ) . '</a>' ),
			3  => esc_html__( 'Template saved', 'mailster' ),
			4  => esc_html__( 'Campaign updated.', 'mailster' ),
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Campaign restored to revision from %s', 'mailster' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( esc_html__( 'Campaign published. %s', 'mailster' ), '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html__( 'View Newsletter', 'mailster' ) . '</a>' ),
			7  => esc_html__( 'Campaign saved.', 'mailster' ),
			8  => sprintf( esc_html__( 'Campaign submitted. %s', 'mailster' ), '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Preview Newsletter', 'mailster' ) . '</a>' ),
			9  => esc_html__( 'Campaign scheduled.', 'mailster' ),
			10 => esc_html__( 'Campaign draft updated.', 'mailster' ),
		);

		return $messages;
	}


	/**
	 *
	 *
	 * @param unknown $columns
	 * @return unknown
	 */
	public function columns( $columns ) {

		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'title'   => esc_html__( 'Name', 'mailster' ),
			'status'  => esc_html__( 'Status', 'mailster' ),
			'total'   => esc_html__( 'Total', 'mailster' ),
			'open'    => esc_html__( 'Open', 'mailster' ),
			'click'   => esc_html__( 'Clicks', 'mailster' ),
			'unsubs'  => esc_html__( 'Unsubscribes', 'mailster' ),
			'bounces' => esc_html__( 'Bounces', 'mailster' ),
			'date'    => esc_html__( 'Date', 'mailster' ),
		);
		return $columns;
	}


	/**
	 *
	 *
	 * @param unknown $columns
	 * @return unknown
	 */
	public function columns_sortable( $columns ) {

		$columns['status'] = 'status';

		return $columns;

	}


	/**
	 *
	 *
	 * @param unknown $query
	 */
	public function pre_get_posts( $query ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( 'newsletter' == get_query_var( 'post_type' ) ) {

			switch ( get_query_var( 'orderby' ) ) {
				case 'status':
					$query->set( 'meta_key', '_mailster_timestamp' );
					// order by post status is not supported by WordPress so we sort by date and fix it later with 'allow_order_by_status'
					add_filter( 'posts_orderby', array( &$this, 'allow_order_by_status' ) );
					$query->set(
						'orderby',
						array(
							'post_date'      => $query->get( 'order' ),
							'meta_value_num' => $query->get( 'order' ),
						)
					);
					break;
			}
		}

	}


	public function allow_order_by_status( $orderby ) {

		return str_replace( 'posts.post_date', 'posts.post_status', $orderby );

	}


	/**
	 *
	 *
	 * @param unknown $column
	 * @return unknown
	 */
	public function get_columns_content( $column ) {

		ob_start();

		$this->columns_content( $column );

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}


	/**
	 *
	 *
	 * @param unknown $column
	 */
	public function columns_content( $column ) {

		global $post, $wp_post_statuses;

		$now        = time();
		$is_ajax    = defined( 'DOING_AJAX' ) && DOING_AJAX;
		$timeformat = mailster( 'helper' )->timeformat();

		if ( ! $is_ajax && $column != 'status' && wp_script_is( 'heartbeat', 'registered' ) ) {
			echo '&ndash;';
			return;
		}

		$error = ini_get( 'error_reporting' );
		error_reporting( E_ERROR );

		$meta = $this->meta( $post->ID );

		switch ( $column ) {

			case 'status':
				$timestamp  = isset( $meta['timestamp'] ) ? $meta['timestamp'] : $now;
				$timeoffset = mailster( 'helper' )->gmt_offset( true );
				$actions    = array();
				$is_active  = $meta['active'];
				$active     = $is_active ? 'active' : 'inactive';

				if ( ! in_array( $post->post_status, array( 'pending', 'auto-draft' ) ) ) {

					// status is finished if this isset, even if the campaign is running;
					$status = isset( $campaign['finished'] ) ? 'finished' : $post->post_status;

					switch ( $status ) {
						case 'paused':
							echo '<span class="mailster-icon paused"></span> ';
							esc_html_e( 'Paused', 'mailster' );
							if ( $totals = $this->get_totals( $post->ID ) ) {
								if ( $sent = $this->get_sent( $post->ID ) ) {
									$p  = round( $sent / $totals * 100 );
									$pg = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
									echo "<br><div class='campaign-progress'><span class='bar' style='width:$p%'><span>&nbsp;$pg</span></span><span>&nbsp;$pg</span><var>$p%</var></div>";
								}
							} elseif ( is_null( $totals ) ) {
							} else {
								echo '<br><span class="mailster-icon no-receiver"></span> ' . esc_html__( 'no receivers!', 'mailster' );
							}
							break;
						case 'active':
							if ( $totals = $this->get_totals( $post->ID ) ) {
								$sent = $this->get_sent( $post->ID );
								echo '<span class="mailster-icon progressing"></span> ' . ( $sent == $totals ? esc_html__( 'completing job', 'mailster' ) : esc_html__( 'progressing', 'mailster' ) ) . '&hellip;' . ( $meta['timezone'] ? ' <span class="timezonebased"  title="' . esc_html__( 'This campaign is based on subscribers timezone and probably will take up to 24 hours', 'mailster' ) . '">24h</span>' : '' );
								$p  = $totals ? round( $sent / $totals * 100 ) : 0;
								$pg = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
								echo "<br><div class='campaign-progress'><span class='bar' style='width:$p%'><span>&nbsp;$pg</span></span><span>&nbsp;$pg</span><var>$p%</var></div>";
							} elseif ( is_null( $totals ) ) {
							} else {
								echo '<span class="mailster-icon no-receiver"></span> ' . esc_html__( 'no receivers!', 'mailster' );
							}
							echo '<div class="campaign-status"></div>';
							break;
						case 'queued':
							echo '<span class="mailster-icon queued"></span> ';
							if ( $meta['timezone'] && $timestamp - $now < 86400 ) :
								$sub       = $this->get_unsent_subscribers( $post->ID, array( 1 ), true );
								$timestamp = min( mailster( 'subscribers' )->get_timeoffset_timestamps( $sub, $timestamp ) );
								endif;
							printf( esc_html__( 'starts in %s', 'mailster' ), ( $timestamp - $now > 60 ) ? human_time_diff( $timestamp ) : esc_html__( 'less than a minute', 'mailster' ) );
							echo $meta['timezone'] ? ' <span class="timezonebased"  title="' . esc_attr__( 'This campaign is based on subscribers timezone and probably will take up to 24 hours', 'mailster' ) . '">24h</span>' : '';
							echo "<br><span class='nonessential'>(" . date( $timeformat, $timestamp + $timeoffset ) . ')</span>';
							echo '<div class="campaign-status"></div>';
							break;
						case 'finished':
							echo '<span class="mailster-icon finished"></span> ' . esc_html__( 'Finished', 'mailster' );
							echo '<br><span class="nonessential">(' . date( $timeformat, $meta['finished'] + $timeoffset ) . ')</span>';
							break;
						case 'draft':
							echo '<span class="mailster-icon draft"></span> ' . $wp_post_statuses['draft']->label;
							break;
						case 'trash':
							echo $wp_post_statuses['trash']->label;
							break;
						case 'autoresponder':
							include MAILSTER_DIR . 'includes/autoresponder.php';

							$autoresponder = $meta['autoresponder'];

							echo '<span class="mailster-icon ' . $active . '"></span> ' . ( $is_active ? esc_html__( 'active', 'mailster' ) : esc_html__( 'inactive', 'mailster' ) );
							echo '<br>';

							echo '<span class="autoresponder-' . $active . '">';

							$time_frame_names = array(
								'minute' => esc_html__( 'minute(s)', 'mailster' ),
								'hour'   => esc_html__( 'hour(s)', 'mailster' ),
								'day'    => esc_html__( 'day(s)', 'mailster' ),
								'week'   => esc_html__( 'week(s)', 'mailster' ),
								'month'  => esc_html__( 'month(s)', 'mailster' ),
								'year'   => esc_html__( 'year(s)', 'mailster' ),
							);

							if ( 'mailster_autoresponder_timebased' == $autoresponder['action'] ) {

								$pts = mailster( 'helper' )->get_post_types( true, 'objects' );

								if ( $meta['timestamp'] && $meta['timestamp'] - $now < 0 ) {
									mailster( 'queue' )->autoresponder_timebased( $post->ID );
								}

								printf(
									esc_html__( 'send every %1$s %2$s', 'mailster' ),
									'<strong>' . $autoresponder['interval'] . '</strong>',
									'<strong>' . $time_frame_names[ $autoresponder['time_frame'] ] . '</strong>'
								);

								if ( $meta['timestamp'] ) {
									echo '<br>';
									printf(
										esc_html__( 'next campaign in %s', 'mailster' ),
										'<strong title="' . date( $timeformat, $meta['timestamp'] + $timeoffset ) . '">' . human_time_diff( $meta['timestamp'] ) . '</strong>'
									);
									echo ' &ndash; ' . sprintf( '#%s', '<strong title="' . sprintf( esc_html__( 'Next issue: %s', 'mailster' ), '#' . $autoresponder['issue'] ) . '">' . $autoresponder['issue'] . '</strong>' );
									if ( isset( $autoresponder['since'] ) && $autoresponder['since'] ) {
										echo '<br>' . esc_html__( 'only if new content is available.', 'mailster' );
									}
									if ( isset( $autoresponder['time_conditions'] ) ) {
										if ( $posts_required = max( 0, ( $autoresponder['time_post_count'] - $autoresponder['post_count_status'] ) ) ) {
											if ( 'rss' == $autoresponder['time_post_type'] ) {
												echo '<br>' . sprintf( esc_html__( 'requires %1$s more %2$s', 'mailster' ), ' <strong>' . $posts_required . '</strong>', ' <strong>' . esc_html__( 'RSS Feed', 'mailster' ) . '</strong>' );
											} else {
												echo '<br>' . sprintf( esc_html__( 'requires %1$s more %2$s', 'mailster' ), ' <strong>' . $posts_required . '</strong>', ' <strong>' . $pts[ $autoresponder['time_post_type'] ]->labels->name . '</strong>' );
											}
										}
									}
								}

								if ( isset( $autoresponder['endtimestamp'] ) ) {
									echo '<br>';
									printf(
										esc_html__( 'until %s', 'mailster' ),
										' <strong>' . date( $timeformat, $autoresponder['endtimestamp'] + $timeoffset ) . '</strong>'
									);
								}

								if ( count( $autoresponder['weekdays'] ) < 7 ) {

									global $wp_locale;
									$start_at = get_option( 'start_of_week' );
									$days     = array();
									for ( $i = $start_at; $i < 7 + $start_at; $i++ ) {
										$j = $i;
										if ( ! isset( $wp_locale->weekday[ $j ] ) ) {
											$j = $j - 7;
										}

										if ( in_array( $j, $autoresponder['weekdays'] ) ) {
											$days[] = '<span title="' . $wp_locale->weekday[ $j ] . '">' . substr( $wp_locale->weekday[ $j ], 0, 2 ) . '</span>';
										}
									}

									echo '<br>';
									printf(
										esc_html_x( 'only on %s', 'only on [weekdays]', 'mailster' ),
										' <strong>' . implode( ', ', $days ) . '</strong>'
									);
								}
							} elseif ( 'mailster_autoresponder_usertime' == $autoresponder['action'] ) {

								$datefields = mailster()->get_custom_date_fields();

								if ( $autoresponder['userexactdate'] ) :

									printf(
										esc_html__( 'send %1$s %2$s %3$s', 'mailster' ),
										'<strong>' . $autoresponder['amount'] . '</strong>',
										'<strong>' . $time_frame_names[ $autoresponder['unit'] ] . '</strong>',
										( $autoresponder['before_after'] > 0 ? esc_html__( 'after', 'mailster' ) : esc_html__( 'before', 'mailster' ) )
									);

									echo ' ' . sprintf( esc_html__( 'the users %1$s value', 'mailster' ), ' <strong>' . ( isset( $datefields[ $autoresponder['uservalue'] ] ) ? $datefields[ $autoresponder['uservalue'] ]['name'] : $autoresponder['uservalue'] ) . '</strong>' );
								else :
									printf(
										esc_html__( 'send every %1$s %2$s', 'mailster' ),
										'<strong>' . $autoresponder['useramount'] . '</strong>',
										'<strong>' . $time_frame_names[ $autoresponder['userunit'] ] . '</strong>'
									);
									echo ' ' . sprintf( esc_html__( 'based on the users %1$s value', 'mailster' ), ' <strong>' . ( isset( $datefields[ $autoresponder['uservalue'] ] ) ? $datefields[ $autoresponder['uservalue'] ]['name'] : $autoresponder['uservalue'] ) . '</strong>' );

								endif;

							} elseif ( 'mailster_autoresponder_followup' == $autoresponder['action'] ) {

								if ( $campaign = $this->get( $post->post_parent ) ) {
									$types = array(
										1 => esc_html__( 'has been sent', 'mailster' ),
										2 => esc_html__( 'has been opened', 'mailster' ),
										3 => esc_html__( 'has been clicked', 'mailster' ),
									);
									printf(
										esc_html__( 'send %1$s %2$s %3$s', 'mailster' ),
										( $autoresponder['amount'] ? '<strong>' . $autoresponder['amount'] . '</strong> ' . $mailster_autoresponder_info['units'][ $autoresponder['unit'] ] : esc_html__( 'immediately', 'mailster' ) ),
										esc_html__( 'after', 'mailster' ),
										' <strong><a href="post.php?post=' . $campaign->ID . '&action=edit">' . $campaign->post_title . '</a></strong> ' . $types[ $autoresponder['followup_action'] ]
									);

								} else {
									echo '<br><span class="mailster-icon warning"></span> ' . esc_html__( 'Campaign does not exist', 'mailster' );
								}
							} else {

								printf(
									esc_html__( 'send %1$s %2$s %3$s', 'mailster' ),
									( $autoresponder['amount'] ? '<strong>' . $autoresponder['amount'] . '</strong> ' . $mailster_autoresponder_info['units'][ $autoresponder['unit'] ] : esc_html__( 'immediately', 'mailster' ) ),
									esc_html__( 'after', 'mailster' ),
									' <strong>' . $mailster_autoresponder_info['actions'][ $autoresponder['action'] ]['label'] . '</strong>'
								);

							}

							echo '</span>';

							if ( ( current_user_can( 'mailster_edit_autoresponders' ) && ( get_current_user_id() == $post->post_author || current_user_can( 'mailster_edit_others_autoresponders' ) ) ) ) {
								if ( $active != 'active' ) {
									$actions['activate'] = '<a class="start live-action" href="?post_type=newsletter&activate=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_activate_nonce' ) . '" title="' . esc_attr__( 'activate', 'mailster' ) . '">' . esc_html__( 'activate', 'mailster' ) . '</a>&nbsp;';
								} else {
									$actions['deactivate'] = '<a class="start live-action" href="?post_type=newsletter&deactivate=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_deactivate_nonce' ) . '" title="' . esc_attr__( 'deactivate', 'mailster' ) . '">' . esc_html__( 'deactivate', 'mailster' ) . '</a>&nbsp;';
								}
							}

							break;
					}
				} else {
					$status = get_post_status_object( $post->post_status );
					echo $status->label;
				}

				echo '<div class="campaign-conditions ' . $active . '">';

				if ( ! $meta['ignore_lists'] ) {

					$lists = $this->get_lists( $post->ID );

					if ( ! empty( $lists ) ) {
						echo esc_html__( 'assigned lists', 'mailster' ) . ':<br>';
						foreach ( $lists as $i => $list ) {
							echo '<strong class="nowrap"><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
							if ( $i + 1 < count( $lists ) ) {
								echo ', ';
							}
						}
					} else {
						if ( ! in_array( $post->post_status, array( 'finished' ) ) ) {
							echo '<br><span class="mailster-icon warning"></span> ' . esc_html__( 'no lists selected', 'mailster' );
						}
					}
					echo '<br>';
				}

				if ( $meta['list_conditions'] ) {

					echo esc_html__( 'only if', 'mailster' ) . '<br>';

					mailster( 'conditions' )->render( $meta['list_conditions'] );
				}
				echo '</div>';

				if ( ( current_user_can( 'publish_newsletters' ) && get_current_user_id() == $post->post_author ) || current_user_can( 'edit_others_newsletters' ) ) {
					if ( $post->post_status == 'queued' ) {
						$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_start_nonce' ) . '" title="' . esc_attr__( 'Start Campaign now', 'mailster' ) . '">' . esc_html__( 'Start now', 'mailster' ) . '</a>';
					}
					if ( in_array( $post->post_status, array( 'active', 'queued' ) ) && $status != 'finished' ) {
						$actions['pause'] = '<a class="pause live-action" href="?post_type=newsletter&pause=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_pause_nonce' ) . '" title="' . esc_attr__( 'Pause Campaign', 'mailster' ) . '">' . esc_html__( 'Pause', 'mailster' ) . '</a>';
					} elseif ( $post->post_status == 'paused' && $totals ) {
						if ( ! empty( $meta['timestamp'] ) && $sent ) {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&resume=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_start_nonce' ) . '" title="' . esc_attr__( 'Resume Campaign', 'mailster' ) . '">' . esc_html__( 'Resume', 'mailster' ) . '</a>';
						} else {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_start_nonce' ) . '" title="' . esc_attr__( 'Start Campaign', 'mailster' ) . '">' . esc_html__( 'Start', 'mailster' ) . '</a>';
						}
					}
					if ( in_array( $post->post_status, array( 'active', 'paused' ) ) && $totals && $sent ) {
						$actions['finish'] = '<a class="finish live-action" href="?post_type=newsletter&finish=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . esc_attr( $_GET['post_status'] ) : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_finish_nonce' ) . '" title="' . esc_attr__( 'Finish Campaign', 'mailster' ) . '">' . esc_html__( 'Finish', 'mailster' ) . '</a>';
					}
				}
				if ( ! empty( $actions ) ) {
					echo '<div class="row-actions">';
					echo implode( ' | ', $actions );
					echo '</div>';
				}
				break;

			case 'total':
				if ( 'finished' == $post->post_status ) {
					echo number_format_i18n( $this->get_sent( $post->ID ) );
				} elseif ( 'autoresponder' == $post->post_status ) {
					echo number_format_i18n( $this->get_sent( $post->ID, true ) );
				} else {
					echo number_format_i18n( $this->get_totals( $post->ID ) );
				}

				$errors = $this->get_errors( $post->ID );
				if ( ! empty( $errors ) ) {
					echo '&nbsp;(<a href="edit.php?post_type=newsletter&page=mailster_subscribers&status=4" class="errors" title="' . sprintf( esc_html__( '%d emails have not been sent', 'mailster' ), $errors ) . '">+' . $errors . '</a>)';
				}

				break;

			case 'open':
				if ( ! $this->meta( $post->ID, 'track_opens' ) ) {
					echo '<span class="mailster-icon-lock" title="' . esc_attr__( 'Tracking is disabled for this campaign!', 'default' ) . '"></span>';
				} elseif ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					echo '<span class="s-opens">' . number_format_i18n( $this->get_opens( $post->ID ) ) . '</span>/<span class="tiny s-sent">' . number_format_i18n( $this->get_sent( $post->ID ) ) . '</span>';
					$rate = round( $this->get_open_rate( $post->ID ) * 100, 2 );
					echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				} else {
					echo '&ndash;';
				}
				break;

			case 'click':
				if ( ! $this->meta( $post->ID, 'track_clicks' ) ) {
					echo '<span class="mailster-icon-lock" title="' . esc_attr__( 'Tracking is disabled for this campaign!', 'default' ) . '"></span>';
				} elseif ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$clicks = $this->get_clicks( $post->ID );
					$rate   = round( $this->get_click_rate( $post->ID ) * 100, 2 );
					$rate_a = round( $this->get_adjusted_click_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $clicks );
					if ( $rate ) {
						echo "<br><span class='nonessential'>(<span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
						echo '' . $rate . '%';
						echo '</span>|';
						echo "<span title='" . sprintf( esc_attr__( '%s of opens', 'mailster' ), $rate_a . '%' ) . "'>";
						echo '' . $rate_a . '%';
						echo '</span>)</span>';
					} else {
						echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
						echo ' (' . $rate . '%)';
						echo '</span>';
					}
				} else {
					echo '&ndash;';
				}
				break;

			case 'unsubs':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$unsubscribes = $this->get_unsubscribes( $post->ID );
					$rate         = round( $this->get_unsubscribe_rate( $post->ID ) * 100, 2 );
					$rate_a       = round( $this->get_adjusted_unsubscribe_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $unsubscribes );
					if ( $rate ) {
						echo "<br><span class='nonessential'>(<span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
						echo '' . $rate . '%';
						echo '</span>|';
						echo "<span title='" . sprintf( esc_attr__( '%s of opens', 'mailster' ), $rate_a . '%' ) . "'>";
						echo '' . $rate_a . '%';
						echo '</span>)</span>';
					} else {
						echo "<br><span title='" . sprintf( esc_attr__( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
						echo ' (' . $rate . '%)';
						echo '</span>';
					}
				} else {
					echo '&ndash;';
				}
				break;

			case 'bounces':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$bounces = $this->get_bounces( $post->ID );
					$rate    = round( $this->get_bounce_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $bounces );
					echo "<br><span title='" . sprintf( esc_attr__( '%s of totals', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				} else {
					echo '&ndash;';
				}
				break;

		}
		error_reporting( $error );
	}


	/**
	 *
	 *
	 * @param unknown $actions
	 * @return unknown
	 */
	public function bulk_actions( $actions ) {

		unset( $actions['edit'] );

		$actions['duplicate'] = esc_html__( 'Duplicate', 'mailster' );
		if ( isset( $_GET['post_status'] ) && 'autoresponder' == $_GET['post_status'] ) {
			$actions['activate']   = esc_html__( 'Activate', 'mailster' );
			$actions['deactivate'] = esc_html__( 'Deactivate', 'mailster' );
		} else {
			$actions['start']  = esc_html__( 'Start', 'mailster' );
			$actions['pause']  = esc_html__( 'Pause', 'mailster' );
			$actions['resume'] = esc_html__( 'Resume', 'mailster' );
			$actions['finish'] = esc_html__( 'Finish', 'mailster' );
		}

		return $actions;
	}


	public function handle_bulk_actions() {

		if ( ! isset( $_GET['post'] ) || empty( $_GET['post'] ) ) {
			return;
		}

		$action = null;

		if ( isset( $_GET['action'] ) && -1 != $_GET['action'] ) {
			$action = $_GET['action'];
		}

		if ( isset( $_GET['action2'] ) && -1 != $_GET['action2'] ) {
			$action = $_GET['action2'];
		}

		$redirect        = add_query_arg( $_GET );
		$success_message = array();
		$error_message   = array();
		$message_postfix = '';
		$post_ids        = array_filter( $_GET['post'], 'is_numeric' );

		foreach ( $post_ids as $post_id ) :

			switch ( $action ) {

				case 'delete':
					if ( current_user_can( 'mailster_delete_subscribers' ) ) {

						$success = $this->remove( $subscriber_ids );
						if ( is_wp_error( $success ) ) {
							$error_message[] = sprintf( esc_html__( 'There was an error while deleting subscribers: %s', 'mailster' ), $success->get_error_message() );

						} elseif ( $success ) {
							$count           = count( $subscriber_ids );
							$error_message[] = sprintf( esc_html__( '%d Subscribers have been removed', 'mailster' ), $count );
						}
					}
					break;

				case 'start':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to start this campaign.', 'mailster' );
					} else {
						$this->start( $post_id );
					}
					break;

				case 'pause':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to pause this campaign.', 'mailster' );
					} else {
						$this->pause( $post_id );
					}
					break;

				case 'resume':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to resume this campaign.', 'mailster' );
					} else {
						$this->resume( $post_id );
					}
					break;

				case 'finish':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to finish this campaign.', 'mailster' );
					} else {
						$this->finish( $post_id );
					}
					break;

				case 'duplicate':
					$post = get_post( $post_id );
					if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() != $post->post_author ) && ! current_user_can( 'duplicate_others_newsletters' ) ) {
						wp_die( esc_html__( 'You are not allowed to duplicate this campaign.', 'mailster' ) );
					} else {
						$this->duplicate( $post_id );
					}
					break;

				case 'activate':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to activate this campaign.', 'mailster' );
					} else {
						$this->activate( $post_id );
					}
					break;

				case 'deactivate':
					if ( ! current_user_can( 'publish_newsletters', $post_id ) ) {
						$error_message[] = esc_html__( 'You are not allowed to deactivate this campaign.', 'mailster' );
					} else {
						$this->deactivate( $post_id );
					}
					break;

			}

		endforeach;

		if ( ! empty( $success_message ) ) {
			mailster_notice( implode( ' ', $success_message ) . $message_postfix, 'success', true, 'campaigns_bulk_success', true, null, true );
		}

		if ( ! empty( $error_message ) ) {
			mailster_notice( implode( ' ', $error_message ) . $message_postfix, 'error', true, 'campaigns_bulk_error', true, null, true );
		}

	}

	/**
	 *
	 *
	 * @param unknown $actions
	 * @param unknown $campaign
	 * @return unknown
	 */
	public function quick_edit_btns( $actions, $campaign ) {

		if ( $campaign->post_type != 'newsletter' ) {
			return $actions;
		}

		if ( ! in_array( $campaign->post_status, array( 'pending', 'auto-draft', 'trash', 'draft' ) ) ) {

			if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() == $campaign->post_author ) || current_user_can( 'duplicate_others_newsletters' ) ) {
				$actions['duplicate'] = '<a class="duplicate" href="?post_type=newsletter&duplicate=' . $campaign->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_duplicate_nonce' ) . '" title="' . sprintf( esc_html__( 'Duplicate Campaign %s', 'mailster' ), '&quot;' . $campaign->post_title . '&quot;' ) . '">' . esc_html__( 'Duplicate', 'mailster' ) . '</a>';
			}

			if ( ( current_user_can( 'publish_newsletters' ) && get_current_user_id() == $campaign->post_author ) || current_user_can( 'edit_others_newsletters' ) ) {
				$actions['statistics'] = '<a class="statistics" href="post.php?post=' . $campaign->ID . '&action=edit&showstats=1" title="' . sprintf( esc_html__( 'See stats of Campaign %s', 'mailster' ), '&quot;' . $campaign->post_title . '&quot;' ) . '">' . esc_html__( 'Statistics', 'mailster' ) . '</a>';
			}

			if ( $parent_id = (int) $this->meta( $campaign->ID, 'parent_id' ) ) {
				$actions['autoresponder_link'] = '<a class="edit_base" href="post.php?post=' . $parent_id . '&action=edit">' . esc_html__( 'Edit base', 'mailster' ) . '</a>';
			}
		}
		return array_intersect_key( $actions, array_flip( array( 'edit', 'trash', 'view', 'statistics', 'duplicate', 'autoresponder_link' ) ) );
	}


	/**
	 *
	 *
	 * @param unknown $title
	 * @return unknown
	 */
	public function title( $title ) {
		return esc_html__( 'Enter Campaign Title here', 'mailster' );
	}


	public function assets() {

		$screen = get_current_screen();

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( 'edit-newsletter' == $screen->id ) {
			wp_enqueue_script( 'mailster-overview', MAILSTER_URI . 'assets/js/overview-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

			wp_enqueue_style( 'mailster-overview', MAILSTER_URI . 'assets/css/overview-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			mailster_localize_script(
				'campaigns',
				array(
					'finish_campaign'  => esc_html__( 'Do you really like to finish this campaign?', 'mailster' ),
					'finish_campaigns' => esc_html__( 'Do you really like to finish selected campaigns?', 'mailster' ),
					'start_campaigns'  => esc_html__( 'Do you really like to start selected campaigns?', 'mailster' ),
				)
			);

		} elseif ( 'newsletter' == $screen->id ) {

			global $post, $wp_locale;
			add_filter( 'enter_title_here', array( &$this, 'title' ) );

			add_action( 'dbx_post_sidebar', array( mailster( 'ajax' ), 'add_ajax_nonce' ) );

			$this->post_data = $this->meta( $post->ID );
			if ( empty( $this->post_data ) ) {
				$this->post_data = $this->meta_defaults();
			}

			add_action( 'submitpost_box', array( &$this, 'notice' ) );

			if ( isset( $_GET['template'] ) ) {
				$file = ( isset( $_GET['file'] ) ) ? $_GET['file'] : 'index.html';
				if ( isset( $this->post_data['head'] ) ) {
					unset( $this->post_data['head'] );
				}

				$this->set_template( $_GET['template'], $file, true );
				$post->post_content = '';
			} elseif ( isset( $this->post_data['template'] ) ) {
				$this->set_template( $this->post_data['template'], $this->post_data['file'] );
			} else {
				$this->set_template( mailster_option( 'default_template' ), $this->post_data['file'] );
			}

			$googlejsapi_url = 'https://www.gstatic.com/charts/loader.js';

			if ( in_array( $post->post_status, array( 'active', 'finished' ) ) || isset( $_GET['showstats'] ) ) {

				wp_enqueue_script( 'google-jsapi', $googlejsapi_url, array(), null, true );
				wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
				wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );
				wp_add_inline_style( 'mailster-newsletter', '#local-storage-notice{display:none !important}' );

			} else {

				if ( isset( $_GET['conditions'] ) && empty( $this->post_data['list_conditions'] ) ) {
					$this->post_data['list_conditions'] = (array) $_GET['conditions'];
				}

				if ( $post->post_status == 'autoresponder' ) {
					wp_enqueue_script( 'google-jsapi', $googlejsapi_url, array(), null, true );
					wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
					wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );
				}

				wp_enqueue_script( 'mailster-codemirror', MAILSTER_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MAILSTER_VERSION );
				wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );

				remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
				wp_enqueue_script( 'mailster-emojipicker', MAILSTER_URI . 'assets/js/libs/emoji-button.js', array(), MAILSTER_VERSION );

				if ( user_can_richedit() ) {
					wp_enqueue_script( 'editor' );
				}

				wp_enqueue_style( 'jquery-ui-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css', array(), MAILSTER_VERSION );
				wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css', array(), MAILSTER_VERSION );

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'jquery-ui-draggable' );

				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'thickbox' );

				wp_enqueue_media();

			}

			mailster_localize_script(
				'google',
				array(
					'key' => mailster_option( 'google_api_key' ),
				)
			);
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( 'mailster-campaign', MAILSTER_URI . 'assets/js/campaign-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-campaign', MAILSTER_URI . 'assets/css/campaign-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_script( 'mailster-editbar', MAILSTER_URI . 'assets/js/editbar-script' . $suffix . '.js', array( 'mailster-campaign' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-editbar', MAILSTER_URI . 'assets/css/editbar-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_script( 'mailster-optionbar', MAILSTER_URI . 'assets/js/optionbar-script' . $suffix . '.js', array( 'mailster-campaign' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-optionbar', MAILSTER_URI . 'assets/css/optionbar-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_style( 'mailster-flags', MAILSTER_URI . 'assets/css/flags' . $suffix . '.css', array(), MAILSTER_VERSION );

			mailster_localize_script(
				'conditions',
				array(
					'next'          => esc_html__( 'next', 'mailster' ),
					'prev'          => esc_html__( 'prev', 'mailster' ),
					'start_of_week' => get_option( 'start_of_week' ),
					'day_names'     => $wp_locale->weekday,
					'day_names_min' => array_values( $wp_locale->weekday_abbrev ),
					'month_names'   => array_values( $wp_locale->month ),
				)
			);

			mailster_localize_script(
				'campaigns',
				array(
					'loading'                => esc_html__( 'loading', 'mailster' ),
					'add'                    => esc_html__( 'Add', 'mailster' ),
					'or'                     => esc_html__( 'or', 'mailster' ),
					'move_module_up'         => esc_html__( 'Move module up', 'mailster' ),
					'move_module_down'       => esc_html__( 'Move module down', 'mailster' ),
					'duplicate_module'       => esc_html__( 'Duplicate module', 'mailster' ),
					'remove_module'          => esc_html__( 'Remove module', 'mailster' ),
					'remove_all_modules'     => esc_html__( 'Do you really like to remove all modules?', 'mailster' ),
					'save_template'          => esc_html__( 'Save Template File', 'mailster' ),
					'add_module'             => esc_html__( 'Add Module', 'mailster' ),
					'module'                 => esc_html__( 'Module %s', 'mailster' ),
					'codeview'               => esc_html__( 'Codeview', 'mailster' ),
					'module_label'           => esc_html__( 'Name of the module (click to edit)', 'mailster' ),
					'edit'                   => esc_html__( 'Edit', 'mailster' ),
					'click_to_edit'          => esc_html__( 'Click to edit %s', 'mailster' ),
					'click_to_add'           => esc_html__( 'Click to add %s', 'mailster' ),
					'auto'                   => esc_html_x( 'Auto', 'for the autoimporter', 'mailster' ),
					'add_button'             => esc_html__( 'Add button', 'mailster' ),
					'add_repeater'           => esc_html__( 'Add repeater', 'mailster' ),
					'remove_repeater'        => esc_html__( 'Remove repeater', 'mailster' ),
					'add_s'                  => esc_html__( 'Add %s', 'mailster' ),
					'remove_s'               => esc_html__( 'Remove %s', 'mailster' ),
					'curr_selected'          => esc_html__( 'Currently selected', 'mailster' ),
					'remove_btn'             => esc_html__( 'An empty link will remove this button! Continue?', 'mailster' ),
					'preview_for'            => esc_html__( 'Preview for %s', 'mailster' ),
					'preview'                => esc_html__( 'Preview', 'mailster' ),
					'read_more'              => esc_html__( 'Read more', 'mailster' ),
					'invalid_image'          => esc_html__( '%s does not contain a valid image', 'mailster' ),
					'for_area'               => esc_html__( 'Area %s', 'mailster' ),
					'enter_list_name'        => esc_html__( 'Enter name of the list', 'mailster' ),
					'create_list'            => esc_html_x( '%1$s of %2$s', '[recipientstype] of [campaignname]', 'mailster' ),
					'next'                   => esc_html__( 'next', 'mailster' ),
					'prev'                   => esc_html__( 'prev', 'mailster' ),
					'start_of_week'          => get_option( 'start_of_week' ),
					'day_names'              => $wp_locale->weekday,
					'day_names_min'          => array_values( $wp_locale->weekday_abbrev ),
					'month_names'            => array_values( $wp_locale->month ),
					'delete_colorschema'     => esc_html__( 'Delete this color schema?', 'mailster' ),
					'delete_colorschema_all' => esc_html__( 'Do you really like to delete all custom color schema for this template?', 'mailster' ),
					'yourscore'              => esc_html__( '%s out of 10', 'mailster' ),
					'yourscores'             => array(
						esc_html__( 'This mail will hardly see any inbox!', 'mailster' ),
						esc_html__( 'You have to make it better!', 'mailster' ),
						esc_html__( 'Many inboxes will refuse this mail!', 'mailster' ),
						esc_html__( 'Not bad at all. Improve it further!', 'mailster' ),
						esc_html__( 'Almost perfect!', 'mailster' ),
						esc_html__( 'Great! Your campaign is ready to send!', 'mailster' ),
					),
					'undosteps'              => mailster_option( 'undosteps', 10 ),
					'statuschanged'          => esc_html__( 'The status of this campaign has changed. Please reload the page or %s', 'mailster' ),
					'click_here'             => esc_html__( 'click here', 'mailster' ),
					'send_now'               => esc_html__( 'Do you really like to send this campaign now?', 'mailster' ),
					'select_image'           => esc_html__( 'Select Image', 'mailster' ),
					'add_attachment'         => esc_html__( 'Add Attachment', 'mailster' ),
					'edit_conditions'        => esc_html__( 'Edit Conditions', 'mailster' ),
					'remove_conditions'      => esc_html__( 'Do you really like to remove all conditions?', 'mailster' ),
					'ready'                  => esc_html__( 'ready!', 'mailster' ),
					'error'                  => esc_html__( 'error!', 'mailster' ),
					'error_occurs'           => esc_html__( 'An error occurs while uploading', 'mailster' ),
					'unsupported_format'     => esc_html__( 'Unsupported file format', 'mailster' ),
					'unknown_locations'      => esc_html__( '+ %d unknown locations', 'mailster' ),
				)
			);

		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function inline_editor() {
		// no IE 11
		if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0' ) !== false ) {
			return false;
		}
		global $wp_version;
		return apply_filters( 'mailster_inline_editor', version_compare( '4.6', $wp_version, '<=' ) );
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function add_post_thumbnail_link( $content, $post_id ) {

		global $post;

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {

			if ( $meta = $this->meta( $post_id, 'auto_post_thumbnail' ) ) {
				// don't cache auto post thumbnails
				$content = str_replace( '.jpg" class="attachment-post-thumbnail', '.jpg?c=' . time() . '" class="attachment-post-thumbnail', $content );
			}

			if ( in_array( $post->post_status, array( 'active', 'finished' ) ) || isset( $_GET['showstats'] ) ) {

			} else {
				$content .= '<p><label><input type="checkbox" name="auto_post_thumbnail" value="1" ' . checked( $meta, true, false ) . '> ' . esc_html__( 'Create Screenshot for Feature Image', 'mailster' ) . '</label></p>';

				$timestamp = wp_next_scheduled( 'mailster_auto_post_thumbnail', array( $post_id ) );
				if ( $timestamp + 2 >= time() ) {
					$content .= '<p class="description" title="' . esc_html__( 'Generating the screenshot may take a while. Please reload the page to update', 'mailster' ) . '"><span class="spinner"></span>' . esc_html__( 'Creating Screenshot', 'mailster' ) . '&hellip;</p>';
				}
			}
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $size
	 * @param unknown $thumbnail_id
	 * @param unknown $post
	 * @return unknown
	 */
	public function admin_post_thumbnail_size( $size, $thumbnail_id, $post ) {

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {
			$size = array( 600, 800 );
		}

		return $size;

	}



	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function remove_kses( $content ) {

		global $post;

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {
			kses_remove_filters();
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $post
	 * @param unknown $postarr
	 * @return unknown
	 */
	public function wp_insert_post_data( $post, $postarr ) {

		if ( ! isset( $post ) || ! $postarr['ID'] ) {
			return $post;
		}

		// if it's an autosave
		$is_autosave = wp_is_post_autosave( $postarr['ID'] );
		// but it's parent isn't a newsletter
		if ( $is_autosave && 'newsletter' != get_post_type( $is_autosave ) ) {
			return $post;
		}
		// no autosave and no newsletter
		if ( ! $is_autosave && 'newsletter' != $post['post_type'] ) {
			return $post;
		}

		if ( $is_autosave && isset( $_POST['data']['mailsterdata'] ) ) {

			parse_str( $_POST['data']['mailsterdata'], $postdata );
			$postdata = $postdata['mailster_data'];

		} elseif ( isset( $_POST['mailster_data'] ) ) {

			$postdata = $_POST['mailster_data'];

		} else {

			$postdata = $this->meta( $postarr['ID'] );
		}

		// sanitize the content and remove all content filters
		$post['post_content'] = mailster()->sanitize_content( $post['post_content'], isset( $postdata['head'] ) ? $postdata['head'] : null );

		$post['post_excerpt'] = ! empty( $postdata['autoplaintext'] )
			? mailster( 'helper' )->plain_text( $post['post_content'] )
			: $post['post_excerpt'];

		if ( ! in_array( $post['post_status'], array( 'pending', 'draft', 'auto-draft', 'trash' ) ) ) {

			if ( $post['post_status'] == 'publish' ) {
				$post['post_status'] = 'paused';
			}

			$post['post_status'] = isset( $_POST['mailster_data']['active'] ) ? 'queued' : $post['post_status'];

			// overcome post status issue where old slugs only for published post are stored
			if ( $postarr['ID'] ) {
				$fakepost              = (object) $post;
				$fakepost->post_status = 'publish';

				wp_check_for_changed_slugs( $postarr['ID'], $fakepost, get_post( $postarr['ID'] ) );
			}
		}

		if ( $post['post_status'] == 'autoresponder' && isset( $postdata['autoresponder'] ) && $postdata['autoresponder']['action'] != 'mailster_autoresponder_followup' ) {
			$post['post_parent'] = 0;
		}

		return $post;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @param unknown $post
	 * @param unknown $update  (optional)
	 * @return unknown
	 */
	public function save_campaign( $post_id, $post, $update = null ) {

		if ( ! isset( $post ) ) {
			return $post;
		}

		$is_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;

		if ( $is_autosave && isset( $_POST['data']['mailsterdata'] ) ) {

			parse_str( $_POST['data']['mailsterdata'], $postdata );
			$postdata = $postdata['mailster_data'];

		} elseif ( isset( $_POST['mailster_data'] ) ) {

			$postdata = $_POST['mailster_data'];
			if ( $post->post_type != 'newsletter' ) {
				return $post;
			}
		} else {
			return $post;
		}

		// activate kses filter
		kses_init_filters();

		$timeoffset = mailster( 'helper' )->gmt_offset( true );
		$now        = time();

		$meta = $old_meta = $this->meta( $post_id );
		if ( in_array( $post->post_status, array( 'active', 'finished' ) ) ) {

			$meta['webversion'] = isset( $postdata['webversion'] );
			$this->update_meta( $post_id, $meta );
			return $post;
		}

		if ( isset( $postdata ) ) {

			$meta['subject']       = $postdata['subject'];
			$meta['preheader']     = $postdata['preheader'];
			$meta['template']      = $postdata['template'];
			$meta['file']          = $postdata['file'];
			$meta['lists']         = isset( $postdata['lists'] ) ? (array) $postdata['lists'] : null;
			$meta['ignore_lists']  = isset( $postdata['ignore_lists'] ) && $postdata['ignore_lists'];
			$meta['from_name']     = $postdata['from_name'];
			$meta['from_email']    = $postdata['from_email'];
			$meta['reply_to']      = $postdata['reply_to'];
			$meta['timezone']      = isset( $postdata['timezone'] ) && $postdata['timezone'];
			$meta['webversion']    = isset( $postdata['webversion'] );
			$meta['editor_height'] = (int) $postdata['editor_height'];

			if ( isset( $postdata['newsletter_color'] ) ) {
				$meta['colors'] = $postdata['newsletter_color'];
			}

			if ( isset( $postdata['attachments'] ) ) {
				$meta['attachments'] = array();
				$total_size          = 0;
				$max_size            = apply_filters( 'mymail_attachments_max_filesize', apply_filters( 'mailster_attachments_max_filesize', 1024 * 1024 ) );
				foreach ( $postdata['attachments'] as $attachment_id ) {
					if ( ! $attachment_id ) {
						continue;
					}
					$file = get_attached_file( $attachment_id );
					if ( is_file( $file ) ) {
						$total_size += filesize( $file );
						if ( $total_size <= $max_size ) {
							$meta['attachments'][] = $attachment_id;
						} else {
							mailster_notice( sprintf( esc_html__( 'Attachments must not exceed the file size limit of %s!', 'mailster' ), '<strong>' . esc_html( size_format( $max_size ) ) . '</strong>' ), 'error', true );
						}
					} else {
						mailster_notice( esc_html__( 'Attachment doesn\'t exist or isn\'t readable!', 'mailster' ), 'error', true );
					}
				}
			} else {
				$meta['attachments'] = array();
			}

			$meta['track_opens']  = isset( $postdata['track_opens'] );
			$meta['track_clicks'] = isset( $postdata['track_clicks'] );

			$meta['head'] = $postdata['head'];

			$is_autoresponder = isset( $postdata['is_autoresponder'] ) && $postdata['is_autoresponder'];

			$autoresponder = $postdata['autoresponder'];

			$post->post_parent   = 0;
			$post->post_password = isset( $_POST['use_pwd'] ) ? $_POST['post_password'] : '';

			if ( ! mailster_is_local() && $meta['auto_post_thumbnail'] = isset( $_POST['auto_post_thumbnail'] ) ) {

				wp_schedule_single_event( time(), 'mailster_auto_post_thumbnail', array( $post_id ) );

			} else {

				if ( $timestamp = wp_next_scheduled( 'mailster_auto_post_thumbnail', array( $post_id ) ) ) {
					wp_unschedule_event( $timestamp, 'mailster_auto_post_thumbnail', array( $post_id ) );
				}
			}

			if ( $is_autoresponder ) {

				if ( $post->post_status != 'autoresponder' && ! $is_autosave && ! isset( $_POST['draft'] ) ) {
					$this->change_status( $post, 'autoresponder' );
					$post->post_status = 'autoresponder';
				}

				$meta['active'] = isset( $postdata['active_autoresponder'] ) && current_user_can( 'publish_newsletters' ) && ! isset( $_POST['draft'] );

				$autoresponder['amount'] = max( 0, (float) $autoresponder['amount'] );

				if ( in_array( $autoresponder['action'], array( 'mailster_subscriber_insert', 'mailster_subscriber_unsubscribed' ) ) ) {
					unset( $autoresponder['terms'] );

					$localtime         = strtotime( $postdata['autoresponder_signup_date'] . ' ' . $postdata['autoresponder_signup_time'] );
					$meta['timestamp'] = $localtime - $timeoffset;

				} elseif ( 'mailster_post_published' == $autoresponder['action'] ) {

					if ( 'rss' == $autoresponder['post_type'] && ! isset( $autoresponder['since'] ) ) {
						$autoresponder['since'] = time();
					}
				} else {
					unset( $autoresponder['terms'] );
				}

				if ( 'mailster_autoresponder_timebased' == $autoresponder['action'] ) {

					$autoresponder['interval'] = max( 1, (int) $autoresponder['interval'] );
					$meta['timezone']          = isset( $autoresponder['timebased_timezone'] );

					$autoresponder['since'] = isset( $autoresponder['since'] ) ? ( $autoresponder['since'] ? $autoresponder['since'] : $now ) : false;

					$localtime = strtotime( $postdata['autoresponder_date'] . ' ' . $postdata['autoresponder_time'] );

					$autoresponder['weekdays'] = ( isset( $autoresponder['weekdays'] )
						? $autoresponder['weekdays']
						: array( date( 'w', $localtime ) ) );

					$localtime = mailster( 'helper' )->get_next_date_in_future( $localtime - $timeoffset, 0, $autoresponder['time_frame'], $autoresponder['weekdays'] );

					$meta['timestamp'] = $localtime;

					if ( isset( $autoresponder['time_conditions'] ) && 'rss' == $autoresponder['time_post_type'] && ! $autoresponder['since'] ) {
						$autoresponder['since'] = $now;
					}

					if ( isset( $autoresponder['endschedule'] ) ) {

						$localtime                     = strtotime( $postdata['autoresponder_enddate'] . ' ' . $postdata['autoresponder_endtime'] );
						$autoresponder['endtimestamp'] = max( $meta['timestamp'], $localtime - $timeoffset );

					}
				} elseif ( 'mailster_autoresponder_followup' == $autoresponder['action'] ) {

					$parent_id = isset( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : null;

					switch ( $autoresponder['followup_action'] ) {
						// sent
						case '1':
							break;
						// open
						case '2':
							if ( ! $this->meta( $parent_id, 'track_opens' ) ) {
								$parent_campaign = get_post( $parent_id );
								mailster_notice( '<strong>' . sprintf( esc_html__( 'Tracking Opens is disabled in campaign %s! Please enable tracking or choose a different campaign.', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $parent_campaign->ID . '&action=edit' ) . '">' . $parent_campaign->post_title . '</a>' ) . '</strong>', 'error', true );
							}
							break;
						// clicked
						case '3':
							if ( ! $this->meta( $_POST['parent_id'], 'track_clicks' ) ) {
								$parent_campaign = get_post( $parent_id );
								mailster_notice( sprintf( esc_html__( 'Tracking Clicks is disabled in campaign %s! Please enable tracking or choose a different campaign.', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $parent_campaign->ID . '&action=edit' ) . '">' . $parent_campaign->post_title . '</a>' ), 'error', true );
							}
							break;

						default:
							break;
					}
				} elseif ( 'mailster_autoresponder_usertime' == $autoresponder['action'] ) {

					$meta['timezone']      = isset( $autoresponder['usertime_timezone'] );
					$autoresponder['once'] = isset( $autoresponder['usertime_once'] );

				} elseif ( 'mailster_autoresponder_hook' == $autoresponder['action'] ) {

					$hooks = get_option( 'mailster_hooks', array() );
					if ( ! is_array( $hooks ) ) {
						$hooks = array();
					}
					$hooks[ $post->ID ] = $autoresponder['hook'];
					if ( ! $meta['active'] ) {
						unset( $hooks[ $post->ID ] );
					}

					update_option( 'mailster_hooks', $hooks );

					if ( isset( $autoresponder['hook_once'] ) ) {
						$autoresponder['once'] = true;
						if ( isset( $autoresponder['multiple'] ) ) {
							$autoresponder['once'] = false;
						}
					}
					if ( empty( $autoresponder['hook'] ) ) {
						mailster_notice( esc_html__( 'Please define a hook which should trigger the campaign!', 'mailster' ), 'error', true );
					}
				} elseif ( 'mailster_post_published' == $autoresponder['action'] ) {

					// if it has been activated or post type has changed => reset it
					if ( ( $meta['active'] && $meta['active'] != $old_meta['active'] ) || $autoresponder['post_type'] != $old_meta['autoresponder']['post_type'] ) {
						$autoresponder['post_count_status'] = 0;
						$autoresponder['since']             = time();
					}

					$meta['timezone'] = isset( $autoresponder['post_published_timezone'] );

				}

				if ( isset( $_POST['post_count_status_reset'] ) ) {
					$autoresponder['post_count_status'] = 0;
					$autoresponder['since']             = time();
				}

				$meta['autoresponder'] = $autoresponder;

			} else {
				// no autoresponder
				if ( $post->post_status == 'autoresponder' && ! $is_autosave ) {
					$meta['active'] = false;
					$this->change_status( $post, 'paused' );
					$post->post_status = 'paused';
				} else {
					$meta['active'] = isset( $postdata['active'] );
				}

				if ( isset( $_POST['sendnow'] ) ) {
					$post->post_status = 'queued';
					$meta['timestamp'] = $now;
					$meta['active']    = true;

				} elseif ( isset( $_POST['resume'] ) ) {
					$post->post_status = 'queued';
					$meta['active']    = true;

				} elseif ( isset( $_POST['draft'] ) ) {
					$post->post_status = 'draft';
					$meta['active']    = false;

				} elseif ( ( isset( $postdata ) && empty( $meta['timestamp'] ) ) || $meta['active'] ) {
					// save in UTC
					if ( isset( $postdata['date'] ) && isset( $postdata['time'] ) ) {
						$localtime = strtotime( $postdata['date'] . ' ' . $postdata['time'] );
					} else {
						$localtime = $now;
					}
					$meta['timestamp'] = max( $now, $localtime - $timeoffset );
				}

				// set status to 'active' if time is in the past
				if ( ! $is_autosave && $post->post_status == 'queued' && $now - $meta['timestamp'] >= 0 ) {
					$this->change_status( $post, 'active' );
					$post->post_status = 'active';

					// set status to 'queued' if time is in the future
				} elseif ( ! $is_autosave && $post->post_status == 'active' && $now - $meta['timestamp'] < 0 ) {
					$this->change_status( $post, 'queued' );
					$post->post_status = 'queued';
				}

				$meta['autoresponder'] = null;

			}

			mailster_remove_notice( 'camp_error_' . $post_id );

		}

		if ( isset( $postdata['conditions'] ) ) {

			foreach ( (array) $postdata['conditions'] as $i => $and_cond ) {
				foreach ( $and_cond as $j => $cond ) {
					if ( ! isset( $postdata['conditions'][ $i ][ $j ]['field'] ) ) {
						unset( $postdata['conditions'][ $i ][ $j ] );
					} elseif ( is_array( $postdata['conditions'][ $i ][ $j ]['value'] ) ) {
						$postdata['conditions'][ $i ][ $j ]['value'] = array_values( array_unique( $postdata['conditions'][ $i ][ $j ]['value'] ) );
					} else {

					}
				}
			}
			$meta['list_conditions'] = array_values( array_filter( $postdata['conditions'] ) );

		} else {

			$meta['list_conditions'] = '';

		}

		$meta['autoplaintext'] = isset( $postdata['autoplaintext'] );

		if ( isset( $meta['active_autoresponder'] ) && $meta['active_autoresponder'] ) {
			if ( isset( $postdata ) ) {
				if ( ! $meta['timestamp'] ) {
					$meta['timestamp'] = max( $now, strtotime( $postdata['date'] . ' ' . $postdata['time'] ) );
				}
			}
		}

		// always inactive if autosave
		if ( $is_autosave ) {
			$meta['active'] = false;
		}

		$this->update_meta( $post_id, $meta );

		if ( ! $is_autosave ) {

			if ( ! $is_autoresponder || isset( $_POST['clearqueue'] ) ) {
				mailster( 'queue' )->clear( $post_id );
			}

			// if post is published, active or queued and campaign start within the next 60 minutes
			if ( in_array( $post->post_status, array( 'active', 'queued', 'autoresponder' ) ) && $now - $meta['timestamp'] > -3600 ) {

				mailster( 'cron' )->update();

			}
			if ( in_array( $post->post_status, array( 'autoresponder' ) ) ) {

				switch ( $autoresponder['action'] ) {
					case 'mailster_autoresponder_usertime':
						mailster( 'queue' )->autoresponder_usertime( $post_id );
						break;
					case 'mailster_autoresponder_timebased':
						mailster( 'queue' )->autoresponder_timebased( $post_id );
						break;
					default:
						mailster( 'queue' )->autoresponder( $post_id );

				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function list_based_opt_out( $id ) {

		if ( ! mailster_options( 'list_based_opt_in' ) ) {
			return false;
		}

		if ( empty( $id ) ) {
			return false;
		}

		$meta = $this->meta( $id );

		if ( ! $meta ) {
			return false;
		}

		return empty( $meta['ignore_lists'] ) && ! empty( $meta['lists'] );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function meta( $id, $key = null ) {

		global $wpdb;

		$cache_key = 'campaign_meta';

		$meta = mailster_cache_get( $cache_key );
		if ( ! $meta ) {
			$meta = array();
		}

		if ( 0 === $id ) {
			return $this->meta_defaults();
		}

		if ( is_numeric( $id ) ) {

			if ( isset( $meta[ $id ] ) ) {
				if ( is_null( $key ) ) {
					return $meta[ $id ];
				}

				return isset( $meta[ $id ][ $key ] ) ? $meta[ $id ][ $key ] : null;
			}

			$ids = array( $id );
		} elseif ( is_array( $id ) ) {

			$ids = $id;
		}

		$defaults = $this->meta_defaults();

		if ( is_null( $id ) && is_null( $key ) ) {
			return $defaults;
		}

		$sql = "SELECT post_id AS ID, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE '_mailster_%'";

		if ( isset( $ids ) ) {
			$sql .= ' AND post_id IN (' . implode( ',', array_filter( $ids, 'is_numeric' ) ) . ')';
		}

		$result = $wpdb->get_results( $sql );

		foreach ( $result as $metadata ) {
			if ( ! isset( $meta[ $metadata->ID ] ) ) {
				$meta[ $metadata->ID ] = $defaults;
			}

			$meta_key = str_replace( '_mailster_', '', $metadata->meta_key );

			// emojis are urlencoded
			if ( in_array( $meta_key, array( 'subject', 'from_name', 'preheader' ) ) ) {
				$metadata->meta_value = rawurldecode( $metadata->meta_value );
			}
			$meta[ $metadata->ID ][ $meta_key ] = $metadata->meta_value;

			if ( ! empty( $meta[ $metadata->ID ]['lists'] ) ) {
				$meta[ $metadata->ID ]['lists'] = maybe_unserialize( $meta[ $metadata->ID ]['lists'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['colors'] ) ) {
				$meta[ $metadata->ID ]['colors'] = maybe_unserialize( $meta[ $metadata->ID ]['colors'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['list_conditions'] ) ) {
				$meta[ $metadata->ID ]['list_conditions'] = maybe_unserialize( $meta[ $metadata->ID ]['list_conditions'] );

				if ( isset( $meta[ $metadata->ID ]['list_conditions']['operator'] ) ) {
					if ( 'OR' == $meta[ $metadata->ID ]['list_conditions']['operator'] ) {
						$meta[ $metadata->ID ]['list_conditions'] = array( $meta[ $metadata->ID ]['list_conditions']['conditions'] );
					} else {
						$cond = array();
						foreach ( $meta[ $metadata->ID ]['list_conditions']['conditions'] as $c ) {
							$cond[] = array( $c );
						}
						$meta[ $metadata->ID ]['list_conditions'] = $cond;
					}
				}
			}

			if ( ! empty( $meta[ $metadata->ID ]['autoresponder'] ) ) {
				$meta[ $metadata->ID ]['autoresponder'] = maybe_unserialize( $meta[ $metadata->ID ]['autoresponder'] );
			}
			if ( ! empty( $meta[ $metadata->ID ]['attachments'] ) ) {
				$meta[ $metadata->ID ]['attachments'] = maybe_unserialize( $meta[ $metadata->ID ]['attachments'] );
			}
		}

		mailster_cache_set( $cache_key, $meta );

		if ( is_null( $id ) && is_null( $key ) ) {
			return $meta;
		}

		if ( is_array( $id ) && is_null( $key ) ) {
			return $meta;
		}

		if ( is_array( $id ) ) {
			return wp_list_pluck( $meta, $key );
		}

		if ( is_null( $key ) ) {
			return isset( $meta[ $id ] ) ? $meta[ $id ] : null;
		}

		if ( is_null( $id ) ) {
			return wp_list_pluck( $meta, $key );
		}

		return isset( $meta[ $id ] ) && isset( $meta[ $id ][ $key ] ) ? $meta[ $id ][ $key ] : null;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key
	 * @param unknown $value (optional)
	 * @return unknown
	 */
	public function update_meta( $id, $key, $value = null ) {

		$cache_key = 'campaign_meta';
		$meta      = mailster_cache_get( $cache_key );
		if ( ! $meta ) {
			$meta = array();
		}

		if ( is_array( $key ) ) {
			$_meta = (array) $key;
		} else {
			$_meta = array( $key => $value );
		}

		$nullvalues = array( 'timezone', 'track_opens', 'track_clicks', 'ignore_lists', 'autoplaintext', 'auto_post_thumbnail', 'webversion' );

		foreach ( $_meta as $k => $v ) {
			// allowed NULL values
			if ( $v == '' && ! in_array( $k, $nullvalues ) ) {
				delete_post_meta( $id, '_mailster_' . $k );
				// default is true => don't save
			} elseif ( $v != '' && in_array( $k, array( 'webversion', 'autoplaintext' ) ) ) {
				delete_post_meta( $id, '_mailster_' . $k );
			} elseif ( in_array( $k, array( 'subject', 'from_name', 'preheader' ) ) ) {
				// emojis are urlencoded
				update_post_meta( $id, '_mailster_' . $k, rawurlencode( wp_unslash( $v ) ) );
			} else {
				update_post_meta( $id, '_mailster_' . $k, $v );
			}
		}

		if ( isset( $meta[ $id ] ) ) {
			unset( $meta[ $id ] );
			mailster_cache_set( $cache_key, $meta );
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function meta_defaults( $key = null ) {
		$defaults = array(
			'parent_id'           => null,
			'timestamp'           => null,
			'finished'            => null,
			'active'              => null,
			'timezone'            => mailster_option( 'timezone' ),
			'sent'                => null,
			'error'               => null,
			'from_name'           => mailster_option( 'from_name' ),
			'from_email'          => mailster_option( 'from' ),
			'reply_to'            => mailster_option( 'reply_to' ),
			'subject'             => null,
			'preheader'           => null,
			'template'            => null,
			'file'                => null,
			'editor_height'       => 500,
			'lists'               => null,
			'ignore_lists'        => null,
			'autoresponder'       => null,
			'list_conditions'     => null,
			'head'                => null,
			'colors'              => null,
			'track_opens'         => mailster_option( 'track_opens' ),
			'track_clicks'        => mailster_option( 'track_clicks' ),
			'autoplaintext'       => true,
			'webversion'          => true,
			'auto_post_thumbnail' => false,
		);

		if ( ! is_null( $key ) ) {
			return isset( $defaults[ $key ] ) ? $defaults[ $key ] : null;
		}

		return $defaults;

	}


	public function trigger_campaign_action( $action, $campaign_id ) {

		if ( in_array( $action, array( 'pause', 'start', 'resume', 'finish', 'duplicate', 'activate', 'deactivate' ) ) ) {
			call_user_func( array( $this, $action ), $campaign_id );
		}
	}

	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @return unknown
	 */
	public function pause( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$meta = $this->meta( $id );

		$meta['active'] = false;

		$this->update_meta( $id, $meta );

		if ( $this->change_status( $campaign, 'paused' ) ) {
			do_action( 'mailster_campaign_pause', $id );
			return true;
		} else {
			return false;
		}
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @return unknown
	 */
	public function start( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$now = time();

		$meta = $this->meta( $id );
		if ( ! $this->get_totals( $id ) ) {
			return false;
		}

		if ( in_array( $campaign->post_status, array( 'finished', 'autoresponder' ) ) ) {
			return false;
		}

		$meta['active'] = true;

		if ( empty( $meta['timestamp'] ) || $campaign->post_status == 'queued' ) {
			$meta['timestamp'] = $now;
		}

		$status = ( $now - $meta['timestamp'] < 0 ) ? 'queued' : 'active';

		$this->update_meta( $id, $meta );

		if ( $this->change_status( $campaign, $status ) ) {
			do_action( 'mailster_campaign_start', $id );
			mailster_remove_notice( 'camp_error_' . $id );
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
	public function resume( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$now = time();

		$meta = $this->meta( $id );
		if ( ! $this->get_totals( $id ) ) {
			return false;
		}
		if ( ! $this->get_sent( $id ) ) {
			return false;
		}

		if ( in_array( $campaign->post_status, array( 'finished', 'autoresponder' ) ) ) {
			return false;
		}

		$meta['active'] = true;

		if ( empty( $meta['timestamp'] ) || $campaign->post_status == 'queued' ) {
			return false;
		}

		$status = ( $now - $meta['timestamp'] < 0 ) ? 'queued' : 'active';

		$this->update_meta( $id, $meta );

		if ( $this->change_status( $campaign, $status ) ) {
			do_action( 'mailster_campaign_start', $id );
			mailster_remove_notice( 'camp_error_' . $id );
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
	public function finish( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		if ( ! in_array( $campaign->post_status, array( 'active', 'queued', 'paused' ) ) ) {
			return;
		}

		$meta             = $this->meta( $id );
		$meta['totals']   = $this->get_totals( $id );
		$meta['sent']     = $this->get_sent( $id );
		$meta['errors']   = $this->get_errors( $id );
		$meta['finished'] = time();

		$placeholder = mailster( 'placeholder' );

		$placeholder->do_conditions( false );
		$placeholder->do_remove_modules( true );

		$placeholder->clear_placeholder();

		$placeholder->set_content( $campaign->post_title );
		$campaign->post_title = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $campaign->post_content );
		$campaign->post_content = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['subject'] );
		$meta['subject'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['preheader'] );
		$meta['preheader'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['from_name'] );
		$meta['from_name'] = $placeholder->get_content( false, array(), true );

		remove_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		kses_remove_filters();

		wp_update_post(
			array(
				'ID'           => $id,
				'post_title'   => $campaign->post_title,
				'post_content' => $campaign->post_content,
			)
		);

		kses_init_filters();
		add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );

		$this->update_meta( $id, $meta );

		$this->change_status( $campaign, 'finished' );

		if ( $parent_id = $this->meta( $id, 'parent_id' ) ) {
			$parent_sent   = $this->meta( $parent_id, 'sent' );
			$parent_errors = $this->meta( $parent_id, 'errors' );

			$this->update_meta( $parent_id, 'sent', $parent_sent + $meta['sent'] );
			$this->update_meta( $parent_id, 'errors', $parent_errors + $meta['errors'] );
		}

		do_action( 'mailster_finish_campaign', $id );

		mailster( 'queue' )->remove( $id );

		mailster_remove_notice( 'camp_error_' . $id );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @return unknown
	 */
	public function duplicate( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$lists = $this->get_lists( $campaign->ID, true );
		$meta  = $this->meta( $campaign->ID );

		$meta['active'] = $meta['date'] = $meta['time'] = $meta['timestamp'] = $meta['parent_id'] = $meta['finished'] = $meta['sent'] = $meta['error'] = null;

		unset( $campaign->ID );
		unset( $campaign->guid );
		unset( $campaign->post_name );
		unset( $campaign->post_author );
		unset( $campaign->post_date );
		unset( $campaign->post_date_gmt );
		unset( $campaign->post_modified );
		unset( $campaign->post_modified_gmt );

		if ( preg_match( '# \((\d+)\)$#', $campaign->post_title, $hits ) ) {
			$campaign->post_title = trim( preg_replace( '#(.*) \(\d+\)$#', '$1 (' . ( ++$hits[1] ) . ')', $campaign->post_title ) );
		} elseif ( $campaign->post_title ) {
			$campaign->post_title .= ' (2)';
		}
		if ( $campaign->post_status == 'autoresponder' ) {
			$meta['autoresponder']['issue']             = 1;
			$meta['autoresponder']['post_count_status'] = 0;
		} else {
			$campaign->post_status = 'draft';
		}

		kses_remove_filters();
		$new_id = wp_insert_post( $campaign );
		kses_init_filters();

		if ( $new_id ) {

			$this->update_meta( $new_id, $meta );
			$this->add_lists( $new_id, $lists );

			do_action( 'mailster_campaign_duplicate', $id, $new_id );

			return $new_id;
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
	public function activate( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$current = $this->meta( $id, 'active' );
		if ( $current ) {
			return true;
		}

		if ( 'autoresponder' == $campaign->post_status ) {
			$autoresponder = $this->meta( $id, 'autoresponder' );
			if ( ! empty( $autoresponder['since'] ) ) {
				$autoresponder['post_count_status'] = 0;
				$autoresponder['since']             = time();
				$this->update_meta( $id, 'autoresponder', $autoresponder );
			}
		}

		return $this->update_meta( $id, 'active', true );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $timestamp (optional)
	 * @return unknown
	 */
	public function deactivate( $id, $timestamp = null ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		wp_clear_scheduled_hook( 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		if ( ! is_null( $timestamp ) ) {
			return wp_schedule_single_event( (int) $timestamp, 'mailster_campaign_action', array( __FUNCTION__, $id ) );
		}

		$current = $this->meta( $id, 'active' );
		if ( ! $current ) {
			return true;
		}
		return $this->update_meta( $id, 'active', false );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $delay                 (optional)
	 * @param unknown $issue                 (optional)
	 * @param unknown $relative_to_absolute  (optional)
	 * @param unknown $index_offset          (optional)
	 * @return unknown
	 */
	public function autoresponder_to_campaign( $id, $delay = 0, $issue = '', $relative_to_absolute = true, $index_offset = 0 ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		if ( $campaign->post_status != 'autoresponder' ) {
			return new WP_Error( 'no_autoresponder_campaign', esc_html__( 'This campaign is not an autoresponder.', 'mailster' ) );
		}

		$id = $campaign->ID;

		$now        = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$lists = $this->get_lists( $campaign->ID, true );
		$meta  = $this->meta( $campaign->ID );

		$meta['autoresponder'] = $meta['sent'] = $meta['errors'] = $meta['finished'] = null;

		$meta['active'] = true;

		$remove_unused = false;

		$meta['timestamp'] = max( $now, $now + $delay );

		unset( $campaign->ID );
		unset( $campaign->guid );
		unset( $campaign->post_name );
		unset( $campaign->post_date );
		unset( $campaign->post_date_gmt );
		unset( $campaign->post_modified );
		unset( $campaign->post_modified_gmt );

		$campaign->post_status = $meta['timestamp'] <= $now ? 'active' : 'queued';

		$placeholder = mailster( 'placeholder' );
		$placeholder->set_campaign( $id );
		$placeholder->set_index_offset( $index_offset );

		$placeholder->do_conditions( false );
		$placeholder->do_remove_modules( true );
		$placeholder->replace_custom_tags( false );

		$placeholder->clear_placeholder();
		$placeholder->add( array( 'issue' => $issue ) );

		$placeholder->set_content( $campaign->post_title );
		$campaign->post_title = $placeholder->get_content( $remove_unused );

		$placeholder->set_content( $campaign->post_content );
		$campaign->post_content = $placeholder->get_content( $remove_unused, array(), $relative_to_absolute );

		$placeholder->set_content( $meta['subject'] );
		$meta['subject'] = $placeholder->get_content( $remove_unused, array(), $relative_to_absolute );

		$placeholder->set_content( $meta['preheader'] );
		$meta['preheader'] = $placeholder->get_content( $remove_unused, array(), $relative_to_absolute );

		$placeholder->set_content( $meta['from_name'] );
		$meta['from_name'] = $placeholder->get_content( $remove_unused, array(), $relative_to_absolute );

		remove_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		kses_remove_filters();

		$new_id = wp_insert_post( $campaign );

		kses_init_filters();
		add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );

		if ( $new_id ) {

			$meta['parent_id'] = $id;
			$this->update_meta( $new_id, $meta );
			$this->add_lists( $new_id, $lists );

			return $new_id;
		}

		return false;
	}

	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function delete( $id ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		return wp_delete_post( $campaign->ID );

	}

	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function trash( $id ) {

		$campaign = get_post( $id );

		if ( ! $campaign ) {
			return new WP_Error( 'no_campaign', esc_html__( 'This campaign doesn\'t exists.', 'mailster' ) );
		}

		return wp_trash_post( $campaign->ID );

	}

	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function maybe_cleanup_after_delete( $post_id ) {
		if ( $post_id && $post = get_post( $post_id ) ) {
			if ( 'newsletter' == $post->post_type ) {
				add_action( 'after_delete_post', array( &$this, 'cleanup_after_delete' ) );
			}
		}
	}

	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function cleanup_after_delete( $id ) {

		global $wpdb;

		// delete action or just set them to NULL
		$delete_action = false;

		// remove or set actions to NULL.
		if ( $delete_action ) {
			$wpdb->query( $wpdb->prepare( "DELETE actions FROM {$wpdb->prefix}mailster_actions AS actions WHERE actions.campaign_id = %d", $id ) );

		} else {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_actions AS actions SET actions.campaign_id = NULL WHERE actions.campaign_id = %d", $id ) );
		}
		// remove queue and subscriber meta
		$wpdb->query( $wpdb->prepare( "DELETE queue FROM {$wpdb->prefix}mailster_queue AS queue WHERE queue.campaign_id = %d", $id ) );
		$wpdb->query( $wpdb->prepare( "DELETE subscriber_meta FROM {$wpdb->prefix}mailster_subscriber_meta AS subscriber_meta WHERE subscriber_meta.campaign_id = %d", $id ) );

		// unassign existing parents
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_value = %d AND meta_key = '_mailster_parent_id'", $id ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get( $id = null ) {

		if ( is_null( $id ) || is_array( $id ) ) {
			return $this->get_campaigns( $id );
		}

		$campaign = get_post( $id );

		return ( $campaign && $campaign->post_type == 'newsletter' ) ? $campaign : false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_formated_lists( $id ) {

		if ( $this->meta( $id, 'ignore_lists' ) ) {
			return '';
		}

		$lists = $this->get_lists( $id );

		if ( empty( $lists ) ) {
			return '';
		}

		$names = wp_list_pluck( $lists, 'name' );

		return implode( ', ', $names );
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_lists( $id, $ids_only = false ) {

		$list_ids = $this->meta( $id, 'lists' );

		if ( empty( $list_ids ) ) {
			return array();
		}

		if ( $ids_only ) {
			return $list_ids;
		}

		return mailster( 'lists' )->get( $list_ids, false );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $list_ids
	 * @param unknown $clear    (optional)
	 * @return unknown
	 */
	public function add_lists( $id, $list_ids, $clear = false ) {

		if ( ! is_array( $list_ids ) ) {
			$list_ids = array( $list_ids );
		}

		if ( ! $clear ) {
			$list_ids = wp_parse_args( $list_ids, $this->meta( $id, 'lists' ) );
		}

		return $this->update_meta( $id, 'lists', array_unique( $list_ids ) );

	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_active( $args = '' ) {
		$defaults = array(
			'post_status' => 'active',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_paused( $args = '' ) {
		$defaults = array(
			'post_status' => 'paused',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_queued( $args = '' ) {
		$defaults = array(
			'post_status' => 'queued',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_drafted( $args = '' ) {
		$defaults = array(
			'post_status' => 'draft',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_finished( $args = '' ) {
		$defaults = array(
			'post_status' => 'finished',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_pending( $args = '' ) {
		$defaults = array(
			'post_status' => 'pending',
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @param unknown $type (optional)
	 * @return unknown
	 */
	public function get_autoresponder( $args = '', $type = null ) {
		$defaults = array(
			'post_status' => 'autoresponder',
		);
		$args     = wp_parse_args( $args, $defaults );
		if ( ! is_null( $type ) ) {
			$args['meta_key']     = '_mailster_autoresponder';
			$args['meta_compare'] = 'LIKE';
			$args['meta_value']   = '"mailster_' . $type . '"';
		}

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_campaigns( $args = '' ) {

		$defaults = array(
			'post_type'              => 'newsletter',
			'post_status'            => array( 'active', 'paused', 'queued', 'draft', 'finished', 'pending', 'autoresponder' ),
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		$campaigns = get_posts( $args );

		return $campaigns;
	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_sent  (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @param unknown $returnsql    (optional)
	 * @return unknown
	 */
	public function get_subscribers( $id = null, $statuses = null, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = null, $offset = 0, $returnsql = false ) {

		if ( $this->meta( $id, 'ignore_lists' ) ) {
			$lists = false;
		} else {
			$lists = $this->meta( $id, 'lists' );

			if ( empty( $lists ) && ! $returnsql ) {
				return $return_ids ? array() : 0;
			}
		}

		$conditions = $this->meta( $id, 'list_conditions' );

		return mailster( 'subscribers' )->query(
			array(
				'lists'         => $lists,
				'conditions'    => $conditions,
				'status'        => $statuses,
				'return_ids'    => $return_ids,
				'return_count'  => ! $return_ids,
				'sent__not_in'  => $ignore_sent ? $id : false,
				'queue__not_in' => $ignore_queue ? $id : false,
				'limit'         => $limit,
				'offset'        => $offset,
				'return_sql'    => $returnsql,
			),
			$id
		);
	}


	/**
	 *
	 *
	 * @param unknown $lists        (optional)
	 * @param unknown $conditions   (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_sent  (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @param unknown $returnsql    (optional)
	 * @return unknown
	 */
	public function get_subscribers_by_lists( $lists = false, $conditions = null, $statuses = null, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = null, $offset = 0, $returnsql = false ) {

		_deprecated_function( __FUNCTION__, '2.3', 'mailster(\'subscribers\')->query()' );

		return mailster( 'subscribers' )->query(
			array(
				'lists'         => $lists,
				'conditions'    => $conditions,
				'status'        => $statuses,
				'return_ids'    => $return_ids,
				'return_count'  => ! $return_ids,
				'sent__not_in'  => $ignore_sent,
				'queue__not_in' => $ignore_queue,
				'limit'         => $limit,
				'offset'        => $offset,
				'return_sql'    => $returnsql,
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @return unknown
	 */
	public function get_unsent_subscribers( $id = null, $statuses = null, $return_ids = false, $ignore_queue = false, $limit = null, $offset = 0 ) {
		return $this->get_subscribers( $id, $statuses, $return_ids, true, $ignore_queue, $limit, $offset );
	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_sent_subscribers( $id = null ) {

		global $wpdb;

		if ( false === ( $sent_subscribers = mailster_cache_get( 'sent_subscribers' ) ) ) {

			$sql = "SELECT a.campaign_id, a.subscriber_id FROM {$wpdb->prefix}mailster_actions AS a WHERE type = 1 ORDER BY a.timestamp ASC";

			$result = $wpdb->get_results( $sql );

			$sent_subscribers = array();

			foreach ( $result as $row ) {
				if ( ! isset( $sent_subscribers[ $row->campaign_id ] ) ) {
					$sent_subscribers[ $row->campaign_id ] = array();
				}

				$sent_subscribers[ $row->campaign_id ][] = $row->subscriber_id;
			}

			mailster_cache_set( 'sent_subscribers', $sent_subscribers );

		}

		return ( is_null( $id ) ) ? $sent_subscribers : ( isset( $sent_subscribers[ $id ] ) ? $sent_subscribers[ $id ] : 0 );

	}


	/**
	 *
	 *
	 * @param unknown $id     (optional)
	 * @param unknown $unique (optional)
	 * @return unknown
	 */
	public function get_links( $id = null, $unique = true ) {

		global $wpdb;

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return array();
		}

		$content = $campaign->post_content;

		preg_match_all( "/(href)=[\"'](.*)[\"']/Ui", $content, $urls );
		$urls = ! empty( $urls[2] ) ? ( $urls[2] ) : array();

		return $unique ? array_values( array_unique( $urls ) ) : $urls;

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_excerpt( $id = null ) {

		global $wpdb;

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return '';
		}

		$content = $campaign->post_content;

		$placeholder = mailster( 'placeholder', $content );

		$placeholder->set_campaign( $campaign->ID );

		$placeholder->add(
			array(
				'preheader' => '',
				'subject'   => '',
				'can-spam'  => '',
				'copyright' => '',
			)
		);

		$content = $placeholder->get_content();
		$content = preg_replace( '#<script[^>]*?>.*?</script>#si', '', $content );
		$content = preg_replace( '#<style[^>]*?>.*?</style>#si', '', $content );

		$allowed_tags = array( 'address', 'a', 'big', 'blockquote', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'dl', 'dt', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'kbd', 'li', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'sub', 'sup', 'tt', 'ul', 'u' );
		$allowed_tags = '<' . implode( '><', $allowed_tags ) . '>';

		$content = strip_tags( $content, $allowed_tags );
		$content = str_replace( array( '&nbsp;', ' editable=""', '<p></p>', ' | ' ), '', $content );
		$content = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $content );

		return trim( $content );

	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $unsubscribes (optional)
	 * @param unknown $bounces      (optional)
	 * @param unknown $deleted      (optional)
	 * @return unknown
	 */
	public function get_totals( $id = null, $unsubscribes = true, $bounces = false, $deleted = true ) {

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return 0;
		}

		if ( 'finished' == $campaign->post_status ) {
			return $this->get_sent( $id, false );
		}
		$subscribers_count = $this->get_subscribers( $id );

		if ( $unsubscribes ) {
			$subscribers_count += $this->get_unsubscribes( $id );
		}

		if ( $bounces ) {
			$subscribers_count += $this->get_bounces( $id );
		}

		if ( $deleted ) {
			$subscribers_count += $this->get_deleted( $id );
		}

		return $subscribers_count;

	}


	/**
	 *
	 *
	 * @param unknown $lists       (optional)
	 * @param unknown $conditions  (optional)
	 * @param unknown $statuses    (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function get_totals_by_lists( $lists = false, $conditions = null, $statuses = null, $campaign_id = null ) {

		return mailster( 'subscribers' )->query(
			array(
				'lists'        => $lists,
				'conditions'   => $conditions,
				'status'       => $statuses,
				'return_count' => true,
			),
			$campaign_id
		);

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
	public function get_deleted( $id = null, $total = false ) {

		return $this->get_action( 'sent_deleted', $id, $total );

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

		return $sent / $totals;

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

		return $errors / $sent;

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

		return $opens / $sent;

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

		return $clicks / $sent;

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

		return $clicks / $open;

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

		return $unsubscribes / $sent;

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

		return $unsubscribes / $open;

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

		return $bounces / ( $totals + $bounces );

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

		return mailster( 'actions' )->get_by_campaign( $id, $action . ( $total ? '_total' : '' ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_clicked_links( $id = null ) {

		return mailster( 'actions' )->get_clicked_links( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_error_list( $id = null ) {

		return mailster( 'actions' )->get_error_list( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_clients( $id = null ) {

		return mailster( 'actions' )->get_clients( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_environment( $id = null ) {

		return mailster( 'actions' )->get_environment( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function get_geo_data_country( $id ) {
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function get_geo_data_city( $id ) {
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_geo_data( $id ) {

		global $wpdb;

		$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS geo, b.meta_value AS coords FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id AND b.meta_key = 'coords' WHERE a.meta_key = 'geo' AND a.campaign_id = %d AND a.meta_value != '|' GROUP BY a.meta_value ORDER BY count DESC";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		$geo_data = array();

		foreach ( $result as $row ) {
			$geo = explode( '|', $row->geo );

			if ( ! isset( $geo_data[ $geo[0] ] ) ) {
				$geo_data[ $geo[0] ] = array( 0 => array( 0, 0, '', 0, '' ) );
			}

			if ( ! $row->coords ) {
				$geo_data[ $geo[0] ][0][3]++;

			} else {
				$coords = $row->coords ? explode( ',', $row->coords ) : array( 0, 0 );

				$geo_data[ $geo[0] ][] = array(
					(float) $coords[0],
					(float) $coords[1],
					$geo[1],
					(int) $row->count,
					$row->count . ' ' . esc_html__( _n( 'opened', 'opens', $row->count, 'mailster' ) ),
				);
			}
		}

		return $geo_data;

	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @param unknown $campaign_id
	 * @param unknown $option      (optional)
	 * @param unknown $countonly   (optional)
	 * @return unknown
	 */
	public function create_list_from_option( $name, $campaign_id, $option = 'open', $countonly = false ) {

		global $wpdb;

		if ( ! current_user_can( 'mailster_edit_lists' ) ) {
			return false;
		}

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign || $campaign->post_status == 'autoresponder' ) {
			return false;
		}

		$args = array();

		switch ( $option ) {
			case 'sent';
				$args['sent'] = $campaign->ID;
			break;
			case 'not_sent';
				$args['sent__not_in'] = $campaign->ID;
			break;
			case 'open':
				$args['open'] = $campaign->ID;
				break;
			case 'not_open':
				$args['open__not_in'] = $campaign->ID;

				break;
			case 'click':
				$args['open']  = $campaign->ID;
				$args['click'] = $campaign->ID;

				break;
			case 'open_not_click':
				$args['open']          = $campaign->ID;
				$args['click__not_in'] = $campaign->ID;
				break;
			default:
				break;
		}

		if ( $countonly ) {
			$args['return_count'] = true;
			return mailster( 'subscribers' )->query( $args, $campaign->ID );
		}

		$args['return_ids'] = true;

		$subscribers = mailster( 'subscribers' )->query( $args, $campaign->ID );

		$options = array(
			'sent'           => esc_html__( 'who have received', 'mailster' ),
			'not_sent'       => esc_html__( 'who have not received', 'mailster' ),
			'open'           => esc_html__( 'who have opened', 'mailster' ),
			'open_not_click' => esc_html__( 'who have opened but not clicked', 'mailster' ),
			'click'          => esc_html__( 'who have opened and clicked', 'mailster' ),
			'not_open'       => esc_html__( 'who have not opened', 'mailster' ),
		);

		$list = mailster( 'lists' )->add_segment(
			array(
				'name'        => $name,
				'description' => sprintf( esc_html_x( 'A segment of all %1$s of %2$s', 'segment of all [recipients] from campaign [campaign]', 'mailster' ), $options[ $option ], '"' . $campaign->post_title . '"' ),
				'slug'        => 'segment-' . $option . '-of-' . $campaign->ID,
			),
			true,
			$subscribers
		);

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $parts       (optional)
	 * @param unknown $page        (optional)
	 * @param unknown $orderby     (optional)
	 * @param unknown $order       (optional)
	 * @return unknown
	 */
	public function get_recipients_part( $campaign_id, $parts = array( 'unopen', 'opens', 'clicks', 'unsubs', 'bounces' ), $page = 0, $orderby = 'sent', $order = 'ASC' ) {

		global $wpdb;

		$return = '';

		$track_opens  = $this->meta( $campaign_id, 'track_opens' );
		$track_clicks = $this->meta( $campaign_id, 'track_clicks' );

		$limit  = apply_filters( 'mailster_get_recipients_part', 1000 );
		$offset = (int) $page * $limit;

		$fields = array(
			'ID'          => esc_html__( 'ID', 'mailster' ),
			'email'       => mailster_text( 'email' ),
			'status'      => esc_html__( 'Status', 'mailster' ),
			'firstname'   => mailster_text( 'firstname' ),
			'lastname'    => mailster_text( 'lastname' ),
			'sent'        => esc_html__( 'Sent Date', 'mailster' ),
			'open'        => esc_html__( 'Open Date', 'mailster' ),
			'open_count'  => esc_html__( 'Open Count', 'mailster' ),
			'clicks'      => esc_html__( 'Click Date', 'mailster' ),
			'click_count' => esc_html__( 'Click Count', 'mailster' ),
			'unsubs'      => esc_html__( 'Unsubscribes', 'mailster' ),
			'bounces'     => esc_html__( 'Bounces', 'mailster' ),
		);

		if ( ! $track_opens ) {
			unset( $fields['open'], $fields['open_count'] );
		}
		if ( ! $track_clicks ) {
			unset( $fields['clicks'], $fields['click_count'] );
		}

		if ( ! in_array( $orderby, array_keys( $fields ) ) ) {
			$orderby = 'sent';
		}

		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'ASC';
		}

		$sql  = $this->get_recipients_part_sql( $campaign_id, $parts );
		$sql .= " ORDER BY $orderby $order";
		$sql .= " LIMIT $offset, $limit";

		$subscribers = $wpdb->get_results( $sql );

		$count = 0;

		$timeformat = mailster( 'helper' )->timeformat();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$subscribers_count = count( $subscribers );
		$deleted           = $this->get_deleted( $campaign_id );

		$unopen  = in_array( 'unopen', $parts );
		$opens   = in_array( 'opens', $parts );
		$clicks  = in_array( 'clicks', $parts );
		$unsubs  = in_array( 'unsubs', $parts );
		$bounces = in_array( 'bounces', $parts );

		if ( ! $offset ) {
			$return .= '<div class="ajax-list-header filter-list"><label>' . esc_html__( 'Filter', 'mailster' ) . ': </label> ';
			if ( $track_opens ) {
				$return .= '<label><input type="checkbox" class="recipients-limit show-unopen" value="unopen" ' . checked( $unopen, true, false ) . '> ' . esc_html__( 'unopens', 'mailster' ) . ' </label> ';
				$return .= '<label><input type="checkbox" class="recipients-limit show-open" value="opens" ' . checked( $opens, true, false ) . '> ' . esc_html__( 'opens', 'mailster' ) . ' </label> ';
			}
			if ( $track_clicks ) {
				$return .= '<label><input type="checkbox" class="recipients-limit show-click" value="clicks"' . checked( $clicks, true, false ) . '> ' . esc_html__( 'clicks', 'mailster' ) . ' </label> ';
			}
			$return .= '<label><input type="checkbox" class="recipients-limit show-unsubscribes" value="unsubs"' . checked( $unsubs, true, false ) . '> ' . esc_html__( 'unsubscribes', 'mailster' ) . '</label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-bounces" value="bounces"' . checked( $bounces, true, false ) . '> ' . esc_html__( 'bounces', 'mailster' ) . ' </label> ';
			$return .= '<label>' . esc_html__( 'order by', 'mailster' ) . ' ';
			$return .= '<select class="recipients-order">';
			foreach ( $fields as $field => $name ) {
				$return .= '<option value="' . $field . '" ' . selected( $field, $orderby, false ) . '>' . $name . '</option>';
			}
			$return .= '</select></label>';
			$return .= '<a title="' . esc_html__( 'order direction', 'mailster' ) . '" class="recipients-order mailster-icon ' . ( $order == 'ASC' ? 'asc' : 'desc' ) . '"></a>';
			$return .= '</div>';
		}

		if ( ! $offset ) {
			$return .= '<table class="wp-list-table widefat recipients-list"><tbody>';
			if ( $deleted ) {
				$return .= '<tr><td>&nbsp;</td><td colspan="8"><em>' . esc_html__( 'Deleted Subscribers are not listed.', 'mailster' ) . '</em></td></tr>';
			}
		}

		foreach ( $subscribers as $i => $subscriber ) {

			$name = trim( $subscriber->firstname . ' ' . $subscriber->lastname );

			$return .= '<tr ' . ( ! ( $i % 2 ) ? ' class="alternate" ' : '' ) . '>';
			$return .= '<td class="textright">' . ( $count + $offset + 1 ) . '</td><td><a class="show-receiver-detail" data-id="' . $subscriber->ID . '">' . ( $name ? $name . ' &ndash; ' : '' ) . $subscriber->email . '</a></td>';
			$return .= '<td title="' . esc_html__( 'sent', 'mailster' ) . '">' . ( $subscriber->sent ? str_replace( ' ', '&nbsp;', date( $timeformat, $subscriber->sent + $timeoffset ) ) : '&ndash;' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->open_count ) && $subscriber->open_count ? '<span title="' . esc_html__( 'has opened', 'mailster' ) . '" class="mailster-icon mailster-icon-open"></span>' : '<span title="' . esc_html__( 'has not opened yet', 'mailster' ) . '" class="mailster-icon mailster-icon-unopen"></span>' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->click_count_total ) && $subscriber->click_count_total ? sprintf( esc_html__( _n( '%s click', '%s clicks', $subscriber->click_count_total, 'mailster' ) ), $subscriber->click_count_total ) : '' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->unsubs ) && $subscriber->unsubs ? '<span title="' . esc_html__( 'has unsubscribed', 'mailster' ) . '" class="mailster-icon mailster-icon-unsubscribe"></span>' : '' ) . '</td>';
			$return .= '<td>';
			$return .= ( isset( $subscriber->bounce_count ) ? '<span class="bounce-indicator mailster-icon mailster-icon-bounce ' . ( $subscriber->status == 3 ? 'hard' : 'soft' ) . '" title="' . sprintf( esc_html__( _n( '%s bounce', '%s bounces', $subscriber->bounce_count, 'mailster' ) ), $subscriber->bounce_count ) . '"></span>' : '' );
			$return .= ( $subscriber->status == 4 ) ? '<span class="bounce-indicator mailster-icon mailster-icon-bounce" title="' . esc_html__( 'an error occurred while sending to this receiver', 'mailster' ) . '">E</span>' : '';
			$return .= '</td>';
			$return .= '</tr>';
			$return .= '<tr id="receiver-detail-' . $subscriber->ID . '" class="receiver-detail' . ( ! ( $i % 2 ) ? '  alternate' : '' ) . '">';
			$return .= '<td></td><td colspan="6">';
			$return .= '<div class="receiver-detail-body"></div>';
			$return .= '</td>';
			$return .= '</tr>';

			$count++;

		}

		if ( $count && $limit == $subscribers_count ) {
			$return .= '<tr ' . ( $i % 2 ? ' class="alternate" ' : '' ) . '><td colspan="7"><a class="load-more-receivers button aligncenter" data-page="' . ( $page + 1 ) . '" data-types="' . implode( ',', $parts ) . '" data-order="' . $order . '" data-orderby="' . $orderby . '">' . esc_html__( 'load more recipients from this campaign', 'mailster' ) . '</a>' . '<span class="spinner"></span></td></tr>';
		}

		if ( ! $offset ) {
			$return .= '</tbody></table>';
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $parts       (optional)
	 * @return unknown
	 */
	public function get_recipients_part_sql( $campaign_id, $parts = array( 'unopen', 'opens', 'clicks', 'unsubs', 'bounces' ) ) {

		global $wpdb;

		$unopen  = in_array( 'unopen', $parts );
		$opens   = in_array( 'opens', $parts );
		$clicks  = in_array( 'clicks', $parts );
		$unsubs  = in_array( 'unsubs', $parts );
		$bounces = in_array( 'bounces', $parts );

		$sql  = 'SELECT a.ID, a.email, a.hash, a.status, firstname.meta_value AS firstname, lastname.meta_value AS lastname';
		$sql .= ', sent.timestamp AS sent, sent.count AS sent_count';

		if ( $unopen || $opens ) {
			$sql .= ', open.timestamp AS open, COUNT(open.count) AS open_count';
		}
		if ( $clicks ) {
			$sql .= ', click.timestamp AS clicks, COUNT(click.count) AS click_count, SUM(click.count) AS click_count_total';
		}
		if ( $unsubs ) {
			$sql .= ', unsub.timestamp AS unsubs, unsub.count AS unsub_count';
		}
		if ( $bounces ) {
			$sql .= ', bounce.timestamp AS bounces, bounce.count AS bounce_count';
		}
		$sql .= " FROM {$wpdb->prefix}mailster_subscribers AS a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS firstname ON a.ID = firstname.subscriber_id AND firstname.meta_key = 'firstname'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS lastname ON a.ID = lastname.subscriber_id AND lastname.meta_key = 'lastname'";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS sent ON a.ID = sent.subscriber_id AND sent.type = 1";

		if ( $unopen || $opens ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS open ON a.ID = open.subscriber_id AND open.type = 2 AND open.campaign_id = sent.campaign_id";
		}
		if ( $clicks ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS click ON a.ID = click.subscriber_id AND click.type = 3 AND click.campaign_id = sent.campaign_id";
		}
		if ( $unsubs ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS unsub ON a.ID = unsub.subscriber_id AND unsub.type = 4 AND unsub.campaign_id = sent.campaign_id";
		}
		if ( $bounces ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS bounce ON a.ID = bounce.subscriber_id AND bounce.type IN (5,6) AND bounce.campaign_id = sent.campaign_id";
		}

		$sql .= ' WHERE sent.campaign_id = %d';

		$extra = array();

		if ( $unopen ) {
			$extra[] = 'open.timestamp IS NULL';
		}

		if ( $opens ) {
			$extra[] = 'open.timestamp IS NOT NULL';
		}

		if ( $clicks ) {
			$extra[] = 'click.timestamp IS NOT NULL';
		}

		if ( $unsubs ) {
			$extra[] = 'unsub.timestamp IS NOT NULL';
		}

		if ( $bounces ) {
			$extra[] = 'bounce.timestamp IS NOT NULL';
		}

		if ( ! empty( $extra ) ) {
			$sql .= ' AND (' . implode( ' OR ', $extra ) . ')';
		}

		$sql .= ' GROUP BY a.ID';

		$wpdb->query( 'SET SQL_BIG_SELECTS=1' );
		return $wpdb->prepare( $sql, $campaign_id );

	}


	/**
	 *
	 *
	 * @param unknown $response
	 * @param unknown $data
	 * @return unknown
	 */
	public function heartbeat( $response, $data ) {

		global $post;

		if ( isset( $data['wp_autosave'] ) && $data['wp_autosave']['post_type'] == 'newsletter' ) {
			kses_remove_filters();
		}

		if ( ! isset( $data['mailster'] ) ) {
			return $response;
		}

		$return = array();

		$cron_status = mailster( 'cron' )->check();
		if ( is_wp_error( $cron_status ) ) {
			mailster_notice( $cron_status->get_error_message(), 'error', false, 'check_cron' );
		}

		switch ( $data['mailster']['page'] ) {

			case 'overview':
				if ( ! isset( $_POST['data']['mailster']['ids'] ) ) {
					break;
				}

				$ids    = array_filter( $_POST['data']['mailster']['ids'], 'is_numeric' );
				$return = array_fill_keys( $ids, null );

				foreach ( $ids as $id ) {

					$post = $this->get( $id );
					if ( ! $post ) {
						continue;
					}

					$meta           = $this->meta( $id );
					$totals         = $this->get_totals( $id );
					$sent           = $this->get_sent( $id );
					$sent_formatted = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
					if ( is_wp_error( $cron_status ) ) {
						$status_title = esc_html__( 'Sending Problem!', 'mailster' );
						if ( current_user_can( 'activate_plugins' ) ) {
							 $status_title .= ' <a href="' . admin_url( 'admin.php?page=mailster_tests&autostart' ) . '" class="button button-small">' . esc_html__( 'Self Test', 'mailster' ) . '</a>';
						}
					} else {
						$status_title = $sent_formatted;
					}

					// finish campaign
					if ( 'active' == $post->post_status && $totals && $sent >= $totals ) {
						$this->finish( $id );
					}

					$return[ $id ] = array(
						'cron'           => ! is_wp_error( $cron_status ),
						'status'         => $post->post_status,
						'is_active'      => $meta['active'],
						'status_title'   => $status_title,
						'total'          => $totals,
						'sent'           => $sent,
						'sent_formatted' => '&nbsp;' . $sent_formatted,
						'column-status'  => $this->get_columns_content( 'status' ),
						'column-total'   => $this->get_columns_content( 'total' ),
						'column-open'    => $this->get_columns_content( 'open' ),
						'column-click'   => $this->get_columns_content( 'click' ),
						'column-unsubs'  => $this->get_columns_content( 'unsubs' ),
						'column-bounces' => $this->get_columns_content( 'bounces' ),
					);

				}
				break;

			case 'edit':
				$id = (int) $_POST['data']['mailster']['id'];

				$post = $this->get( $id );
				if ( ! $post ) {
					break;
				}

				$meta             = $this->meta( $id );
				$totals           = $this->get_totals( $id );
				$sent             = $this->get_sent( $id );
				$deleted          = $this->get_deleted( $id );
				$opens            = $this->get_opens( $id );
				$clicks           = $this->get_clicks( $id );
				$clicks_total     = $this->get_clicks( $id, true );
				$unsubs           = $this->get_unsubscribes( $id );
				$bounces          = $this->get_bounces( $id );
				$open_rate        = round( $this->get_open_rate( $id ) * 100, 2 );
				$click_rate       = round( $this->get_click_rate( $id ) * 100, 2 );
				$bounce_rate      = round( $this->get_bounce_rate( $id ) * 100, 2 );
				$unsubscribe_rate = round( $this->get_unsubscribe_rate( $id ) * 100, 2 );

				$environment = $this->get_environment( $id );

				$geolocation = '';

				if ( $geo_data = $this->get_geo_data( $post->ID ) ) :

					$unknown_cities = array();
					$countrycodes   = array();

					foreach ( $geo_data as $countrycode => $data ) {
						$x = wp_list_pluck( $data, 3 );
						if ( $x ) {
							$countrycodes[ $countrycode ] = array_sum( $x );
						}

						if ( $data[0][3] ) {
							$unknown_cities[ $countrycode ] = $data[0][3];
						}
					}

					arsort( $countrycodes );
					$total = array_sum( $countrycodes );

					$i           = 0;
					$geolocation = '';

					foreach ( $countrycodes as $countrycode => $count ) {

						$geolocation .= '<label title="' . mailster( 'geo' )->code2Country( $countrycode ) . '"><span class="big"><span class="mailster-flag-24 flag-' . strtolower( $countrycode ) . '"></span> ' . round( $count / $opens * 100, 2 ) . '%</span></label> ';
						if ( ++$i >= 5 ) {
							break;
						}
					}

				endif;

				// finish campaign
				if ( 'active' == $post->post_status && $totals && $sent >= $totals ) {
					$this->finish( $id );
				}

				$return[ $id ] = array(
					'cron'           => ! is_wp_error( $cron_status ),
					'status'         => $post->post_status,
					'total'          => $post->post_type == 'autoresponder' ? $sent : $totals,
					'sent'           => $sent,
					'deleted'        => $deleted,
					'opens'          => $opens,
					'clicks'         => $clicks,
					'clicks_total'   => $clicks_total,
					'unsubs'         => $unsubs,
					'bounces'        => $bounces,
					'open_rate'      => $open_rate,
					'click_rate'     => $click_rate,
					'unsub_rate'     => $unsubscribe_rate,
					'bounce_rate'    => $bounce_rate,
					'total_f'        => number_format_i18n( $totals ),
					'sent_f'         => number_format_i18n( $sent ),
					'deleted_f'      => number_format_i18n( $deleted ),
					'opens_f'        => number_format_i18n( $opens ),
					'clicks_f'       => number_format_i18n( $clicks ),
					'clicks_total_f' => number_format_i18n( $clicks_total ),
					'unsubs_f'       => number_format_i18n( $unsubs ),
					'bounces_f'      => number_format_i18n( $bounces ),
					'environment'    => $environment,
					'clickbadges'    => array(
						'total'  => $this->get_clicks( $id, true ),
						'clicks' => $this->get_clicked_links( $id ),
					),
					'sent_formatted' => '&nbsp;' . sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) ),
					'geo_location'   => $geolocation,
				);

				break;
		}

		$response['mailster'] = $return;

		// maybe change status
		mailster( 'queue' )->update();
		// maybe change status
		mailster( 'queue' )->update_status();

		return $response;
	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_id
	 * @param unknown $track         (optional)
	 * @param unknown $force         (optional)
	 * @param unknown $log           (optional)
	 * @return unknown
	 */
	public function send_to_subscriber( $campaign_id, $subscriber_id, $track = null, $force = false, $log = false ) {

		_deprecated_function( __FUNCTION__, '2.2', "mailster('campaigns')->send()" );

		return $this->send( $campaign_id, $subscriber_id, $track, $force, $log );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_id
	 * @param unknown $track         (optional)
	 * @param unknown $force         (optional)
	 * @param unknown $log           (optional)
	 * @param unknown $tags          (optional)
	 * @return unknown
	 */
	public function send( $campaign_id, $subscriber_id, $track = null, $force = false, $log = true, $tags = array() ) {

		global $wpdb;

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign || $campaign->post_type != 'newsletter' ) {
			return new WP_Error( 'wrong_post_type', esc_html__( 'wrong post type', 'mailster' ) );
		}

		$subscriber = mailster( 'subscribers' )->get( $subscriber_id, true );

		if ( ! $subscriber ) {
			return new WP_Error( 'no_subscriber', esc_html__( 'No subscriber found', 'mailster' ) );
		}

		if ( ! $force && $subscriber->status > 2 ) {
			return new WP_Error( 'user_unsubscribed', esc_html__( 'User has not subscribed', 'mailster' ) );
		}

		if ( ! $force && ! mailster( 'helper' )->in_timeframe() ) {
			return new WP_Error( 'system_error', esc_html__( 'Not in time frame', 'mailster' ) );
		}

		$campaign_meta = $this->meta( $campaign->ID );

		$mail = mailster( 'mail' );

		// stop if send limit is reached
		if ( $mail->sentlimitreached ) {
			return new WP_Error( 'system_error', sprintf( esc_html__( 'Sent limit of %1$s reached! You have to wait %2$s before you can send more mails!', 'mailster' ), mailster_option( 'send_limit' ), human_time_diff( get_option( '_transient_timeout__mailster_send_period_timeout' ) ) ) );
		}

		$mail->to           = $subscriber->email;
		$mail->to_name      = $subscriber->fullname;
		$mail->subject      = $campaign_meta['subject'];
		$mail->from         = $campaign_meta['from_email'];
		$mail->from_name    = $campaign_meta['from_name'];
		$mail->reply_to     = $campaign_meta['reply_to'];
		$mail->bouncemail   = mailster_option( 'bounce' );
		$mail->preheader    = $campaign_meta['preheader'];
		$mail->embed_images = mailster_option( 'embed_images' );

		$mail->add_tracking_image = $track || $campaign_meta['track_opens'];
		$mail->hash               = $subscriber->hash;
		$mail->set_subscriber( $subscriber->ID );

		$placeholder = mailster( 'placeholder' );

		$mail->set_campaign( $campaign->ID );
		$placeholder->set_campaign( $campaign->ID );
		$placeholder->set_hash( $subscriber->hash );
		$placeholder->replace_custom_tags( false );

		if ( ! empty( $campaign_meta['attachments'] ) ) {
			$mail->attachments = array();
			foreach ( (array) $campaign_meta['attachments'] as $attachment_id ) {
				if ( ! $attachment_id ) {
					continue;
				}
				$file = get_attached_file( $attachment_id );
				if ( ! is_file( $file ) ) {
					continue;
				}
				$mail->attachments[ basename( $file ) ] = $file;
			}
		}

		// campaign specific stuff (cache it)
		if ( ! ( $content = mailster_cache_get( 'campaign_send_' . $campaign->ID ) ) ) {

			$content = mailster()->sanitize_content( $campaign->post_content, $campaign_meta['head'] );

			$content = mailster( 'helper' )->prepare_content( $content );
			if ( apply_filters( 'mailster_inline_css', true, $campaign->ID, $subscriber->ID ) ) {
				$content = mailster( 'helper' )->inline_css( $content );
			}

			mailster_cache_set( 'campaign_send_' . $campaign->ID, $content );

		}

		$placeholder->add_defaults( $campaign->ID );
		$placeholder->set_content( $content );

		// user specific stuff
		$placeholder->replace_custom_tags( true );

		$placeholder->set_subscriber( $subscriber->ID );
		$placeholder->add_custom(
			$campaign->ID,
			array(
				'emailaddress' => $subscriber->email,
			)
		);

		// add subscriber info
		$placeholder->add( (array) $subscriber );

		// add subscriber specific tags
		if ( $subscriber_tags = mailster( 'subscribers' )->meta( $subscriber->ID, 'tags', $campaign->ID ) ) {
			$placeholder->add( (array) $subscriber_tags );
		}

		if ( $tags ) {
			$placeholder->add( (array) $tags );
		}

		$content = $placeholder->get_content();

		if ( is_null( $track ) ) {
			$track = $campaign_meta['track_clicks'];
		}

		if ( $track ) {
			// always replace links
			$content = mailster()->replace_links( $content, $subscriber->hash, $campaign->ID );

		}

		// strip all unwanted stuff from the content
		$content = mailster( 'helper' )->strip_structure_html( $content );

		// maybe inline again
		if ( apply_filters( 'mailster_inline_css', true, $campaign->ID, $subscriber->ID ) ) {
			$content = mailster( 'helper' )->inline_css( $content );
		}

		$mail->content = apply_filters( 'mailster_campaign_content', $content, $campaign, $subscriber );

		if ( ! $campaign_meta['autoplaintext'] ) {
			$placeholder->set_content( $campaign->post_excerpt );
			$mail->plaintext = mailster( 'helper' )->plain_text( $placeholder->get_content(), true );
		}

		$unsubscribelink = mailster()->get_unsubscribe_link( $campaign->ID );

		$MID = mailster_option( 'ID' );

		$listunsubscribe = array();
		if ( mailster_option( 'mail_opt_out' ) ) {
			$listunsubscribe_mail    = $mail->bouncemail ? $mail->bouncemail : $mail->from;
			$listunsubscribe_subject = 'Please remove me from the list';
			$listunsubscribe_body    = rawurlencode( "Please remove me from your list! {$subscriber->email} X-Mailster: {$subscriber->hash} X-Mailster-Campaign: {$campaign->ID} X-Mailster-ID: {$MID}" );

			$listunsubscribe[] = "<mailto:$listunsubscribe_mail?subject=$listunsubscribe_subject&body=$listunsubscribe_body>";
		}
		$listunsubscribe[] = '<' . mailster( 'frontpage' )->get_link( 'unsubscribe', $subscriber->hash, $campaign->ID ) . '>';

		$headers = array(
			'X-Mailster'          => $subscriber->hash,
			'X-Mailster-Campaign' => (string) $campaign->ID,
			'X-Mailster-ID'       => $MID,
			'List-Unsubscribe'    => implode( ',', $listunsubscribe ),
		);

		if ( mailster_option( 'single_opt_out' ) ) {
			$headers['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
		}

		if ( 'autoresponder' != get_post_status( $campaign->ID ) ) {
			$headers['Precedence'] = 'bulk';
		}

		$mail->add_header( apply_filters( 'mailster_mail_headers', $headers, $campaign->ID, $subscriber->ID ) );

		$placeholder->set_content( $mail->subject );
		$mail->subject = $placeholder->get_content();

		if ( $placeholder->has_error() ) {
			return new WP_Error( 'error', sprintf( esc_html__( 'There was an error during replacing tags in this campaign! %s', 'mailster' ), '<br>' . implode( '<br>', $placeholder->get_error_messages() ) ) );
		}
		$result = $mail->send();

		if ( $result && ! is_wp_error( $result ) ) {
			if ( $log ) {
				do_action( 'mailster_send', $subscriber->ID, $campaign->ID, $result );
			}

			return $result;
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $mail->is_user_error() ) {
			if ( $log ) {
				do_action( 'mailster_subscriber_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
			}

			return new WP_Error( 'user_error', $mail->last_error->getMessage() );
		}

		if ( $mail->is_system_error() ) {
			if ( $log ) {
				do_action( 'mailster_system_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
			}

			return new WP_Error( 'system_error', $mail->last_error->getMessage() );
		}

		if ( $mail->last_error ) {
			if ( $log ) {
				do_action( 'mailster_campaign_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
			}

			return new WP_Error( 'error', $mail->last_error->getMessage() );
		}

		return new WP_Error( 'unknown', esc_html__( 'unknown', 'mailster' ) );

	}


	/**
	 *
	 *
	 * @param unknown $post
	 * @param unknown $new_status
	 * @param unknown $silent     (optional)
	 * @return unknown
	 */
	public function change_status( $post, $new_status, $silent = false ) {
		if ( ! $post ) {
			return false;
		}

		if ( $post->post_status == $new_status ) {
			return true;
		}

		$old_status = $post->post_status;

		global $wpdb;

		if ( $wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $post->ID ) ) ) {
			if ( ! $silent ) {
				wp_transition_post_status( $new_status, $old_status, $post );
			}

			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $new_status
	 * @param unknown $old_status
	 * @param unknown $post
	 */
	public function maybe_queue_post_changed( $new_status, $old_status, $post ) {

		if ( defined( 'WP_IMPORTING' ) ) {
			return;
		}

		if ( $new_status == $old_status ) {
			return;
		}

		if ( 'newsletter' == $post->post_type ) {
			return;
		}

		$accepted_status = apply_filters( 'mailster_check_for_autoresponder_accepted_status', 'publish', $post );

		if ( ! is_array( $accepted_status ) ) {
			$accepted_status = array( $accepted_status );
		}

		if ( ! in_array( $new_status, $accepted_status ) ) {
			return;
		}

		$this->post_changed[] = $post->ID;

		add_action( 'shutdown', array( &$this, 'process_queue_post_changed' ) );

	}


	public function process_queue_post_changed() {

		foreach ( $this->post_changed as $post_id ) {

			$this->check_for_autoresponder( $post_id );

		}
	}


	/**
	 *
	 *
	 * @param unknown $post
	 */
	public function check_for_autoresponder( $post ) {

		$post = get_post( $post );

		if ( ! $post || get_post_meta( $post->ID, 'mailster_ignore', true ) ) {
			return;
		}

		if ( 'newsletter' == $post->post_type ) {
			return;
		}

		$now = time();

		$campaigns = $this->get_autoresponder();

		if ( empty( $campaigns ) ) {
			return;
		}

		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		// delete cache;
		mailster_cache_delete( 'get_last_post' );

		foreach ( $campaigns as $campaign ) {

			if ( ! $this->meta( $campaign->ID, 'active' ) ) {
				continue;
			}

			$meta    = $this->meta( $campaign->ID, 'autoresponder' );
			$created = 0;

			if ( 'mailster_post_published' == $meta['action'] ) {

				if ( $meta['post_type'] != $post->post_type ) {
					continue;
				}

				$meta['post_count_status']++;

				// if post count is reached
				if ( ! ( $meta['post_count_status'] % ( $meta['post_count'] + 1 ) ) ) {

					if ( isset( $meta['terms'] ) ) {

						$pass = true;

						foreach ( $meta['terms'] as $taxonomy => $term_ids ) {
							// ignore "any taxonomy"
							if ( $term_ids[0] == '-1' ) {
								continue;
							}

							$post_terms = get_the_terms( $post->ID, $taxonomy );

							// no post_terms set but required => give up (not passed)
							if ( ! $post_terms ) {
								$pass = false;
								break;
							}

							$pass = $pass && ! ! count( array_intersect( wp_list_pluck( $post_terms, 'term_id' ), $term_ids ) );

						}

						if ( ! $pass ) {
							continue;
						}
					}

					$integer = floor( $meta['amount'] );
					$decimal = $meta['amount'] - $integer;

					$send_offset = ( strtotime( '+' . $integer . ' ' . $meta['unit'], 0 ) + ( strtotime( '+1 ' . $meta['unit'], 0 ) * $decimal ) );

					// multiply the offset with the number of created campaigns
					$send_offset = $send_offset * ( $created + 1 );

					// sleep one second if multiples are created to prevent the same timestamps
					if ( $created ) {
						sleep( 1 );
					}

					if ( $new_id = $this->autoresponder_to_campaign( $campaign->ID, $send_offset, $meta['issue']++ ) ) {

						$created++;
						$new_campaign = $this->get( $new_id );

						mailster_notice( sprintf( esc_html__( 'New campaign %1$s has been created and is going to be sent in %2$s.', 'mailster' ), '<strong>"<a href="post.php?post=' . $new_campaign->ID . '&action=edit">' . $new_campaign->post_title . '</a>"</strong>', '<strong>' . date( mailster( 'helper' )->timeformat(), $now + $send_offset + $timeoffset ) . '</strong>' ), 'info', true );

						do_action( 'mailster_autoresponder_post_published', $campaign->ID, $new_id );

					}
				}

				$this->update_meta( $campaign->ID, 'autoresponder', $meta );

			} elseif ( 'mailster_autoresponder_timebased' == $meta['action'] ) {

				if ( $meta['time_post_type'] != $post->post_type ) {
					continue;
				}

				if ( ! isset( $meta['time_conditions'] ) ) {
					continue;
				}

				$meta['post_count_status']++;

				$this->update_meta( $campaign->ID, 'autoresponder', $meta );

				mailster( 'queue' )->autoresponder_timebased( $campaign->ID, true );

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file   (optional)
	 * @param unknown $verify (optional)
	 */
	public function set_template( $slug, $file = 'index.html', $verify = false ) {

		if ( $verify ) {

			if ( ! is_dir( mailster( 'templates' )->path . '/' . $slug ) ) {
				$slug = mailster_option( 'default_template', $this->defaultTemplate );
			}
			if ( ! file_exists( mailster( 'templates' )->path . '/' . $slug . '/' . $file ) ) {
				$file = 'index.html';
			}
		}

		$this->template     = $slug;
		$this->templatefile = $file;

		$this->templateobj = mailster( 'template', $slug, $file );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_template() {
		return $this->template;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_file() {
		return ( ! empty( $this->templatefile ) ) ? $this->templatefile : 'index.html';
	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_post_thumbnail( $campaign_id ) {

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign ) {
			return;
		}

		$campaign_url = add_query_arg( 'frame', 0, get_permalink( $campaign_id ) );

		if ( $campaign->post_password ) {
			$campaign_url = add_query_arg( 'pwd', md5( $campaign->post_password . AUTH_KEY ), $campaign_url );
		}

		$hash = md5( $campaign->post_content );

		$url = 'https://s.wordpress.com/mshots/v1/' . ( rawurlencode( add_query_arg( 'c', $hash, $campaign_url ) ) ) . '?w=600&h=800';

		$response = wp_remote_get(
			$url,
			array(
				'redirection' => 0,
				'method'      => 'HEAD',
			)
		);
		$code     = wp_remote_retrieve_response_code( $response );

		if ( 307 == $code ) {
			wp_schedule_single_event( time() + 10, 'mailster_auto_post_thumbnail', array( $campaign_id ) );
		}

		if ( 200 != $code ) {
			return false;
		}

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$tmp_file = download_url( $url );

		if ( is_wp_error( $tmp_file ) ) {
			return false;
		}

		$time_string = date( 'Y/m', strtotime( $campaign->post_date ) );

		$wp_upload_dir = wp_upload_dir( $time_string );

		$filename = apply_filters( 'mymail_post_thumbnail_filename', apply_filters( 'mailster_post_thumbnail_filename', 'newsletter-' . $campaign_id, $campaign ), $campaign ) . '.jpg';

		if ( $file_exits = file_exists( $wp_upload_dir['path'] . '/' . $filename ) ) {
			unlink( $wp_upload_dir['path'] . '/' . $filename );
		}

		$file = array(
			'name'     => $filename,
			'type'     => 'image/jpeg',
			'tmp_name' => $tmp_file,
			'error'    => 0,
			'size'     => filesize( $tmp_file ),
		);

		$overrides = array(
			'test_form'   => false,
			'test_size'   => true,
			'test_upload' => false,
		);

		$results = wp_handle_sideload( $file, $overrides, $time_string );
		$file    = $results['file'];

		if ( isset( $results['error'] ) ) {
			return false;
		}

		$filetype = wp_check_filetype( $file, null );

		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $file ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => apply_filters( 'mymail_post_thumbnail_title', apply_filters( 'mailster_post_thumbnail_title', $campaign->post_title, $campaign ), $campaign ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		if ( ( $post_thumbnail_id = get_post_thumbnail_id( $campaign_id ) ) && $file_exits ) {
			$attachment['ID'] = $post_thumbnail_id;
		}

		$attach_id = wp_insert_attachment( $attachment, $file, $campaign_id );

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $campaign_id, $attach_id );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $file
	 * @param unknown $modules     (optional)
	 * @param unknown $editorstyle (optional)
	 * @return unknown
	 */
	public function get_template_by_id( $id, $file, $modules = true, $editorstyle = false ) {

		$post = get_post( $id );
		// must be a newsletter and have a content
		if ( 'newsletter' == $post->post_type && ! empty( $post->post_content ) ) {
			$html = $post->post_content;

			if ( $editorstyle ) {
				$html = str_replace( '</head>', $this->iframe_script_styles() . '</head>', $html );
				$html = str_replace( '</body>', $this->iframe_body_stuff() . '</body>', $html );
			}

			$html = str_replace( ' !DOCTYPE', '!DOCTYPE', $html );

			if ( strpos( $html, 'data-editable' ) ) {

				$templateobj = mailster( 'template' );
				$x           = $templateobj->new_template_language( $html );
				$html        = $x->saveHTML();

			}
		} elseif ( $post->post_type == 'newsletter' ) {

			$html = $this->get_template_by_slug( $this->get_template(), $file, $modules, $editorstyle );

		} else {

			$html = '';

		}

		return $html;

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file        (optional)
	 * @param unknown $modules     (optional)
	 * @param unknown $editorstyle (optional)
	 * @return unknown
	 */
	public function get_template_by_slug( $slug, $file = 'index.html', $modules = true, $editorstyle = false ) {

		$template = mailster( 'template', $slug, $file );
		$html     = $template->get( $modules, true );

		if ( $editorstyle ) {
			$html = str_replace( '</head>', $this->iframe_script_styles() . '</head>', $html );
			$html = str_replace( '</body>', $this->iframe_body_stuff() . '</body>', $html );
		}

		return $html;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function iframe_script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-icons', MAILSTER_URI . 'assets/css/icons' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_register_style( 'mailster-editor-style', MAILSTER_URI . 'assets/css/editor-style' . $suffix . '.css', array( 'mailster-icons' ), MAILSTER_VERSION );
		wp_register_script( 'mailster-editor-script', MAILSTER_URI . 'assets/js/editor-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );

		$inline = $this->inline_editor();

		$mailsterdata = array(
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'url'        => MAILSTER_URI,
			'inline'     => $inline,
			'codeview'   => current_user_can( 'mailster_see_codeview' ),
			'datefields' => array_merge( array( 'added', 'updated', 'signup', 'confirm' ), mailster()->get_custom_date_fields( true ) ),
			'_wpnonce'   => wp_create_nonce( 'mailster_nonce' ),
			'isrtl'      => is_rtl(),
			'plupload'   => array(
				'runtimes'            => 'html5,flash',
				'browse_button'       => 'mailster-editorimage-upload-button',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'max_file_size'       => wp_max_upload_size() . 'b',
				'url'                 => admin_url( 'admin-ajax.php' ),
				'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
				'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
				'filters'             => array(
					array(
						'title'      => esc_html__( 'Image files', 'mailster' ),
						'extensions' => 'jpg,gif,png',
					),
				),
				'multipart'           => true,
				'urlstream_upload'    => true,
				'multipart_params'    => array(
					'action'   => 'mailster_editor_image_upload_handler',
					'ID'       => isset( $_GET['id'] ) ? (int) $_GET['id'] : null,
					'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
				),
				'multi_selection'     => false,
			),
		);

		if ( $inline ) {
			$toolbar1 = (string) apply_filters( 'mymail_editor_toolbar1', apply_filters( 'mailster_editor_toolbar1', 'bold,italic,underline,strikethrough,|,mailster_mce_button,|,forecolor,backcolor,|,undo,redo,|,link,unlink,|,removeformat,|,mailster_remove_element' ) );
			$toolbar2 = (string) apply_filters( 'mymail_editor_toolbar2', apply_filters( 'mailster_editor_toolbar2', 'bullist,numlist,|,alignleft,aligncenter,alignright,alignjustify' ) );
			$toolbar3 = (string) apply_filters( 'mymail_editor_toolbar3', apply_filters( 'mailster_editor_toolbar3', '' ) );

			$single_toolbar1 = (string) apply_filters( 'mailster_editor_single_toolbar1', 'bold,italic,underline,strikethrough,|,mailster_mce_button,|,forecolor,backcolor,|,link,unlink,|,removeformat,|,mailster_remove_element' );
			$single_toolbar2 = (string) apply_filters( 'mailster_editor_single_toolbar2', '' );
			$single_toolbar3 = (string) apply_filters( 'mailster_editor_single_toolbar3', '' );

			$mailsterdata['tinymce'] = array(
				'args'   => apply_filters(
					'mailster_editor_tinymce_args',
					array(
						'cache_suffix'           => 'mailster-mce-' . MAILSTER_VERSION,
						'hidden_input'           => false,
						'forced_root_block'      => false,
						'force_hex_style_colors' => true,
						'inline'                 => true,
						'menubar'                => false,
						'branding'               => false,
						'invalid_elements'       => 'script,iframe,frameset,applet,embed',
						'block_formats'          => 'Paragraph=p',
						'relative_urls'          => false,
						'remove_script_host'     => false,
						'convert_urls'           => true,
						'browser_spellcheck'     => false,
						'directionality'         => 'ltr',
						'fontsize_formats'       => '8px 10px 12px 14px 18px 24px 36px',
						'skin_url'               => MAILSTER_URI . 'assets/css/tinymce',
						'plugins'                => 'textcolor colorpicker charmap hr lists paste wordpress wplink wpdialogs',
					)
				),
				'single' => array(
					'selector'        => 'single',
					'custom_elements' => 'single',
					'toolbar1'        => $single_toolbar1,
					'toolbar2'        => $single_toolbar2,
					'toolbar3'        => $single_toolbar3,
				),
				'multi'  => array(
					'selector'        => 'multi',
					'custom_elements' => 'multi',
					'toolbar1'        => $toolbar1,
					'toolbar2'        => $toolbar2,
					'toolbar3'        => $toolbar3,
				),
			);
		}

		wp_localize_script( 'mailster-editor-script', 'mailsterdata', $mailsterdata );

		wp_register_script( 'mailster-tinymce', includes_url( 'js/tinymce/' ) . 'tinymce.min.js', array(), false, true );
		wp_register_script( 'mailster-tinymce-compat', includes_url( 'js/tinymce/plugins/compat3x/' ) . 'plugin' . $suffix . '.js', array(), false, true );
		wp_register_style( 'mailster-wp-editor', includes_url( 'css/editor' . $suffix . '.css' ) );

		ob_start();

		if ( $inline ) {
			wp_print_styles( 'dashicons' );
			wp_print_styles( 'mailster-wp-editor' );
			wp_print_scripts( 'utils' );
			mailster( 'tinymce' )->editbar_translations();
			wp_print_scripts( 'mailster-tinymce' );
			wp_print_scripts( 'mailster-tinymce-compat' );
		}

		wp_print_styles( 'mailster-icons' );
		wp_print_styles( 'mailster-editor-style' );

		wp_print_scripts( 'jquery' );
		wp_print_scripts( 'jquery-ui-draggable' );
		wp_print_scripts( 'jquery-ui-droppable' );
		wp_print_scripts( 'jquery-ui-sortable' );
		wp_print_scripts( 'jquery-ui-autocomplete' );
		wp_print_scripts( 'jquery-touch-punch' );
		wp_print_scripts( 'plupload-all' );
		wp_print_scripts( 'mailster-editor-script' );

		mailster( 'helper' )->get_mailster_styles( true );

		do_action( 'mailster_iframe_script_styles' );

		$script_styles = ob_get_contents();

		ob_end_clean();

		return $script_styles;

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $field
	 * @return unknown
	 */
	public function iframe_body_stuff() {

		ob_start();

		echo '<mailster>';

		wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false );

		do_action( 'mailster_iframe_body' );

		echo '</mailster>';

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function remove_revisions( $post_id ) {

		if ( ! $post_id ) {
			return false;
		}

		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE a FROM $wpdb->posts AS a WHERE a.post_type = '%s' AND a.post_parent = %d", 'revision', $post_id ) );
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	private function replace_colors( $content ) {
		// replace the colors
		global $post_id;
		global $post;

		$html   = $this->templateobj->get( true );
		$colors = array();
		preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
		$original_colors = array_unique( $hits[0] );
		$html            = $post->post_content;

		if ( ! empty( $html ) && isset( $this->post_data['template'] ) && $this->post_data['template'] == $this->get_template() && $this->post_data['file'] == $this->get_file() ) {
			preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
			$current_colors = array_unique( $hits[0] );
		} else {
			$current_colors = $original_colors;
		}

		if ( isset( $this->post_data ) && isset( $this->post_data['colors'] ) ) {

			$search = $replace = array();
			foreach ( $this->post_data['colors'] as $from => $to ) {

				$to = array_shift( $current_colors );
				if ( $from == $to ) {
					continue;
				}

				// add '#' back to color codes to prevent ERR_CONNECTION_RESET on some Apache (since 2.4.7)
				$search[]  = '#' . str_replace( '#', '', $from );
				$replace[] = $to;
			}

			$content = str_replace( $search, $replace, $content );
		}

		return $content;

	}


}
