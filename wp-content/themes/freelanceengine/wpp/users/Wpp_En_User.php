<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Date: 17.06.2021
	 * Time: 23:05
	 */


	class Wpp_En_User{

		/**
		 * Пользователь
		 *
		 * @var int
		 */
		public $user_id;
		public $data = [];


		/**
		 * Wpp_En_User constructor.
		 *
		 * @param null $user_id
		 */
		public function __construct( $user_id = null ) {
			global $wpdb;
			$this->user_id = $user_id ?? get_current_user_id();

			$this->data = [ 'profile_id', 'ihs_country_code', 'user_phone','register_status' ];

		}

		/**
		 * Мета Дата
		 *
		 * @param $meta_key
		 *
		 * @return mixed
		 */
		private function get_user_meta( $meta_key ) {
			return get_user_meta( $this->user_id, $meta_key, true );
		}


		public function get_user_data() {

			$data = apply_filters( 'ae_define_user_meta', $this->data );
			$out  = [];

			foreach ( $data as $one ) {

				$key = false;
				if ( 'ihs_country_code' === $one ) {
					$key = 'ihs-country-code';
				} elseif ( 'profile_id' === $one ) {
					$key = 'user_profile_id';
				}

				$out[ $one ] = $this->get_user_meta( ! empty( $key ) ? $key : $one ) ?? false;
			}

			return (object) $out;

		}

		/*
				public function get_profile_id() {
					return $this->get_user_meta( 'user_profile_id' );
				}

				public function get_country_code() {
					return $this->get_user_meta( 'ihs-country-code' );
				}

				public function get_user_phone() {
					return $this->get_user_meta( 'user_phone' );
				}*/


	}

	//$GLOBALS[ 'wpp_ae_user' ] = new Wpp_En_User();