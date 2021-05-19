<?php
	/**
	 * @package wpp.framework
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	class Wpp_Console_Log{

		public $data;
		private $message;

		public function __construct( $data ) {

			$this->data = $data;
			$this->error_logger();

			if ( ! $this->is_debug() ) {
				return;
			}

			$this->add_action();
		}

		public function error_logger() {
			if ( $this->is_debug() ) {
				error_log( 'WPP LOG: ' . wp_json_encode( $this->data, JSON_UNESCAPED_UNICODE ), 0 );
			}
		}

		public function add_action() {
			if ( wp_doing_ajax() && class_exists( 'AJAX_Simply_Core' ) ) {
				if ( is_string( $this->data ) ) {
					$this->data = (object) $this->data;
				}
				$html = json_encode( $this->data, JSON_UNESCAPED_UNICODE );

				if ( is_array( $this->data ) ) {
					$html = $this->data;
				}

				$jx = new AJAX_Simply_Core;

				$jx->console( $html );
			} else {


				if ( is_string( $this->data ) ) {
					$this->data = (object) $this->data;
				}

				/**
				 * Тут фикс вообще надо ретурн
				 */
				if ( headers_sent() ) {
					echo '<script> console.log(' . json_encode( $this->data, JSON_UNESCAPED_UNICODE ) . '); </script>';
				} else {

					echo '<script>console.log(' . json_encode( $this->data, JSON_UNESCAPED_UNICODE ) . '); </script>';
				}
			}
			do_action( 'wpp_admin_notice' );
		}


		public function the_notice() {
			echo '<div class="notice notice-error is-dismissible"><p>' . $this->message . '</p></div>';
		}


		public function is_debug() {

			$return = defined( 'WP_DEBUG' ) && WP_DEBUG === true ? true : false;

			return $return;
		}

	}
