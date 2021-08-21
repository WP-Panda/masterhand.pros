<?php

/**
 * Stripe escrow class
 */
class AE_Escrow_Stripe extends AE_Base {
	private static $instance;
	public $client_id;
	public $client_secret;
	public $client_public;
	public $token_uri;
	public $authorize_uri;
	public $redirect_uri;

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
	 * @since 1.0
	 * @author Tambh
	 */
	private function __construct() {
	}

	/**
	 * Init for class AE_Escrow_Stripe
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function init() {
		require_once dirname(__FILE__) . '/init.php';
		$stripe_api            = ae_get_option( 'escrow_stripe_api' );
		$this->client_id       = isset( $stripe_api['client_id'] ) ? $stripe_api['client_id'] : 'ca_6haoa1xY6ECo3GG6MU1zo9yeuF6DviYz';
		$this->client_secret   = isset( $stripe_api['client_secret'] ) ? $stripe_api['client_secret'] : 'sk_test_Q1YPkPqgUbUloB0ZC9eE8KhL';
		$this->client_public   = isset( $stripe_api['client_public'] ) ? $stripe_api['client_public'] : ' pk_test_Sl5wiqSBuabUX5QndfTX5Bzn';
		$this->token_uri       = 'https://connect.stripe.com/oauth/token';
		$this->authorize_uri   = 'https://connect.stripe.com/oauth/authorize';
		$this->deauthorize_uri = 'https://connect.stripe.com/oauth/deauthorize';
		$this->redirect_uri    = et_get_page_link( 'process-payment' ) . '/?paymentType=stripe';
		$this->init_ajax();
	}

	/**
	 * Put all ajax function here
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function init_ajax() {
		$this->add_action( 'wp_footer', 'ae_stripe_escrow_template' );
		$this->add_ajax( 'fre-stripe-escrow-customer', 'ae_create_stripe_customer' );
		$this->add_ajax( 'fre-stripe-escrow-deauthorize', 'ae_stripe_disconnect' );
		$this->add_action( 'ae_escrow_payment_gateway', 'ae_escrow_stripe_payment_gateway' );
		$this->add_action( 'fre_finish_escrow', 'ae_escrow_stripe_finish', 10, 2 );
		$this->add_filter( 'fre_process_escrow', 'ae_escrow_stripe_process', 10, 3 );
		$this->add_action( 'ae_escrow_execute', 'ae_stripe_escrow_execute', 10, 4 );
		$this->add_action( 'ae_escrow_refund', 'ae_stripe_escrow_refund', 10, 2 );
//        $this->add_filter('ae_accept_bid_infor', 'ae_accept_bid_infor_filter');
		$this->add_action( 'fre_transfer_money_ajax', 'ae_stripe_transfer_money_ajax', 10, 2 );
	}

	/**
	 * Deconnect to a stripe account
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author ThanhTu
	 */
	public function ae_stripe_disconnect() {
		global $user_ID;
		$deauthorize_request_body = array(
			'client_id'      => $this->client_id,
			'client_secret'  => $this->client_secret,
			'stripe_user_id' => $this->ae_get_stripe_user_id( $user_ID )
		);

		$a    = wp_remote_post( $this->deauthorize_uri, array(
			'body'        => $deauthorize_request_body,
			'httpversion' => '1.1'
		) );
		$body = json_decode( $a['body'] );

		if ( isset( $body->stripe_user_id ) ) {
			$this->ae_delete_stripe_user_id( $user_ID, $body->stripe_user_id );
			wp_send_json( array(
				'success' => true,
				'msg'     => __( 'Your Stripe account has been disconnected!', ET_DOMAIN )
			) );
		}
		wp_send_json( array( 'success' => false, 'msg' => $body->error_description ) );
	}

	/**
	 * Connect to a stripe account
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_connect() {
		global $user_ID;
		if ( isset( $_GET['code'] ) ) {
			$code               = $_GET['code'];

			// $token_request_body = array(
			// 	'client_secret' => $this->client_secret,
			// 	'grant_type'    => 'authorization_code',
			// 	'client_id'     => $this->client_id,
			// 	'code'          => $code
			// );
			// $a              = wp_remote_post( $this->token_uri, array(
			// 	'body'        => $token_request_body,
			// 	//'httpversion' => '1.1'
			// ) );
			//$resp           = json_decode( $a['body'] );
			/**
			 * above code commentted from version 1.3.1 by danng
			 * @reason: fix bug can not connect stripe account.
			 * method wp_remote_post return 403 permission.
			*/


