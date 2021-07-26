<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 17.06.2021
 * Time: 23:05
 */


class Wpp_En_User {

	/**
	 * Пользователь
	 *
	 * @var int
	 */
	public $user_id;
	public $data = [];
	public $post_data = [];
	public $post_convert = [];
	public $defaults = [];


	/**
	 * Wpp_En_User constructor.
	 *
	 * @param null $user_id
	 */
	public function __construct( $user_id = null ) {

		if ( ! defined( 'PROFILE' ) ) {
			define( 'PROFILE', 'fre_profile' );
		}

		$this->user_id = $user_id ?? get_current_user_id();

		$this->data = [
			'profile_id',
			'ihs_country_code',
			'user_phone',
			'register_status',
			'user_hour_rate',
			'user_profile_id',
			'user_currency',
			'user_skills',
			'user_available',
			'use_escrow',
			'paypal'
		];


		$this->post_data = [
			'et_professional_title',
			'hour_rate',
			'currency',
			'et_experience',
			'hour_rate',
			'post_content'
		];
		$this->defaults  = [
			'address',
			'avatar',
			'post_count',
			'comment_count',
			'et_featured',
			'et_expired_date'
		];

		$this->post_convert = [
			'post_parent',
			'post_title',
			'post_name',
			'post_content',
			'post_excerpt',
			'post_author',
			'post_status',
			'ID',
			'post_type',
			'comment_count',
			'guid'
		];

	}

	public function get_user_data() {

		$data = $this->data;
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

		$profile_data = $this->get_profile_data();

		$end = array_merge( $out, $profile_data );


		return (object) $end;

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

	public function get_profile_data( $profile_id = null ) {

		$profile_id = $profile_id ?? $this->get_profile_id();

		$post = get_post( $profile_id );
		$out  = [];

		foreach ( $this->post_convert as $one ) {
			$out[ $one ] = $post->{$one};
		}

		foreach ( $this->post_data as $one ) {
			$out[ $one ] = get_post_meta( $profile_id, $one, true );
		}

		return $out;
	}


	public function get_profile_id() {
		return $this->get_user_meta( 'user_profile_id' );
	}

	/*
					public function get_country_code() {
						return $this->get_user_meta( 'ihs-country-code' );
					}

					public function get_user_phone() {
						return $this->get_user_meta( 'user_phone' );
					}*/


}

//$GLOBALS[ 'wpp_ae_user' ] = new Wpp_En_User();