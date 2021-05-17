<?php

// modified for the Mailster Newsletter plugin by EverPress

/* geoipcity.inc
 *
 * Copyright (C) 2004 Maxmind LLC
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307    USA
 */

/*
 * Changelog:
 *
 * 2005-01-13 Andrew Hill, Awarez Ltd. (http://www.awarez.net)
 * Formatted file according to PEAR library standards.
 * Changed inclusion of geoip.inc file to require_once, so that
 * this library can be used in the same script as geoip.inc.
 */

define( "FULL_RECORD_LENGTH", 50 );

require_once 'geoip.inc.php';
require_once 'geoipregionvars.php';

if ( !class_exists( 'geoiprecord' ) ) {
	class geoiprecord {
		public $country_code;
		public $country_code3;
		public $country_name;
		public $region;
		public $city;
		public $postal_code;
		public $latitude;
		public $longitude;
		public $area_code;
		public $dma_code; // metro and dma code are the same. use metro_code
		public $metro_code;
		public $continent_code;
	}


}
if ( !class_exists( 'geoipdnsrecord' ) ) {
	class geoipdnsrecord {
		public $country_code;
		public $country_code3;
		public $country_name;
		public $region;
		public $regionname;
		public $city;
		public $postal_code;
		public $latitude;
		public $longitude;
		public $areacode;
		public $dmacode;
		public $isp;
		public $org;
		public $metrocode;
	}


}

class mailster_CityIP {

	public $geoIP;
	public $GEOIP_STANDARD = 0;
	public $GEOIP_MEMORY_CACHE = 1;
	public $GEOIP_SHARED_MEMORY = 2;
	public $GEOIP_CITY_EDITION_REV1 = 2;

	/**
	 *
	 *
	 * @param unknown $filename
	 * @param unknown $flags    (optional)
	 */
	public function __construct( $filename, $flags = '' ) {
		if ( empty( $flags ) ) {
			$flags = $this->GEOIP_STANDARD;
		}

		$this->geoIP = new mailster_GeoIP( $filename, $flags );
	}


