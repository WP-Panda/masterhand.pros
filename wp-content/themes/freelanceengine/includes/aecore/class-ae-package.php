<?php

/**
 * class AE_Package control and manage payment plan
 *
 * @package AE Package
 * @category payment
 *
 * @version 1.0
 * @author Dakachi
 */
class AE_Package extends AE_Pack {
	static $instance;

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new AE_Package();
		}

		return self::$instance;
	}

	function __construct( $post_type = 'pack', $meta = array() ) {
		$this->post_type   = $post_type;
		$this->option_name = 'payment_package';

		// backend text
		$this->localize = array(
			'backend_text' => array(
				'text' => __( '%s for %d days', ET_DOMAIN ),
				'data' => array(
					'et_price',
					'et_duration'
				)
			)
		);

		$this->meta = array_merge( $meta, array(
			'sku',
			'et_price',
			'et_number_posts',
			'order',
			'option_name',
			'et_duration',
			'et_featured',
			'et_not_duration'
		) );

		$this->convert = array(
			'post_title',
			'post_name',
			'post_content',
			'post_excerpt',
			'post_author',
			'post_status',
			'ID',
			'post_type'
		);

		self::$instance = $this;
	}

	/**
	 * override convert function to update backend text
	 *
	 * @param object $post
	 * @param string $thumbnail
	 * @param bool $excerpt
	 * @param bool $singular
	 *
	 * @return void
	 * @since 1.0
	 * @package Appengine
	 * @category void
	 * @author Daikachi
	 */
	function convert( $post, $thumbnail = '', $excerpt = false, $singular = false ) {
		$result = parent::convert( $post, $thumbnail );
		if ( $result ) {
			$currency = ae_currency_sign( false );
			$align    = ae_currency_align( false );
			if ( $align ) {
				$result->backend_text = sprintf( __( "(%s)%s for %d days", ET_DOMAIN ), $currency, $result->et_price, $result->et_duration );
			} else {
				$result->backend_text = sprintf( __( "%s(%s) for %d days", ET_DOMAIN ), $result->et_price, $currency, $result->et_duration );
			}
		}

		return $result;
	}

	/**
	 * get a package by sku
	 */
	/**
	 * get a package by sku
	 *
	 * @param string $sku
	 *
	 * @return object $value or false
	 * @since 1.0
	 * @package Appengine
	 * @category void
	 * @author Daikachi
	 */
	public function get( $sku ) {
		$options     = AE_Options::get_instance();
		$option_name = $this->option_name;

		if ( $options->$option_name ) {
			$this->fetch();
		}
		if ( $options->$option_name ) {
			$packages = $options->$option_name;
			foreach ( $packages as $key => $value ) {
				if ( $value->sku == $sku ) {
					return $value;
				}
			}
		}

		return false;
	}

	/**
	 * get a package by sku and package post type
	 *
	 * @param string $sku
	 * @param string $pack_post_type the post type of package
	 *
	 * @return mixed data of package
	 * @since FrE version 1.5.4
	 * @author Tambh
	 */
	public function get_pack( $sku = '', $pack_post_type = '' ) {
		$options     = AE_Options::get_instance();
		$option_name = $this->option_name;

		if ( $options->$option_name ) {
			$this->fetch();
		}
		if ( $options->$option_name ) {
			$packages = $options->$option_name;
			foreach ( $packages as $key => $value ) {
				if ( $value->sku == $sku && $value->post_type == $pack_post_type ) {
					return $value;
				}
			}
		}

		// Đang có 1 lỗi rất lạ ở đây là không lấy đc gói credit khi mua credit đầu tiên, trước khi mua các gói mua tin khác
		// Nên đang lấy lại gói credit 1 lần nữa
		if( empty($options->$option_name) && in_array( $pack_post_type ,array('fre_credit_plan','bid_plan') ) ){ // 1.8.6 fix bug pay bid
			$list_credit_plan = get_posts(
				array(
					'post_type' => $pack_post_type,
					'post_status' => 'publish',
					'meta_query' => array(
						array(
							'key'   => 'sku',
							'value' => $sku,
						)
					)
				)
			);
			if(!empty($list_credit_plan)){
				$pack = array_shift($list_credit_plan);
				$et_featured = get_post_meta($pack->ID,'et_featured',true);
				$et_number_posts = get_post_meta($pack->ID,'et_number_posts',true);
				$et_price = get_post_meta($pack->ID,'et_price',true);
				$pack->et_number_posts = $et_number_posts;
				$pack->sku = $sku;
				$pack->et_price = $et_price;
				$pack->et_featured = $et_featured;

				return $pack;
			}
		}

		return false;
	}

	/**
	 * check user are using a pakage or not
	 *
	 * @param string $sku the id of package
	 * @param integer $user_id The user id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	public static function check_use_package( $sku, $user_id = 0 ) {

		// if set user to current user id if not have input
		if ( ! $user_id ) {
			global $user_ID;
			$user_id = $user_ID;
		}
		//
		$orders = AE_Payment::get_current_order( $user_id );
		// order not exists
		if ( ! isset( $orders[ $sku ] ) ) {
			return false;
		}
		$order = get_post( $orders[ $sku ] );
		// invalid order, or order was trashed
		if ( ! $order || is_wp_error( $order ) || ! in_array( $order->post_status, array( 'pending', 'publish' ) ) ) {
			return false;
		}
		// get user package data
		$used_package = self::get_package_data( $user_id );

		// if user use the package with qty greater than 0 return true ( has post left )
		if ( isset( $used_package[ $sku ] ) && $used_package[ $sku ]['qty'] > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * get all used package data
	 *
	 * @param integer $user_id
	 *
	 * @return object $used_package
	 * @since 1.0
	 * @package Appengine
	 * @category void
	 * @author Daikachi
	 */
	public static function get_package_data( $user_id ) {
		$used_package = get_user_meta( $user_id, 'ae_member_packages_data', true );
		if ( $used_package == '' ) {
			return unserialize( $used_package );
		} else {
			return $used_package;
		}
	}

	public static function set_package_data( $user_id, $used_package ) {
		update_user_meta( $user_id, 'ae_member_packages_data', $used_package );
	}

	/**
	 * add user package data when purchase a package
	 *
	 * @param String $sku The package stock keep unit
	 * @param Integer $user_id The user ID
	 *
	 * @return bool true if set package successful
	 * @since  1.0
	 * @author  Dakachi <ledd@youngworld.vn>
	 */
	public static function add_package_data( $sku, $user_id = 0 ) {

		if ( ! $user_id ) {
			global $user_ID;
			$user_id = $user_ID;
		}

		$instance   = self::get_instance();
		$packageObj = $instance->get( $sku );
		// validate package object
		if ( ! $packageObj || is_wp_error( $packageObj ) ) {
			return $packageObj;
		}
		$qty = (int) $packageObj->et_number_posts;

		$used_package = self::get_package_data( $user_id );

		$used_package[ $sku ] = array(
			'ID'  => $sku,
			'qty' => $qty
		);

		self::set_package_data( $user_id, $used_package );

		return true;
	}

	/**
	 * update user package data after post an ad
	 *
	 * @param string $package the package sku
	 * @param integer $user_id the user id
	 *
	 * @return null
	 * @author  Dakachi
	 */
	public static function update_package_data( $package, $user_id = 0 ) {
		if ( ! $user_id ) {
			global $user_ID;
			$user_id = $user_ID;
		}

		$used_package = self::get_package_data( $user_id );

		if ( ! isset( $used_package[ $package ] ) ) {
			return;
		}
		$qty = (int) ( $used_package[ $package ]['qty'] - 1 );
		// $package = $used_package[$package];
		// $qty = (int)$package['qty'] - 1;

		if ( $qty == 0 ) {

			// remove user current order for the package
			$group = AE_Payment::get_current_order( $user_id );
			unset( $group[ $package ] );

			AE_Payment::set_current_order( $user_id, $group );
		}
		$used_package[ $package ]['qty'] = $qty;
		self::set_package_data( $user_id, $used_package );
	}

	/**
	 * check user use package or use freepackage
	 *
	 * @param  string $package_id The pacakge sku to identify package
	 * @param  object $ad Current purchase post
	 *
	 * @return array
	 *         'url' => string process-payment-url base on type free/usePackage,
	 *         'success' => bool
	 * @author Dakachi
	 */
	public static function package_or_free( $package_id, $ad ) {

		$instance = self::get_instance();

		$response = array(
			'success' => false
		);

		$use_package = AE_Package::check_use_package( $package_id );
		$package     = $instance->get( $package_id );

		if ( $use_package ) {
			et_write_session( 'ad_id', $ad->ID );
			$response['success'] = true;
			$response['url']     = et_get_page_link( 'process-payment', array(
				'paymentType' => 'usePackage'
			) );

			return $response;
		}

		if ( $package->et_price == 0 ) {
			et_write_session( 'ad_id', $ad->ID );
			$response['success'] = true;
			$response['url']     = et_get_page_link( 'process-payment', array(
				'paymentType' => 'free'
			) );

			return $response;
		}

		return $response;
	}

	public static function limit_free_plan( $package ) {

		// check and limit seller user free plan
		(int) $limit_free_plan = ae_get_option( 'limit_free_plan' );

		$instance = self::get_instance();
		$package  = $instance->get( $package );

		$response = array(
			'success' => false
		);
		if ( $package && $package->et_price == 0 && $limit_free_plan

			//&& !current_user_can( 'manage_options' )
		) {

			/**
			 * update number of free plan seller used
			 */

			// $number = self::update_used_free_plan();
			// if ($number > $limit_free_plan) {

			//     $response['success'] = true;
			//     $response['msg'] = __("You have reached the maximum number of Free posts. Please select another plan.", ET_DOMAIN);

			//     return $response;
			// }
		}

		return $response;
	}
	/**
	 * check if user post via free package one more time.
	*/
	static function can_post_free( $package ){

		$pack = self::get_instance()->get( $package );

		$number_free_posted = (int) self::get_used_free_plan();


		// if( $pack && $pack->et_price > 0)
		// 	return true; // ignore if current packge not free.

		if( $pack && $pack->et_price == 0 && $number_free_posted >= $pack->et_number_posts){
			return false;
		}

		return true; // user can not post more free project

	}

	/**
	 * Update number post free
	 *
	 * @param integer $user_ID
	 *
	 * @return integer $number or bool false
	 * @since 1.0
	 * @package Appengine
	 * @category void
	 * @author Tambh
	 */
	public static function update_used_free_plan( $user_ID = '' ) {
		global $user_ID;
		if ( $user_ID ) {
			$number = self::get_used_free_plan();
			$number = (int) $number + 1;
			update_user_meta( $user_ID, 'ae_free_plan_used', $number );

			return $number;
		}

		return false;
	}

	/**
	 * get number project posted via free package
	*/
	public static function get_used_free_plan( $user_ID = '' ) {
		global $user_ID;

		return get_user_meta( $user_ID, 'ae_free_plan_used', true );
	}
}