<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes
 */

use Yoast\WP\SEO\Helpers\Home_Url_Helper;

/**
 * Represents a single redirect
 */
class WPSEO_Redirect implements ArrayAccess {

	/**
	 * Redirect origin.
	 *
	 * @var string
	 */
	protected $origin;

	/**
	 * Redirect target.
	 *
	 * @var string
	 */
	protected $target = '';

	/**
	 * A HTTP code determining the redirect type.
	 *
	 * @var int
	 */
	protected $type;

	/**
	 * A string determining the redirect format (plain or regex).
	 *
	 * @var string
	 */
	protected $format;

	/**
	 * A string holding a possible redirect validation error.
	 *
	 * @var string
	 */
	protected $validation_error;

	/**
	 * The home URL helper.
	 *
	 * @var Home_Url_Helper
	 */
	protected static $home_url;

	/**
	 * WPSEO_Redirect constructor.
	 *
	 * @param string $origin The origin of the redirect.
	 * @param string $target The target of the redirect.
	 * @param int    $type   The type of the redirect.
	 * @param string $format The format of the redirect.
	 */
	public function __construct( $origin, $target = '', $type = WPSEO_Redirect_Types::PERMANENT, $format = WPSEO_Redirect_Formats::PLAIN ) {
		if ( static::$home_url === null ) {
			static::$home_url = new Home_Url_Helper();
		}

		$this->origin = ( $format === WPSEO_Redirect_Formats::PLAIN ) ? $this->sanitize_origin_url( $origin ) : $origin;
		$this->target = $this->sanitize_target_url( $target );
		$this->format = $format;
		$this->type   = (int) $type;
	}

	/**
	 * Returns the origin.
	 *
	 * @return string The set origin.
	 */
	public function get_origin() {
		return $this->origin;
	}

	/**
	 * Returns the target
	 *
	 * @return string The set target.
	 */
	public function get_target() {
		return $this->target;
	}

	/**
	 * Returns the type
	 *
	 * @return int The set type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Returns the format
	 *
	 * @return string The set format.
	 */
	public function get_format() {
		return $this->format;
	}

	/**
	 * Whether a offset exists.
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param string $offset An offset to check for.
	 *
	 * @return bool True on success or false on failure.
	 *              The return value will be cast to boolean if non-boolean was returned.
	 */
	public function offsetExists( $offset ) {
		return in_array( $offset, [ 'url', 'type' ], true );
	}

	/**
	 * Offset to retrieve.
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param string $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {
		switch ( $offset ) {
			case 'old':
				return $this->origin;

			case 'url':
				return $this->target;

			case 'type':
				return $this->type;
		}

		return null;
	}

	/**
	 * Offset to set.
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param string $offset The offset to assign the value to.
	 * @param string $value  The value to set.
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		switch ( $offset ) {
			case 'url':
				$this->target = $value;
				break;
			case 'type':
				$this->type = $value;
				break;
		}
	}

	/**
	 * Offset to unset.
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $offset The offset to unset.
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {
	}

	/**
	 * Compares an URL with the origin of the redirect.
	 *
	 * @param string $url The URL to compare.
	 *
	 * @return bool True when url matches the origin.
	 */
	public function origin_is( $url ) {
		// Sanitize the slash in case of plain redirect.
		if ( $this->format === WPSEO_Redirect_Formats::PLAIN ) {
			$url = $this->sanitize_slash( $url, $this->parse_url( $url ) );
		}

		return (string) $this->origin === (string) $url;
	}

	/**
	 * Strip the trailing slashes for relative URLs.
	 *
	 * @param string $url_to_sanitize The URL to sanitize.
	 * @param array  $url_pieces      The url pieces.
	 *
	 * @return string The sanitized url.
	 */
	private function sanitize_slash( $url_to_sanitize, array $url_pieces = [] ) {
		$url = $url_to_sanitize;
		if ( $url !== '/' && ! isset( $url_pieces['scheme'] ) ) {
			return trim( $url_to_sanitize, '/' );
		}


		return $url;
	}

