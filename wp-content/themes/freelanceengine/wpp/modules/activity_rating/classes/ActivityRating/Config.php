<?php

	namespace ActivityRating;

	class Config{
		protected static $_instance = null;

		const tbConfig = 'activity_rating_config';

		public $tbConfig = '';

		private $_checkList = [
			'coefficient.proStatus',
			'coefficient.amountPayment',
			'value.siteVisit',
			'value.oneFieldProfile',
			'freelancer.value.installmentPlan',
			'freelancer.value.onePortfolio',
			'freelancer.value.asReferral',
			'freelancer.value.asReferrer',
			'freelancer.value.forReward',
			'freelancer.value.forSkill',
			'freelancer.value.forReview',
			'freelancer.value.forEndorseSkill',
			'freelancer.value.fromRatingEmployer',
			'freelancer.value.projectSuccess',
			'employer.value.fromRatingFreelancer',
			'employer.value.installmentPlan',
			'employer.value.asReferrer',
			'employer.value.asReferral',
			'employer.value.forReward',
			'employer.value.forReview',
			'employer.value.projectSuccess',
			'employer.value.bidAccepted',
			'employer.value.forSkill',
			'employer.value.forEndorseSkill',
		];

		private $_list = [];

		public function __construct() {
			global $wpdb;

			$this->db = $wpdb;

			$this->tb_prefix = $wpdb->prefix;
			$this->tbConfig  = $this->tb_prefix . self::tbConfig;

			$this->logger = Log::getInstance();
		}

		public function update( $data = [] ) {
			if ( ! empty( $data ) && is_array( $data ) ) {
				$sumUpd = 0;
				foreach ( $data as $item => $value ) {
					$param = str_replace( '_', '.', $item );
					if ( in_array( $param, $this->_checkList ) ) {

						$result = $this->db->update( $this->tbConfig, [ 'value' => $this->escapeStr( floatval( $value ) ) ], [ 'name' => $this->escapeStr( $param ) ] );
						$sumUpd += (int) $result;
					}
				}

				return ( $sumUpd ) ? 1 : 0;
			}

			return false;
		}

		public function getAll() {
			$items = [];
			$res   = $this->db->get_results( "SELECT name, value FROM {$this->tbConfig}", ARRAY_A );
			if ( ! empty( $res ) ) {
				foreach ( $res as $r ) {
					$items[ $r[ 'name' ] ] = $r[ 'value' ];
				}
			}

			return $items;
		}

		public function getCoeffProStatus() {
			return intval( $this->get( "coefficient.proStatus" ) ) / 100;
		}

		public function getCoeffFromRatingEmployer() {
			return intval( $this->getFreelancer( 'fromRatingEmployer' ) ) / 100;
		}

		public function getCoeffFromRatingFreelancer() {
			return intval( $this->getEmployer( 'fromRatingFreelancer' ) ) / 100;
		}

		public function getCoeffAmountPayment() {
			return $this->get( "coefficient.amountPayment" );
		}

		public function getValSiteVisit() {
			return $this->get( "value.siteVisit" );
		}

		public function getValOneFieldProfile() {
			return $this->get( "value.oneFieldProfile" );
		}

		public function getFreelancer( $key = '' ) {
			return $this->get( "freelancer.value.{$key}" );
		}

		public function getEmployer( $key = '' ) {
			return $this->get( "employer.value.{$key}" );
		}

		public function get( $name, $force = false ) {
			if ( empty( $name ) ) {
				return null;
			}

			if ( $force ) {
				$value                = $this->db->get_var( "SELECT value FROM {$this->tbConfig} WHERE name = '{$this->escapeStr($name)}'" );
				$this->_list[ $name ] = $value;
			} elseif ( ! isset( $this->_list[ $name ] ) ) {
				$value                = $this->db->get_var( "SELECT value FROM {$this->tbConfig} WHERE name = '{$this->escapeStr($name)}'" );
				$this->_list[ $name ] = $value;
			}

			return $this->_list[ $name ];
		}

		public function addError( $msg ) {
			$this->logger->addLog( 'error', $msg );
		}

		public function getError() {
			$error = $this->logger->getLog( 'error' );

			return ( is_array( $error ) ) ? implode( ', ', $error ) : $error;
		}

		public function escapeStr( $str ) {
			$str = is_array( $str ) ? '' : trim( strval( $str ) );

			return $this->db->_escape( htmlspecialchars( $str ) );
		}

		public static function getInstance() {
			if ( self::$_instance === null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}