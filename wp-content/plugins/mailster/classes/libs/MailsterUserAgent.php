<?php

class MailsterUserAgent {

	private $string = null;
	private $parsed = null;

	/**
	 *
	 *
	 * @param unknown $string (optional)
	 */
	public function __construct( $string = null ) {

		$this->string = ( is_null( $string ) ) ? $this->get_user_agent() : $string;

	}


	/**
	 *
	 *
	 * @param unknown $string (optional)
	 * @return unknown
	 */
	public function get( $string = null ) {
		return $this->get_parsed();
	}


	/**
	 *
	 *
	 * @param unknown $string (optional)
	 * @return unknown
	 */
	private function get_user_agent( $string = null ) {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? trim( urldecode( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_client_by_referer() {

		$object = false;

		if ( $ref = wp_get_raw_referer() ) {
			$object = new StdClass();

			$host = parse_url( $ref, PHP_URL_HOST );

			$object->client  = '';
			$object->version = '';
			$object->type    = 'webmail';

			switch ( $host ) {
				case 'outlook.live.com':
					$object->client = 'Outlook Web';
					break;
				case 'mail.google.com':
					$object->client = 'Gmail';
					break;
				default:
					if ( false !== strpos( $host, 'gmx.net' ) ) {
						$object->client = 'GMX';
					}
			}
		} elseif ( preg_match( '#(Chrome|Safari|Firefox|Opera)/([0-9a-z.]+)#i', $this->string, $hit ) ) {

			$object          = new StdClass();
			$object->client  = 'Web Client (' . $hit[1] . ')';
			$object->version = '';
			$object->type    = 'webmail';
		}

		return $object;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_client() {
		if ( ! $this->string ) {
			return '';
		}

		$this->parse();

		return $this->parsed->client;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_version() {
		if ( ! $this->string ) {
			return '';
		}

		$this->parse();

		return $this->parsed->version;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_parsed() {
		if ( ! $this->string ) {
			return '';
		}

		$this->parse();

		return $this->parsed;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function parse() {
		if ( $this->parsed ) {
			return $this->parsed;
		}

		$object = new StdClass();

		if ( preg_match( '# Thunderbird/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Thunderbird';
			$object->version = ( $hit[1] );
			$object->type    = 'desktop';

		} elseif ( preg_match( '#Airmail ([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Airmail';
			$object->version = $hit[1];
			$object->type    = 'desktop';

		} elseif ( preg_match( '# ANDROIDGMAILAPP#i', $this->string, $hit ) ) {
			$object->client  = 'Gmail App (Android)';
			$object->version = '';
			$object->type    = 'mobile';

		} elseif ( preg_match( '# GoogleImageProxy#i', $this->string, $hit ) ) {
			$object->client  = 'Gmail';
			$object->version = '';
			$object->type    = 'webmail';

		} elseif ( preg_match( '#YahooMailProxy#i', $this->string, $hit ) ) {
			$object->client  = 'Yahoo';
			$object->version = '';
			$object->type    = 'webmail';

		} elseif ( preg_match( '#(iPod|iPod touch).*OS ([0-9_]+)#i', $this->string, $hit ) ) {
			$object->client  = 'iPod Touch';
			$object->version = 'iOS ' . (int) $hit[2];
			$object->type    = 'mobile';

		} elseif ( preg_match( '#(iPhone|iPad).*OS ([0-9_]+)#i', $this->string, $hit ) ) {
			$object->client  = $hit[1];
			$object->version = 'iOS ' . (int) $hit[2];
			$object->type    = 'mobile';

		} elseif ( preg_match( '#(Android|BlackBerry|Windows Phone OS) ([0-9.]+)#i', $this->string, $hit ) ) {
			$object->client  = $hit[1];
			$object->version = $hit[2];
			$object->type    = 'mobile';

		} elseif ( preg_match( '#(Kindle Fire|Kindle|IEMobile)/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = $hit[1];
			$object->version = $hit[2];
			$object->type    = 'mobile';

		} elseif ( preg_match( '#(Sparrow|Postbox|Eudora|Lotus-Notes|Shredder|PocoMail|Barca|BarcaPro)/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = str_replace( '-', ' ', $hit[1] );
			$object->version = $hit[2];
			$object->type    = 'desktop';

		} elseif ( preg_match( '#Outlook-Express/7\.0 \(MSIE ([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Windows Live Mail';
			$object->version = $hit[1];
			$object->type    = 'desktop';

		} elseif ( preg_match( '#Outlook-Express/6\.0#i', $this->string, $hit ) ) {
			$object->client  = 'Outlook Express';
			$object->version = '6.0';
			$object->type    = 'desktop';

		} elseif ( preg_match( '#(MSAppHost)/([0-9.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Windows Live Mail';
			$object->version = '';
			$object->type    = 'desktop';

		} elseif ( preg_match( '#(Outlook-iOS)#i', $this->string, $hit ) ) {
			$object->client  = 'Outlook for iOS';
			$object->version = preg_replace( '/(.*)\(([0-9.]+)\)(.*)/', '$2', $this->string );
			$object->type    = 'mobile';

		} elseif ( preg_match( '# (Microsoft Outlook|MSOffice) ([0-9]+)#i', $this->string, $hit ) ) {
			$object->client = 'Microsoft Outlook';
			$version        = (int) $hit[2];
			switch ( $version ) {
				case 12:
					$object->version = '2007';
					break;
				case 14:
					$object->version = '2010';
					break;
				case 15:
					$object->version = '2013';
					break;
				case 16:
					$object->version = '2016';
					break;
				default:
					$object->version = $hit[2];
			}
			$object->type = 'desktop';

		} elseif ( preg_match( '#(Chrome|Safari|Firefox|Opera)/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object = $this->get_client_by_referer();

		} elseif ( preg_match( '# Trident/.* rv:([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Web Client (Internet Explorer ' . (int) $hit[1] . ')';
			$object->version = '';
			$object->type    = 'webmail';

		} elseif ( preg_match( '#MSIE ([0-9.]+).* Trident/#i', $this->string, $hit ) ) {
			$version = (int) $hit[1];
			if ( $version <= 7 ) {
				// most likely Outlook 2000-2003
				$object->client  = 'Microsoft Outlook';
				$object->version = '2000-2003';
				$object->type    = 'desktop';
			} else {
				$object->client  = 'Web Client (Internet Explorer ' . $version . ')';
				$object->version = '';
				$object->type    = 'webmail';
			}
		} elseif ( preg_match( '# AppleWebKit/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			if ( preg_match( '#Mac OS X 10_(\d+)#i', $this->string, $versionhit ) ) {
				$object->client  = 'Apple Mail';
				$object->version = $versionhit[1] - 2;
				$object->type    = 'desktop';
			} else {
				$object->client  = 'Web Client (Webkit based)';
				$object->version = $hit[1];
				$object->type    = 'webmail';
			}
		} elseif ( preg_match( '#Mozilla/([0-9a-z.]+)#i', $this->string, $hit ) ) {
			$object->client  = 'Web Client (Mozilla based)';
			$object->version = $hit[1];
			$object->type    = 'webmail';

		} else {
			$object = $this->get_client_by_referer();
		}

		$this->parsed = $object;

	}


}
