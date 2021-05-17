<?php

class Ip2Country {

	public $zip = 'https://static.mailster.co/geo/CountryDB.zip';
	private $dbfile;
	public $gi;
	private $renew = false;

	public function __construct( $dbfile ) {

		require_once MAILSTER_DIR . 'classes/libs/geoip.inc.php';

		$this->dbfile = $dbfile;

		if ( file_exists( $this->dbfile ) ) {
			$this->gi = new mailster_GeoIP( $this->dbfile );
		}
	}


	/**
	 *
	 *
	 * @param unknown $code
	 * @return unknown
	 */
	public function country( $code ) {
		return ( isset( $this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[ strtoupper( $code ) ] ) ) ? $this->gi->GEOIP_COUNTRY_NAMES[ $this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[ strtoupper( $code ) ] ] : $code;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_countries() {

		$countries = array();
		if ( $this->gi ) {
			$rawcountries = $this->gi->GEOIP_COUNTRY_NAMES;
			foreach ( $rawcountries as $key => $country ) {
				if ( ! $key ) {
					continue;
				}

				$countries[ $this->gi->GEOIP_COUNTRY_CODES[ $key ] ] = $country;
			}
		}

		return $countries;
	}


	/**
	 *
	 *
	 * @param unknown $ip
	 * @param unknown $part (optional)
	 * @return unknown
	 */
	public function get( $ip, $part = null ) {

		// append two semicollons for ipv4 addresses
		if ( strlen( $ip ) <= 15 ) {
			$ip = '::' . $ip;
		}

		// prevent some errors
		$error = ini_get( 'error_reporting' );
		error_reporting( E_ERROR );

		if ( ! is_null( $part ) ) {
			if ( method_exists( $this->gi, 'geoip_country_' . $part . '_by_addr_v6' ) ) {
				return call_user_func( array( $this->gi, 'geoip_country_' . $part . '_by_addr_v6' ), $ip );
			} else {
				return false;
			}
		}
		$return = (object) array(
			'id'      => call_user_func( array( $this->gi, 'geoip_country_ip_by_addr_v6' ), $ip ),
			'code'    => call_user_func( array( $this->gi, 'geoip_country_code_by_addr_v6' ), $ip ),
			'country' => call_user_func( array( $this->gi, 'geoip_country_name_by_addr_v6' ), $ip ),
		);

		error_reporting( $error );

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function update( $force = false ) {

		global $wp_filesystem;

		$filemtime = file_exists( $this->dbfile ) ? filemtime( $this->dbfile ) : 0;

		if ( ! $filemtime || $force || $this->renew ) {
			$do_renew = true;
		} else {
			$r       = wp_remote_get( $this->zip, array( 'method' => 'HEAD' ) );
			$headers = wp_remote_retrieve_headers( $r );
			// check header
			if ( ! isset( $headers['content-type'] ) || $headers['content-type'] != 'application/zip' ) {
				return new WP_Error( 'wrong_filetype', 'wrong file type' );
			}

			$lastmodified = strtotime( $headers['last-modified'] );
			$do_renew     = $lastmodified - $filemtime > 0;
		}

		if ( $do_renew ) {

			mailster_require_filesystem();
			set_time_limit( 120 );

			if ( ! function_exists( 'download_url' ) ) {
				include ABSPATH . 'wp-admin/includes/file.php';
			}

			// download
			$tempfile = download_url( $this->zip );

			if ( is_wp_error( $tempfile ) ) {
				return $tempfile;
			}

			// create directory
			if ( ! is_dir( dirname( $this->dbfile ) ) ) {
				if ( ! wp_mkdir_p( dirname( $this->dbfile ) ) ) {
					return new WP_Error( 'create_directory', sprintf( 'not able to create directory %s', dirname( $this->dbfile ) ) );
				}
			}

			// unzip
			if ( ! unzip_file( $tempfile, dirname( $this->dbfile ) ) ) {
				return new WP_Error( 'unzip_file', 'error unzipping file' );
			}

			if ( ! file_exists( $this->dbfile ) ) {
				return new WP_Error( 'file_missing', 'file is missing' );
			}

			$this->gi = new mailster_GeoIP( $this->dbfile );

		}

		return file_exists( $this->dbfile );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function remove() {

		global $wp_filesystem;
		mailster_require_filesystem();

		return $wp_filesystem->delete( $this->dbfile );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_real_ip() {
		return mailster_get_ip();
	}


}
