<?php

class TwitterApiClass {

	private $token = '';
	private $token_secret = '';
	private $consumer_key = '';
	private $consumer_secret = '';

	private $host = 'api.twitter.com';
	private $method = 'GET';
	private $version = '1.1';

	/**
	 *
	 *
	 * @param unknown $token
	 * @param unknown $token_secret
	 * @param unknown $consumer_key
	 * @param unknown $consumer_secret
	 */
	public function __construct( $token, $token_secret, $consumer_key, $consumer_secret ) {
		$this->token = $token;
		$this->token_secret = $token_secret;
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
	}


	/**
	 *
	 *
	 * @param unknown $method (optional)
	 * @param unknown $query  (optional)
	 */
	public function query( $method = 'statuses/user_timeline', $query = array() ) {

		$oauth = array(
			'oauth_consumer_key' => $this->consumer_key,
			'oauth_token' => $this->token,
			'oauth_nonce' => (string) mt_rand(), // a stronger nonce is recommended
			'oauth_timestamp' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_version' => '1.0',
		);

		$oauth = array_map( "rawurlencode", $oauth ); // must be encoded before sorting
		$query = array_map( "rawurlencode", $query );

		$arr = array_merge( $oauth, $query ); // combine the values THEN sort

		asort( $arr ); // secondary sort (value)
		ksort( $arr ); // primary sort (key)

		// http_build_query automatically encodes, but our parameters
		// are already encoded, and must be by this point, so we undo
		// the encoding step
		$querystring = urldecode( http_build_query( $arr, '', '&' ) );

		$url = "https://" . $this->host . '/' . $this->version . '/' . $method . ".json";

		// mash everything together for the text to hash
		$base_string = $this->method . "&" . rawurlencode( $url ) . "&" . rawurlencode( $querystring );

		// same with the key
		$key = rawurlencode( $this->consumer_secret ) . "&" . rawurlencode( $this->token_secret );

		// generate the hash
		$signature = rawurlencode( base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) ) );

		// this time we're using a normal GET query, and we're only encoding the query params
		// (without the oauth params)
		$url .= "?" . http_build_query( $query );
		$url = str_replace( "&amp;", "&", $url ); //Patch by @Frewuill

		$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
		ksort( $oauth ); // probably not necessary, but twitter's demo does it


		/**
		 * also not necessary, but twitter's demo does this too
		 *
		 * @param unknown $str
		 * @return unknown
		 */
		function add_quotes( $str ) {return '"' . $str . '"';}


		$oauth = array_map( "add_quotes", $oauth );

		// this is the full value of the Authorization line
		$auth = "OAuth " . urldecode( http_build_query( $oauth, '', ', ' ) );

		$result = wp_remote_get( $url, array(
				'sslverify' => false,
				'headers' => array( 'Authorization' => $auth ),
				'method' => $this->method,
			) );

		$code = wp_remote_retrieve_response_code( $result );
		$body = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $code == 200 ) {

			return json_decode( $body );

		} else {
			return false;
		}

	}


}
