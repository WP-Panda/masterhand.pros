<?php

/**
 * Milestone Actions class
 */
class AE_Milestone_Actions extends AE_Base {
	static $instance;

	/**
	 * getInstance method
	 */
	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct method
	 */
	public function __construct() {

	}

	/**
	 * Init method
	 *
	 * @param void
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function init() {
		$this->add_action( 'wp_footer', 'ae_add_milestone_template_js', 100 );

		// Action for submit project
		$this->add_action( 'ae_submit_post_form', 'ae_add_milestone_to_submit_form', 10, 2 );
		$this->add_action( 'ae_insert_project', 'ae_create_milestone_after_insert_project', 10, 2 );

		// Action for edit project
		$this->add_action( 'ae_edit_post_form', 'ae_add_milestone_to_edit_form', 10, 2 );
		$this->add_action( 'ae_update_project', 'ae_update_milestone_after_edit_project', 10, 2 );

		// Action for single project
		$this->add_action( 'after_sidebar_single_project', 'ae_add_milestone_to_single_project_sidebar', 5, 1 );
		$this->add_action( 'after_sidebar_single_project_workspace', 'ae_add_milestone_to_workspace', 10, 1 );
		$this->add_action( 'after_mobile_project_workspace', 'ae_add_milestone_to_workspace_mobile', 10, 1 );
		$this->add_action( 'after_mobile_single_project', 'ae_add_milestone_to_single_project_sidebar', 10, 1 );

		$this->add_action( 'ae_insert_ae_milestone', 'ae_after_insert_milestone', 10, 2 );

		// Ajax request
		$this->add_ajax( 'ae_sync_milestone', 'ae_milestone_sync' );
		$this->add_ajax( 'ae_fetch_milestones', 'ae_fetch_milestones' );

		// Filters
		$this->add_filter( 'ae_convert_message', 'ae_add_milestone_changelog_meta', 9, 1 );
		$this->add_filter( 'ae_convert_ae_milestone', 'ae_add_milestone_meta', 10, 1 );
		$this->add_filter( 'fre_notify_item', 'ae_render_milestone_notification', 10, 3 );

		// Heartbeat
		$this->add_filter( 'heartbeat_received', 'ae_milestone_send_data_to_heartbeat', 10, 2 );
	}

	/**
	 * Add heartbeat for changelog
	 *
	 * @param array $response
	 * @param array $data
	 *
	 * @return array $response
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_milestone_send_data_to_heartbeat( $response, $data ) {
		if ( isset( $data['new_changelog'] ) && isset( $data['new_message'] ) ) {
			$project                             = get_post( $data['new_message'] );
			$response['milestone_new_changelog'] = ae_get_project_new_changelog_meta( $project );
			$response['milestone_project_id']    = $data['new_message'];
		}

		return $response;
	}

	/**
	 * Function render milestone list and adding form to submit project form
	 *
	 * @param string $post_type
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_to_submit_form( $post_type ) {
		if ( $post_type == PROJECT ) {
			ae_milestone_template_for_submit_form();
		}
	}

	/**
	 * Create milestone after inster project
	 *
	 * @param int $result Project ID
	 * @param array $args Milestone request value
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_create_milestone_after_insert_project( $result, $args ) {
		if ( isset( $args['milestones'] ) && ! empty( $args['milestones'] ) ) {
			global $ae_post_factory;
			$milestone = $ae_post_factory->get( 'ae_milestone' );

			$i = 0;
			foreach ( $args['milestones'] as $post_title ) {
				$res_milestone = $milestone->sync( array(
					'post_title'     => $post_title,
					'post_status'    => 'open',
					'post_parent'    => $result,
					'class_label'    => 'open',
					'status_label'   => 'Open',
					'position_order' => $i,
					'method'         => 'create',
				) );
				wp_update_post( array( 'ID' => $res_milestone->ID, 'post_status' => 'open' ) );
				$i ++;
			}
		}
	}

	/**
	 * Update meta value index
	 *
	 * @param int $result
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_after_insert_milestone( $result ) {
		update_post_meta( $result, 'index', $result );
	}

	/**
	 * Function render milestones in edit project
	 *
	 * @param string $post_type
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_to_edit_form( $post_type ) {
		if ( $post_type == PROJECT ) {
			ae_milestone_template_for_edit_form();
		}
	}

	/**
	 * Function update milestone when edit project
	 *
	 * @param int $result Project ID
	 * @param array $args Milestone request value
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_update_milestone_after_edit_project( $result, $args ) {
		if ( isset( $args['milestones_id'] ) && ! empty( $args['milestones_id'] ) && isset( $args['milestones'] ) && ! empty( $args['milestones'] ) ) {

			// Check privilege when update milestone
			$project = get_post( $result );
			if ( $project->post_status != 'publish' && $project->post_status != 'pending' && $project->post_status != 'draft' ) {
				wp_send_json( array(
					'success' => false,
					'msg'     => __( "Cannot update milestone when project is in process" )
				) );
			}

			global $ae_post_factory;
			$milestone = $ae_post_factory->get( 'ae_milestone' );

			// Array of milestone id
			$milestones_id = $args['milestones_id'];

			// Array of milestone title
			$milestones = $args['milestones'];

			$milestone_count = count( $milestones );


			for ( $i = 0; $i < $milestone_count; $i ++ ) {
				if ( isset( $milestones_id[ $i ] ) && isset( $milestones[ $i ] ) && ! empty( $milestones[ $i ] ) ) {
					if ( ! empty( $milestones_id[ $i ] ) ) {
						$post = $milestone->sync( array(
							'ID'             => $milestones_id[ $i ],
							'post_title'     => $milestones[ $i ],
							'post_content'   => '',
							'position_order' => $i,
							'method'         => 'update',
						) );

						if ( is_wp_error( $post ) ) {
							wp_send_json( array(
								'success' => false,
								'msg'     => __( 'Cannot update milestone', ET_DOMAIN )
							) );
						}
					} else {
						$post = $milestone->sync( array(
							'post_title'     => $milestones[ $i ],
							'post_status'    => 'open',
							'post_parent'    => $result,
							'position_order' => $i,
							'method'         => 'create',
						) );

						if ( is_wp_error( $post ) ) {
							wp_send_json( array(
								'success' => false,
								'msg'     => __( 'Cannot create milestone', ET_DOMAIN )
							) );
						}
					}
				}
			}

			// Send email notify the changes of scope
			$bidder_email = $this->ae_get_bidder_emails( $result );

			if ( $bidder_email ) {
				$this->ae_send_email_to_bidders( $bidder_email, $result );
			}
		}
	}

	/**
	 * Add milestone template for backbone usage
	 *
	 * @param void
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_template_js() {
		?>
        <script type="text/template" id="milestone_template">
            <span class="arrow-milestone-item"><i class="fa fa-arrows-v"></i></span>
            <input type="text" data-index="{{= position_order }}" class="txt-milestone-item" value="{{= post_title }}">
            <a class="btn-del-milestone-item" href=""><i class="fa fa-remove"></i></a>
        </script>
		<?php
	}

	/**
	 * Add milestone list view to single project
	 *
	 * @param object $project The convert of project post data
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_to_single_project_sidebar( $project ) {
		ae_milestone_template_for_single_project( $project );
	}

	/**
	 * Add milestone list view to workspace
	 *
	 * @param object $project The convert of project post data
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_to_workspace( $project ) {
		ae_milestone_template_for_workspace( $project );
	}

	/**
	 * Add milestone list view to workspace on mobile
	 *
	 * @param object $project The convert of project post data
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_to_workspace_mobile( $project ) {
		ae_milestone_template_for_workspace_mobile( $project );
	}


	/**
	 * Sync milestone data
	 *
	 * @param void
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_milestone_sync() {
		$request = $_REQUEST;
		$result  = "";
		global $ae_post_factory, $user_ID;

		// Get employer and freelancer ID
		$bid_accepted   = get_post_meta( $request['post_parent'], 'accepted', true );
		$bid_author     = get_post_field( 'post_author', $bid_accepted );
		$project_author = get_post_field( 'post_author', $request['post_parent'] );

		// Get project status
		$project_status = get_post_field( 'post_status', $request['post_parent'] );

		// Update project meta
		$project = get_post( $request['post_parent'] );
		ae_update_project_new_changelog_meta( $project );

		// Set permission for freelancer who a author.
		if ( ( current_user_can( 'manage_options' ) || $user_ID == $bid_author ) && $request['do_action'] == 'resolve_milestone' ) {
			$result = $this->ae_update_milestone( $request );
		} elseif ( current_user_can( 'manage_options' ) || $user_ID == $project_author ) {
			$milestone = $ae_post_factory->get( 'ae_milestone' );

			// Check project status
			if ( $project_status != 'complete' && $project_status != 'disputing' ) {
				$result = $milestone->sync( $request );
			} else {
				wp_send_json( array(
					'success' => false,
					'msg'     => __( "Project is in process", ET_DOMAIN )
				) );
			}

		}

		if ( ! is_wp_error( $result ) ) {
			switch ( $request['do_action'] ) {
				case 'resolve_milestone':
					// Send email
					$message = ae_get_option( 'ae-resolve-milestone-mail-template', RESOLVE_MAIL );
					$this->ae_milestone_mail_notification( $project_author, $bid_author, $result, $message, 'resolve' );

					$msg = __( 'You have resolved a milestone.', ET_DOMAIN );
					$this->ae_milestone_action_response( $result, $request['post_parent'], 'resolve', $msg, $project_author );
					break;

				case 'close_milestone':
					// Send email
					$message = ae_get_option( 'ae-close-milestone-mail-template', CLOSE_MAIL );
					$this->ae_milestone_mail_notification( $project_author, $bid_author, $result, $message, 'close' );

					$msg = __( 'You have closed a milestone.', ET_DOMAIN );
					$this->ae_milestone_action_response( $result, $request['post_parent'], 'close', $msg, $bid_author );
					break;

				case 'reopen_milestone':
					// Send email
					$message = ae_get_option( 'ae-reopen-milestone-mail-template', REOPEN_MAIL );
					$this->ae_milestone_mail_notification( $project_author, $bid_author, $result, $message, 'reopen' );

					$msg = __( 'You have re-opended milestone.', ET_DOMAIN );
					$this->ae_milestone_action_response( $result, $request['post_parent'], 'reopen', $msg, $bid_author );
					break;
			}

			wp_send_json( array(
				'success' => true,
				'data'    => $result,
				'msg'     => __( 'Success.', ET_DOMAIN )
			) );
		} else {
			wp_send_json( array(
				'success' => false,
				'data'    => $result,
				'msg'     => __( 'Error.', ET_DOMAIN )
			) );
		}
	}

	/**
	 * Send response data to client after marking an action
	 *
	 * @param object $result Milestone post data
	 * @param int $post_parent Project ID
	 * @param string $action Milestone action
	 * @param string $message Email content
	 * @param int $user_id Notification author ID
	 *
	 * @return void
	 */
	public function ae_milestone_action_response( $result, $post_parent, $action, $message, $user_id ) {
		$comment = $this->ae_add_changelog( $result->ID, $post_parent, $action );

		if ( is_wp_error( $comment ) ) {
			wp_send_json( array(
				'success' => false,
				'msg'     => __( 'Cannot create changelog.', ET_DOMAIN )
			) );
		}

		// Add notification
		$notify = $this->ae_add_notification_for_milestone_action( $action, $result, $post_parent, $user_id );

		if ( is_wp_error( $notify ) ) {
			wp_send_json( array(
				'success' => false,
				'msg'     => __( 'Cannot create notification.' . ET_DOMAIN )
			) );
		}

		wp_send_json( array(
			'success' => true,
			'data'    => $result,
			'msg'     => $message,
		) );
	}