			$token_request_body = array(
				'grant_type' => 'authorization_code',
				'client_id' => $this->client_id,
				'code' => $code,
				'client_secret' => $this->client_secret,
			);

			$req = curl_init( $this->token_uri );
			curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($req, CURLOPT_POST, true );
			curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

			// TODO: Additional error handling
			$respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
			$resp = json_decode(curl_exec($req), true);
			curl_close($req);

			if( isset( $resp['stripe_user_id'] ) ){
				$stripe_user_id = $resp['stripe_user_id'];
				$this->ae_update_stripe_user_id( $user_ID, $stripe_user_id );
				ae_stripe_escrow_notification();
			} else  if( isset( $resp['error'] ) ){
				$msg = $resp['error_description'];
				ae_stripe_escrow_notification($msg);
			}



		} else if ( isset( $_GET['error'] ) ) { // Error
			echo $_GET['error_description'];
		}
		$authorize_request_body = array(
			'response_type' => 'code',
			'scope'         => 'read_write',
			'client_id'     => $this->client_id
		);
		$url                    = $this->authorize_uri . '?' . http_build_query( $authorize_request_body );
		$text                   = __( 'Connect with Stripe', ET_DOMAIN );
		if ( $this->ae_get_stripe_user_id( $user_ID ) ) {
			$text = __( 'Reconnect with Stripe', ET_DOMAIN );
		}
		$html = "<span class='update-stripe-container'>";
		$html .= "<a class='' href='" . $url . "' target ='_blank'>" . $text . "</a>";
		$html .= "</span>";
		// deauthorize stripe
		if ( $this->ae_get_stripe_user_id( $user_ID ) ) {
			$html .= ' ' . __( 'or', ET_DOMAIN ) . ' ';
			$html .= "<span class='update-stripe-container'>";
			$html .= "<a class='stripe_disconnect' href='#'>" . __( 'Disconnect with Stripe', ET_DOMAIN ) . "</a>";
			$html .= "</span>";
		}
		echo $html;
	}

	/**
	 * Check if use stripe escrow
	 *
	 * @param void
	 *
	 * @return bool true/false, true if use stripe escrow and false if don't
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function is_use_stripe_escrow() {
		$stripe_api = ae_get_option( 'escrow_stripe_api' );

		return apply_filters( 'use_stripe_escrow', $stripe_api['use_stripe_escrow'] );
	}

	/**
	 * Create a stripe customer
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_create_stripe_customer() {
		try {
			global $user_ID;
			if ( ! isset( $_POST['token'] ) || ! isset( $_POST['stripe_email'] ) ) {
				$response = array(
					'success' => false,
					'msg'     => __( 'Update failed!', ET_DOMAIN )
				);
				wp_send_json( $response );
			}
			$token        = $_POST['token'];
			$stripe_email = $_POST['stripe_email'];
			\Stripe\Stripe::setApiKey( $this->client_secret );
			$stripe_object  = array(
				'card'  => $token,
				'email' => $stripe_email
			);
			$customer_obj   = wp_parse_args( array(
				'description' => 'Customer from ' . home_url()
			), $stripe_object );
			$customer       = \Stripe\Customer::create( $customer_obj );
			$stripe_user_id = $customer->id;
			if ( $stripe_user_id ) {
				$this->ae_update_stripe_user_id( $user_ID, $stripe_user_id );
			} else {
				$response = array(
					'success' => false,
					'msg'     => __( 'Update failed!', ET_DOMAIN )
				);
			}
			$response = array(
				'success' => true,
				'msg'     => __( 'You updated successfully!', ET_DOMAIN )
			);

		} catch ( Exception $ex ) {
			$value    = $ex->getJsonBody();
			$response = array(
				'success' => false,
				'msg'     => $value['error']['message']
			);
		}
		wp_send_json( $response );

	}
	function debug($code = '', $client_secret = 'sk_live_RcDkTgbZFkqR2aUIAMebmHi',$client_id = 'ca_Bc3KLnmJEuvUYNo9ki03gcyAHLdOb1Eg'){
        $code  = 'ac_BlT1qoKcROYmO8Kal1SpUq5MhCrYNGzD';
        $token_request_body = array(
            'client_secret' => $client_secret,
            'grant_type'    => 'authorization_code',
            'client_id'     => $client_id,
            'code'          => $code
        );
        var_dump($token_request_body);
        $a              = wp_remote_post( $this->token_uri, array(
            'body'        => $token_request_body,
            'httpversion' => '1.1'
        ) );
        echo '<pre>';
        var_dump($a);
        $resp           = json_decode( $a['body'] );
        //var_dump($resp);
        echo '</pre>';
    }
	/**
	 * Include all template were used for stripe escrow
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_escrow_template() {
		fre_update_stripe_info_modal();
	}

	/**
	 * Get stripe public key* @param void
	 * @return string $public_key
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_get_stripe_public_key() {
		return apply_filters( 'ae_stripe_public_key', $this->client_public );
	}

	/**
	 * Get stripe customer id of  a Employer
	 *
	 * @param integer $user_id
	 *
	 * @return string of customer id
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_get_stripe_user_id( $user_id = null ) {
		$stripe_user_id = '';
		if ( null != $user_id ) {
			$stripe_user_id = get_user_meta( $user_id, 'ae_stripe_user_id', true );
		}

		return apply_filters( 'ae_stripe_user_id', $stripe_user_id );
	}

	/**
	 * Update stripe user id
	 *
	 * @param integer $user_id
	 * @param string $stripe_user_id
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_update_stripe_user_id( $user_id = null, $stripe_user_id = null ) {
		if ( null != $user_id && null != $stripe_user_id ) {
			update_user_meta( $user_id, 'ae_stripe_user_id', $stripe_user_id );
		}
	}

	/**
	 * Delete stripe user id
	 *
	 * @param integer $user_id
	 * @param string $stripe_user_id
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author ThanhTu
	 */
	public function ae_delete_stripe_user_id( $user_id = null, $stripe_user_id = null ) {
		if ( null != $user_id && null != $stripe_user_id ) {
			delete_user_meta( $user_id, 'ae_stripe_user_id' );
		}
	}

	/**
	 * Get list of zero-decimal currencies
	 * @author Tudt
	 */
	public function get_zero_decimal_currencies() {
		return apply_filters( 'zero_decimal_currencies', array(
			'BIF',
			'DJF',
			'JPY',
			'KRW',
			'PYG',
			'VND',
			'XAF',
			'XPF',
			'CLP',
			'GNF',
			'KMF',
			'MGA',
			'RWF',
			'VUV',
			'XOF'
		) );
	}

	/**
	 * Execute Escrow by Stripe gateway
	 * Process escrow after employer accept a project for a freelancer account.
	 * Make a tranfer funds to the stripe account of admin.
	 *
	 * @param array $escrow_data
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_escrow_stripe_payment_gateway( $escrow_data ) {
		global $user_ID;
		try {
			$escrow_data['customer']  = $this->ae_get_stripe_user_id( $user_ID );
			$escrow_data['recipient'] = $this->ae_get_stripe_user_id( $escrow_data['bid_author'] );
			if ( empty( $escrow_data['customer'] ) ) {
				$response = array(
					'success' => false,
					'msg'     => __( 'Please go to your profile to set your stripe account before accepting a project.', ET_DOMAIN )
				);
				wp_send_json( $response );
			}
			if ( empty( $escrow_data['recipient'] ) ) {
				$response = array(
					'success' => false,
					'msg'     => __( 'Has something wrong with the stripe account of freelancer.', ET_DOMAIN )
				);
				wp_send_json( $response );
			}
			// Zero decimal currencies
			$amount = $escrow_data['total'];
			if ( ! in_array( $escrow_data['currency'], $this->get_zero_decimal_currencies() ) ) {
				$amount *= 100;
			}
			$charge_obj = array(
				'amount'   => (float) $amount,
				'currency' => $escrow_data['currency'],
				'customer' => $escrow_data['customer'],
			);
			$bid_id     = $escrow_data['bid_id'];
			$bid        = get_post( $bid_id );
			$charge     = $this->ae_stripe_charge( $charge_obj );
			$order_post = array(
				'post_type'    => 'fre_order',
				'post_status'  => 'pending',
				'post_parent'  => $bid_id,
				'post_author'  => $user_ID,
				'post_title'   => 'Pay for accept bid',
				'post_content' => 'Pay for accept bid ' . $bid_id
			);
			if ( $charge && isset( $charge->id ) ) {
				do_action( 'fre_accept_bid', $bid_id );
				$order_id = wp_insert_post( $order_post );
				update_post_meta( $order_id, 'fre_paykey', $charge->id );
				update_post_meta( $order_id, 'gateway', 'stripe' );

				update_post_meta( $bid_id, 'fre_bid_order', $order_id );
				update_post_meta( $bid_id, 'commission_fee', $escrow_data['commission_fee'] );
				update_post_meta( $bid_id, 'payer_of_commission', $escrow_data['payer_of_commission'] );
				update_post_meta( $bid_id, 'fre_paykey', $charge->id );

				et_write_session( 'payKey', $charge->id );
				et_write_session( 'order_id', $order_id );
				et_write_session( 'bid_id', $bid_id );
				et_write_session( 'ad_id', $bid->post_parent );
				$response = array(
					'success'      => true,
					'msg'          => 'Success!',
					'redirect_url' => $this->redirect_uri
				);
				wp_send_json( $response );
			} else {
				wp_send_json( array(
					'success' => false,
					'msg'     => __( 'charge failed', ET_DOMAIN )
				) );
			}
		} catch ( Exception $ex ) {
			$value    = $ex->getJsonBody();
			$response = array(
				'success' => false,
				'msg'     => $value['error']['message']
			);
			wp_send_json( $response );
		}
		exit;
	}

	/**
	 * Stripe transfer process
	 *
	 * @param array $transfer_obj
	 *
	 * @return object $transfer
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_transfer( $transfer_obj ) {

		\Stripe\Stripe::setApiKey( $this->client_secret );

		$application_fee = (float) $transfer_obj['application_fee'];
		$amount          = (float) $transfer_obj['amount'];
		$amount          = $amount - $application_fee;

		// Create a transfer to the bank account associated with your Stripe account
		$transfer_obj = array(
			"amount"      => $amount, // amount in cents
			"currency"    => $transfer_obj['currency'],
			"destination" => $transfer_obj['destination']
		);

		$transfer     = new WP_Error( 'broke_default', __( 'Has something wrong', ET_DOMAIN ) );
		try {
			$transfer = \Stripe\Transfer::create( $transfer_obj );
		} catch ( Stripe\Error\Base $e ) {
			return new WP_Error( 'broke_stripe', $e->getMessage() );
		} catch ( Exception $e ) {
			return new WP_Error( 'broke_php', $e->getMessage() );
		}

		return $transfer;
	}

	/**
	 * Stripe transfer revert
	 *
	 * @param string $transfer_id
	 *
	 * @return object $re reversals
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_reversals( $transfer_id ) {
		\Stripe\Stripe::setApiKey( $this->client_id );
		$tr = \Stripe\Transfer::retrieve( $transfer_id );
		$re = $tr->reversals->create();

		return $re;
	}

	/**
	 * Charge money from customer when they acept bid
	 *
	 * @param array $charge_obj
	 *
	 * @return object $charge
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_charge( $charge_obj ) {
		\Stripe\Stripe::setApiKey( $this->client_secret );
		$charge = \Stripe\Charge::create( $charge_obj );

		return $charge;
	}

	/**
	 * Refund money
	 *
	 * @param string $charge_id
	 *
	 * @return object $refund
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_refund( $charge_id, $transfer_obj ) {
		\Stripe\Stripe::setApiKey( $this->client_secret );
		$ch = \Stripe\Charge::retrieve( $charge_id );
		$re = $ch->refunds->create( $transfer_obj );

		return $re;
	}

	public function ae_stripe_transfer_money_ajax( $project_id, $bid_id_accepted ) {
		if ( $this->is_use_stripe_escrow() ) {
			// execute payment and send money to freelancer
			$charge_id = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
			if ( $charge_id ) {
				$charge      = $this->ae_stripe_retrieve_charge( $charge_id );
				$bid         = get_post( $bid_id_accepted );
				$destination = '';
				$bid_budget  = $charge->amount;
				if ( $bid && ! empty( $bid ) ) {
					$destination         = $this->ae_get_stripe_user_id( $bid->post_author );
					$bid_budget          = get_post_meta( $bid_id_accepted, 'bid_budget', true );
					$payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
					if ( $payer_of_commission != 'project_owner' ) {
						$commission_fee = get_post_meta( $bid_id_accepted, 'commission_fee', true );
					} else {
						$commission_fee = 0;
					}
				}

				$commission_fee = $commission_fee * 100;
				$amount         = (float) $bid_budget * 100;
				$amount         = $amount - $commission_fee;

				$transfer_obj = array(
					"amount"               => $amount, // amount in cents
					"currency"             => $charge->currency,
					"destination"          => $destination,
					"statement_descriptor" => __( "Freelance escrow", ET_DOMAIN )
				);
				$transfer     = $this->ae_stripe_transfer( $transfer_obj );

				if ( ! is_wp_error( $transfer ) ) {
					$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
					if ( $order ) {
						wp_update_post( array(
							'ID'          => $order,
							'post_status' => 'finish'
						) );
						$mail = Fre_Mailing::get_instance();
						$mail->execute( $project_id, $bid_id_accepted );
						// send json back
						wp_send_json( array(
							'success' => true,
							'msg'     => __( "The payment has been successfully transferred .", ET_DOMAIN ),
							'data'    => $response
						) );
					}
				} else {
					$errors = $transfer->errors;
					if ( isset( $errors['broke_stripe'] ) ) {
						wp_send_json( array(
							'success' => false,
							'msg'     => $errors['broke_stripe'][0]
						) );
					} else if ( isset( $errors['broke_php'] ) ) {
						wp_send_json( array(
							'success' => false,
							'msg'     => $errors['broke_php'][0]
						) );
					}

				}
			} else {
				wp_send_json( array(
					'success' => false,
					'msg'     => __( "Invalid key.", ET_DOMAIN )
				) );
			}
		}
	}

	/**
	 * Transfer money to freelancer when employer finish their project
	 * This action run after the freelancer review employer/project
	 *
	 * @param integer $project_id the project's id that employer finished
	 * @param $bid_id_accepted
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_escrow_stripe_finish( $project_id, $bid_id_accepted ) {
		if ( $this->is_use_stripe_escrow() ) {
			// execute payment and send money to freelancer
			$charge_id = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
			if ( $charge_id ) {
				$charge = $this->ae_stripe_retrieve_charge( $charge_id );
				if ( $charge ) {
					$bid         = get_post( $bid_id_accepted );
					$destination = '';
					$bid_budget  = $charge->amount;
					if ( $bid && ! empty( $bid ) ) {
						$destination         = $this->ae_get_stripe_user_id( $bid->post_author );
						$bid_budget          = get_post_meta( $bid_id_accepted, 'bid_budget', true );
						$payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
						if ( $payer_of_commission != 'project_owner' ) {
							$commission_fee = get_post_meta( $bid_id_accepted, 'commission_fee', true );
						} else {
							$commission_fee = 0;
						}
					}

					$commission_fee = (float) $commission_fee * 100;
					$amount         = (float) $bid_budget * 100;
					$amount         = $amount - $commission_fee;
					$transfer_obj   = array(
						"amount"               => $amount, // amount in cents
						"currency"             => $charge->currency,
						"destination"          => $destination,
						"statement_descriptor" => __( "Freelance escrow", ET_DOMAIN )
					);
					$transfer       = $this->ae_stripe_transfer( $transfer_obj );

					if ( ! is_wp_error( $transfer ) ) {
						$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
						if ( $order ) {

							wp_update_post( array(
								'ID'          => $order,
								'post_status' => 'finish'
							) );
							$mail = Fre_Mailing::get_instance();
							$mail->alert_transfer_money( $project_id, $bid_id_accepted );
							$mail->notify_execute( $project_id, $bid_id_accepted );
						}
					} else {
						//delete the just review of fre account for employer account.
						$comments = get_comments( array(
							'status'  => 'approve',
							'type'    => 'fre_review',
							'post_id' => $project_id
						) );
						if ( ! empty( $comments ) ) {
							foreach ( $comments as $comment ) :
								wp_delete_comment( $comment->comment_ID );
							endforeach;
						}
						wp_send_json( array( 'success' => false, 'msg' => $transfer->get_error_message() ) );

					}
				}
			} else {
				$mail = Fre_Mailing::get_instance();
				$mail->alert_transfer_money( $project_id, $bid_id_accepted );
			}
		}
	}

	/**
	 * Retrieve a charge
	 *
	 * @param string $charge_id
	 *
	 * @return object $charge or false if there isn't any charge
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_retrieve_charge( $charge_id ) {
		\Stripe\Stripe::setApiKey( $this->client_secret );
		$charge = \Stripe\Charge::retrieve( $charge_id );
		if ( isset( $charge->status ) && $charge->status == 'succeeded' ) {
			return $charge;
		}

		return false;
	}

	/**
	 * Process payment accept bid
	 *
	 * @param array $payment_return
	 * @param string $payment_type
	 * @param array $data
	 *
	 * @return array $payment_return
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_escrow_stripe_process( $payment_return, $payment_type, $data ) {
		if ( $payment_type == 'stripe' ) {
			$response                         = $this->ae_stripe_retrieve_charge( $data['payKey'] );
			$payment_return['payment_status'] = isset( $response->paid ) ? $response->paid : false;
			if ( isset( $response->status ) && $response->status == 'succeeded' ) {
				$payment_return['ACK'] = true;
				wp_update_post( array(
					'ID'          => $data['order_id'],
					'post_status' => 'publish'
				) );
				// assign project
				$bid_action = Fre_BidAction::get_instance();
				$bid_action->assign_project( $data['bid_id'] );
			} else {
				$payment_return['msg'] = __( 'Payment failed!', ET_DOMAIN );
			}
		}

		return $payment_return;
	}

	/**
	 * Refund escrow by stripe
	 *
	 * @param interger $project_id
	 * @param integer $bid_id_accepted
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_escrow_refund( $project_id, $bid_id_accepted ) {
		$charge_id    = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
		$transfer_obj = array();

		if ( $charge_id ) {
			$charge = $this->ae_stripe_retrieve_charge( $charge_id );
			if ( $charge ) {
				$bid = get_post( $bid_id_accepted );
				if ( $bid && ! empty( $bid ) ) {
					$bid_budget          = get_post_meta( $bid_id_accepted, 'bid_budget', true );
					$payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
					if ( $payer_of_commission != 'project_owner' ) {
						// freelancer
						$bid_budget = $charge->amount;
					} else {
						// employer
						$bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );

						$transfer_obj = array(
							"amount" => (float) $bid_budget * 100, // amount in cents
						);
					}
				}
			}
		}

		$re = $this->ae_stripe_refund( $charge_id, $transfer_obj );
		if ( $re ) {
			$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
			if ( $order ) {
				wp_update_post( array(
					'ID'          => $order,
					'post_status' => 'refund'
				) );
			}
			wp_update_post( array(
				'ID'          => $project_id,
				'post_status' => 'disputed'
			) );
			wp_update_post( array(
				'ID'          => $bid_id_accepted,
				'post_status' => 'disputed'
			) );

			// update meta when admin arbitrate
			if ( isset( $_REQUEST['comment'] ) && isset( $_REQUEST['winner'] ) ) {
				$comment = $_REQUEST['comment'];
				$winner  = $_REQUEST['winner'];
				update_post_meta( $project_id, 'comment_of_admin', $comment );
				update_post_meta( $project_id, 'winner_of_arbitrate', $winner );
			}
			$mail = Fre_Mailing::get_instance();
			$mail->refund( $project_id, $bid_id_accepted );
			do_action( 'fre_resolve_project_notification', $project_id );
			// send json back
			wp_send_json( array(
				'success' => true,
				'msg'     => __( "Send payment successful.", ET_DOMAIN ),
				'data'    => __( 'Success', ET_DOMAIN )
			) );
		} else {
			wp_send_json( array(
				'success' => false,
				'msg'     => __( 'Refund failed!', ET_DOMAIN )
			) );
		}
	}

	/**
	 * execute escrow by stripe
	 *
	 * @param $project_id
	 * @param integer $bid_id_accepted
	 *
	 * @return void
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_stripe_escrow_execute( $project_id, $bid_id_accepted, $arbitrate_data = false, $receiver = false ) {
		$charge_id = get_post_meta( $bid_id_accepted, 'fre_paykey', true );
		$charge    = $this->ae_stripe_retrieve_charge( $charge_id );

		if ( $charge ) {
			$bid         = get_post( $bid_id_accepted );
			$destination = '';
			$bid_budget  = $charge->amount;

			if ( $bid && ! empty( $bid ) ) {
				$destination         = $this->ae_get_stripe_user_id( $bid->post_author );
				$bid_budget          = get_post_meta( $bid_id_accepted, 'bid_budget', true );
				$payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
				if ( $payer_of_commission != 'project_owner' ) {
					$commission_fee = get_post_meta( $bid_id_accepted, 'commission_fee', true );
				} else {
					$commission_fee = 0;
				}
			}

			if ($receiver){
                $amount = $receiver == 'freelancer' ? $arbitrate_data['freelancer_value'] : $arbitrate_data['client_value'];

                // if split type is percents - convert value into number
                if ($arbitrate_data['split_type'] == 'percent'){
                    $amount = $bid_budget * $amount / 100;
                }
            } else {
                $amount = $bid_budget;
            }

            // convert dollars into cents
			$amount         = (float) $amount * 100;
            $commission_fee = (float) $commission_fee * 100;

            // if reciever is client - substract comission from amount
            if ($receiver == 'client'){
                $amount = $amount - $commission_fee;
            }

			$transfer_obj = array(
				"amount"               => $amount, // amount in cents
				"currency"             => $charge->currency,
				"destination"          => $destination,
				"statement_descriptor" => "Freelance escrow"
			);

			$transfer     = $this->ae_stripe_transfer( $transfer_obj );
			if ( ! is_wp_error( $transfer ) ) {
				$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
				if ( $order ) {
					wp_update_post( array(
						'ID'          => $order,
						'post_status' => 'completed'
					) );
				}

				// success update project status
				wp_update_post( array(
					'ID'          => $project_id,
					'post_status' => 'disputed'
				) );

				// success update project status
				wp_update_post( array(
					'ID'          => $bid_id_accepted,
					'post_status' => 'disputed'
				) );

				// update meta when admin arbitrate
				if ( isset( $_REQUEST['comment'] ) && isset( $_REQUEST['winner'] ) ) {
					$comment = $_REQUEST['comment'];
					$winner  = $_REQUEST['winner'];
					update_post_meta( $project_id, 'comment_of_admin', $comment );
					update_post_meta( $project_id, 'winner_of_arbitrate', $winner );
				}

				// send mail
				$mail = Fre_Mailing::get_instance();
				$mail->execute_payment( $project_id, $bid_id_accepted );

				do_action( 'fre_resolve_project_notification', $project_id );
				wp_send_json( array(
					'success' => true,
					'msg'     => __( "Send payment successful.", ET_DOMAIN )
				) );
			} else {
				wp_send_json( array( 'success' => false, 'msg' => $transfer->get_error_message() ) );
			}
		} else {
			wp_send_json( array(
				'success' => false,
				'msg'     => __( "Invalid charge.", ET_DOMAIN )
			) );
		}
	}

	/**
	 * Get the stripe user who will pay the fee
	 *
	 * @param void
	 *
	 * @return string $fee_payer
	 * @since 1.0
	 * @package AE_ESCROW
	 * @category STRIPE
	 * @author Tambh
	 */
	public function ae_get_stripe_fee_payer() {
		$stripe_api = ae_get_option( 'escrow_stripe_api' );
		$fee_payer  = $stripe_api['stripe_fee'] ? $stripe_api['stripe_fee'] : 'EACHRECEIVER';

		return $fee_payer;
	}

}
// add_action( 'wp_footer','debug_stripe_connect');
// function debug_stripe_connect(){
//    AE_Escrow_Stripe::getInstance()->debug();
// }