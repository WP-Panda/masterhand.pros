<?php

/**
 * Class AE Payment is an abstract class handle function releate to payment setup , process payment
 *
 * @since    1.0
 * @package  AE Payment
 * @category payment
 *
 * @property Array $no_priv_ajax Contain all no private ajax action name
 * @property Array $priv_ajax    All private ajax action name
 *
 * @author   Dakachi
 */
abstract class AE_Payment extends AE_Base {

	/**
	 * no private ajax
	 */
	protected $no_priv_ajax = [];

	// private ajax
	protected $priv_ajax = [
		'et-setup-payment'
	];

	function __construct() {

		$this->init_ajax();
	}

	/**
	 * init ajax to process payment
	 *
	 * @return null
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	function init_ajax() {
		foreach ( $this->no_priv_ajax as $key => $value ) {
			$function = str_replace( 'et-', '', $value );
			$function = str_replace( '-', '_', $function ); //setup_payment here
			$this->add_ajax( $value, $function );
		}

		foreach ( $this->priv_ajax as $key => $value ) {
			$function = str_replace( 'et-', '', $value );
			$function = str_replace( '-', '_', $function );
			$this->add_ajax( $value, $function, true, false );
		}

		// catch action ae_save_option to update payment api settings
		$this->add_action( 'ae_save_option', 'update_payment_settings', 10, 2 );

		// process payment
		$this->add_action( 'ae_process_payment_action', 'process_payment', 10, 2 );
	}

	/**
	 * callback update option for Paypal, 2checkout, cash api settings
	 *
	 * @param String $name The payment gateway name
	 * @param String $value The payment gateway api value
	 *
	 * @return  null
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	public function update_payment_settings( $name, $value ) {

		// update paypal api settings
		if ( $name == 'paypal' ) {
			ET_Paypal::set_api( $value );
		}

		// update 2checkout api settings
		if ( $name == '2checkout' ) {
			ET_2CO::set_api( $value );
		}

		// update 2checkout api settings
		if ( $name == 'cash' ) {
			ET_Cash::set_message( $value['cash_message'] );
		}
	}

	/**
	 * catch action ae_process_payment_action and update post data after payment success
	 *
	 * @param $payment_return
	 * @param $data
	 *
	 * @return array $payment_return
	 *
	 * @since  1.0
	 * @author dakachi <ledd@youngworld.vn>
	 */
	function process_payment( $payment_return, $data ) {
		global $user_ID, $ae_post_factory;

		// process user order after pay
		do_action( 'ae_select_process_payment', $payment_return, $data );
		$this->member_payment_process( $payment_return, $data );
		//  if not exist post id
		if ( ! isset( $data['ad_id'] ) || ! $data['ad_id'] ) {
			return $payment_return;
		}

		$options = AE_Options::get_instance();
		$ad_id   = $data['ad_id'];

		extract( $data );
		if ( ! $payment_return['ACK'] ) {
			return 0;
		}

		$post = get_post( $ad_id );
		/**
		 * get object by post type and convert
		 */
		$post_obj = $ae_post_factory->get( $post->post_type );
		$ad       = $post_obj->convert( $post );
		if ( $payment_type == 'free' ) {
			AE_Package::update_used_free_plan( $ad->post_author );
		}

		if ( $payment_type != 'usePackage' && isset( $ad->et_payment_package ) ) {
			/**
			 * update seller package quantity
			 */
			AE_Package::update_package_data( $ad->et_payment_package, $ad->post_author );

			$post_data = [
				'ID'          => $ad_id,
				'post_status' => 'publish'
			];

			if ( ! current_user_can( 'administrator' ) ) {
				$post_data = [
					'ID'          => $ad_id,
					'post_status' => 'pending'
				];
			}
			/*Send email for admin if pending payment*/

			do_action( 'ae_after_process_payment', $post_data );
		}

		// disable pending and auto publish post


		/** @var string $payment_type */
		if ( ! $options->use_pending && ( 'cash' != $payment_type ) ) {
			$post_data['post_status'] = 'publish';
		}

		// when buy new package will got and order
		if ( isset( $data['order_id'] ) ) {

			// update post order id
			update_post_meta( $ad_id, 'et_invoice_no', $data['order_id'] );
		}

		// Change Status Publish places that posted by Admin
		if ( current_user_can( 'administrator' ) ) {
			wp_update_post( [
				'ID'          => $ad_id,
				'post_status' => 'publish'
			] );
		}

		switch ( $payment_type ) {
			case 'cash':
				if ( ! empty( $post_data ) ) {
					wp_update_post( $post_data );
				}

				// update unpaid payment
				update_post_meta( $ad_id, 'et_paid', 0 );

				return $payment_return;
			case 'free':
				wp_update_post( $post_data );

				// update free payment
				update_post_meta( $ad_id, 'et_paid', 2 );

				return $payment_return;
			case 'usePackage':
				$mail = new Fre_Mailing();
				$mail->new_project_of_category( $ad );

				return $payment_return;
			default:

				// code...
				break;
		}

		/**
		 * payment succeed
		 */
		if ( 'PENDING' != strtoupper( $payment_return['payment_status'] ) ) {
			if ( isset( $post_data['ID'] ) ) {
				wp_update_post( $post_data );
			}

			// paid
			update_post_meta( $ad_id, 'et_paid', 1 );
		} else {

			/**
			 * in some case the payment will be pending
			 */
			wp_update_post( [
				'ID'          => $ad_id,
				'post_status' => 'pending'
			] );

			// unpaid
			update_post_meta( $ad_id, 'et_paid', 0 );
		}

		if ( current_user_can( 'administrator' ) || ! ae_get_option( 'use_pending', false ) ) {
			do_action( 'ae_after_process_payment_by_admin', $ad_id );
		}

		return $payment_return;
	}