	/**
	 * Update milestone action
	 * Based on function update of AE_Posts
	 *
	 * @param object $args
	 *
	 * @return object $result
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_update_milestone( $args ) {
		global $ae_post_factory;
		$post_object = $ae_post_factory->get( 'ae_milestone' );
		$milestone   = AE_Milestone_Posttype::getInstance();

		// unset post date
		if ( isset( $args['post_date'] ) ) {
			unset( $args['post_date'] );
		}

		if ( is_wp_error( $args ) ) {
			return $args;
		}

		// if missing ID, return errors
		if ( empty( $args['ID'] ) ) {
			return new WP_Error( 'ae_missing_ID', __( 'Post not found!', ET_DOMAIN ) );
		}

		// update post data into database use wordpress function
		$result = wp_update_post( $args, true );

		if ( $result != false && ! is_wp_error( $result ) ) {
			// update post meta
			if ( ! empty( $milestone->meta_data ) ) {
				foreach ( $milestone->meta_data as $key => $meta ) {
					// do not update expired date
					if ( $meta == 'et_expired_date' ) {
						continue;
					}

					if ( isset( $args[ $meta ] ) ) {
						if ( ! is_array( $args[ $meta ] ) ) {
							$args[ $meta ] = esc_attr( $args[ $meta ] );
						}
						update_post_meta( $result, $meta, $args[ $meta ] );
					}
				}
			}

			$post   = get_post( $result );
			$result = $post_object->convert( $post );
		}

		return $result;
	}

	/**
	 * Get milestone by post parent and response to client
	 *
	 * @param object void
	 *
	 * @return object
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_fetch_milestones() {
		global $ae_post_factory;
		$post_object = $ae_post_factory->get( 'ae_milestone' );
		$request     = $_REQUEST;

		if ( isset( $request['post_parent'] ) && ! empty( $request['post_parent'] ) ) {
			$project = get_post( $request['post_parent'] );
			$posts   = get_posts( array(
				'post_type'      => 'ae_milestone',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'post_parent'    => $project->ID,
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'meta_key'       => 'position_order'
			) );

			// Reset new change log meta
			ae_reset_project_new_changelog_meta( $project );

			$milestones = array();
			foreach ( $posts as $post ) {
				$milestones[] = $post_object->convert( $post );
			}

			if ( $posts ) {
				wp_send_json( array(
					'success' => true,
					'posts'   => $milestones,
				) );
			} else {
				wp_send_json( array(
					'success' => false,
					'posts'   => array()
				) );
			}
		} else {
			wp_send_json( array(
				'success' => false,
				'msg'     => 'No project id found!'
			) );
		}
	}

	/**
	 * Add milestone validation
	 * Limit amount of milestones
	 *
	 * @param int $project_id Project ID
	 *
	 * @return boolean
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_validate( $project_id ) {
		$posts = get_posts( array(
			'post_type'      => 'ae_milestone',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_parent'    => $project_id,
		) );

		$maximum_milestone = MAX_MILESTONE;

		if ( count( $posts ) >= $maximum_milestone ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Send message notification
	 *
	 * @param int $employer Employer ID
	 * @param int $freelancer Freelancer ID
	 * @param object $milestone Milestone data
	 * @param string $message Mail content
	 * @param string $action Milestone action (reopen, resolve, close, remove...)
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_milestone_mail_notification( $employer, $freelancer, $milestone, $message, $action ) {
		// Get sender, receiver name
		$employer_name   = get_the_author_meta( 'display_name', $employer );
		$freelancer_name = get_the_author_meta( 'display_name', $freelancer );

		$project = get_post( $milestone->post_parent );
		$subject = "";
		switch ( $action ) {
			case 'reopen':
				$subject = sprintf( __( "[Re-open milestone] %s has re-opened milestone: %s", ET_DOMAIN ), $employer_name, $milestone->post_title );
				break;

			case 'resolve':
				$subject = sprintf( __( "[Resolve milestone] %s has resolved milestone: %s", ET_DOMAIN ), $freelancer_name, $milestone->post_title );
				break;

			case 'close':
				$subject = sprintf( __( "[Close milestone] %s has closed milestone: %s", ET_DOMAIN ), $employer_name, $milestone->post_title );
				break;
		}

		// Get receiver email
		if ( in_array( $action, array( 'reopen', 'close' ) ) ) {
			$receiver_email = get_the_author_meta( 'user_email', $freelancer );
		} else {
			$receiver_email = get_the_author_meta( 'user_email', $employer );
		}

		// employer, freelancer, milestone_name
		$project_link = '<a href="' . get_permalink( $project->ID ) . '?workspace=1">' . $project->post_title . '</a>';
		$message      = str_ireplace( '[employer]', $employer_name, $message );
		$message      = str_ireplace( '[freelancer]', $freelancer_name, $message );
		$message      = str_ireplace( '[milestone_name]', $milestone->post_title, $message );
		$message      = str_ireplace( '[project]', $project_link, $message );

		$ae_mailing = AE_Mailing::get_instance();
		$ae_mailing->wp_mail( $receiver_email, $subject, $message );
	}

	/**
	 * Send email to all bidders
	 *
	 * @param array $bidder_email Collection of bidder emails
	 * @param int $project_id Project ID
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_send_email_to_bidders( $bidder_email, $project_id ) {
		//Get project
		$project = get_post( $project_id );

		// Get employer name
		$employer_name = get_the_author_meta( 'display_name', $project->post_author );

		// Get massage
		$message      = ae_get_option( 'ae-update-milestone-mail-template', UPDATE_MAIL );
		$project_link = '<a href="' . get_permalink( $project->ID ) . '">' . $project->post_title . '</a>';
		$message      = str_ireplace( '[employer]', $employer_name, $message );
		$message      = str_ireplace( '[project]', $project_link, $message );

		// Email subject
		$subject = sprintf( __( 'Project "%s" has been updated', ET_DOMAIN ), $project->post_title );

		// Send email
		$ae_mailing = AE_Mailing::get_instance();
		$ae_mailing->wp_mail( $bidder_email, $subject, $message );
	}

	/**
	 * Get email of all bidders
	 *
	 * @param int $project_id Project ID
	 *
	 * @return array $bidder_emails    Collection of bidder emails
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_get_bidder_emails( $project_id ) {
		$bids = get_posts( array(
			'post_type'      => 'bid',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_parent'    => $project_id,
		) );

		$bidder_emails = array();

		foreach ( $bids as $bid ) {
			$bidder_id       = get_post_field( 'post_author', $bid->ID );
			$bidder_emails[] = get_the_author_meta( 'user_email', $bidder_id );
		}

		return $bidder_emails;
	}

	/**
	 * Add changelog when complete an action
	 *
	 * @param int $milestone_id Milestone ID
	 * @param int $project_id Project ID
	 * @param string $action The type of milestone action
	 *
	 * @return object $changelog            The return of changelog data
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_changelog( $milestone_id, $project_id, $action ) {
		$milestone = get_post( $milestone_id );
		$comment_content = '';
		if ( $action == 'reopen' ) {
			$comment_content = sprintf( __( 'has reopened the %s section', ET_DOMAIN ), $milestone->post_title );
		} else if ( $action == 'resolve' ) {
			$comment_content = sprintf( __( 'has resolved the %s section', ET_DOMAIN ), $milestone->post_title );
		} else if ( $action == 'close' ) {
			$comment_content = sprintf( __( 'has closed the %s section', ET_DOMAIN ), $milestone->post_title );
		}
		$args                = array(
			'comment_post_ID'      => $project_id,
			'comment_approved'     => 1,
			'comment_content'      => $comment_content,
			'type'                 => 'message',
			'changed_milestone_id' => $milestone_id,
			'action'               => $action,
			'changelog'            => 1
		);
		$comment             = new AE_Comments( 'message', array(
			'changed_milestone_id' => '',
			'action'               => '',
			'changelog'            => ''
		) );
		$comment->duplicate  = true;
		$comment->limit_time = 0;
		$changelog           = $comment->insert( $args );

		// Update new message meta for project
		$project        = get_post( $project_id );
		$message_action = Fre_MessageAction::get_instance();
		$message_action->fre_update_project_meta( $project );

		return $changelog;
	}

	/**
	 * Add meta data for change log
	 *
	 * @param object $comment The comment with type is message
	 *
	 * @return object $comment        The return of comment after adding meta
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_changelog_meta( $comment ) {
		global $current_user;

		// Change log data
		$changelog_data = get_comment_meta( $comment->comment_ID );
		$milestone_id   = isset( $changelog_data['changed_milestone_id'][0] ) ? $changelog_data['changed_milestone_id'][0] : '';
		$action         = isset( $changelog_data['action'][0] ) ? $changelog_data['action'][0] : '';
		$is_changelog   = isset( $changelog_data['changelog'][0] ) ? $changelog_data['changelog'][0] : '';

		// Get milestone
		$milestone = get_post( $milestone_id );

		// Get comment author
		$author_name = get_the_author_meta( 'display_name', $comment->user_id );

		/*if ( $comment->user_id == $current_user->ID ) {
			$author_name = __( 'You', ET_DOMAIN );
		}*/