	/**
	 * Strip the protocol from the URL.
	 *
	 * @param string $scheme The scheme to strip.
	 * @param string $url    The URL to remove the scheme from.
	 *
	 * @return string The url without the scheme.
	 */
	private function strip_scheme_from_url( $scheme, $url ) {
		return str_replace( $scheme . '://', '', $url );
	}

	/**
	 * Remove the home URL from the redirect to ensure that relative URLs are created.
	 *
	 * @param string $url The URL to sanitize.
	 *
	 * @return string The sanitized url.
	 */
	private function sanitize_origin_url( $url ) {
		$home_url        = static::$home_url->get();
		$home_url_pieces = static::$home_url->get_parsed();
		$url_pieces      = $this->parse_url( $url );

		if ( $this->match_home_url( $home_url_pieces, $url_pieces ) ) {
			$url = substr(
				$this->strip_scheme_from_url( $url_pieces['scheme'], $url ),
				strlen( $this->strip_scheme_from_url( $home_url_pieces['scheme'], $home_url ) )
			);

			$url_pieces['scheme'] = null;
		}

		return $this->sanitize_slash( $url, $url_pieces );
	}

	/**
	 * Sanitizes the target url.
	 *
	 * @param string $url The url to sanitize.
	 *
	 * @return string The sanitized url.
	 */
	private function sanitize_target_url( $url ) {
		$home_url_pieces = static::$home_url->get_parsed();
		$url_pieces      = $this->parse_url( $url );

		if ( $this->match_home_url( $home_url_pieces, $url_pieces ) ) {
			$url = substr(
				$this->strip_scheme_from_url( $url_pieces['scheme'], $url ),
				strlen( $home_url_pieces['host'] )
			);

			$url_pieces['scheme'] = null;
		}

		return $this->sanitize_slash( $url, $url_pieces );
	}

	/**
	 * Checks if the URL matches the home URL.
	 *
	 * @param array $home_url_pieces The pieces (wp_parse_url) from the home_url.
	 * @param array $url_pieces      The pieces (wp_parse_url) from the url to match.
	 *
	 * @return bool True when the URL matches the home URL.
	 */
	private function match_home_url( $home_url_pieces, $url_pieces ) {
		if ( ! isset( $url_pieces['scheme'] ) ) {
			return false;
		}

		if ( ! isset( $url_pieces['host'] ) || ! $this->match_home_url_host( $home_url_pieces['host'], $url_pieces['host'] ) ) {
			return false;
		}

		if ( ! isset( $home_url_pieces['path'] ) ) {
			return true;
		}

		return isset( $url_pieces['path'] ) && $this->match_home_url_path( $home_url_pieces['path'], $url_pieces['path'] );
	}

	/**
	 * Checks if the URL matches the home URL by comparing their host.
	 *
	 * @param string $home_url_host The home URL host.
	 * @param string $url_host      The URL host.
	 *
	 * @return bool True when both hosts are equal.
	 */
	private function match_home_url_host( $home_url_host, $url_host ) {
		return $url_host === $home_url_host;
	}

	/**
	 * Checks if the URL matches the home URL by comparing their path.
	 *
	 * @param string $home_url_path The home URL path.
	 * @param string $url_path      The URL path.
	 *
	 * @return bool True when the home URL path is empty or when the URL path begins with the home URL path.
	 */
	private function match_home_url_path( $home_url_path, $url_path ) {
		$home_url_path = trim( $home_url_path, '/' );
		if ( empty( $home_url_path ) ) {
			return true;
		}

		return strpos( trim( $url_path, '/' ), $home_url_path ) === 0;
	}

	/**
	 * Parses the URL into separate pieces.
	 *
	 * @param string $url The URL string.
	 *
	 * @return array Array of URL pieces.
	 */
	private function parse_url( $url ) {
		$parsed_url = wp_parse_url( $url );
		if ( is_array( $parsed_url ) ) {
			return $parsed_url;
		}

		return [];
	}
}