	/**
	 * action process payment update seller order data
	 *
	 * @param Array $payment_return The payment return data
	 * @param Array $data Order data and payment type
	 *
	 * @return bool true/false
	 * @since   1.0
	 * @author  Dakachi
	 *
	 * @package AE Payment
	 */
	public function member_payment_process( $payment_return, $data ) {
		extract( $data );
		if ( ! $payment_return['ACK'] ) {
			return false;
		}
		if ( $payment_type == 'free' ) {
			return false;
		}

		if ( $payment_type == 'usePackage' ) {
			return false;
		}

		$order_pay = $data['order']->get_order_data();

		// update user current order data associate with package
		self::update_current_order( $order_pay['payer'], $order_pay['payment_package'], $data['order_id'] );
		AE_Package::add_package_data( $order_pay['payment_package'], $order_pay['payer'] );

		/**
		 * do action after process user order
		 *
		 * @param $order_pay ['payer'] the user id
		 * @param $data      The order data
		 */
		do_action( 'ae_member_process_order', $order_pay['payer'], $order_pay );

		return true;
	}

	/**
	 *  update order id user paid for package
	 *
	 * @param Integer $user_id The user ID
	 * @param Integer $package The package ID
	 * @param Integer $order_id The order ID want to update
	 *
	 * @return bool
	 */
	public static function update_current_order( $user_id, $package, $order_id ) {
		$group             = self::get_current_order( $user_id );
		$group[ $package ] = $order_id;


		/****** BUG RẤT LỚN Ở ĐÂY ******/


		return self::set_current_order( $user_id, $group );
	}

	/**
	 * return the order id user paid for the package
	 *
	 * @param integer $user_id The user ID
	 * @param integer $package_id The package id want to get order
	 *
	 * @return array $oder
	 *
	 * @since   1.0
	 * @author  Dakachi
	 */
	public static function get_current_order( $user_id, $package_id = '' ) {
		$order = get_user_meta( $user_id, 'ae_member_current_order', true );
		if ( $package_id == '' ) {
			if ( $order == '' ) {
				return unserialize( $order );
			} else {
				return $order;
			}
		} else {
			return ( isset( $order[ $package_id ] ) ? $order[ $package_id ] : '' );
		}
	}