		$comment->changed_milestone_id = $milestone_id;
		$comment->action               = $action;
		$comment->changelog            = $is_changelog;
		$comment->milestone_title      = ! empty( $milestone ) ? $milestone->post_title : '';
		$comment->author_name          = $author_name;

		return $comment;
	}

	/**
	 * Add meta data for milestone when convert
	 *
	 * @param object $result The convert of milestone post data
	 *
	 * @return object $result        The return of milestone after adding meta
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_milestone_meta( $result ) {
		switch ( $result->post_status ) {
			case 'open':
				$result->status_label = __( 'Open', ET_DOMAIN );
				$result->class_label  = 'opened';
				break;

			case 'reopen':
				$result->status_label = __( 'Re-opened', ET_DOMAIN );
				$result->class_label  = 'opened';
				break;

			case 'resolve':
				$result->status_label = __( 'Resolved', ET_DOMAIN );
				$result->class_label  = 'resolved';
				break;

			case 'done':
				$result->status_label = __( 'Closed', ET_DOMAIN );
				$result->class_label  = 'closed';
				break;
		}

		return $result;
	}

	/**
	 * Add notification for user when do a milestone action
	 *
	 * @param string $action Type of acton (reopen, resolve, close)
	 * @param object $milestone The convert of milestone post data
	 * @param string $project_id Project ID
	 * @param string $user_id The user that notification belong to
	 *
	 * @return object notification
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_add_notification_for_milestone_action( $action, $milestone, $project_id, $user_id ) {
		$project        = get_post( $project_id );
		$notify_content = "";
		$post_title     = "";
		switch ( $action ) {
			case 'reopen':
				$notify_content = sprintf( 'type=reopen_milestone&milestone=%s&project=%s', $milestone->ID, $project_id );
				$post_title     = sprintf( __( 'Milestone %s of project %s was re-opened', ET_DOMAIN ), $milestone->post_title, $project->post_title );
				break;

			case 'resolve':
				$notify_content = sprintf( 'type=resolve_milestone&milestone=%s&project=%s', $milestone->ID, $project_id );
				$post_title     = sprintf( __( 'Milestone %s of project %s was resolved', ET_DOMAIN ), $milestone->post_title, $project->post_title );
				break;

			case 'close':
				$notify_content = sprintf( 'type=close_milestone&milestone=%s&project=%s', $milestone->ID, $project_id );
				$post_title     = sprintf( __( 'Milestone %s of project %s was closed', ET_DOMAIN ), $milestone->post_title, $project->post_title );
				break;

			case 'create':
				$notify_content = sprintf( 'type=create_milestone&milestone=%s&project=%s', $milestone->ID, $project_id );
				$post_title     = sprintf( __( 'Milestone %s of project %s was created', ET_DOMAIN ), $milestone->post_title, $project->post_title );
				break;
		}

		$notify       = Fre_Notification::getInstance();
		$notification = $notify->insert( array(
			'post_type'    => 'notify',
			'post_status'  => 'publish',
			'post_author'  => $user_id,
			'post_content' => $notify_content,
			'post_excerpt' => $notify_content,
			'post_parent'  => $project_id,
			'post_title'   => $post_title
		) );

		return $notification;
	}

	/**
	 * Hook to render notification for milestone
	 *
	 * @param string $content
	 * @param object $notify
	 *
	 * @return string $content
	 *
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category MILESTONE
	 * @author tatthien
	 */
	public function ae_render_milestone_notification( $content, $notify ) {
		$post_excerpt = $notify->post_excerpt;
		$post_excerpt = str_replace( '&amp;', '&', $post_excerpt );
		$type         = "";
		$project      = "";
		$milestone    = "";
		parse_str( $post_excerpt );

		$content = ae_milestone_notification_template( $type, $milestone, $project, $content, $notify );

		return $content;
	}

}