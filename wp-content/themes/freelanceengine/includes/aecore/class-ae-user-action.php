<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;
	/**
	 * class acting with all action for user
	 */
	class AE_UserAction extends AE_Base{

		/**
		 * property AE_Users
		 */
		protected $user;

		public function __construct( AE_Users $user ) {
			$this->user = $user;
			$this->add_ajax( 'ae-fetch-users', 'fetch' );
			$this->add_ajax( 'ae-sync-user', 'sync' );
		}

		/**
		 * ajax fetch users sync
		 */
		function fetch() {

			$post_per_page = get_option( 'posts_per_page' );
			$request       = $_REQUEST;

			$offset   = ( $request[ 'paged' ] ) * $post_per_page;
			$args     = [
				'offset' => $offset,
				'number' => $post_per_page
			];
			$args     = wp_parse_args( $args, $request );
			$users    = $this->user->fetch( $args );
			$response = [
				'success'  => true,
				'data'     => $users[ 'data' ],
				'pages'    => $users[ 'pages' ],
				'paginate' => $users[ 'paginate' ],
				'paged'    => $request[ 'paged' ] + 1,
				'msg'      => __( "Get users successfull", ET_DOMAIN ),
				'total'    => $users[ 'total' ]
			];

			if ( empty( $users[ 'data' ] ) ) {
				$response[ 'msg' ] = __( "No user found by your query", ET_DOMAIN );
			}

			wp_send_json( $response );
		}

		/**
		 * callback for ajax ae-sync-user action
		 */
		function sync() {
			global $user_ID;
			$request = $_REQUEST;
			/**
			 * sync user base on method and do param
			 */
			$result = $this->user->sync( $request );
			do_action( 'activityRating_oneFieldProfile' );
			// check the result and send json to client
			if ( $result && ! is_wp_error( $result ) ) {
				$response = [
					'success' => true,
					'data'    => $result,
					'msg'     => is_object( $result ) && isset( $result->msg ) ? $result->msg : $result[ 'msg' ]
				];

				// user sync and try to request confirm email
				if ( isset( $request[ 'do' ] ) && $request[ 'do' ] == 'confirm_mail' && $user_ID == $request[ 'ID' ] ) {
					if ( ae_is_send_activation_code() ) {
						$mailing   = AE_Mailing::get_instance();
						$send_mail = $mailing->request_confirm_mail( $user_ID );
						if ( $send_mail ) {
							$response[ 'msg' ] = __( 'Email confirmation has been sent.', ET_DOMAIN );
							$secure            = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
							setcookie( 'ae_sent_activation_code', 1, time() + 300, COOKIEPATH, COOKIE_DOMAIN, $secure );
							if ( SITECOOKIEPATH != COOKIEPATH ) {
								setcookie( 'ae_sent_activation_code', 1, time() + 300, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
							}
						} else {
							$response[ 'msg' ]     = __( 'An unknown error has occurred. Please try again later.', ET_DOMAIN );
							$response[ 'success' ] = false;
						}
					} else {
						$response = [
							'success' => false,
							'msg'     => __( 'Next re-send is available in 5 minutes!', ET_DOMAIN )
						];
					}
				}

			} else {
				$response = [
					'success' => false,
					'msg'     => $result->get_error_message()
				];
			}
			wp_send_json( $response );
		}
	}
