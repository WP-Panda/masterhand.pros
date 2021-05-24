<?php

	/**
	 * Private message action class
	 */
	class AE_Private_Message_Actions extends AE_PostAction{
		public static $instance;

		/**
		 * getInstance method
		 *
		 */
		public static function getInstance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * The constructor
		 *
		 * @param void
		 *
		 * @return void
		 * @since  1.0
		 * @author Tambh
		 */
		public function __construct() {
			$this->post_type = 'ae_private_message';
		}

		/**
		 * Init for class AE_Private_Message_Actions
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function init() {
			$this->add_action( 'ae_bid_item_template', 'ae_private_message_add_button', 10, 3 );
			$this->add_action( 'wp_footer', 'ae_private_message_add_template' );
			$this->add_action( 'fre_profile_tabs', 'ae_private_message_add_profile_tab' );
			//$this->add_action( 'fre_profile_tabs_on_mobile', 'ae_private_message_add_profile_tab_on_mobile');
			$this->add_action( 'fre_profile_tab_content_private_message', 'ae_private_message_add_profile_tab_content' );
			//$this->add_action( 'fre_profile_mobile_tab_content', 'ae_private_message_add_profile_tab_content');

			$this->add_filter( 'ae_convert_ae_private_message', 'ae_private_message_convert' );
			$this->add_filter( 'ae_fetch_ae_private_message_args', 'ae_private_message_filter_args', 10, 2 );
			$this->add_filter( 'posts_join', 'ae_private_message_add_join' );
			$this->add_filter( 'posts_search', 'ae_private_message_alter_search', 10, 2 );
			$this->add_filter( 'posts_groupby', 'ae_private_message_posts_groupby', 10, 2 );
			$this->add_filter( 'fre_notify_item', 'ae_private_message_notify_item', 10, 3 );
			$this->add_action( 'fre_header_before_notify', 'ae_private_message_add_notification_menu' );
			// disable from 1.1.5
			// reason:miss the list conversation in page profile.
			// $this->add_filter( 'posts_where' , 'ae_private_message_posts_where', 10, 2);
			$this->add_action( 'posts_where', 'custom_post_where', 10, 2 );
			// $this->add_ajax( 'ae-fetch-replies', 'ae_private_message_fetch_replies');
			$this->add_ajax( 'ae-fetch-conversation', 'fetch_post' );

			$this->add_ajax( 'ae-sync-ae_private_message', 'ae_private_message_sync' );
			$this->add_action( 'ask_final_bid_conversation', 'ae_private_message_sync', 10, 1 );

			$this->add_ajax( 'ae-private-message-get-replies', 'ae_private_message_get_replies_info' );
			$this->add_ajax( 'ae-private-message-get-unread', 'ae_private_message_get_unread' );
			// disable from 1.1.5
			//$this->add_action( 'ae_login_user', 'ae_private_message_update_unread');
			$this->add_filter( 'ae_convert_bid_message', 'ae_private_message_convert_bid' );
			/**
			 * fix enabling pending post
			 */
			$this->add_filter( 'use_pending', 'ae_private_disable_pending_status', 10, 2 );

		}

		/**
		 * get number of unread message
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_get_unread() {
			global $ae_post_factory, $user_ID;
			$post = $ae_post_factory->get( $this->post_type );

			if ( ae_user_role( $user_ID ) == EMPLOYER || ae_user_role( $user_ID ) == 'administrator' ) {
				$array                      = [ 'post_status' => [ 'unread' ], 'posts_per_page' => - 1 ];
				$query_args[ 'meta_query' ] = [
					[
						'key'   => 'is_conversation',
						'value' => 1
					]
				];
			} elseif ( ae_user_role( $user_ID ) == FREELANCER ) {
				$array                      = [ 'post_status' => [ 'unread', 'publish' ], 'posts_per_page' => - 1 ];
				$query_args[ 'meta_query' ] = [
					[
						'key'   => 'is_conversation',
						'value' => 1
					],
					[
						'key'   => 'conversation_status',
						'value' => 'unread'
					]
				];
			}
			$query_args = ae_private_message_default_query_args( $query_args, true );
			$query_args = wp_parse_args( $array, $query_args );
			$data       = $post->fetch( $query_args );
			$data       = empty( $data[ 'query' ]->found_posts ) ? '0' : $data[ 'query' ]->found_posts;
			wp_cache_set( "reply_unread-{$user_id}", $data, 'counts' );
			update_user_meta( $user_ID, 'total_unread', $data );
			wp_send_json( [
				'success' => true,
				'data'    => $data
			] );
		}

		/**
		 * fetch data
		 */
		function fetch_post() {
			global $ae_post_factory, $user_ID;
			$post = $ae_post_factory->get( $this->post_type );

			$page = $_REQUEST[ 'page' ];
			extract( $_REQUEST );

			$thumb = isset( $_REQUEST[ 'thumbnail' ] ) ? $_REQUEST[ 'thumbnail' ] : 'thumbnail';

			$query_args = [
				'paged'       => $page,
				'thumbnail'   => $thumb,
				'post_status' => 'publish',
				'post_type'   => $this->post_type
			];

			// check args showposts
			if ( isset( $query[ 'showposts' ] ) && $query[ 'showposts' ] ) {
				$query_args[ 'showposts' ] = $query[ 'showposts' ];
			}
			$query_args[ 'posts_per_page' ] = - 1;
			if ( isset( $query[ 'posts_per_page' ] ) && $query[ 'posts_per_page' ] ) {
				$query_args[ 'posts_per_page' ] = $query[ 'posts_per_page' ];
			}

			$query_args = $this->filter_query_args( $query_args );
			/**
			 * filter fetch post query args
			 *
			 * @param Array  $query_args
			 * @param object $this
			 *
			 * @since  1.2
			 * @author Dakachi
			 */
			$query_args = apply_filters( 'ae_fetch_' . $this->post_type . '_args', $query_args, $this );

			if ( isset( $query[ 'category_name' ] ) && $query[ 'category_name' ] ) {
				$query_args[ 'category_name' ] = $query[ 'category_name' ];
			}

			//check query post parent
			if ( isset( $query[ 'post_parent' ] ) && $query[ 'post_parent' ] != '' ) {
				$query_args[ 'post_parent' ] = $query[ 'post_parent' ];
			}

			//check query author
			if ( isset( $query[ 'author' ] ) && $query[ 'author' ] != '' ) {
				$query_args[ 'author' ] = $query[ 'author' ];
			}
			if ( isset( $query[ 's' ] ) && $query[ 's' ] ) {
				//search by name of employer or freelancer
				global $wpdb;

				$search_string      = $query[ 's' ];
				$user_role          = ae_user_role( $user_ID );
				$query_select_users = ' SELECT ID FROM  ' . $wpdb->users . ' WHERE display_name LIKE  "%' . $search_string . '%" ';
				$users_found        = $wpdb->get_results( $query_select_users );
				$list_users_id      = [ 0 ];
				if ( $users_found ) {
					foreach ( $users_found as $v ) {
						$list_users_id[] = $v->ID;
					}
				}
				if ( $user_role == 'freelancer' ) {
					$query_args[ 'meta_query' ][] = [
						'key'     => 'from_user',
						'value'   => $list_users_id,
						'compare' => 'IN'
					];
				} else {
					$query_args[ 'meta_query' ][] = [
						'key'     => 'to_user',
						'value'   => $list_users_id,
						'compare' => 'IN'
					];
				}
			}


			/**
			 * fetch data
			 */
			$data = $post->fetch( $query_args );
			ob_start();
			ae_pagination( $data[ 'query' ], $page, $_REQUEST[ 'paginate' ] );
			$paginate = ob_get_clean();

			$display_name  = '';
			$project_link  = '';
			$message_title = '';
			$avatar        = '';
			if ( isset( $query[ 'post_parent' ] ) ) {
				$conversation_id = $query[ 'post_parent' ];
				$from_user       = get_post_meta( $conversation_id, 'from_user', true );
				$to_user         = get_post_meta( $conversation_id, 'to_user', true );
				if ( $user_ID == $from_user ) {
					$avatar       = get_avatar( $to_user );
					$display_name = get_user_meta( $to_user, 'nickname', true );
				}
				if ( $user_ID == $to_user ) {
					$avatar       = get_avatar( $from_user );
					$display_name = get_user_meta( $from_user, 'nickname', true );
				}

				// get Message title
				$message_title = get_post_field( 'post_content', $conversation_id );
				$project_id    = get_post_meta( $conversation_id, 'project_id', true );
				// get link project
				$project_title = "<a class='text-underline' href='" . get_permalink( $project_id ) . "'>" . get_post_field( 'post_title', $conversation_id ) . "</a>";
				$project_link  = __( 'Project', ET_DOMAIN ) . ': ' . $project_title;
			}


			/**
			 * send data to client
			 */
			if ( ! empty( $data ) ) {
				if ( ! empty( $query[ 'post_parent' ] ) ) {
					global $wpdb;
					$querry    = 'SELECT ID FROM ' . $wpdb->posts . '
                WHERE  post_parent = ' . $query[ 'post_parent' ] . ' and post_type ="ae_private_message" GROUP BY DATE_FORMAT(post_date,"%d-%m-%Y")';
					$list_date = $wpdb->get_results( $querry );

					if ( ! empty( $list_date ) && ! empty( $data[ 'posts' ] ) ) {
						foreach ( $list_date as &$d ) {
							$d = $d->ID;
						}
						foreach ( $data[ 'posts' ] as &$v ) {
							if ( in_array( $v->ID, $list_date ) ) {
								$v->first_in_day = 1;
							} else {
								$v->first_in_day = 0;
							}
						}
					}
				}

				wp_send_json( [
					'data'          => $data[ 'posts' ],
					'paginate'      => $paginate,
					'msg'           => __( "Success", ET_DOMAIN ),
					'success'       => true,
					'max_num_pages' => $data[ 'max_num_pages' ],
					'total'         => $data[ 'query' ]->found_posts,
					'message_title' => $message_title,
					'display_name'  => $display_name,
					'project_link'  => $project_link,
					'avatar_user'   => $avatar
				] );
			} else {
				wp_send_json( [
					'success' => false,
					'data'    => []
				] );
			}
		}

		/**
		 * Private message button - Convert Bid
		 *
		 * @param object $result
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   ThanhTu
		 */
		public function ae_private_message_convert_bid( $result ) {
			global $user_ID;
			$to_user      = ae_private_msg_user_profile( (int) $result->post_author );
			$response     = ae_private_message_created_a_conversation( [ 'bid_id' => $result->ID ] );
			$html_message = '';

			if ( $user_ID == (int) $result->project_author && $result->project_status == 'publish' ) {
				if ( $response[ 'success' ] ) {
					$data = [
						'bid_id'        => $result->ID,
						'to_user'       => $to_user,
						'project_id'    => $result->project_id,
						'project_title' => $result->project_title,
						'from_user'     => $user_ID
					];
					ob_start();
					?>
                    <button rel="<?php echo $result->ID; ?>"
                            class="btn btn-link btn-send-msg btn-open-msg-modal"
                            title="">
                        <i class="fa fa-comment"></i><span><?php _e( 'Message', ET_DOMAIN ) ?></span>
                        <script type="data/json" class="privatemsg_data">
                        <?php echo json_encode( $data ) ?>













                        </script>
                    </button>
					<?php
					$html_message = ob_get_clean();

				}
			} else {
				ob_start();
				?>
                <button
                        class="btn btn-link btn-send-msg btn-redirect-msg"
                        title=""
                        data-conversation="<?php if ( isset( $response[ 'conversation_id' ] ) )
							echo $response[ 'conversation_id' ] ?>">
                    <i class="fa fa-comment"></i><span><?php _e( 'Message', ET_DOMAIN ) ?></span>
                </button>
				<?php
				$html_message = ob_get_clean();
			}
			$result->button_message = $html_message;

			return $result;
		}

		/**
		 * Private message button
		 *
		 * @param object $bid
		 * @param object $project
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_button( $bid, $project ) {
			ae_private_message_button( $bid, $project );
		}

		/**
		 * Add private message modal
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_template() {
			ae_private_message_modal();
			ae_private_message_loop_item();
			ae_private_message_redirect();
			ae_private_message_reply_loop_item();
		}

		/**
		 * Sync private message data
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_sync( $ask_final_bid_data = false ) {
			global $user_ID;
			$request = $ask_final_bid_data == false ? $_REQUEST : $ask_final_bid_data;

			// code for detection reading status
			// if send message
			if ( $request[ 'method' ] == 'create' ) {
				if ( ae_user_role( $user_ID ) == EMPLOYER ) {
					update_post_meta( $request[ 'post_parent' ], 'freelancer_has_read', false );
				} else if ( ae_user_role( $user_ID ) == FREELANCER ) {
					update_post_meta( $request[ 'post_parent' ], 'employer_has_read', false );
				}
			} else {
				// if read dialog
				if ( ae_user_role( $user_ID ) == EMPLOYER ) {
					update_post_meta( $request[ 'ID' ], 'employer_has_read', true );
				} else if ( ae_user_role( $user_ID ) == FREELANCER ) {
					update_post_meta( $request[ 'ID' ], 'freelancer_has_read', true );
				}
			}

			if ( isset( $request[ 'sync_type' ] ) && $request[ 'sync_type' ] == 'reply' ) {
				unset( $request[ 'sync_type' ] );
				$this->ae_private_message_sync_reply( $request );
			} else {
				$this->ae_private_message_sync_conversation( $request );
			}

		}

		/**
		 * Validate data
		 *
		 * @param array $data
		 *
		 * @return array $response
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_validate( $data ) {
			global $user_ID;
			if ( ! empty( $data ) ) {
				if ( isset( $data[ 'method' ] ) && $data[ 'method' ] == 'create' ) {
					//check time delay
					$response = $this->ae_private_message_check_time_delay();
					if ( ! $response[ 'success' ] ) {
						return $response;
					}
					$response = ae_private_message_created_a_conversation( $data );
					if ( ! $response[ 'success' ] ) {
						return $response;
					}
					//check sender can send a message
					if ( isset( $data[ 'from_user' ] ) && $data[ 'project_id' ] ) {
						$response = $this->ae_private_message_authorize_sender( $data[ 'from_user' ], $data[ 'project_id' ], $data );
					} else {
						$response = [
							'success' => false,
							'msg'     => __( "Your account can't send this message!", ET_DOMAIN )
						];

						return $response;
					}
					// check receiver can receive a message
					if ( isset( $data[ 'to_user' ] ) && $data[ 'bid_id' ] ) {
						$response = $this->ae_private_message_authorize_receiver( $data[ 'to_user' ], $data[ 'bid_id' ], $data );
					} else {
						$response = [
							'success' => false,
							'msg'     => __( "Your account can't send this message!", ET_DOMAIN )
						];

						return $response;
					}
					//check message content
					$response = $this->ae_private_message_authorize_message( $data );
				}
			} else {
				$response = [
					'success' => false,
					'msg'     => __( 'Data is empty!', ET_DOMAIN )
				];
			}

			return $response;
		}

		/**
		 * authorize sender
		 *
		 * @param integer $user_id
		 * @param integer $project_id
		 * @param array   $data
		 *
		 * @return array $response
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_authorize_sender( $user_id, $project_id, $data ) {
			global $user_ID;
			if ( $user_id == $user_ID ) {
				if ( is_project_owner( $user_ID, $project_id ) ) {
					$response = [
						'success' => true,
						'msg'     => __( "Authorize successful", ET_DOMAIN ),
						'data'    => $data
					];

					return $response;
				}
			}
			$response = [
				'success' => false,
				'msg'     => __( "Your account can't send this message!", ET_DOMAIN )
			];

			return $response;
		}

		/**
		 * authorize receiver
		 *
		 * @param integer $user_id
		 * @param integer $bid_id
		 * @param array   $data
		 *
		 * @return array $response
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_authorize_receiver( $user_id, $bid_id, $data ) {
			global $user_ID;
			if ( $user_id == $user_ID ) {
				if ( is_bid_owner( $user_ID, $bid_id ) ) {
					$response = [
						'success' => true,
						'msg'     => __( "Authorize successful", ET_DOMAIN ),
						'data'    => $data
					];

					return $response;
				}
			}
			$response = [
				'success' => false,
				'msg'     => __( "Your account can't send this message!", ET_DOMAIN )
			];

			return $response;
		}

		/**
		 * authorize message content
		 *
		 * @param array $data
		 *
		 * @return array $response
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_authorize_message( $data ) {
			if ( isset( $data[ 'post_title' ] ) && $data[ 'post_title' ] !== '' ) {
				$response = [
					'success' => true,
					'msg'     => __( "This title is valid!", ET_DOMAIN ),
					'data'    => $data
				];
			} else {
				$response = [
					'success' => false,
					'msg'     => __( "Please enter your message's subject!", ET_DOMAIN )
				];

				return $response;
			}
			if ( isset( $data[ 'post_content' ] ) && $data[ 'post_content' ] != '' ) {
				$response = [
					'success' => true,
					'msg'     => __( "This content is valid!", ET_DOMAIN ),
					'data'    => $data
				];
			} else {
				$response = [
					'success' => false,
					'msg'     => __( "Please enter your message's content!", ET_DOMAIN )
				];

				return $response;
			}

			return $response;
		}

		/**
		 * Send message notification to freelancer
		 *
		 * @param string|array $user_email
		 * @param string       $subject
		 * @param string       $message
		 * @param array        $filter
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_email_notification( $user_email, $subject, $message, $filter ) {
			$ae_mailing = AE_Mailing::get_instance();
			$ae_mailing->wp_mail( $user_email, $subject, $message, $filter );

		}

		/**
		 * Filter message content - send to freelancer whenever has new conversation.
		 *
		 * @param $result convresation id.
		 *
		 * @return string $message
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_get_email_message( $result ) {

			$message = ae_get_option( 'ae_private_message_mail_template' );
			if ( empty( $message ) ) {
				$message = '<p>Hello [display_name],</p><p>You have a new message from [from_user] on [blogname].<p>Message: [private_message]</p>You can view your message via the link: [message_link]</p>';
				//$message = ae_private_message_default_option();
			}
			$message      = str_ireplace( '[from_user]', ae_get_user_display_name( $result->from_user ), $message );
			$profile_link = et_get_page_link( 'profile' );

			$inbox_link   = add_query_arg( 'pr_msg_c_id', $result->ID, et_get_page_link( 'private-message' ) );
			$message_link = sprintf( __( '<a href="%s">here</a>', ET_DOMAIN ), $inbox_link );
			$message      = str_ireplace( '[message_link]', $message_link, $message );
			$message      = str_ireplace( '[private_message]', $result->post_content, $message );
			$message      = apply_filters( 'aepm_content_email', $message, $result );

			return $message;
		}

		/**
		 * Check time delay
		 *
		 * @param string $type conversation | reply
		 *
		 * @return array $response
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_check_time_delay( $type = 'conversation' ) {
			global $user_ID;
			$response = [
				'success' => true
			];
			if ( $type == 'reply' ) {
				$time_delay = (int) ae_get_option( 'ae_private_message_reply_time_delay', 0 );
				if ( $time_delay > 0 ) {
					$user_latest = get_user_meta( $user_ID, 'ae_private_message_reply_latest', true );
					$time_dis    = 0;
					if ( $user_latest ) {
						$time_dis = time() - $user_latest;
					}
					if ( $time_dis < (int) $time_delay && $user_latest > 0 ) {
						$response = [
							'success' => false,
							'msg'     => __( "Please wait a few second for your next message!", ET_DOMAIN )
						];
					}
				}
			} else {
				$time_delay = (int) ae_get_option( 'ae_private_message_time_delay', 0 );
				if ( $time_delay > 0 ) {
					$user_latest = get_user_meta( $user_ID, 'ae_private_message_latest', true );
					$time_dis    = 0;
					if ( $user_latest ) {
						$time_dis = time() - $user_latest;
					}
					if ( $time_dis < (int) $time_delay && $user_latest > 0 ) {
						$response = [
							'success' => false,
							'msg'     => __( "Please wait a few second for your next message!", ET_DOMAIN )
						];
					}
				}
			}

			return $response;
		}

		/**
		 * add profile tab
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_profile_tab() {
			ae_private_message_add_profile_tab_template();
		}
		/*public function ae_private_message_add_profile_tab_on_mobile(){
      ae_private_message_add_profile_tab_template_on_mobile();
    }*/
		/**
		 * add profile tab content
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_profile_tab_content() {
			ae_private_message_add_profile_tab_content_template();
		}

		/**
		 * Convert private message
		 *
		 * @param object $result
		 *
		 * @return object $result
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_convert( $result ) {
			$result->project_name = '';
			$project              = get_post( $result->project_id );
			if ( isset( $project->post_title ) ) {
				$result->project_name = $project->post_title;
			}
			$author                             = $this->ae_private_message_get_author( $result );
			$result->conversation_author_name   = '';
			$result->conversation_author_url    = '';
			$result->conversation_author_avatar = '';
			if ( $author ) {
				$result->conversation_author_name   = $author->display_name;
				$result->conversation_author_url    = $author->author_url;
				$result->conversation_author_avatar = get_avatar( $author->ID, 70 );
			}
			//$result->strip_tag_post_content = strip_tags($result->post_content);
			//$post_date = get_post_time('Y-m-d G:i:s', true, $result->ID);
			$result->post_author_avatar = get_avatar( $result->post_author, 40 );
			//if( $result->last_date == ''){
			//    $result->last_date = $post_date;
			//}
			// $result->last_date = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($result->last_date), time() ));
			// $result->conversation_date = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($post_date), time() ));

			//$result->last_date = sprintf( __( '%s ago', ET_DOMAIN ), human_time_diff( strtotime($result->last_date), time() ));

			//$result->conversation_date = sprintf( __( '%s ago', ET_DOMAIN ), human_time_diff( strtotime($post_date), time() ));

			$result->conversation_latest_reply = $this->ae_private_message_latest_reply( $result );

			if ( $result->is_conversation ) {
				$last_conversation                 = get_posts( [
					'post_parent'    => $result->ID,
					'post_status'    => [ 'publish' ],
					'post_type'      => 'ae_private_message',
					'posts_per_page' => 1,
				] );
				$result->last_conversation_content = '';
				$result->last_conversation_date    = '';
				$result->last_conversation_icon    = '';
				if ( ! empty( $result->conversation_latest_reply ) ) {
					$result->last_conversation_icon = '<i class="fa fa-reply" aria-hidden="true"></i>';
				}
				if ( ! empty( $last_conversation ) ) {
					$last_conversation                 = array_shift( $last_conversation );
					$result->last_conversation_content = strip_tags( $last_conversation->post_content );
					$result->last_conversation_date    = date_i18n( get_option( 'date_format' ), strtotime( $last_conversation->post_date ) );
				}
			}

			$date = date( get_option( 'date_format' ), strtotime( $result->post_date ) );
			if ( $date == date( get_option( 'date_format' ), time() ) ) {
				$date = __( 'Today', ET_DOMAIN );
			}
			$result->date = $date;

			return $result;
		}

		/**
		 * Get conversation author
		 *
		 * @param array $post
		 *
		 * @return array $author
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */

		public function ae_private_message_get_author( $postr ) {
			global $current_user, $user_ID;
			$ae_user = AE_Users::get_instance();
			$user    = $ae_user->convert( $current_user->data );
			if ( $postr->is_conversation == 1 ) {
				if ( $user_ID == $postr->post_author ) {
					$user = get_userdata( $postr->to_user );
				} else {
					$user = get_userdata( $postr->post_author );
				}
			}

			return $user;
		}

		/**
		 * Filter args when fetch data
		 *
		 * @param array $query_args
		 *
		 * @return array $query_args
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_filter_args( $query_args ) {
			global $user_ID;
			extract( $_REQUEST );

			if ( isset( $query[ 'fetch_type' ] ) && $query[ 'fetch_type' ] = 'replies' ) {
				$args       = [ 'post_parent' => $query[ 'post_parent' ] ];
				$args       = ae_private_message_replies_default_query_args( $args );
				$query_args = wp_parse_args( $args, $query_args );
			} else {
				if ( isset( $query[ 'post_status' ] ) && $query[ 'post_status' ] == 'unread' ) {
					$query_args = wp_parse_args( [ 'post_status' => [ 'unread' ] ], $query_args );
				} else {
					$query_args = wp_parse_args( [ 'post_status' => [ 'publish', 'unread' ] ], $query_args );
				}
				$query_args = ae_private_message_default_query_args( $query_args );
				if ( isset( $query[ 'conversation_status' ] ) && $query[ 'conversation_status' ] == 'unread' ) {
					$meta_query                 = [
						[
							'key'   => 'conversation_status',
							'value' => 'unread'
						]
					];
					$query_args[ 'meta_query' ] = wp_parse_args( $meta_query, $query_args[ 'meta_query' ] );
				}
			}

			return $query_args;
		}

		/**
		 * add more join
		 *
		 * @param string $joins
		 *
		 * @return string $joins
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_join( $joins ) {

			if ( empty( $_REQUEST ) ) {
				return $joins;
			}
			extract( $_REQUEST );

			if ( fre_share_role() ) {
				global $wpdb, $wp_query;
				if ( isset( $query[ 'post_type' ] ) && $query[ 'post_type' ] == 'ae_private_message' ) {
					$joins .= " INNER JOIN {$wpdb->postmeta} as postmeta1 ON {$wpdb->posts}.ID = postmeta1.post_id ";
					$joins .= " INNER JOIN {$wpdb->postmeta} as postmeta2 ON {$wpdb->posts}.ID = postmeta2.post_id ";
				}
			}
			if ( ! isset( $query[ 'post_type' ] ) || $query[ 'post_type' ] != 'ae_private_message' ) {
				return $joins;
			}
			if ( isset( $query[ 's' ] ) && $query[ 's' ] != '' ) {
				$joins = $this->ae_private_message_join_clauses( $joins );
			}

			return $joins;
		}


		public function custom_post_where( $where ) {
			global $wp_query, $user_ID;
			if ( empty( $_REQUEST ) ) {
				return $where;
			}
			extract( $_REQUEST );
			if ( fre_share_role() && isset( $query[ 'post_type' ] ) && $query[ 'post_type' ] == 'ae_private_message' ) {
				$where .= " AND (
              ( postmeta1.meta_key = 'archive_on_sender' AND postmeta1.meta_value = '0' AND postmeta2.meta_key = 'from_user' AND postmeta2.meta_value = $user_ID) OR

              ( postmeta1.meta_key = 'archive_on_receiver' AND postmeta1.meta_value = '0' AND postmeta2.meta_key = 'to_user' AND postmeta2.meta_value = $user_ID)
            )";
			}

			return $where;
		}

		/**
		 * Search conversation by project title
		 *
		 * @param string $search
		 * @param mixed  $query
		 *
		 * @return string search
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_alter_search( $search, $qry ) {
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				return $search;
			}
			global $user_ID;
			$s = $qry->get( 's' );
			if ( $s != '' && $qry->get( 'post_type' ) == 'ae_private_message' ) {
				$add    = $this->ae_private_message_search_main_where_clauses( $s );
				$pat    = '|\(\((.+)\)\)|';
				$search = preg_replace( $pat, '((($1) ' . $add . '))', $search );
			}

			return $search;
		}

		/**
		 * Group by post id
		 *
		 * @param string $groupby
		 * @param mixed  $query
		 *
		 * @return string $groupby
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_posts_groupby( $groupby, $query ) {
			if ( $query->get( 's' ) != '' && $query->get( 'post_type' ) == 'ae_private_message' ) {
				global $wpdb;
				$groupby = "{$wpdb->posts}.ID";
			}

			return $groupby;
		}

		public function ae_private_message_set_archive_on_receiver( $request, $value ) {
			$request[ 'archive_on_receiver' ] = $value;
		}

		/**
		 * Check current user can archive this message on their board
		 *
		 * @param array $data
		 *
		 * @return string sender if is employer, receiver if is freelancer and none if else
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_archive_message( $data ) {
			global $user_ID;
			if ( isset( $data[ 'bid_id' ] ) ) {
				delete_post_meta( $data[ 'bid_id' ], 'sent_private_msg' );
			}
			if ( $user_ID == (int) $data[ 'post_author' ] ) {
				$data[ 'archive_on_sender' ] = 1;
			} else if ( $user_ID == (int) $data[ 'to_user' ] ) {
				$data[ 'archive_on_receiver' ] = 1;
			} else {
				return $data;
			}

			return $data;
		}

		/**
		 * Private message join query for freelancer
		 *
		 * @param string $joins
		 *
		 * @return string $joins
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_join_clauses( $joins ) {
			global $user_ID, $wpdb;
			$joins .= " INNER JOIN {$wpdb->postmeta} AS gmt1 ON ({$wpdb->posts}.ID = gmt1.post_id)";

			return $joins;
		}

		/**
		 * private message main where clause for freelancer
		 *
		 * @param void
		 *
		 * @return string $add1
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_freelancer_main_where_clauses() {
			global $wpdb, $user_ID;
			$add1 = '';
			if ( ae_user_role( $user_ID ) == FREELANCER ) {
				$add1 = $wpdb->prepare( "((fmt1.meta_key = 'to_user' AND CAST(fmt1.meta_value AS CHAR) LIKE '%%%s%%') AND (fmt2.meta_key = 'archive_on_receiver' AND CAST(fmt2.meta_value AS CHAR) LIKE '%%%s%%'))", $user_ID, 0 );
			}

			return $add1;
		}

		/**
		 * private message main where clause for employer
		 *
		 * @param void
		 *
		 * @return string $add
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_employer_main_where_clauses() {
			global $wpdb, $user_ID;
			$add = '';
			if ( ae_user_role( $user_ID ) == EMPLOYER || current_user_can( 'manage_options' ) ) {
				$add = $wpdb->prepare( " (emt1.meta_key = 'archive_on_sender' AND CAST(emt1.meta_value AS CHAR) LIKE '%%%s%%')", 0 );
			}

			return $add;
		}

		/**
		 * private message main where clause for all user
		 *
		 * @param void
		 *
		 * @return string $add
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_all_main_where_clauses() {
			global $wpdb;
			$add = '';
			$add = $wpdb->prepare( " (gmt1.meta_key = 'is_conversation' AND CAST(gmt1.meta_value AS CHAR) LIKE '%%%s%%')", 1 );

			return $add;
		}

		/**
		 * private message main search by keywords clause
		 *
		 * @param string $s
		 *
		 * @return string $add
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_search_main_where_clauses( $s ) {
			global $wpdb;
			$add       = '';
			$ae_search = AE_Search::getInstance();
			$s_arr     = $ae_search->ae_parse_search( $s );
			$csearchor = 'OR';
			foreach ( $s_arr as $term ) {
				$like = '%' . $wpdb->esc_like( $term ) . '%';
				$add  .= $wpdb->prepare( "{$csearchor}(gmt1.meta_key = 'project_name' AND CAST(gmt1.meta_value AS CHAR) LIKE %s )", $like );
			}

			return $add;

		}

		/**
		 * check current can update this conversation
		 *
		 * @param array $conversation
		 *
		 * @return bool true if user can update conversation false if can't
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_user_can_update_conversation( $conversation ) {
			global $user_ID;
			if ( ! $user_ID ) {
				return false;
			}
			$user_role = ae_user_role( $user_ID );
			if ( fre_share_role() ) {
				if ( current_user_can( 'manage_options' ) || $user_role == EMPLOYER || $user_role == FREELANCER ) {
					return true;
				}
			} else {
				if ( current_user_can( 'manage_options' ) || $user_role == EMPLOYER ) {
					if ( $user_ID == $conversation[ 'post_author' ] ) {
						return true;
					}
				}
				if ( $user_role == FREELANCER ) {
					if ( $user_ID == $conversation[ 'to_user' ] ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * notication
		 *
		 * @param object $private_message
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_notification( $private_message, $author_id = 0 ) {
			global $user_ID;
			$content      = 'type=new_private_message&project=' . $private_message->project_id . '&sender=' . $user_ID . '&private_message=' . $private_message->post_title . '&private_message_id=' . $private_message->ID;
			$notification = [
				'post_content' => $content,
				'post_excerpt' => $content,
				'post_status'  => 'publish',
				'post_author'  => $author_id,
				'post_type'    => 'notify',
				'post_title'   => sprintf( __( "New private message on project %s ", ET_DOMAIN ), get_the_title( $private_message->ID ) ),
				'post_parent'  => $private_message->ID
			];

			if ( class_exists( 'Fre_Notification' ) ) {
				$fre_noti = Fre_Notification::getInstance();
				$noti     = $fre_noti->insert( $notification );
			}
		}

		public function ae_private_message_notify_item( $content, $notify ) {
			$post_excerpt = str_replace( '&amp;', '&', $notify->post_excerpt );
			parse_str( $post_excerpt, $data );

			extract( $data );

			if ( ! isset( $type ) || ! $type ) {
				return 0;
			}

			if ( $type == 'new_private_message' ) {

				if ( ! empty( $private_message_id ) ) {
					$private_message = get_post( $private_message_id );
					if ( ! empty( $private_message->post_parent ) ) {
						$private_message_id = $private_message->post_parent;
					}
				}

				$project = ! empty( $project ) ? $project : [];

				//$user_profile_id = get_user_meta($sender, 'user_profile_id', true);
				if ( ! isset( $sender ) ) {
					$sender = 1;
				}
				// Text: [Employer] sent you a private message on the project [project_title]
				$message_link = et_get_page_link( 'private-message' ) . '?pr_msg_c_id=' . $private_message_id;
				$message      = sprintf( __( '<strong class="notify-name">%s</strong> sent you a <a href="' . $message_link . '">private message</a> on the project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $sender ), '<strong>' . get_the_title( $project ) . '</strong>' );
				$content      .= ' <div <a class="fre-notify-wrap"> 
                          <span class="notify-avatar">' . get_avatar( $sender, 75 ) . '</span>
                          <span class="notify-info">' . $message . '</span>
                          <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->ID ) ) . '</div>
                          <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->ID ) ) . '</div></div>
                        </div>';
			}

			return $content;
		}

		/**
		 * add more message notification menu on header
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_add_notification_menu() {
			ae_private_message_add_notification_menu_template();
		}

		/**
		 * Get all reply of this conversation
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_get_replies_info() {
			$request  = $_REQUEST;
			$response = [
				'success' => false,
				'msg'     => __( 'There are no reply in this conversation!1', ET_DOMAIN )
			];
			if ( ! isset( $request[ 'post_parent' ] ) || $request[ 'post_parent' ] == '' ) {
				wp_send_json( $response );
			}
			$args = [ 'post_parent' => $request[ 'post_parent' ] ];
			$args = ae_private_message_replies_default_query_args( $args );
			global $ae_post_factory, $post;
			$new_query    = new WP_Query( $args );
			$post_object  = $ae_post_factory->get( 'ae_private_message' );
			$replies_data = [];
			if ( $new_query->have_posts() ) {
				while ( $new_query->have_posts() ) {
					$new_query->the_post();
					$convert        = $post_object->convert( $post );
					$replies_data[] = $convert;
				}
			}
			if ( ! empty( $replies_data ) ) {
				$response = [
					'success' => true,
					'data'    => $replies_data,
					'msg'     => __( 'You got replies list success!', ET_DOMAIN )
				];
			}
			wp_send_json( $response );
		}

		/**
		 * Sync conversation
		 *
		 * @param array $request
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_sync_conversation( $request ) {
			global $ae_post_factory, $user_ID;
			$private_message = $ae_post_factory->get( 'ae_private_message' );
			$response        = [
				'success' => true,
				'data'    => $request
			];
			$success_msg     = '';
			$is_create       = false;
			if ( isset( $request[ 'method' ] ) && $request[ 'method' ] == 'create' ) {
				$response                         = $this->ae_private_message_validate( $request );
				$request[ 'archive_on_receiver' ] = 0;
				$request[ 'archive_on_sender' ]   = 0;
				$request[ 'post_status' ]         = 'unread';
				$response[ 'data' ]               = $request;
				$success_msg                      = __( "Message has been sent", ET_DOMAIN );
				$is_create                        = true;
			}
			if ( $response[ 'success' ] ) {
				$request = $response[ 'data' ];
			} else {
				wp_send_json( $response );
			}
			if ( isset( $request[ 'ID' ] ) && $request[ 'ID' ] != '' ) {
				if ( ! $this->ae_private_message_user_can_update_conversation( $request ) ) {
					wp_send_json( [
						'success' => false,
						'data'    => '',
						'msg'     => _e( 'You can not update this post', ET_DOMAIN )
					] );
				}
				$success_msg = __( "Your message is updated successful!", ET_DOMAIN );
			}
			if ( isset( $request[ 'archive' ] ) && $request[ 'archive' ] == 1 ) {
				$request     = $this->ae_private_message_archive_message( $request );
				$success_msg = __( "Your message is archived successful!", ET_DOMAIN );
			}

			if ( isset( $request[ 'last_date' ] ) ) {
				unset( $request[ 'last_date' ] );
			}

			if ( isset( $request[ 'conversation_date' ] ) ) {
				unset( $request[ 'conversation_date' ] );
			}

			$result = $private_message->sync( $request );
			if ( ! is_wp_error( $result ) ) {
				//send email notification
				$receiver_id     = $result->to_user;
				$user_email      = ae_get_user_email( $receiver_id );
				$conversation_id = $result->ID;

				if ( $user_email && $is_create ) {
					$message     = $this->ae_private_message_get_email_message( $result );
					$author_name = get_the_author_meta( 'display_name', $result->from_user );
					$subject     = sprintf( __( 'You have a new message from %s', ET_DOMAIN ), $author_name );
					// send email to freelancer only - in the action admin create a conversation.
					$this->ae_private_message_email_notification( $user_email, $subject, $message, [ 'user_id' => $receiver_id ] );

					//save the latest time receive email of this user
					update_user_meta( $receiver_id, 'ae_prm_latest_time_receive_email', time() );
				}
				if ( $is_create ) {
					$this->ae_private_message_notification( $result, $receiver_id );

					update_user_meta( $user_ID, 'ae_private_message_latest', time() );
					update_post_meta( $result->bid_id, 'sent_private_msg', $result->ID );
					do_action( 'aepm_created_conversation', $result );
					/**
					 * danng
					 * clone this conversation to a new reply.
					 * 1.1.5
					 **/
					$clone_reply                  = [];
					$clone_reply                  = $request;
					$clone_reply[ 'post_status' ] = 'publish';
					$clone_reply[ 'post_parent' ] = $result->ID;
					$clone_reply[ 'post_type' ]   = 'ae_private_message';
					$clone_reply[ 'meta_input' ]  = [ 'is_conversation' => 0, 'is_conversation_clone' => 1 ];
					$clone_id                     = wp_insert_post( $clone_reply );

					aepm_update_count_message_unread( $receiver_id, $conversation_id );

				} else {
					if ( isset( $request[ 'post_status' ] ) && ( ae_user_role( $user_ID ) == EMPLOYER || current_user_can( 'manage_options' ) ) ) {
						$my_post = [
							'ID'          => $result->ID,
							'post_status' => $request[ 'post_status' ]
						];
						wp_update_post( $my_post );
					}

					aem_reset_count_unread( $result, $user_ID );
					$this->ae_private_message_update_latest_reply( $result );
				}
				wp_send_json( [
					'success' => true,
					'data'    => $result,
					'msg'     => $success_msg
				] );
			} else {
				wp_send_json( [
					'success' => false,
					'data'    => $result,
					'msg'     => $result->get_error_message()
				] );
			}
		}

		/**
		 * Sync reply
		 *
		 * @param array $request
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_sync_reply( $request ) {
			global $ae_post_factory, $user_ID;
			$private_message = $ae_post_factory->get( 'ae_private_message' );
			$is_create       = false;
			if ( isset( $request[ 'method' ] ) && $request[ 'method' ] == 'create' ) {
				$is_create = true;
			}

			if ( $this->user_can_create_this_reply( $request ) && $is_create ) {
				$response = $this->ae_private_message_check_time_delay( 'reply' );
				if ( ! $response[ 'success' ] ) {
					wp_send_json( $response );
				}
				$request[ 'is_conversation' ] = 0;
				$request[ 'post_status' ]     = 'publish';

				$result = $private_message->sync( $request );
				if ( ! is_wp_error( $result ) ) {

					$reply_id        = $result->ID;
					$conversation_id = $result->post_parent;
					update_user_meta( $user_ID, 'ae_private_message_reply_latest', time() );
					$conversation = ae_private_message_get_conversation( $conversation_id );
					// ae_private_message_update_unread_reply($conversation);
					$is_employer_reply = 0;
					$user_id_receiver  = 0;
					// number reply unread of this conversation;
					$number_reply_unread_meta = 'number_reply_emp_unread';

					if ( $conversation->post_author == $user_ID || current_user_can( 'manage_options' ) ) {
						//send to freelancer
						update_post_meta( $result->post_parent, 'conversation_status', 'unread' );
						update_post_meta( $result->post_parent, 'archive_on_receiver', 0 );
						update_post_meta( $result->post_parent, 'employer_latest_reply', $result->post_content );
						$user_id_receiver         = $conversation->to_user;
						$is_employer_reply        = 1;
						$number_reply_unread_meta = 'number_reply_fre_unread';

					} else {

						//send to employer;
						update_post_meta( $result->post_parent, 'archive_on_sender', 0 );
						update_post_meta( $result->post_parent, 'freelancer_latest_reply', $result->post_content );
						$user_id_receiver = $conversation->post_author;
						$mypost           = [
							'ID'          => $conversation_id,
							'post_status' => 'unread',
						];
						wp_update_post( $mypost );
					}
					// uncomment from 1.1.6
					// insert new notification
					$result->project_id = get_post_meta( $conversation_id, 'project_id', true );
					$this->ae_private_message_notification( $result, $user_id_receiver );

					// update number message is unread
					aepm_update_count_message_unread( $user_id_receiver, $conversation_id );
					do_action( 'aepm_inserted_reply', $conversation, $result );

					/**
					 * check time delay and send email to person who just have received this reply.
					 *
					 * @version 1.1.5
					 **/
					$this->ae_private_message_send_reply_email( $conversation, $result );

					wp_send_json( [
						'success' => true,
						'data'    => $result,
						'msg'     => __( "You have created a reply successful!", ET_DOMAIN )
					] );
				} else {
					do_action( 'aepm_inserted_reply_fail', $result );

					wp_send_json( [
						'success' => false,
						'data'    => $result,
						'msg'     => $result->get_error_message()
					] );
				}
			} else if ( $this->user_can_update_this_reply( $request ) ) {
				if ( isset( $request[ 'archive' ] ) && $request[ 'archive' ] == 1 ) {
					$request[ 'post_status' ] = 'archive';
					$result                   = $private_message->sync( $request );

					do_action( 'aepm_archive_reply', $result );

					if ( ! is_wp_error( $result ) ) {

						wp_send_json( [
							'success' => true,
							'data'    => $result,
							'msg'     => __( "You updated this reply successful!", ET_DOMAIN )
						] );
					}
					wp_send_json( [
						'success' => false,
						'data'    => $result,
						'msg'     => $result->get_error_message()
					] );
				} else {
					wp_send_json( [
						'success' => false,
						'data'    => '',
						'msg'     => __( "You can't update this reply!", ET_DOMAIN )
					] );
				}
			} else {
				wp_send_json( [
					'success' => false,
					'data'    => '',
					'msg'     => __( "You can't sync reply!", ET_DOMAIN )
				] );
			}
		}

		/**
		 * check user can sync this reply
		 *
		 * @param array $request
		 *
		 * @return bool true if user can create a reply or false if user can't
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function user_can_create_this_reply( $request ) {
			global $user_ID, $ae_post_factory;
			if ( ! $user_ID ) {
				return false;
			}
			if ( ! isset( $request[ 'post_parent' ] ) || $request[ 'post_parent' ] == '' ) {
				return false;
			}
			$post = get_post( $request[ 'post_parent' ] );
			if ( ! $post ) {
				return false;
			}
			$post_obj     = $ae_post_factory->get( 'ae_private_message' );
			$conversation = $post_obj->convert( $post );
			$response1    = $this->ae_private_message_authorize_sender( $user_ID, $conversation->project_id, $conversation );
			$response2    = $this->ae_private_message_authorize_receiver( $user_ID, $conversation->bid_id, $conversation );
			if ( $response1[ 'success' ] || $response2[ 'success' ] ) {
				return true;
			}

			return false;


		}

		/**
		 * check user can update this reply
		 *
		 * @param array $request
		 *
		 * @return bool true if user can update this reply or false if can't
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function user_can_update_this_reply( $request ) {
			global $user_ID;
			if ( ! $user_ID ) {
				return false;
			}
			if ( ! isset( $request[ 'ID' ] ) ) {
				return false;
			}
			$post = get_post( $request[ 'ID' ] );
			if ( ! $post ) {
				return false;
			}
			if ( $post->post_author == $user_ID && $post->post_type == 'ae_private_message' ) {
				return true;
			}

			return false;


		}

		/**
		 * post where clause
		 *
		 * @param string $where
		 * @param mixed  $qry
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_posts_where( $where, $qry ) {
			$post_parent = $qry->get( 'post_parent' );
			$post_type   = $qry->get( 'post_type' );
			if ( $post_type == 'ae_private_message' && $post_parent != '' ) {
				global $wpdb, $user_ID;
				$add   = $wpdb->prepare( " AND ( ( {$wpdb->posts}.post_parent = %s AND ( {$wpdb->postmeta}.meta_key = 'is_conversation' AND CAST( {$wpdb->postmeta}.meta_value AS CHAR) = %s) ) OR ( {$wpdb->posts}.ID = %s AND ( {$wpdb->postmeta}.meta_key = 'is_conversation' AND CAST( {$wpdb->postmeta}.meta_value AS CHAR) = %s)", $post_parent, 0, $post_parent, 1 );
				$where = explode( " ", $where );
				$where = array_slice( $where, 20 );
				$where = implode( " ", $where );
				$where = $add . $where;
			}

			return $where;
		}

		/**
		 * get latest reply of a conversation
		 *
		 * @param object $conversation
		 *
		 * @return string latest reply content of 0
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_latest_reply( $conversation ) {
			global $user_ID;
			if ( ae_user_role( $user_ID ) == EMPLOYER || current_user_can( 'manage_options' ) ) {
				return $conversation->freelancer_latest_reply;
			} else if ( ae_user_role( $user_ID ) == FREELANCER ) {
				return $conversation->employer_latest_reply;
			}

			return false;

		}

		/**
		 * update latest reply of a conversation
		 *
		 * @param object $conversation
		 *
		 * @return object $conversation
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_update_latest_reply( $conversation ) {
			global $user_ID;
			if ( ae_user_role( $user_ID ) == EMPLOYER || current_user_can( 'manage_options' ) ) {
				update_post_meta( $conversation->ID, 'freelancer_latest_reply', false );
			} else if ( ae_user_role( $user_ID ) == FREELANCER ) {
				update_post_meta( $conversation->ID, 'employer_latest_reply', false );
			}
		}

		/**
		 * update unread message
		 *
		 * @param void
		 *
		 * @return void
		 * @since    1.0
		 * @package  FREELANCEENGINE
		 * @category PRIVATE MESSAGE
		 * @author   Tambh
		 */
		public function ae_private_message_update_unread() {
			global $user_ID, $ae_post_factory;
			$private_obj = $ae_post_factory->get( 'ae_private_message' );
			$args        = [];
			$args        = ae_private_message_default_query_args( $args, true );
			$args        = wp_parse_args( [ 'showposts' => - 1 ], $args );
			$posts       = query_posts( $args );
			$role        = ae_user_role( $user_ID );
			$number      = 0;
			if ( ! empty( $posts ) ) {
				if ( $role == FREELANCER ) {
					foreach ( $posts as $key => $value ) {
						$value = $private_obj->convert( $value );
						if ( $value->freelancer_unread > 0 ) {
							$number += $value->freelancer_unread;
						}
					}
				} elseif ( $role == EMPLOYER || current_user_can( 'manage_options' ) ) {
					foreach ( $posts as $key => $value ) {
						$value = $private_obj->convert( $value );
						if ( $value->employer_unread > 0 ) {
							$number += $value->employer_unread;
						}
					}
				}
				update_user_meta( $user_ID, 'fre_new_private_message', $number );
			}
			wp_reset_query();
		}

		/**
		 * fix issue pending the message when enabling the use_pending option.
		 *
		 * @return  boolean post_status
		 * @author  danng
		 * @version 1.1
		 */
		function ae_private_disable_pending_status( $pending, $post_type ) {
			if ( $post_type == 'ae_private_message' ) {
				$pending = false;
			}

			return $pending;
		}

		/**
		 *check the delay time and send email to the person who just have received a private message.
		 *
		 * @author  danng
		 * @version 1.1.5
		 * @return void
		 **/
		function ae_private_message_send_reply_email( $conversation, $reply ) {

			$author_id        = $reply->post_author;
			$receiver_id      = $conversation->to_user;
			$time_delay       = (int) ae_get_option( 'ae_private_message_email_time_delay', 15 );
			$reply->from_user = $author_id;
			$is_send_mail     = get_theme_mod( 'ae_prm_send_mail', 1 );

			if ( ! $is_send_mail ) {
				return false;
			}

			if ( is_null( $time_delay ) ) {
				$time_delay = 15;
			}

			if ( ae_user_role( $author_id ) == FREELANCER ) {
				//set receiver id by employer id
				$receiver_id = $conversation->post_author;
			}
			$latest_receive_email_time = get_user_meta( $receiver_id, 'ae_prm_latest_time_receive_email', true );

			if ( empty( $latest_receive_email_time ) && isset( $conversation->post_date_gmt ) ) {
				//get the time of conversiation and assign to latest receive email
				$latest_receive_email_time = strtotime( $conversation->post_date_gmt );
			}

			$current_delay = time() - $latest_receive_email_time;

			if ( $current_delay / 60 > $time_delay ) {
				$user_email = ae_get_user_email( $receiver_id );
				if ( $user_email ) {
					$message     = $this->ae_private_message_get_email_message( $reply );
					$author_name = get_the_author_meta( 'display_name', $author_id );
					$subject     = sprintf( __( 'You have a new message from %s', ET_DOMAIN ), $author_name );
					$this->ae_private_message_email_notification( $user_email, $subject, $message, [ 'user_id' => $receiver_id ] );
				}
				update_user_meta( $receiver_id, 'ae_prm_latest_time_receive_email', time() );
			}
		}
	}