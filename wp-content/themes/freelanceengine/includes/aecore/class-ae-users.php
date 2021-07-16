<?php

	// if(!defined('ET_DOMAIN')) {
	//  wp_die('API NOT SUPPORT');
	//}


	/**
	 * Class AE users, control all action with user data
	 *
	 * @author  Dakachi
	 * @version 1.0
	 * @package AE
	 * @since   1.0
	 */
	class AE_Users{
		static $instance;
		public $current_user;

		/**
		 * return class $instance
		 */
		public static function get_instance() {
			if ( null == self::$instance ) {

				self::$instance = new AE_Users();
			}

			return self::$instance;
		}

		/**
		 * contruct a object user with meta data
		 *
		 * @param array $meta_data all user meta data you want to control
		 *
		 * @author Dakachi
		 * @since  1.0
		 */
		public function __construct( $meta_data = [] ) {
			$defaults = [
				'location',
				'address',
				'user_phone',
				'et_avatar',
				'et_avatar_url',
				'post_count',
				'comment_count',
				'hour_rate',
				'facebook',
				'twitter',
				//            new start
				'register_status',
				//            new end
				'banned',
			];

			global $wpdb;

			if ( $wpdb->blogid > 0 ) {
				$this->meta_ban_expired = 'et_' . $wpdb->blogid . '_ban_expired';
				$this->meta_ban_note    = 'et_' . $wpdb->blogid . '_ban_note';
				$ban_meta               = [
					$this->meta_ban_expired,
					$this->meta_ban_note
				];
				$defaults               = wp_parse_args( $defaults, $ban_meta );
			} else {
				$this->meta_ban_expired = 'et_ban_expired';
				$this->meta_ban_note    = 'et_ban_note';
				$ban_meta               = [
					$this->meta_ban_expired,
					$this->meta_ban_note
				];
				$defaults               = wp_parse_args( $defaults, $ban_meta );
			}

			$this->meta_data = wp_parse_args( $meta_data, $defaults );
			$this->meta_data = apply_filters( 'ae_define_user_meta', $this->meta_data );
		}

		/**
		 * get userdata from id
		 *
		 * @param string $id
		 *
		 * @return user userdata object after convert
		 *         - wp_error object if id invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function get( $id ) {
			$user      = get_userdata( $id );
			$user->msg = __( 'Get user data successfully!', ET_DOMAIN );

			return $this->convert( $user );
		}

		/**
		 * convert userdata to an object
		 *
		 * @param object $user
		 *
		 * @return user object after convert
		 *         - wp_error object if user invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function convert( $user ) {
			global $current_user, $user_ID;
			if ( ! isset( $user->ID ) || ! $user->ID ) {
				return new WP_Error( 'ae_invalid_user_data', __( "Input invalid", ET_DOMAIN ) );
			}
			$result = isset( $user->data ) ? $user->data : $user;

			foreach ( $this->meta_data as $key ) {
				$result->$key = get_user_meta( $result->ID, $key, true );
			}

			$result->avatar        = get_avatar( $user->ID, '150' );
			$result->avatar_mobile = get_avatar( $user->ID, '36' );

			$result->join_date = sprintf( __( "Join on %s", ET_DOMAIN ), (string) date( get_option( 'date_format' ), strtotime( $user->user_registered ) ) );

			/**
			 * get user role
			 */
			if ( current_user_can( 'edit_users' ) && isset( $user->roles ) ) {
				$user_role     = $user->roles;
				$result->role  = array_pop( $user_role );
				$result->roles = $user->roles;
			}

			/**
			 * get all user meta data
			 */
			$author_metas = [
				'display_name',
				'first_name',
				'last_name',
				'description',
				'user_url',
				//new start
				'user_new_email'
				//new end
			];
			foreach ( $author_metas as $key => $author_meta ) {
				$result->$author_meta = get_the_author_meta( $author_meta, $result->ID );
			}

			$result->label      = sprintf( __( 'Logged in as <span class="name">%s<span>', ET_DOMAIN ), $result->display_name );
			$result->author_url = get_author_posts_url( $result->ID );

			//new
			//        $result->pro_url=home_url( '/' );
			//new

			// update ajax nonce
			if ( $current_user->ID == $result->ID ) {
				// temporary use de create nonce to find solution
				$result->ajaxnonce     = de_create_nonce( 'ad_carousels_et_uploader' );
				$result->logoajaxnonce = de_create_nonce( 'user_avatar_et_uploader' );
			}

			/**
			 * return post count
			 */
			$result->post_count = count_user_posts( $result->ID );

			/**
			 * return comment count
			 */
			if ( isset( $result->user_email ) && $result->user_email !== '' ) {
				$result->comment_count = comment_count( $result->user_email );
			} else {
				$result->comment_count = 0;
			}
			if ( ! current_user_can( 'edit_users' ) && $user_ID != $result->ID ) {
				unset( $result->role );
				unset( $result->user_email );
			}
			// unset($result->user_email);

			//$result->id = $result->ID;

			// Convert banned
			if ( $this->is_ban( $result ) ) {
				$result->banned = true;
			} else {
				$result->banned = false;
			}

			// Convert ban info
			$ban_info            = $this->get_ban_info( $result->ID );
			$result->ban_expired = $ban_info[ 'expired' ];
			$result->ban_note    = $ban_info[ 'note' ];

			unset( $result->user_pass );
			unset( $result->user_activation_key );

			$this->current_user = apply_filters( 'ae_convert_user', $result );

			return $this->current_user;
		}

		/**
		 * get user avatar url
		 *
		 * @uses   function get_user_meta
		 * @uses   function get_intermediate_image_sizes
		 * # user AE_Users function convert
		 *
		 * @param   Int /string $id_or_email User id or email
		 * @param array $size
		 * # wordpress user fields data
		 * # user custom meta data
		 *
		 * @return  String user avatar url
		 * # wp_error object if user data invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function get_avatar( $id_or_email, $size, $default = '' ) {

			if ( $id_or_email ) {
				$user = get_userdata( $id_or_email );
				/**
				 * get avatar by user upload
				 */
				$img = get_user_meta( $user->ID, 'et_avatar_url', true );
				if ( $img == '' ) {

					$img = $this->update_avatar( $user->ID );
				}
				if ( $img != '' ) {
					return $img;
				}
				/**
				 * get default avatar from admin settings
				 */
				$default_avatar = ae_get_option( 'default_avatar', '' );
				if ( $default_avatar ) {
					return $default_avatar[ 'thumbnail' ][ 0 ];
				}
				/**
				 * get user avatar from gravatar by email
				 */
				$email = $user->user_email;

				$email_hash = md5( strtolower( trim( $email ) ) );

				if ( is_ssl() ) {
					$host = 'https://secure.gravatar.com';
				} else {
					if ( ! empty( $email ) ) {
						$host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash[ 0 ] ) % 2 ) );
					} else {
						$host = 'http://0.gravatar.com';
					}
				}

				$out = "$host/avatar/";
				$out .= $email_hash;
				$out .= '?s=' . $size;
				$out .= '&amp;d=' . urlencode( $default );

				$rating = get_option( 'avatar_rating' );
				if ( ! empty( $rating ) ) {
					$out .= "&amp;r={$rating}";
				}
				$default = $out;
			}

			return $default;
		}

		/**
		 * update user avatar image
		 *
		 * @param integer $user_id
		 *
		 * @return void
		 * @since    1.0
		 * @package  Appengine
		 * @category void
		 * @author   Daikachi
		 */
		function update_avatar( $user_id ) {
			$avatar = get_user_meta( $user_id, 'et_avatar', true );
			if ( $avatar != '' ) {
				$img = wp_get_attachment_image_src( $avatar, 'thumbnail' );
				$img = $img[ 0 ];

				update_user_meta( $user_id, 'et_avatar_url', $img );

				return $img;
			}
		}

		/**
		 * insert userdata and user metadata to an database
		 * # used wp_insert_user
		 * # used update_user_meta
		 * # user AE_Users function convert
		 *
		 * @param   array $user_data
		 * # WordPress user fields data
		 * # user custom meta data
		 *
		 * @return  user object after insert
		 * # wp_error object if user data invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function insert( $user_data ) {
			//the insert function could not have the ID
			if ( isset( $user_data[ 'ID' ] ) ) {
				unset( $user_data[ 'ID' ] );
			}

			// if ( ! $user_data['user_login'] || ! preg_match( '/^[a-z\d_]{2,20}$/i', $user_data['user_login'] ) ) {
			//  return new WP_Error( 'username_invalid', __( "Username only lowercase letters (a-z) and numbers are allowed.", ET_DOMAIN ) );
			// }
			if ( ! isset( $user_data[ 'user_email' ] ) || ! $user_data[ 'user_email' ] || $user_data[ 'user_email' ] == '' || ! is_email( $user_data[ 'user_email' ] ) ) {
				return new WP_Error( 'email_invalid', __( "Email field is invalid.", ET_DOMAIN ) );
			}
			if ( ! isset( $user_data[ 'user_pass' ] ) || ! $user_data[ 'user_pass' ] || $user_data[ 'user_pass' ] == '' ) {
				return new WP_Error( 'pass_invalid', __( "Password field is required.", ET_DOMAIN ) );
			}
			if ( isset( $user_data[ 'repeat_pass' ] ) && $user_data[ 'user_pass' ] != $user_data[ 'repeat_pass' ] ) {
				return new WP_Error( 'pass_invalid', __( "Repeat Passwords mismatch.", ET_DOMAIN ) );
			}

			$user_data = apply_filters( 'ae_pre_insert_user', $user_data );
			if ( ! $user_data || is_wp_error( $user_data ) ) {
				return $user_data;
			}
			/**
			 * prevent normal user try to insert user with role is administrator or editor
			 *
			 * @author Dakachi
			 */
			if ( isset( $user_data[ 'role' ] ) ) {
				if ( strtolower( $user_data[ 'role' ] ) == 'administrator' || strtolower( $user_data[ 'role' ] ) == 'editor' ) {
					return new WP_Error( 'user_role_error', __( "You can't create an administrator account.", ET_DOMAIN ) );
					exit();
				}
			}
			//check role for users
			if ( ! isset( $user_data[ 'role' ] ) ) {
				$user_data[ 'role' ] = 'author';
			}
			/**
			 * insert user by wp_insert_user
			 */
			$result    = wp_insert_user( $user_data );
			$user_pass = $user_data[ 'user_pass' ];

			if ( $result != false && ! is_wp_error( $result ) ) {

				/**
				 * update user meta data
				 */
				foreach ( $this->meta_data as $key => $value ) {

					// update if meta data exist
					if ( isset( $user_data[ $value ] ) ) {
						update_user_meta( $result, $value, $user_data[ $value ] );
					}
				}

				// people can modify here
				if ( isset( $user_data[ 'reg_social' ] ) ) {
					$params[ 'id' ]         = $result;
					$params[ 'user_pass' ]  = $user_data[ 'user_pass' ];
					$params[ 'user_login' ] = $user_data[ 'user_login' ];
					do_action( 'ae_social_insert_user', $params, $user_data );
				} else {
					do_action( 'ae_insert_user', $result, $user_data );
				}

				/**
				 * add ID to user data and return object
				 */
				$user_data[ 'ID' ] = $result;

				// sign on user
				// if( !get_option( 'user_confirm' ) && !is_user_logged_in() ) {
				//  return $this->login($user_data);
				// }else {
				//  return $this->convert(get_userdata($user_data['ID']));
				// }
				$result = $this->login( $user_data );
			}
			//set return message
			//if(isset($result->msg)){
			$result->msg = ! ae_get_option( 'user_confirm' ) ? __( "You have registered successfully!", ET_DOMAIN ) : __( "You have registered successfully. Please check your mailbox to activate your account.", ET_DOMAIN );

			//}
			return apply_filters( 'ae_after_insert_user', $result );
		}

		/**
		 * insert user without step check email for social login
		 * Solve the problem -facebook api return without an email
		 *
		 * @author : danng
		 * @since  : 1.8.3.1
		 */

		public function insert_social_user( $user_data ) {

			//the insert function could not have the ID
			if ( isset( $user_data[ 'ID' ] ) ) {
				unset( $user_data[ 'ID' ] );
			}

			// if ( ! $user_data['user_login'] || ! preg_match( '/^[a-z\d_]{2,20}$/i', $user_data['user_login'] ) ) {
			//  return new WP_Error( 'username_invalid', __( "Username only lowercase letters (a-z) and numbers are allowed.", ET_DOMAIN ) );
			// }
			if ( isset( $user_data[ 'user_email' ] ) && ! is_email( $user_data[ 'user_email' ] ) ) {
				return new WP_Error( 'email_invalid', __( "Email field is invalid.", ET_DOMAIN ) );
			}
			if ( ! isset( $user_data[ 'user_pass' ] ) || empty( $user_data[ 'user_pass' ] ) ) {
				return new WP_Error( 'pass_invalid', __( "Password field is required.", ET_DOMAIN ) );
			}


			$user_data = apply_filters( 'ae_pre_insert_user', $user_data );
			if ( ! $user_data || is_wp_error( $user_data ) ) {
				return $user_data;
			}
			/**
			 * prevent normal user try to insert user with role is administrator or editor
			 *
			 * @author Dakachi
			 */
			if ( isset( $user_data[ 'role' ] ) ) {
				if ( strtolower( $user_data[ 'role' ] ) == 'administrator' || strtolower( $user_data[ 'role' ] ) == 'editor' ) {
					return new WP_Error( 'user_role_error', __( "You can't create an administrator account.", ET_DOMAIN ) );
					exit();
				}
			}
			//check role for users
			if ( ! isset( $user_data[ 'role' ] ) ) {
				$user_data[ 'role' ] = 'author';
			}
			/**
			 * insert user by wp_insert_user
			 */
			$result    = wp_insert_user( $user_data );
			$user_pass = $user_data[ 'user_pass' ];

			if ( $result != false && ! is_wp_error( $result ) ) {

				/**
				 * update user meta data
				 */
				foreach ( $this->meta_data as $key => $value ) {

					// update if meta data exist
					if ( isset( $user_data[ $value ] ) ) {
						update_user_meta( $result, $value, $user_data[ $value ] );
					}
				}

				// people can modify here
				if ( isset( $user_data[ 'reg_social' ] ) ) {
					$params[ 'id' ]         = $result;
					$params[ 'user_pass' ]  = $user_data[ 'user_pass' ];
					$params[ 'user_login' ] = $user_data[ 'user_login' ];
					do_action( 'ae_social_insert_user', $params, $user_data );
				} else {
					do_action( 'ae_insert_user', $result, $user_data );
				}

				/**
				 * add ID to user data and return object
				 */
				$user_data[ 'ID' ] = $result;

				$result = $this->login( $user_data );
			}
			$result->msg = ! ae_get_option( 'user_confirm' ) ? __( "You have registered successfully!", ET_DOMAIN ) : __( "You have registered successfully. Please check your mailbox to activate your account.", ET_DOMAIN );

			//}

			return apply_filters( 'ae_after_insert_user', $result );
		}

		/**
		 * update userdata and user metadata to an database
		 * # used wp_update_user , wp_authenticate, email_exists ,get_userdata
		 * # used update_user_meta
		 * # used AE_Users function convert
		 *
		 * @param   array $user_data
		 * # WordPress user fields data
		 * # user custom meta data
		 *
		 * @return  user object after insert
		 * # wp_error object if user data invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function update( $user_data ) {
			global $current_user, $user_ID;

			/**
			 * prevent user edit other user profile
			 */
			if ( ! ae_user_can( 'edit_users' ) && $user_data[ 'ID' ] != $user_ID ) {
				return new WP_Error( 'denied', __( "Permission Denied!", ET_DOMAIN ) );
			}

			/**
			 * check user password if have new password update
			 */
			if ( isset( $user_data[ 'new_password' ] ) && ! empty( $user_data[ 'new_password' ] ) ) {
				$validate = $this->check_password( $user_data );
				if ( $validate ) {
					$user_data[ 'user_pass' ] = $user_data[ 'new_password' ];
				} else {
					return new WP_Error( 'wrong_pass', __( "Old password does not match!", ET_DOMAIN ) );
				}

				if ( $user_data[ 'new_password' ] !== $user_data[ 'renew_password' ] ) {
					return new WP_Error( 'pass_mismatch', __( "Retype password is not equal.", ET_DOMAIN ) );
				}
			}

			if ( isset( $user_data[ 'user_email' ] ) ) {
				$email = $user_data[ 'user_email' ];

				/**
				 * current user also update his email
				 */
				if ( $user_ID == $user_data[ 'ID' ] && $email != $current_user->user_email ) {
					if ( email_exists( $email ) ) {
						return new WP_Error( 'email_existed', __( "This email is already used. Please enter a new email.", ET_DOMAIN ) );
					}
				}

				//new start
				// $result = wp_update_user($user_data);
				// update_user_meta($result, 'user_new_email', $user_data['user_email']);
				update_user_meta( $user_data[ 'ID' ], 'user_new_email', $user_data[ 'user_email' ] );
				update_user_meta( $user_data[ 'ID' ], 'register_status', 'unconfirmnew' );
				$user_data[ 'user_email' ] = $current_user->user_email;
				//new end
			}

			//new start
			if ( isset( $user_data[ 'user_phone' ] ) ) {
				//тут будет проверка на уникальность
				//...

				$result = wp_update_user( $user_data );
				update_user_meta( $result, 'user_phone', $user_data[ 'user_phone' ] );
				update_user_meta( $result, 'ihs-country-code', $user_data[ 'ihs-country-code' ] );
			}
			//new end

			// don't allow upgrade from common user to admin
			if ( ! ae_user_can( 'edit_users' ) ) {
				unset( $user_data[ 'role' ] );
				unset( $user_data[ 'user_login' ] );
			}

			/**
			 * Set data for ban/unban user
			 */
			if ( isset( $user_data[ 'do' ] ) && $user_data[ 'do' ] == 'ban' ) {
				$user_data[ 'banned' ]                = true;
				$user_data[ $this->meta_ban_expired ] = date( get_option( 'date_format' ), strtotime( $user_data[ 'expired' ] ) );
				$user_data[ $this->meta_ban_note ]    = strip_tags( $user_data[ 'reason' ] );
			} elseif ( isset( $user_data[ 'do' ] ) && $user_data[ 'do' ] == 'unban' ) {
				$user_data[ 'banned' ]                = false;
				$user_data[ $this->meta_ban_expired ] = "";
				$user_data[ $this->meta_ban_note ]    = "";
			}

			/**
			 * insert user
			 */
			$result = wp_update_user( $user_data );

			if ( $result != false && ! is_wp_error( $result ) ) {

				/**
				 * update user meta data
				 */
				foreach ( $this->meta_data as $key => $value ) {
					// update if meta data exist
					if ( isset( $user_data[ $value ] ) ) {
						$usermeta = $this->ae_filter_usermeta( $user_data[ $value ] );
						update_user_meta( $result, $value, $usermeta );
					}
				}

				// hook to add custom
				do_action( 'ae_update_user', $result, $user_data );

				/**
				 * get user data and return a full profile
				 */
				$result = $this->convert( get_userdata( $result ) );
			}
			if ( isset( $user_data[ 'do' ] ) ) {
				switch ( $user_data[ 'do' ] ) {
					case 'profile':
						$result->msg = __( "Your profile has been saved successfully!", ET_DOMAIN );
						break;
					case 'changephone':
						$result->msg = __( "Your phone has been changed successfully!", ET_DOMAIN );
						break;
					case 'changepass':
						$result->msg = __( "Your password has been changed successfully!", ET_DOMAIN );
						break;
					case 'ban':
						$email = $this->get_ban_email_content( $result );
						$this->send_email( $result->user_email, $email[ 'subject' ], $email[ 'message' ] );
						$result->msg = __( "User has been banned!", ET_DOMAIN );
						break;
					default:
						$result->msg = __( "User's data update successfully!", ET_DOMAIN );
						break;
				}
			} else {
				$result->msg = __( "User's data update successfully!", ET_DOMAIN );
			}

			return $result;
		}

		/**
		 * Get ban message
		 *
		 * @param object $user
		 *
		 * @return array $content
		 *
		 * @author tatthien
		 */
		public function get_ban_email_content( $user ) {
			$content  = [
				'subject' => '',
				'message' => '',
			];
			$message  = ae_get_option( 'ban_mail_template' );
			$blogname = get_bloginfo( 'name' );
			$params   = [
				'blogname'     => $blogname,
				'display_name' => $user->display_name,
				'reason'       => $user->ban_note,
				'ban_expired'  => $user->ban_expired
			];

			foreach ( $params as $key => $value ) {
				$message = str_replace( "[$key]", $value, $message );
			}

			$content[ 'subject' ] = sprintf( __( 'You have been banned from %s', ET_DOMAIN ), $blogname );
			$content[ 'message' ] = $message;

			return $content;
		}

		/**
		 * Send email
		 *
		 * @param string $user_email
		 * @param string $subject
		 * @param string $message
		 *
		 * @return void
		 *
		 * @author tatthien
		 */
		public function send_email( $user_email, $subject, $message ) {
			if ( function_exists( 'et_get_customization' ) ) {
				$ae_mailing = AE_Mailing::get_instance();
				$ae_mailing->wp_mail( $user_email, $subject, $message );
			} else {
				$header = 'MIME-Version: 1.0' . "\r\n";
				$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$header .= "From: " . get_option( 'blogname' ) . " < " . get_option( 'admin_email' ) . "> \r\n";

				wp_mail( $user_email, $subject, $message, $header );
			}
		}

		/**
		 * Check if user is banned or not
		 *
		 * @param object $result
		 *
		 * @return boolean
		 *
		 * @author tatthien
		 */
		public function is_ban( $result ) {
			$ban_expired = get_user_meta( $result->ID, $this->meta_ban_expired, true );
			if ( ! empty( $ban_expired ) ) {
				$banned = true;
			} else {
				$banned = false;
			}

			return $banned;
		}

		/**
		 * Unban a user
		 *
		 * @param string|int $user_id
		 *
		 * @return void
		 */
		public function unban( $user_id ) {
			delete_user_meta( $user_id, $this->meta_ban_expired );
			delete_user_meta( $user_id, $this->meta_ban_note );
		}

		/**
		 * Get ban infomation: expired day and ban reason
		 *
		 * @param string $user_id
		 *
		 * @return array $ban_info
		 *
		 * @author tatthien
		 */
		public function get_ban_info( $user_id ) {
			$ban_expired = get_user_meta( $user_id, $this->meta_ban_expired, true );
			$ban_info    = [
				'expired' => date( get_option( 'date_format' ), strtotime( $ban_expired ) ),
				'note'    => get_user_meta( $user_id, $this->meta_ban_note, true )
			];

			if ( empty( $ban_info[ 'expired' ] ) ) {
				return false;
			} else {
				return $ban_info;
			}
		}

		/**
		 * login user into website
		 * # used wp_signon
		 * # used AE_Users function convert
		 *
		 * @param   array $user_data
		 * # wordpress user fields data
		 * # user custom meta data
		 *
		 * @return  user object after insert
		 * # wp_error object if user data invalid
		 * @author Dakachi
		 * @since  1.0
		 */
		public function login( $user_data ) {
			// echo 'login';
			// check users if he is member of this blog
			$user = get_user_by( 'login', $user_data[ 'user_login' ] );
			// if login by username failed check by email
			if ( is_wp_error( $user ) || ! $user ) {
				$user = get_user_by( 'email', $user_data[ 'user_login' ] );
			}
			/**
			 * check user infomation
			 */
			if ( ! $user ) {
				return new WP_Error( 'login_failed', __( "Invalid username or password.", ET_DOMAIN ) );
			}
			if ( $this->is_ban( $user ) ) {
				return new WP_Error( 'ban_account', __( "Your account is banned.", ET_DOMAIN ) );
			}
			if ( is_multisite() && ! is_user_member_of_blog( $user->ID ) ) {
				$roles = $user->roles;
				$role  = array_pop( $roles );
				add_user_to_blog( get_current_blog_id(), $user->ID, $role );
			}

			$user_login = $user->user_login;

			$creds                    = [];
			$creds[ 'user_login' ]    = $user_login;
			$creds[ 'user_password' ] = $user_data[ 'user_pass' ];
			if ( isset( $user_data[ 'remember' ] ) && $user_data[ 'remember' ] == 1 ) {
				$creds[ 'remember' ] = true;
			}

			$result = wp_signon( $creds, false );

			/**
			 * get user data and return a full profile
			 */
			if ( $result && ! is_wp_error( $result ) ) {
				// set current user to logged in
				wp_set_current_user( $result->ID );
				$result = $this->convert( $result );
				/**
				 * action ae_login_user
				 *
				 * @param Object $result User object
				 *
				 * @author Dakachi
				 */
				do_action( 'ae_login_user', $result );
			} else {
				// replace msg error default of WP
				if ( $result->get_error_code() == 'incorrect_password' ) {
					return new WP_Error( 'incorrect_password', __( "Invalid username, email, or password. Please try again!", ET_DOMAIN ) );
				}
			}
			if ( ! isset( $result->msg ) ) {
				$result->msg = __( "You have signed in successfully", ET_DOMAIN );
			}

			return apply_filters( 'ae_after_login_user', $result );
		}

		/**
		 * check user password, compare it with retype pass, validate old pass
		 *
		 * @param array $data
		 *
		 * @return  object WP_Error
		 *          bool true
		 * @author  Dakachi
		 * @since   1.0
		 */
		public function check_password( $data ) {
			global $current_user;

			if ( (int) $data[ 'ID' ] !== $current_user->ID && ! current_user_can( 'remove_users' ) ) {
				return new WP_Error( 'ae_permission_denied', __( "You cannot change other user password", ET_DOMAIN ) );
			}

			if ( $data[ 'renew_password' ] != $data[ 'new_password' ] ) {
				// password missmatch
				return new WP_Error( 'ae_pass_mismatch', __( "Retype password is not equal.", ET_DOMAIN ) );
			}

			$old_pass = $data[ 'old_password' ];
			$user     = get_user_by( 'login', $current_user->user_login );

			return ( $user && wp_check_password( $old_pass, $user->data->user_pass, $user->ID ) );
		}

		/**
		 * check user password, compare it with retype pass, validate old pass
		 *
		 * @param Array $user_data
		 * # - user_login or user_email
		 *
		 * @return  object WP_Error
		 *          bool true
		 * @author  Dakachi
		 * @since   1.0
		 */
		public function forgot( $user_data ) {
			global $wpdb;

			$errors = new WP_Error();

			/**
			 * validate user input
			 */
			if ( empty( $user_data[ 'user_login' ] ) ) {
				$errors->add( 'empty_username', __( 'ERROR: Enter username or email address.', ET_DOMAIN ) );
			} else if ( strpos( $user_data[ 'user_login' ], '@' ) ) {
				$user_data = get_user_by( 'email', trim( $user_data[ 'user_login' ] ) );

				// user is not exist
				if ( empty( $user_data ) ) {
					$errors->add( 'invalid_email', __( 'Please provide your correct email address.', ET_DOMAIN ) );
				}
			} else {
				$login     = trim( $user_data[ 'user_login' ] );
				$user_data = get_user_by( 'login', $login );
			}

			do_action( 'lostpassword_post' );

			if ( $errors->get_error_code() ) {
				return $errors;
			}

			if ( ! $user_data ) {
				$errors->add( 'invalidcombo', __( 'ERROR: Invalid username or email address.', ET_DOMAIN ) );

				return $errors;
			}

			// redefining user_login ensures we return the right case in the email
			$user_login = $user_data->user_login;

			do_action( 'retrieve_password', $user_login );

			$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

			if ( ! $allow ) {
				return new WP_Error( 'no_password_reset', __( 'Password reset is not allowed for this user', ET_DOMAIN ) );
			} else if ( is_wp_error( $allow ) ) {
				return $allow;
			}

			$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );

			if ( empty( $key ) ) {

				// Generate something random for a key...
				$key = wp_generate_password( 20, false );
				do_action( 'retrieve_password_key', $user_login, $key );

				// Now insert the new md5 key into the db
				$wpdb->update( $wpdb->users, [
					'user_activation_key' => $key
				], [
					'user_login' => $user_login
				] );
			}
			do_action( 'ae_user_forgot', $user_data->ID, $key );

			return [
				'success' => true,
				'msg'     => __( "Please check your mailbox for a password reset link.", ET_DOMAIN )
			];
		}

		/**
		 * check user activation key and username is match
		 *
		 * @param string $key   the activation key
		 * @param string $login user login
		 *
		 * @return Object | WP Object
		 */
		function check_activation_key( $key, $login ) {
			global $wpdb;

			$key = preg_replace( '/[^a-z0-9]/i', '', $key );

			if ( empty( $key ) || ! is_string( $key ) ) {
				return new WP_Error( 'invalid_key', __( 'Invalid Activation Key.', ET_DOMAIN ) );
			}

			if ( empty( $login ) || ! is_string( $login ) ) {
				return new WP_Error( 'invalid_key', __( 'Invalid User Name', ET_DOMAIN ) );
			}

			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", esc_sql( $key ), esc_sql( $login ) ) );

			if ( empty( $user ) ) {
				return new WP_Error( 'invalid_key', __( 'Invalid Activation Key.', ET_DOMAIN ) );
			}

			return $user;
		}

		/**
		 * user forgot password and reset it
		 *
		 * @param Array $args
		 * # - user_key : activation key when request pass
		 * # - user_name : user name
		 * # - new_password : user new password
		 *
		 * @return Object
		 * @author Dakachi
		 * @since  1.0.1
		 */
		function resetpass( $args ) {
			try {
				if ( empty( $args[ 'user_login' ] ) ) {
					throw new Exception( __( "Username is empty.", ET_DOMAIN ) );
				}
				if ( empty( $args[ 'user_key' ] ) ) {
					throw new Exception( __( "Invalid Activation Key", ET_DOMAIN ) );
				}
				if ( empty( $args[ 'new_password' ] ) ) {
					throw new Exception( __( "Please enter your new password", ET_DOMAIN ) );
				}

				$args[ 'user_pass' ] = $args[ 'new_password' ];
				// validate activation key
				$validate_result = $this->check_activation_key( $args[ 'user_key' ], $args[ 'user_login' ] );
				if ( is_wp_error( $validate_result ) ) {
					return $validate_result;
				}

				// do reset password
				$user = get_user_by( 'login', $args[ 'user_login' ] );
				// set new pass
				wp_set_password( $args[ 'user_pass' ], $user->ID );
				wp_password_change_notification( $user );

				return [
					'success' => true,
					'msg'     => __( 'Password have been updated.', ET_DOMAIN )
				];

			} catch ( Exception $e ) {
				return new WP_Error( 'reset_error', $e->getMessage() );
			}
		}

		/**
		 * send private message between 2 users
		 *
		 * @param object $request
		 *
		 * @return array of objects user
		 * @author Dakachi
		 * @since  1.0
		 */
		public function inbox( $request ) {
			$author = get_user_by( 'id', $request[ "send_to" ] );
			do_action( 'ae_user_inbox', $author, $request[ 'message' ] );

			return [
				'success' => true,
				'msg'     => __( "Your message has been sent", ET_DOMAIN )
			];
		}

		/**
		 * convert userdata to an object , use function convert
		 *
		 * @param object $args
		 *
		 * @return array of objects user
		 * @author Dakachi
		 * @since  1.0
		 */
		public function fetch( $args ) {
			$number = isset( $args[ 'number' ] ) ? $args[ 'number' ] : 10;
			$paged  = 1;
			if ( isset( $args[ 'paged' ] ) ) {
				$args[ 'offset' ] = $number * ( $args[ 'paged' ] - 1 );
				$paged            = $args[ 'paged' ];
			}
			if ( isset( $args[ 'query' ][ 'role' ] ) && $args[ 'query' ][ 'role' ] !== '' ) {
				$role = $args[ 'query' ][ 'role' ];
				$args = wp_parse_args( $args, [ 'role' => $role ] );
			}
			if ( isset( $args[ 'query' ][ 'user_search' ] ) && $args[ 'query' ][ 'user_search' ] !== '' ) {
				$user_search = $args[ 'query' ][ 'user_search' ];
				$args        = wp_parse_args( $args, [ 'search' => $user_search ] );
			}
			if ( isset( $args[ 'search' ] ) && '' !== $args[ 'search' ] ) {
				$search_string            = $args[ 'search' ];
				$args[ 'search' ]         = "*{$search_string}*";
				$args[ 'search_columns' ] = [
					'user_login',
					// 'user_email',
					'user_nicename',
					'display_name'
				];
			}

			$users_query = new WP_User_Query( $args );
			$users       = $users_query->results;
			$total_users = $users_query->total_users;
			$user_data   = [];
			foreach ( $users as $key => $user ) {
				$convert = $this->convert( $user );
				if ( ! is_wp_error( $convert ) ) {
					$user_data[] = $this->convert( $user );
				}
			}
			ob_start();
			$total_pages = ceil( $total_users / $number );
			if ( $total_users > $number && $paged <= $total_pages ) {
				ae_user_pagination( $args, $total_pages );
			}
			$paginate = ob_get_clean();

			return [
				'pages'    => ceil( $users_query->total_users / $args[ 'number' ] ),
				'data'     => $user_data,
				'paginate' => $paginate,
				'total'    => $total_users
			];
		}

		/**
		 * sync user data with server
		 *
		 * @param array $request The user data array will be insert to database
		 *
		 * @return object user object after converted
		 * @since  1.0
		 * @author Dakachi
		 */
		//TODO : Kiểm tra security
		public function sync( $request ) {
			extract( $request );
			unset( $request[ 'method' ] );
			/**
			 * check request method to set the action
			 */
			/** @var string $method */
			switch ( $method ) {
				case 'create':
					/** @var string $do */
					if ( isset( $do ) && ( 'resetpass' === $do ) ) {
						$result = $this->resetpass( $request );
					} else {
						// check captcha here.

						$captcha  = isset( $request[ 'g-recaptcha-response' ] ) ? $request[ 'g-recaptcha-response' ] : '';
						$response = ae_verify_captcha( $captcha );

						if ( is_wp_error( $response ) ) {
							return $response;
						}
						$result = $this->insert( $request );
					}
					break;

				case 'update':
					$result = $this->update( $request );
					break;

				case 'remove':
					$result = $this->delete( $request[ 'ID' ] );
					break;

				case 'read':
					$do = isset( $do ) ? $do : "";
					if ( 'login' === $do ) {
						$result = $this->login( $request );
					} elseif ( 'forgot' === $do ) {
						$result = $this->forgot( $request );
					} elseif ( 'inbox' === $do ) {
						$result = $this->inbox( $request );
					} else {
						$result = $this->get( $request[ 'ID' ] );
					}

					break;

				default:
					return new WP_Error( 'invalid_method', __( "Invalid method", ET_DOMAIN ) );
			}

			/**
			 * return object user
			 */
			return $result;
		}

		/**
		 * user confirm email
		 *
		 * @param String $key
		 *
		 * @return integer user id if confirm success and false if is activate
		 * @since  1.0
		 * @author ThaiNT
		 */
		public static function confirm( $key ) {
			global $de_confirm;
			$user    = get_users( [
				'meta_key'   => 'key_confirm',
				'meta_value' => $key
			] );
			$user_id = $user[ '0' ]->ID;

			// new start
			$register_status = get_user_meta( $user_id, 'register_status' );
			if ( $register_status[ 0 ] == 'unconfirmnew' ) {
				$new_email = get_user_meta( $user_id, 'user_new_email' );
				wp_update_user( [
					'ID'         => $user_id,
					'user_email' => $new_email[ 0 ]
				] );
			}

			// не удалять
			//        // user had activated
			//        if (self::is_activate($user[0]->ID)) {
			//            return false;
			//        }

			$de_confirm = update_user_meta( $user_id, 'register_status', 'confirm' );

			$referral_code    = get_user_meta( $user_id, '_activityRating_asReferral', true );
			$referral_user_id = get_user_meta( $user_id, '_activityRating_asReferrer', true );

			if ( ! empty( $referral_code ) ) :
				do_action( 'activityRating_asReferral', $user_id );
			endif;

			if ( ! empty( $referral_user_id ) ) :
				do_action( 'activityRating_asReferrer', $referral_user_id );
			endif;


			$de_confirm = delete_user_meta( $user_id, 'user_new_email', '' );
			$de_confirm = delete_user_meta( $user_id, 'key_confirm', '' );
			// new end

			//sign on user after active
			if ( $de_confirm ) {
				wp_clear_auth_cookie();
				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id );
			}


			/**
			 * do action after user confirm
			 *
			 * @param Int    $user_id
			 * @param string $key The activation key
			 *
			 * @since  1.0
			 * @author Dakachi
			 */
			do_action( 'ae_after_confirm_user', $user_id, $key );

			return $user_id;
		}

		/**
		 * The function for checking user is activated or not
		 *
		 * @param Integer $user_id The user ID
		 *
		 * @return bool true if is activate and false if isn't activate
		 * @since  1.0
		 * @author Dakachi
		 */

		public static function is_activate( $user_id ) {
			return ( ae_get_option( 'user_confirm' ) && get_user_meta( $user_id, 'register_status', true ) == "unconfirm" ) ? false : true;
		}

		/**
		 * filter  usermeta value
		 *
		 * @param string $usermeta
		 *
		 * @return string $usermete after remove all tag
		 * @since  1.0
		 *
		 * @author Tambh
		 */
		public function ae_filter_usermeta( $usermeta ) {
			$usermeta = wp_strip_all_tags( $usermeta );

			return apply_filters( 'ae_filter_usermeta', $usermeta );
		}
	}