<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author XLPlugins
 * @package XLCore
 */
if ( ! class_exists( 'XL_Cache' ) ) {
	class XL_Cache {

		protected static $instance;
		protected $xl_core_cache = array();

		/**
		 * XL_Cache constructor.
		 */
		public function __construct() {

		}

		/**
		 * Creates an instance of the class
		 * @return type
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Set the cache contents by key and group within page scope
		 *
		 * @param $key
		 * @param $data
		 * @param string $group
		 */
		public function set_cache( $key, $data, $group = '0' ) {
			$this->xl_core_cache[ $group ][ $key ] = $data;
		}

		/**
		 * Get the cache contents by the cache key or group.
		 *
		 * @param $key
		 * @param string $group
		 *
		 * @return bool|mixed
		 */
		public function get_cache( $key, $group = '0' ) {
			if ( isset( $this->xl_core_cache[ $group ] ) && isset( $this->xl_core_cache[ $group ][ $key ] ) ) {
				return $this->xl_core_cache[ $group ][ $key ];
			}

			return false;
		}

		/**
		 * Reset the cache by group or complete reset by force param
		 *
		 * @param string $group
		 * @param bool $force
		 */
		function reset_cache( $group = '0', $force = false ) {
			if ( true === $force ) {
				$this->xl_core_cache = array();
			} elseif ( isset( $this->xl_core_cache[ $group ] ) ) {
				$this->xl_core_cache[ $group ] = array();
			}
		}

	}
}
