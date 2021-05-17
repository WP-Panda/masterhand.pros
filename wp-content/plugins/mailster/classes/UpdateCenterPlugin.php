<?php

// Version 4.1
// UpdateCenterPlugin Class
if ( class_exists( 'UpdateCenterPlugin' ) ) {
	return;
}

class UpdateCenterPlugin {

	private static $plugins     = null;
	private static $plugin_data = array();
	public static $caller       = null;

	private static $_instance  = null;
	private static $optionname = 'updatecenter_plugins';

	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public static function add( $args = array() ) {

		if ( ! isset( $args['plugin'] ) ) {
			$caller = array_shift( debug_backtrace() );
			$error  = sprintf( '[UpdateCenter] You have to define a "plugin" parameter for your plugin in %s on line %d', $caller['file'], $caller['line'] );

			return ( is_admin() )
				? wp_die( $error )
				: false;

		}

		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self( $args['plugin'] );
		}

		$plugin_data = (object) wp_parse_args(
			$args,
			array(
				'remote_url' => null,
				'slug'       => strtolower( dirname( $args['plugin'] ) ),
			)
		);

		$plugin_data->remote_url = trailingslashit( $plugin_data->remote_url );

		self::$plugin_data[ $plugin_data->slug ] = $plugin_data;

		register_deactivation_hook( $plugin_data->plugin, array( 'UpdateCenterPlugin', 'deactivate' ) );

