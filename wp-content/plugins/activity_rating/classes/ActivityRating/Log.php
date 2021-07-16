<?php

	namespace ActivityRating;

	class Log{
		static $_instance = null;

		private $debug = false;
		private $logDebug = [];

		public function __construct() {

		}

		public function logging( $flag = true ) {
			$this->debug = $flag ? true : false;
		}

		public function addLog( $flag = '', $msg = '', $count = false ) {
			$flag = is_string( $flag ) ? $flag : 'debug';
			if ( $this->debug ) {
				if ( is_array( $msg ) ) {
					if ( $count ) {
						$this->logDebug[ $flag ][] = $msg;
					} else {
						$ar                      = isset( $this->logDebug[ $flag ] ) ? $this->logDebug[ $flag ] : [];
						$this->logDebug[ $flag ] = array_merge( (array) $ar, $msg );
					}
				} else {
					if ( $count ) {
						$c                       = isset( $this->logDebug[ $flag ] ) ? (int) $this->logDebug[ $flag ] : 0;
						$this->logDebug[ $flag ] = $c + ( ( is_numeric( $msg ) && $msg > 1 ) ? $msg : 1 );
					} else {
						if ( empty( $flag ) ) {
							$this->logDebug[] = $msg;
						} else {
							if ( isset( $this->logDebug[ $flag ] ) && is_array( $this->logDebug[ $flag ] ) ) {
								$this->logDebug[ $flag ][] = $msg;
							} else {
								if ( ! isset( $this->logDebug[ $flag ] ) ) {
									$this->logDebug[ $flag ] = '';
								}
								$this->logDebug[ $flag ] .= ! empty( $this->logDebug[ $flag ] ) ? ', ' . $msg : $msg;
							}
						}
					}
				}
			}
		}

		public function getLog( $key = null ) {
			if ( is_null( $key ) ) {
				return $this->logDebug;
			}

			return isset( $this->logDebug[ $key ] ) ? $this->logDebug[ $key ] : null;
		}

		public function reset( $key = null ) {
			if ( is_null( $key ) ) {
				$this->logDebug = [];
			} else {
				$this->logDebug[ $key ] = [];
			}
		}

		public function save( $fileSuffix = '' ) {
			$log = $this->getLog();
			if ( ! empty( $log ) ) {
				$curr_m = microtime( true );
				$now    = \DateTime::createFromFormat( 'U.u', $curr_m );
				$now->setTimeZone( new \DateTimeZone( 'UTC' ) );

				$fileName = 'log_' . ( ! empty( strval( $fileSuffix ) ) ? strval( $fileSuffix ) . '_' : '' );
				file_put_contents( dirname( dirname( __DIR__ ) ) . '/' . $fileName . $now->format( "Y-m-d_H" ) . '.log', $now->format( "Y-m-d H:i:s" ) . "\n" . json_encode( $log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ), FILE_APPEND );

				$this->reset();
			}
		}

		public static function getInstance() {
			if ( self::$_instance === null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}