	/**
	 * update user current order
	 *
	 * @param $user_id the user pay id
	 * @param $group   array of order and package 'sku' => 'order_id'
	 *
	 * @return  null
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	public static function set_current_order( $user_id, $group ) {
		update_user_meta( $user_id, 'ae_member_current_order', $group );
	}

	function setup_payment() {
		$order_data = $this->setup_orderdata( $_POST );

		$adID = isset( $_POST['ID'] ) ? $_POST['ID'] : '';
		//$author = isset($_POST['author']) ? $_POST['author'] : $user_ID;
		$packageID   = isset( $_POST['packageID'] ) ? $_POST['packageID'] : '';
		$paymentType = isset( $_POST['paymentType'] ) ? $_POST['paymentType'] : '';


		if ( ! empty( $order_data['options_name'] ) && ! empty( $order_data['options_days'] ) ) {
			$user_status = get_user_pro_status( $order_data['payer'] );
			$plans       = $this->get_plans();
			if ( ! empty( $plans ) ) {
				foreach ( $order_data['options_name'] as $key => $item ) {
					foreach ( $plans as $value ) {
						if ( $value->sku == $item ) {
							$price                         = getValueByProperty( $user_status, $value->sku );
							$option_info[ $key ]           = $value;
							$option_info[ $key ]->post_id  = $adID;
							$option_info[ $key ]->ID       = $option_info[ $key ]->sku;
							$option_info[ $key ]->et_price = $price != 'free' ? $price * $order_data['options_days'][ $key ] : 0;
							break;
						}
					}
				}
			}
		}

		/*switch($packageID) {
			// buy credits
			case 'no_pack':
				$plan_info = array();
				$plan_info['ID'] = 'emptysku';
				$plan_info['post_id'] = $adID;
				$plan_info['post_title'] = 'check fix number';
				$plan_info['et_price'] = 0;
				$plan_info['post_content'] = 'buy credit';
				$plan_info['post_type'] = $_POST['packageType'];//'fre_credit_plan - fre_credit_fix;
				$plan_info = apply_filters('fre_order_infor', $plan_info);
				break;

			// buy plan for PRO users
			case 'pro_plan':
				$plan_info = array();
				$plan_info['ID'] = 'emptysku';
				$plan_info['post_id'] = $adID;
				$plan_info['post_title'] = $_POST['planName'];
				$plan_info['et_price'] = $_POST['price'];
				$plan_info['post_content'] = $_POST['status'] . '_' . $_POST['time'];
				$plan_info['post_type'] = $_POST['packageType'];
				$plan_info = apply_filters('fre_order_infor', $plan_info);
				break;
		}*/

		switch ( $packageID ) {
			// buy credits
			case 'no_pack':
				$plan_info                 = [];
				$plan_info['ID']           = 'emptysku';
				$plan_info['post_id']      = $adID;
				$plan_info['post_title']   = 'check fix number';
				$plan_info['et_price']     = 0;
				$plan_info['post_content'] = 'buy credit';
				$plan_info['post_type']    = $_POST['packageType'];//'fre_credit_plan - fre_credit_fix;
				$plan_info                 = apply_filters( 'fre_order_infor', $plan_info );
				break;

			// buy plan for PRO users
			case 'pro_plan':
				$plan_info                 = [];
				$plan_info['ID']           = 'emptysku';
				$plan_info['post_id']      = $adID;
				$plan_info['post_title']   = $_POST['planName'];
				$plan_info['et_price']     = $_POST['price'];
				$plan_info['post_content'] = $_POST['status'] . '_' . $_POST['time'];
				$plan_info['post_type']    = $_POST['packageType'];
				$plan_info                 = apply_filters( 'fre_order_infor', $plan_info );
				break;

			// PRO-user pay for review
			case 'review_payment':
				$plan_info               = [];
				$plan_info['ID']         = 'emptysku';
				$plan_info['review_id']  = $_POST['reviewId'];
				$plan_info['post_id']    = $adID;
				$plan_info['post_title'] = $_POST['planName'];
				$plan_info['et_price']   = $_POST['price'];
				#$plan_info['post_content'] = $_POST['status'].'_'.$_POST['time'];
				$plan_info['post_type'] = $_POST['packageType'];
				$plan_info              = apply_filters( 'fre_order_infor', $plan_info );

				break;

			// buy plan for clients
			default:
				$plans = $this->get_plans();
				if ( empty( $plans ) ) {
					wp_send_json( [
						'success' => false,
						'msg'     => __( "There is no payment plan.", ET_DOMAIN )
					] );
				}

				foreach ( $plans as $key => $value ) {
					if ( $value->sku == $packageID ) {
						$plan_info = $value;
						break;
					}
				}
				$plan_info->post_id = $adID;
				$plan_info->ID      = $plan_info->sku;

				$ship = apply_filters( 'ae_payment_ship', [], $order_data, $_POST );
				break;
		}

		$ship = apply_filters( 'ae_payment_ship', [], $order_data, $_POST );

		$order_data = apply_filters( 'ae_payment_order_data', $order_data, $_POST );

		// insert order into database
		$order = new AE_Order( $order_data, $ship );

		//        file_put_contents(__DIR__ . '/c.txt', "\r\n" . '1-' . json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);
		$package_data = AE_Package::get_package_data( $_POST['author'] );