		return self::$_instance;
	}



	/**
	 *
	 *
	 * @param unknown $caller
	 */
	private function __construct( $caller ) {

		self::$caller = $caller;

		self::$plugins = self::get_options();

		add_action( 'admin_init', array( &$this, 'init' ), 100 );
		add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter' ), 1 );

		add_action( 'wp_update_plugins', array( &$this, 'check_periodic_updates' ), 99 );
		add_action( 'updatecenterplugin_check', array( &$this, 'check_periodic_updates' ) );
		add_filter( 'upgrader_post_install', array( &$this, 'upgrader_post_install' ), 99, 3 );

		add_filter( 'http_request_args', array( &$this, 'http_request_args' ), 100, 2 );

	}


	/**
	 *
	 *
	 * @param unknown $pluginslug
	 * @param unknown $field      (optional)
	 * @return unknown
	 */
	public static function get( $pluginslug, $field = null ) {

		$pluginslug = strtolower( $pluginslug );

		if ( ! isset( self::$plugins[ $pluginslug ] ) ) {
			$pluginslug = dirname( $pluginslug );
			if ( ! isset( self::$plugins[ $pluginslug ] ) ) {
				return null;
			}
		}

		if ( is_null( $field ) ) {
			return (object) wp_parse_args( (array) self::$plugin_data[ $pluginslug ], (array) self::$plugins[ $pluginslug ] );
		}

		if ( isset( self::$plugins[ $pluginslug ]->$field ) ) {

			return self::$plugins[ $pluginslug ]->$field;
		}

		if ( isset( self::$plugin_data[ $pluginslug ]->$field ) ) {
			return self::$plugin_data[ $pluginslug ]->$field;
		}

		return null;

	}


	/**
	 *
	 *
	 * @param unknown $pluginslug
	 * @param unknown $licensecode   (optional)
	 * @param unknown $returnWPError (optional)
	 * @return unknown
	 */
	public static function verify( $pluginslug, $licensecode = null, $returnWPError = true ) {

		$pluginslug = strtolower( $pluginslug );

		if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
			$pluginslug = dirname( $pluginslug );
			if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
				return null;
			}
		}

		$plugin = self::$plugin_data[ $pluginslug ];

		if ( ! is_null( $licensecode ) ) {
			$plugin->licensecode = $licensecode;
		}

		$response = self::check( $pluginslug, 'verify' );

		if ( ! is_wp_error( $response ) ) {

			if ( isset( $response['verified'] ) && $response['verified'] ) {
				return $response['data'];
			}
			if ( ! $returnWPError ) {
				return false;
			}
			return new WP_Error( $response['code'], $response['message'], $response['data'] );

		} else {

			if ( ! $returnWPError ) {
				return false;
			}
			return $response;

		}

	}


	/**
	 *
	 *
	 * @param unknown $pluginslug
	 * @param unknown $userdata      (optional)
	 * @param unknown $licensecode   (optional)
	 * @param unknown $returnWPError (optional)
	 * @return unknown
	 */
	public static function register( $pluginslug, $userdata = array(), $licensecode = null, $returnWPError = true ) {

		$pluginslug = strtolower( $pluginslug );

		if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
			$pluginslug = dirname( $pluginslug );
			if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
				return null;
			}
		}

		$plugin = self::$plugin_data[ $pluginslug ];

		if ( ! is_null( $licensecode ) ) {
			$plugin->licensecode = $licensecode;
		}

		$response = self::check( $pluginslug, 'register', $userdata );

		if ( ! is_wp_error( $response ) ) {

			if ( isset( $response['verified'] ) && $response['verified'] ) {
				return $response['data'];
			}
			if ( ! $returnWPError ) {
				return false;
			}
			return new WP_Error( $response['code'], $response['message'], $response['data'] );

		} else {

			if ( ! $returnWPError ) {
				return false;
			}
			return $response;

		}

	}


	/**
	 *
	 *
	 * @param unknown $pluginslug
	 * @param unknown $licensecode   (optional)
	 * @param unknown $returnWPError (optional)
	 * @return unknown
	 */
	public static function reset( $pluginslug, $licensecode = null, $returnWPError = true ) {

		$pluginslug = strtolower( $pluginslug );

		if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
			$pluginslug = dirname( $pluginslug );
			if ( ! isset( self::$plugin_data[ $pluginslug ] ) ) {
				return null;
			}
		}

		$plugin = self::$plugin_data[ $pluginslug ];

		if ( ! is_null( $licensecode ) ) {
			$plugin->licensecode = $licensecode;
		}

		$response = self::check( $pluginslug, 'reset' );

		if ( ! is_wp_error( $response ) ) {

			if ( isset( $response['reset'] ) && $response['reset'] ) {
				return $response['data'];
			}
			if ( ! $returnWPError ) {
				return false;
			}
			return new WP_Error( $response['code'], $response['message'], $response['data'] );

		} else {

			if ( ! $returnWPError ) {
				return false;
			}
			return $response;

		}

	}


	public function init() {

		add_filter( 'plugins_api', array( &$this, 'plugins_api' ), 10, 3 );
		add_filter( 'plugins_api_result', array( &$this, 'plugins_api_result' ), 10, 3 );

		if ( ! is_admin() ) {
			return;
		}

		if ( is_multisite() && ! is_network_admin() ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			foreach ( self::$plugins as $slug => $plugin ) {
				if ( ! is_plugin_active_for_network( plugin_basename( $plugin->plugin ) ) && time() - $plugin->last_update > 3600 ) {
					do_action( 'updatecenterplugin_check' );
					break;
				}
			}
		}

		add_filter( 'admin_notices', array( &$this, 'admin_notices' ), 99 );

		global $pagenow;

		if ( empty( self::$plugins ) || 'update-core.php' == $pagenow ) {
			do_action( 'updatecenterplugin_check' );
		}

	}


	public function admin_notices() {

		global $pagenow;

		if ( 'update-core.php' == $pagenow || 'update.php' == $pagenow ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		foreach ( self::$plugins as $slug => $data ) {

			$notices = array();

			if ( isset( $data->admin_notices ) ) {
				foreach ( $data->admin_notices as $version => $notice ) {
					if ( version_compare( $version, $data->version, '<=' ) ) {
						continue;
					}

					$notices[] = stripslashes( $notice );

				}
			}

			if ( empty( $notices ) ) {
				continue;
			}

			$output = array();

			$nonce = wp_create_nonce( 'upgrade-plugin_' . $data->plugin );
			foreach ( $notices as $notice ) {
				$output[] = str_replace(
					'%%updateurl%%',
					admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $data->plugin ) . '&_wpnonce=' . $nonce ),
					$notice
				);
			}

			echo '<div class="update-nag update-nag-' . $slug . '"><div>' . implode( '</div><div>', $output ) . '</div></div>';

		}

	}



	/**
	 *
	 *
	 * @param unknown $new_version
	 * @param unknown $old_version
	 * @param unknown $only_minor  (optional)
	 * @return unknown
	 */
	public function version_compare( $new_version, $old_version, $only_minor = false ) {

		if ( $only_minor ) {

			$new = explode( '.', rtrim( $new_version, '.0' ) );
			$old = explode( '.', rtrim( $old_version, '.0' ) );

			$is_major_update = version_compare( $new[1], $old[1], '>' ) || version_compare( (int) $new_version, (int) $old_version, '>' );

			$is_minor_update = ( ! $is_major_update && version_compare( strstr( $new_version, '.' ), strstr( $old_version, '.' ), '>' ) );

			return $is_minor_update;
		}

		return version_compare( $new_version, $old_version, '>' );

	}


	/**
	 *
	 *
	 * @param unknown $r
	 * @param unknown $url
	 * @return unknown
	 */
	public function http_request_args( $r, $url ) {

		if ( empty( self::$plugins ) ) {
			return $r;
		}

		$plugin_urls = wp_list_pluck( self::$plugins, 'package' );

		if ( ( $slug = array_search( $url, $plugin_urls ) ) !== false ) {
			if ( ! isset( $r['headers'] ) ) {
				$r['headers'] = array();
			}
			$r['headers']['x-updatecenter'] = serialize( self::header_infos( $slug ) );
		}

		return $r;

	}


	/**
	 *
	 *
	 * @param unknown $res
	 * @param unknown $action
	 * @param unknown $args
	 * @return unknown
	 */
	public function plugins_api( $res, $action, $args ) {

		global $pagenow;

		if ( ! isset( $args->slug ) ) {
			return $res;
		}

		$slug = $args->slug;

		if ( ! isset( self::$plugins[ $slug ] ) ) {
			return $res;
		}

		if ( ! isset( self::$plugin_data[ $slug ] ) ) {
			return $res;
		}

		if ( $pagenow != 'update-core.php' ) {

			$version_info = self::check( $slug );

			if ( ! $version_info ) {
				wp_die( 'There was an error while getting the information about the plugin. Please try again later' );
			}

			$res       = (object) $version_info;
			$res->slug = $slug;
			if ( isset( $res->contributors ) ) {
				$res->contributors = (array) $res->contributors;
			}

			$res->sections = isset( $res->sections ) ? (array) $res->sections : array();

		} else {

			$res = self::$plugins[ $slug ];

		}

		return $res;

	}


	/**
	 *
	 *
	 * @param unknown $res
	 * @param unknown $action
	 * @param unknown $args
	 * @return unknown
	 */
	public function plugins_api_result( $res, $action, $args ) {

		if ( ! isset( $this->slug ) ) {
			return $res;
		}

		if ( $args->slug == $this->slug ) {
			$res->external = true;
		}

		return $res;

	}


	public function check_periodic_updates() {

		if ( did_action( 'updatecenterplugin_check' ) > 1 ) {
			return;
		}

		// get the actual version
		foreach ( self::$plugin_data as $slug => $plugin ) {
			if ( ! isset( self::$plugins[ $plugin->slug ] ) ) {
				self::$plugins[ $plugin->slug ] = (object) array(
					'slug'          => $slug,
					'plugin'        => $plugin->plugin,
					'new_version'   => null,
					'url'           => null,
					'package'       => null,
					'version'       => null,
					'last_update'   => 0,
					'support'       => null,
					'update'        => null,
					'verified'      => false,
					'compatibility' => new StdClass(),
				);
			}

			if ( is_readable( WP_PLUGIN_DIR . '/' . $plugin->plugin ) ) {
				$plugin_data                             = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin->plugin );
				self::$plugins[ $plugin->slug ]->version = $plugin_data['Version'];
			}
		}

		$collection = $this->get_collection();

		foreach ( $collection as $remote_url => $plugins ) {

			if ( empty( $plugins ) ) {
				continue;
			}

			$result = $this->check_collection( $remote_url, $plugins );

			if ( is_wp_error( $result ) || empty( $result ) || ! is_array( $result ) ) {
				continue;
			}

			foreach ( $result as $slug => $updatecenterinfo ) {

				if ( ! is_object( $updatecenterinfo ) ) {

					self::$plugins[ $slug ]->last_update = time();
					self::$plugins[ $slug ]->new_version = null;

					// $version_info should be an array with keys ['version'] and ['download_link']
				} elseif ( isset( $updatecenterinfo->version ) && isset( $updatecenterinfo->download_link ) ) {

					self::$plugins[ $slug ]->new_version = $updatecenterinfo->version;
					self::$plugins[ $slug ]->package     = $updatecenterinfo->download_link;
					self::$plugins[ $slug ]->update      = version_compare( self::$plugins[ $slug ]->new_version, self::$plugins[ $slug ]->version, '>' );
					self::$plugins[ $slug ]->last_update = time();

					if ( isset( $updatecenterinfo->icons ) ) {
						self::$plugins[ $slug ]->icons = (array) $updatecenterinfo->icons;
					}

					if ( isset( $updatecenterinfo->banners ) ) {
						self::$plugins[ $slug ]->banners = (array) $updatecenterinfo->banners;
					}

					if ( isset( $updatecenterinfo->requires ) ) {
						self::$plugins[ $slug ]->requires = $updatecenterinfo->requires;
					}

					if ( isset( $updatecenterinfo->tested ) ) {
						self::$plugins[ $slug ]->tested = $updatecenterinfo->tested;
					}

					if ( isset( $updatecenterinfo->upgrade_notice ) ) {
						self::$plugins[ $slug ]->upgrade_notice = stripslashes_deep( $updatecenterinfo->upgrade_notice );
					}

					if ( isset( $updatecenterinfo->admin_notices ) ) {
						self::$plugins[ $slug ]->admin_notices = stripslashes_deep( $updatecenterinfo->admin_notices );
					}

					if ( isset( $updatecenterinfo->verified ) ) {
						self::$plugins[ $slug ]->verified = $updatecenterinfo->verified;
					}

					if ( isset( $updatecenterinfo->support ) ) {
						self::$plugins[ $slug ]->support = $updatecenterinfo->support;
					}

					if ( isset( $updatecenterinfo->compatibility ) ) {
						self::$plugins[ $slug ]->compatibility = (object) $updatecenterinfo->compatibility;
					}
				}
			}
		}

		self::save_options();

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function upgrader_post_install( $bool, $hook_extra, $result ) {

		if ( isset( $hook_extra['plugin'] ) && isset( self::$plugins[ dirname( $hook_extra['plugin'] ) ] ) ) {
			unset( self::$plugins[ dirname( $hook_extra['plugin'] ) ] );
			self::save_options();
		}
		return $bool;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_collection() {

		$timeout = 12 * HOUR_IN_SECONDS;

		if ( 'updatecenterplugin_check' == current_filter() ) {
			$timeout = 60;
		}

		$collection = array();

		foreach ( self::$plugin_data as $slug => $plugin ) {

			if ( isset( self::$plugins[ $slug ] ) && time() - self::$plugins[ $slug ]->last_update >= $timeout ) {
				$collection[ $plugin->remote_url ]          = isset( $collection[ $plugin->remote_url ] ) ? $collection[ $plugin->remote_url ] : array();
				$collection[ $plugin->remote_url ][ $slug ] = self::header_infos( $slug );
			}
		}

		return $collection;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public static function deactivate() {

		$plugin = str_replace( 'deactivate_', '', current_filter() );
		$slug   = dirname( $plugin );

		if ( isset( self::$plugins[ $slug ] ) ) {
			self::$plugins[ $slug ]->last_update = 0;
			self::$plugins[ $slug ]->update      = null;
			self::save_options();
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public static function clear_options() {

		self::$plugins = array();
		update_option( self::$optionname, self::$plugins );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private static function get_options() {

		return get_option( self::$optionname, array() );

	}


	private static function save_options() {

		update_option( self::$optionname, self::$plugins );

	}


	/**
	 *
	 *
	 * @param unknown $remote_url
	 * @param unknown $plugins
	 * @return unknown
	 */
	public static function check_collection( $remote_url, $plugins ) {

		$body = http_build_query( array( 'updatecenter_data' => array_values( $plugins ) ), null, '&' );

		$response = self::save_response(
			add_query_arg(
				array(
					'updatecenter_action' => 'versions',
					'updatecenter_slug'   => array_keys( $plugins ),
				),
				$remote_url
			),
			$body
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = trim( wp_remote_retrieve_body( $response ) );

		if ( is_wp_error( $response_body ) ) {
			return $response_body;
		}

		$result = json_decode( $response_body );

		if ( empty( $result ) ) {
			return array_flip( array_keys( $plugins ) );
		}

		return is_array( $result ) ? array_combine( array_keys( $plugins ), $result ) : array();

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $action (optional)
	 * @param unknown $data   (optional)
	 * @return unknown
	 */
	public static function check( $slug, $action = 'info', $data = array() ) {

		if ( ! isset( self::$plugins[ $slug ] ) ) {
			return null;
		}

		$body = self::header_infos( $slug );
		if ( ! empty( $data ) ) {
			$body = wp_parse_args( array( 'data' => $data ), $body );
		}
		$body = http_build_query( $body, null, '&' );

		$response = self::save_response(
			add_query_arg(
				array(
					'updatecenter_action' => $action,
					'updatecenter_slug'   => $slug,
				),
				self::$plugin_data[ $slug ]->remote_url
			),
			$body
		);

		if ( is_wp_error( $response ) ) {

			if ( 'http_request_failed' == $response->get_error_code() ) {
				return new WP_Error( 'http_err', sprintf( 'Not able to get request from %s', parse_url( self::$plugin_data[ $slug ]->remote_url, PHP_URL_HOST ) ) );
			}
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code != 200 ) {
			return array(
				'code'    => 'http_err',
				'message' => wp_remote_retrieve_response_message( $response ),
				'data'    => '',
			);
		}

		$response_body = trim( wp_remote_retrieve_body( $response ) );

		$result = json_decode( $response_body, true );

		return $result;

	}


	/**
	 *
	 *
	 * @param unknown $url
	 * @param unknown $body
	 * @return unknown
	 */
	public static function save_response( $url, $body ) {

		$ssl = wp_http_supports( array( 'ssl' ) );

		$args = array(
			'headers' => array(
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
				'x-ip'           => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : null,
			),
			'body'    => $body,
			'timeout' => 5,
		);

		$response = wp_remote_post( $url, $args );

		if ( $ssl && is_wp_error( $response ) ) {
			$http_url = set_url_scheme( $url, 'http' );
			$response = wp_remote_post( $http_url, $args );
		}

		return $response;

	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	public function update_plugins_filter( $value ) {

		if ( empty( self::$plugins ) || ! $value ) {
			return $value;
		}

		foreach ( self::$plugins as $slug => $plugin ) {

			if ( empty( $plugin->package ) ) {
				continue;
			}

			if ( version_compare( $plugin->version, $plugin->new_version, '<' ) ) {
				$value->response[ $plugin->plugin ] = self::$plugins[ $slug ];
			} elseif ( isset( $value->no_update ) ) {
				$value->no_update[ $plugin->plugin ] = self::$plugins[ $slug ];
			}
		}

		return $value;
	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @return unknown
	 */
	private static function header_infos( $slug ) {

		global $pagenow, $wpdb;

		include ABSPATH . WPINC . '/version.php';

		$is_multisite = is_multisite();

		$return = array(
			'licensecode' => isset( self::$plugin_data[ $slug ]->licensecode ) ? self::$plugin_data[ $slug ]->licensecode : null,
			'version'     => self::$plugins[ $slug ]->version,
			'wp-version'  => $wp_version,
			'referer'     => $is_multisite ? network_site_url() : home_url(),
			'multisite'   => $is_multisite ? get_blog_count() : false,
			'auto'        => $pagenow == 'wp-cron.php',
			'php'         => phpversion(),
			'mysql'       => method_exists( $wpdb, 'db_version' ) ? $wpdb->db_version() : null,
			'theme'       => function_exists( 'get_stylesheet' ) ? get_stylesheet() : null,
			'locale'      => get_locale(),
		);

		if ( isset( self::$plugin_data[ $slug ]->custom ) ) {
			$return['custom'] = self::$plugin_data[ $slug ]->custom;
		}

		return $return;
	}


}