	/**
	 *
	 *
	 * @param unknown $str
	 * @return unknown
	 */
	public function getrecordwithdnsservice( $str ) {
		$record = new geoipdnsrecord;
		$keyvalue = explode( ";", $str );
		foreach ( $keyvalue as $keyvalue2 ) {
			list( $key, $value ) = explode( "=", $keyvalue2 );
			if ( $key == "co" ) {
				$record->country_code = $value;
			}
			if ( $key == "ci" ) {
				$record->city = $value;
			}
			if ( $key == "re" ) {
				$record->region = $value;
			}
			if ( $key == "ac" ) {
				$record->areacode = $value;
			}
			if ( $key == "dm" || $key == "me" ) {
				$record->dmacode = $value;
				$record->metrocode = $value;
			}
			if ( $key == "is" ) {
				$record->isp = $value;
			}
			if ( $key == "or" ) {
				$record->org = $value;
			}
			if ( $key == "zi" ) {
				$record->postal_code = $value;
			}
			if ( $key == "la" ) {
				$record->latitude = $value;
			}
			if ( $key == "lo" ) {
				$record->longitude = $value;
			}
		}
		$number = $GLOBALS['GEOIP_COUNTRY_CODE_TO_NUMBER'][$record->country_code];
		$record->country_code3 = $GLOBALS['GEOIP_COUNTRY_CODES3'][$number];
		$record->country_name = $GLOBALS['GEOIP_COUNTRY_NAMES'][$number];
		if ( $record->region != "" ) {
			if ( ( $record->country_code == "US" ) || ( $record->country_code == "CA" ) ) {
				$record->regionname = $GLOBALS['ISO'][$record->country_code][$record->region];
			} else {
				$record->regionname = $GLOBALS['FIPS'][$record->country_code][$record->region];
			}
		}
		return $record;
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _get_record_v6( $ipnum ) {
		$seek_country = $this->geoIP->_geoip_seek_country_v6( $ipnum );
		if ( $seek_country == $this->geoIP->databaseSegments ) {
			return null;
		}
		return $this->_common_get_record( $seek_country );
	}


	/**
	 *
	 *
	 * @param unknown $seek_country
	 * @return unknown
	 */
	public function _common_get_record( $seek_country ) {
		// workaround php's broken substr, strpos, etc handling with
		// mbstring.func_overload and mbstring.internal_encoding
		if ( function_exists( 'mb_internal_encoding' ) ) {
			$enc = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}

		$record_pointer = $seek_country + ( 2 * $this->geoIP->record_length - 1 ) * $this->geoIP->databaseSegments;

		if ( $this->geoIP->flags & $this->GEOIP_MEMORY_CACHE ) {
			$record_buf = substr( $this->geoIP->memory_buffer, $record_pointer, FULL_RECORD_LENGTH );
		} elseif ( $this->geoIP->flags & $this->GEOIP_SHARED_MEMORY ) {
			$record_buf = @shmop_read( $this->geoIP->shmid, $record_pointer, FULL_RECORD_LENGTH );
		} else {
			fseek( $this->geoIP->filehandle, $record_pointer, SEEK_SET );
			$record_buf = fread( $this->geoIP->filehandle, FULL_RECORD_LENGTH );
		}
		$record = new geoiprecord;
		$record_buf_pos = 0;
		$char = ord( substr( $record_buf, $record_buf_pos, 1 ) );
		$record->country_code = $this->geoIP->GEOIP_COUNTRY_CODES[$char];
		$record->country_code3 = $this->geoIP->GEOIP_COUNTRY_CODES3[$char];
		$record->country_name = $this->geoIP->GEOIP_COUNTRY_NAMES[$char];
		$record->continent_code = $this->geoIP->GEOIP_CONTINENT_CODES[$char];
		$record_buf_pos++;
		$str_length = 0;
		// Get region
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( $char != 0 ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}
		if ( $str_length > 0 ) {
			$record->region = substr( $record_buf, $record_buf_pos, $str_length );
		}
		$record_buf_pos += $str_length + 1;
		$str_length = 0;
		// Get city
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( $char != 0 ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}
		if ( $str_length > 0 ) {
			$record->city = substr( $record_buf, $record_buf_pos, $str_length );
		}
		$record_buf_pos += $str_length + 1;
		$str_length = 0;
		// Get postal code
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( $char != 0 ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}
		if ( $str_length > 0 ) {
			$record->postal_code = substr( $record_buf, $record_buf_pos, $str_length );
		}
		$record_buf_pos += $str_length + 1;
		$str_length = 0;
		// Get latitude and longitude
		$latitude = 0;
		$longitude = 0;
		for ( $j = 0; $j < 3; ++$j ) {
			$char = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
			$latitude += ( $char << ( $j * 8 ) );
		}
		$record->latitude = ( $latitude / 10000 ) - 180;
		for ( $j = 0; $j < 3; ++$j ) {
			$char = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
			$longitude += ( $char << ( $j * 8 ) );
		}
		$record->longitude = ( $longitude / 10000 ) - 180;
		if ( $this->GEOIP_CITY_EDITION_REV1 == $this->geoIP->databaseType ) {
			$metroarea_combo = 0;
			if ( $record->country_code == "US" ) {
				for ( $j = 0; $j < 3; ++$j ) {
					$char = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
					$metroarea_combo += ( $char << ( $j * 8 ) );
				}
				$record->metro_code = $record->dma_code = floor( $metroarea_combo / 1000 );
				$record->area_code = $metroarea_combo % 1000;
			}
		}
		if ( isset( $enc ) ) {
			mb_internal_encoding( $enc );
		}

		return $record;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_record_by_addr_v6( $addr ) {
		if ( $addr == null ) {
			return 0;
		}
		$ipnum = inet_pton( $addr );
		return $this->_get_record_v6( $ipnum );
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _get_record( $ipnum ) {
		$seek_country = $this->geoIP->_geoip_seek_country( $ipnum );
		if ( $seek_country == $this->geoIP->databaseSegments ) {
			return null;
		}
		return $this->_common_get_record( $seek_country );
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_record_by_addr( $addr ) {
		if ( $addr == null ) {
			return 0;
		}
		$ipnum = ip2long( $addr );
		return $this->_get_record( $ipnum );
	}


}