		if ( empty( $package_data[ $_POST['packageID'] ] ) ) {
			$order->add_product( (array) $plan_info );
		} else {
			$package_data_sku = $package_data[ $_POST['packageID'] ];
			if ( ! isset( $package_data_sku['qty'] ) || $package_data_sku['qty'] == 0 ) {
				$order->add_product( (array) $plan_info );
			}
		}

		if ( ! empty( $option_info ) ) {
			foreach ( $option_info as $value ) {
				$order->add_product( (array) $value );
			}
		}
		$order_data = $order->generate_data_to_pay();


		if ( $packageID == 'no_pack' ) {
			update_post_meta( $order_data['ID'], 'order_type', 'fre_credit_fix' );
		}
		// write session
		et_write_session( 'order_id', $order_data['ID'] );
		et_write_session( 'ad_id', $adID );
		$arg = apply_filters( 'ae_payment_links', [
			'return' => et_get_page_link( 'process-payment' ),
			'cancel' => et_get_page_link( 'process-payment' )
		] );
		/**
		 * process payment
		 */
		$paymentType_raw = $paymentType;
		$paymentType     = strtoupper( $paymentType );

		/**
		 * factory create payment visitor
		 */
		$visitor = AE_Payment_Factory::createPaymentVisitor( $paymentType, $order, $paymentType_raw );
		// setup visitor setting
		$visitor->set_settings( $arg );
		// accept visitor process payment
		$nvp = $order->accept( $visitor );
		if ( $nvp['ACK'] ) {
			$response = [
				'success'     => $nvp['ACK'],
				'data'        => $nvp,
				'paymentType' => $paymentType
			];
		} else {
			$response = [
				'success'     => false,
				'paymentType' => $paymentType,
				'msg'         => __( "Invalid payment gateway", ET_DOMAIN )
			];
		}
		/**
		 * filter $response send to client after process payment
		 *
		 * @param Array $response
		 * @param String $paymentType The payment gateway user select
		 * @param Array $order The order data
		 *
		 * @package  AE Payment
		 * @category payment
		 *
		 * @since    1.0
		 * @author   Dakachi
		 */
		$response = apply_filters( 'ae_setup_payment', $response, $paymentType, $order );
		wp_send_json( $response );
	}

	/**
	 *
	 * @param snippet
	 *
	 * @return snippet
	 * @since    snippet
	 * @package  snippet
	 * @category snippet
	 * @author   Dakachi
	 */
	function setup_orderdata( $data ) {
		global $user_ID;

		// remember to check isset or empty here
		$adID         = isset( $data['ID'] ) ? $data['ID'] : '';
		$isPro        = $data['isPro'] ?? false;
		$author       = isset( $data['author'] ) ? $data['author'] : $user_ID;
		$packageID    = isset( $data['packageID'] ) ? $data['packageID'] : '';
		$paymentType  = isset( $data['paymentType'] ) ? $data['paymentType'] : '';
		$options_name = isset( $data['options_name'] ) ? explode( ',', $data['options_name'] ) : '';
		$options_days = isset( $data['options_days'] ) ? explode( ',', $data['options_days'] ) : '';
		$errors       = [];

		// job id invalid
		if ( ! $isPro ) {
			if ( $adID ) {
				// author does not authorize job
				$job = get_post( $adID );

				if ( $job->post_type != BID && $author != $job->post_author && ! current_user_can( 'manage_options' ) ) {
					$author_error = __( "Post author information is incorrect!" . json_encode( $data ), ET_DOMAIN );
					$errors[]     = $author_error;
				}
			}
		}

		// input data error
		if ( ! empty( $errors ) ) {
			$response = [
				'success' => false,
				'errors'  => $errors
			];

			wp_send_json( $response );
		}

		////////////////////////////////////////////////
		////////////// process payment//////////////////
		////////////////////////////////////////////////

		$order_data = [
			'payer'        => $author,
			'total'        => '',
			'status'       => 'draft',
			'payment'      => $paymentType,
			'paid_date'    => '',
			'payment_plan' => $packageID,
			'post_parent'  => $adID,
			'options_name' => $options_name,
			'options_days' => $options_days
		];

		return $order_data;
	}

	/**
	 * abstract function get payment package for submit place
	 *
	 * @since  1.0
	 * @author Dakachi <ledd@youngworld.vn>
	 */
	abstract public function get_plans();
}