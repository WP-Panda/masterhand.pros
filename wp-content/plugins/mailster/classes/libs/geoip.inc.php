<?php

// modified for the Mailster Newsletter plugin by EverPress

/* -*- Mode: C; indent-tabs-mode: t; c-basic-offset: 2; tab-width: 2 -*- */
/* geoip.inc
 *
 * Copyright (C) 2007 MaxMind LLC
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 */


class mailster_GeoIP {
	public $flags;
	public $filehandle;
	public $memory_buffer;
	public $databaseType;
	public $databaseSegments;
	public $record_length;
	public $shmid;
	public $GEOIP_COUNTRY_CODE_TO_NUMBER = array(
		"" => 0, "AP" => 1, "EU" => 2, "AD" => 3, "AE" => 4, "AF" => 5,
		"AG" => 6, "AI" => 7, "AL" => 8, "AM" => 9, "CW" => 10, "AO" => 11,
		"AQ" => 12, "AR" => 13, "AS" => 14, "AT" => 15, "AU" => 16, "AW" => 17,
		"AZ" => 18, "BA" => 19, "BB" => 20, "BD" => 21, "BE" => 22, "BF" => 23,
		"BG" => 24, "BH" => 25, "BI" => 26, "BJ" => 27, "BM" => 28, "BN" => 29,
		"BO" => 30, "BR" => 31, "BS" => 32, "BT" => 33, "BV" => 34, "BW" => 35,
		"BY" => 36, "BZ" => 37, "CA" => 38, "CC" => 39, "CD" => 40, "CF" => 41,
		"CG" => 42, "CH" => 43, "CI" => 44, "CK" => 45, "CL" => 46, "CM" => 47,
		"CN" => 48, "CO" => 49, "CR" => 50, "CU" => 51, "CV" => 52, "CX" => 53,
		"CY" => 54, "CZ" => 55, "DE" => 56, "DJ" => 57, "DK" => 58, "DM" => 59,
		"DO" => 60, "DZ" => 61, "EC" => 62, "EE" => 63, "EG" => 64, "EH" => 65,
		"ER" => 66, "ES" => 67, "ET" => 68, "FI" => 69, "FJ" => 70, "FK" => 71,
		"FM" => 72, "FO" => 73, "FR" => 74, "SX" => 75, "GA" => 76, "GB" => 77,
		"GD" => 78, "GE" => 79, "GF" => 80, "GH" => 81, "GI" => 82, "GL" => 83,
		"GM" => 84, "GN" => 85, "GP" => 86, "GQ" => 87, "GR" => 88, "GS" => 89,
		"GT" => 90, "GU" => 91, "GW" => 92, "GY" => 93, "HK" => 94, "HM" => 95,
		"HN" => 96, "HR" => 97, "HT" => 98, "HU" => 99, "ID" => 100, "IE" => 101,
		"IL" => 102, "IN" => 103, "IO" => 104, "IQ" => 105, "IR" => 106, "IS" => 107,
		"IT" => 108, "JM" => 109, "JO" => 110, "JP" => 111, "KE" => 112, "KG" => 113,
		"KH" => 114, "KI" => 115, "KM" => 116, "KN" => 117, "KP" => 118, "KR" => 119,
		"KW" => 120, "KY" => 121, "KZ" => 122, "LA" => 123, "LB" => 124, "LC" => 125,
		"LI" => 126, "LK" => 127, "LR" => 128, "LS" => 129, "LT" => 130, "LU" => 131,
		"LV" => 132, "LY" => 133, "MA" => 134, "MC" => 135, "MD" => 136, "MG" => 137,
		"MH" => 138, "MK" => 139, "ML" => 140, "MM" => 141, "MN" => 142, "MO" => 143,
		"MP" => 144, "MQ" => 145, "MR" => 146, "MS" => 147, "MT" => 148, "MU" => 149,
		"MV" => 150, "MW" => 151, "MX" => 152, "MY" => 153, "MZ" => 154, "NA" => 155,
		"NC" => 156, "NE" => 157, "NF" => 158, "NG" => 159, "NI" => 160, "NL" => 161,
		"NO" => 162, "NP" => 163, "NR" => 164, "NU" => 165, "NZ" => 166, "OM" => 167,
		"PA" => 168, "PE" => 169, "PF" => 170, "PG" => 171, "PH" => 172, "PK" => 173,
		"PL" => 174, "PM" => 175, "PN" => 176, "PR" => 177, "PS" => 178, "PT" => 179,
		"PW" => 180, "PY" => 181, "QA" => 182, "RE" => 183, "RO" => 184, "RU" => 185,
		"RW" => 186, "SA" => 187, "SB" => 188, "SC" => 189, "SD" => 190, "SE" => 191,
		"SG" => 192, "SH" => 193, "SI" => 194, "SJ" => 195, "SK" => 196, "SL" => 197,
		"SM" => 198, "SN" => 199, "SO" => 200, "SR" => 201, "ST" => 202, "SV" => 203,
		"SY" => 204, "SZ" => 205, "TC" => 206, "TD" => 207, "TF" => 208, "TG" => 209,
		"TH" => 210, "TJ" => 211, "TK" => 212, "TM" => 213, "TN" => 214, "TO" => 215,
		"TL" => 216, "TR" => 217, "TT" => 218, "TV" => 219, "TW" => 220, "TZ" => 221,
		"UA" => 222, "UG" => 223, "UM" => 224, "US" => 225, "UY" => 226, "UZ" => 227,
		"VA" => 228, "VC" => 229, "VE" => 230, "VG" => 231, "VI" => 232, "VN" => 233,
		"VU" => 234, "WF" => 235, "WS" => 236, "YE" => 237, "YT" => 238, "RS" => 239,
		"ZA" => 240, "ZM" => 241, "ME" => 242, "ZW" => 243, "A1" => 244, "A2" => 245,
		"O1" => 246, "AX" => 247, "GG" => 248, "IM" => 249, "JE" => 250, "BL" => 251,
		"MF" => 252, "BQ" => 253, "SS" => 254, "O1" => 255,
	);
	public $GEOIP_COUNTRY_CODES = array(
		"", "AP", "EU", "AD", "AE", "AF", "AG", "AI", "AL", "AM", "CW",
		"AO", "AQ", "AR", "AS", "AT", "AU", "AW", "AZ", "BA", "BB",
		"BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BM", "BN", "BO",
		"BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA", "CC", "CD",
		"CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR",
		"CU", "CV", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO",
		"DZ", "EC", "EE", "EG", "EH", "ER", "ES", "ET", "FI", "FJ",
		"FK", "FM", "FO", "FR", "SX", "GA", "GB", "GD", "GE", "GF",
		"GH", "GI", "GL", "GM", "GN", "GP", "GQ", "GR", "GS", "GT",
		"GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU", "ID",
		"IE", "IL", "IN", "IO", "IQ", "IR", "IS", "IT", "JM", "JO",
		"JP", "KE", "KG", "KH", "KI", "KM", "KN", "KP", "KR", "KW",
		"KY", "KZ", "LA", "LB", "LC", "LI", "LK", "LR", "LS", "LT",
		"LU", "LV", "LY", "MA", "MC", "MD", "MG", "MH", "MK", "ML",
		"MM", "MN", "MO", "MP", "MQ", "MR", "MS", "MT", "MU", "MV",
		"MW", "MX", "MY", "MZ", "NA", "NC", "NE", "NF", "NG", "NI",
		"NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA", "PE", "PF",
		"PG", "PH", "PK", "PL", "PM", "PN", "PR", "PS", "PT", "PW",
		"PY", "QA", "RE", "RO", "RU", "RW", "SA", "SB", "SC", "SD",
		"SE", "SG", "SH", "SI", "SJ", "SK", "SL", "SM", "SN", "SO",
		"SR", "ST", "SV", "SY", "SZ", "TC", "TD", "TF", "TG", "TH",
		"TJ", "TK", "TM", "TN", "TO", "TL", "TR", "TT", "TV", "TW",
		"TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE",
		"VG", "VI", "VN", "VU", "WF", "WS", "YE", "YT", "RS", "ZA",
		"ZM", "ME", "ZW", "A1", "A2", "O1", "AX", "GG", "IM", "JE",
		"BL", "MF", "BQ", "SS", "O1" );
	public $GEOIP_COUNTRY_CODES3 = array(
		"", "AP", "EU", "AND", "ARE", "AFG", "ATG", "AIA", "ALB", "ARM", "CUW",
		"AGO", "ATA", "ARG", "ASM", "AUT", "AUS", "ABW", "AZE", "BIH", "BRB",
		"BGD", "BEL", "BFA", "BGR", "BHR", "BDI", "BEN", "BMU", "BRN", "BOL",
		"BRA", "BHS", "BTN", "BVT", "BWA", "BLR", "BLZ", "CAN", "CCK", "COD",
		"CAF", "COG", "CHE", "CIV", "COK", "CHL", "CMR", "CHN", "COL", "CRI",
		"CUB", "CPV", "CXR", "CYP", "CZE", "DEU", "DJI", "DNK", "DMA", "DOM",
		"DZA", "ECU", "EST", "EGY", "ESH", "ERI", "ESP", "ETH", "FIN", "FJI",
		"FLK", "FSM", "FRO", "FRA", "SXM", "GAB", "GBR", "GRD", "GEO", "GUF",
		"GHA", "GIB", "GRL", "GMB", "GIN", "GLP", "GNQ", "GRC", "SGS", "GTM",
		"GUM", "GNB", "GUY", "HKG", "HMD", "HND", "HRV", "HTI", "HUN", "IDN",
		"IRL", "ISR", "IND", "IOT", "IRQ", "IRN", "ISL", "ITA", "JAM", "JOR",
		"JPN", "KEN", "KGZ", "KHM", "KIR", "COM", "KNA", "PRK", "KOR", "KWT",
		"CYM", "KAZ", "LAO", "LBN", "LCA", "LIE", "LKA", "LBR", "LSO", "LTU",
		"LUX", "LVA", "LBY", "MAR", "MCO", "MDA", "MDG", "MHL", "MKD", "MLI",
		"MMR", "MNG", "MAC", "MNP", "MTQ", "MRT", "MSR", "MLT", "MUS", "MDV",
		"MWI", "MEX", "MYS", "MOZ", "NAM", "NCL", "NER", "NFK", "NGA", "NIC",
		"NLD", "NOR", "NPL", "NRU", "NIU", "NZL", "OMN", "PAN", "PER", "PYF",
		"PNG", "PHL", "PAK", "POL", "SPM", "PCN", "PRI", "PSE", "PRT", "PLW",
		"PRY", "QAT", "REU", "ROU", "RUS", "RWA", "SAU", "SLB", "SYC", "SDN",
		"SWE", "SGP", "SHN", "SVN", "SJM", "SVK", "SLE", "SMR", "SEN", "SOM",
		"SUR", "STP", "SLV", "SYR", "SWZ", "TCA", "TCD", "ATF", "TGO", "THA",
		"TJK", "TKL", "TKM", "TUN", "TON", "TLS", "TUR", "TTO", "TUV", "TWN",
		"TZA", "UKR", "UGA", "UMI", "USA", "URY", "UZB", "VAT", "VCT", "VEN",
		"VGB", "VIR", "VNM", "VUT", "WLF", "WSM", "YEM", "MYT", "SRB", "ZAF",
		"ZMB", "MNE", "ZWE", "A1", "A2", "O1", "ALA", "GGY", "IMN", "JEY",
		"BLM", "MAF", "BES", "SSD", "O1",
	);
	public $GEOIP_COUNTRY_NAMES = array(
		"", "Asia/Pacific Region", "Europe", "Andorra", "United Arab Emirates", "Afghanistan", "Antigua and Barbuda", "Anguilla", "Albania", "Armenia", "Curacao",
		"Angola", "Antarctica", "Argentina", "American Samoa", "Austria", "Australia", "Aruba", "Azerbaijan", "Bosnia and Herzegovina", "Barbados",
		"Bangladesh", "Belgium", "Burkina Faso", "Bulgaria", "Bahrain", "Burundi", "Benin", "Bermuda", "Brunei Darussalam", "Bolivia",
		"Brazil", "Bahamas", "Bhutan", "Bouvet Island", "Botswana", "Belarus", "Belize", "Canada", "Cocos (Keeling) Islands", "Congo, The Democratic Republic of the",
		"Central African Republic", "Congo", "Switzerland", "Cote D'Ivoire", "Cook Islands", "Chile", "Cameroon", "China", "Colombia", "Costa Rica",
		"Cuba", "Cape Verde", "Christmas Island", "Cyprus", "Czech Republic", "Germany", "Djibouti", "Denmark", "Dominica", "Dominican Republic",
		"Algeria", "Ecuador", "Estonia", "Egypt", "Western Sahara", "Eritrea", "Spain", "Ethiopia", "Finland", "Fiji",
		"Falkland Islands (Malvinas)", "Micronesia, Federated States of", "Faroe Islands", "France", "Sint Maarten (Dutch part)", "Gabon", "United Kingdom", "Grenada", "Georgia", "French Guiana",
		"Ghana", "Gibraltar", "Greenland", "Gambia", "Guinea", "Guadeloupe", "Equatorial Guinea", "Greece", "South Georgia and the South Sandwich Islands", "Guatemala",
		"Guam", "Guinea-Bissau", "Guyana", "Hong Kong", "Heard Island and McDonald Islands", "Honduras", "Croatia", "Haiti", "Hungary", "Indonesia",
		"Ireland", "Israel", "India", "British Indian Ocean Territory", "Iraq", "Iran, Islamic Republic of", "Iceland", "Italy", "Jamaica", "Jordan",
		"Japan", "Kenya", "Kyrgyzstan", "Cambodia", "Kiribati", "Comoros", "Saint Kitts and Nevis", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait",
		"Cayman Islands", "Kazakhstan", "Lao People's Democratic Republic", "Lebanon", "Saint Lucia", "Liechtenstein", "Sri Lanka", "Liberia", "Lesotho", "Lithuania",
		"Luxembourg", "Latvia", "Libya", "Morocco", "Monaco", "Moldova, Republic of", "Madagascar", "Marshall Islands", "Macedonia", "Mali",
		"Myanmar", "Mongolia", "Macau", "Northern Mariana Islands", "Martinique", "Mauritania", "Montserrat", "Malta", "Mauritius", "Maldives",
		"Malawi", "Mexico", "Malaysia", "Mozambique", "Namibia", "New Caledonia", "Niger", "Norfolk Island", "Nigeria", "Nicaragua",
		"Netherlands", "Norway", "Nepal", "Nauru", "Niue", "New Zealand", "Oman", "Panama", "Peru", "French Polynesia",
		"Papua New Guinea", "Philippines", "Pakistan", "Poland", "Saint Pierre and Miquelon", "Pitcairn Islands", "Puerto Rico", "Palestinian Territory", "Portugal", "Palau",
		"Paraguay", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saudi Arabia", "Solomon Islands", "Seychelles", "Sudan",
		"Sweden", "Singapore", "Saint Helena", "Slovenia", "Svalbard and Jan Mayen", "Slovakia", "Sierra Leone", "San Marino", "Senegal", "Somalia", "Suriname",
		"Sao Tome and Principe", "El Salvador", "Syrian Arab Republic", "Swaziland", "Turks and Caicos Islands", "Chad", "French Southern Territories", "Togo", "Thailand",
		"Tajikistan", "Tokelau", "Turkmenistan", "Tunisia", "Tonga", "Timor-Leste", "Turkey", "Trinidad and Tobago", "Tuvalu", "Taiwan",
		"Tanzania, United Republic of", "Ukraine", "Uganda", "United States Minor Outlying Islands", "United States", "Uruguay", "Uzbekistan", "Holy See (Vatican City State)", "Saint Vincent and the Grenadines", "Venezuela",
		"Virgin Islands, British", "Virgin Islands, U.S.", "Vietnam", "Vanuatu", "Wallis and Futuna", "Samoa", "Yemen", "Mayotte", "Serbia", "South Africa",
		"Zambia", "Montenegro", "Zimbabwe", "Anonymous Proxy", "Satellite Provider", "Other", "Aland Islands", "Guernsey", "Isle of Man", "Jersey",
		"Saint Barthelemy", "Saint Martin", "Bonaire, Saint Eustatius and Saba",
		"South Sudan", "Other",
	);

	public $GEOIP_CONTINENT_CODES = array(
		"--", "AS", "EU", "EU", "AS", "AS", "NA", "NA", "EU", "AS", "NA",
		"AF", "AN", "SA", "OC", "EU", "OC", "NA", "AS", "EU", "NA",
		"AS", "EU", "AF", "EU", "AS", "AF", "AF", "NA", "AS", "SA",
		"SA", "NA", "AS", "AN", "AF", "EU", "NA", "NA", "AS", "AF",
		"AF", "AF", "EU", "AF", "OC", "SA", "AF", "AS", "SA", "NA",
		"NA", "AF", "AS", "AS", "EU", "EU", "AF", "EU", "NA", "NA",
		"AF", "SA", "EU", "AF", "AF", "AF", "EU", "AF", "EU", "OC",
		"SA", "OC", "EU", "EU", "NA", "AF", "EU", "NA", "AS", "SA",
		"AF", "EU", "NA", "AF", "AF", "NA", "AF", "EU", "AN", "NA",
		"OC", "AF", "SA", "AS", "AN", "NA", "EU", "NA", "EU", "AS",
		"EU", "AS", "AS", "AS", "AS", "AS", "EU", "EU", "NA", "AS",
		"AS", "AF", "AS", "AS", "OC", "AF", "NA", "AS", "AS", "AS",
		"NA", "AS", "AS", "AS", "NA", "EU", "AS", "AF", "AF", "EU",
		"EU", "EU", "AF", "AF", "EU", "EU", "AF", "OC", "EU", "AF",
		"AS", "AS", "AS", "OC", "NA", "AF", "NA", "EU", "AF", "AS",
		"AF", "NA", "AS", "AF", "AF", "OC", "AF", "OC", "AF", "NA",
		"EU", "EU", "AS", "OC", "OC", "OC", "AS", "NA", "SA", "OC",
		"OC", "AS", "AS", "EU", "NA", "OC", "NA", "AS", "EU", "OC",
		"SA", "AS", "AF", "EU", "EU", "AF", "AS", "OC", "AF", "AF",
		"EU", "AS", "AF", "EU", "EU", "EU", "AF", "EU", "AF", "AF",
		"SA", "AF", "NA", "AS", "AF", "NA", "AF", "AN", "AF", "AS",
		"AS", "OC", "AS", "AF", "OC", "AS", "EU", "NA", "OC", "AS",
		"AF", "EU", "AF", "OC", "NA", "SA", "AS", "EU", "NA", "SA",
		"NA", "NA", "AS", "OC", "OC", "OC", "AS", "AF", "EU", "AF",
		"AF", "EU", "AF", "--", "--", "--", "EU", "EU", "EU", "EU",
		"NA", "NA", "NA", "AF", "--",
	);

	public $GEOIP_COUNTRY_BEGIN = 16776960;
	public $GEOIP_STATE_BEGIN_REV0 = 16700000;
	public $GEOIP_STATE_BEGIN_REV1 = 16000000;
	public $GEOIP_STANDARD = 0;
	public $GEOIP_MEMORY_CACHE = 1;
	public $GEOIP_SHARED_MEMORY = 2;
	public $STRUCTURE_INFO_MAX_SIZE = 20;
	public $DATABASE_INFO_MAX_SIZE = 100;
	public $GEOIP_COUNTRY_EDITION = 106;
	public $GEOIP_PROXY_EDITION = 8;
	public $GEOIP_ASNUM_EDITION = 9;
	public $GEOIP_NETSPEED_EDITION = 10;
	public $GEOIP_REGION_EDITION_REV0 = 112;
	public $GEOIP_REGION_EDITION_REV1 = 3;
	public $GEOIP_CITY_EDITION_REV0 = 111;
	public $GEOIP_CITY_EDITION_REV1 = 2;
	public $GEOIP_ORG_EDITION = 110;
	public $GEOIP_ISP_EDITION = 4;
	public $SEGMENT_RECORD_LENGTH = 3;
	public $STANDARD_RECORD_LENGTH = 3;
	public $ORG_RECORD_LENGTH = 4;
	public $MAX_RECORD_LENGTH = 4;
	public $MAX_ORG_RECORD_LENGTH = 300;
	public $GEOIP_SHM_KEY = 0x4f415401;
	public $US_OFFSET = 1;
	public $CANADA_OFFSET = 677;
	public $WORLD_OFFSET = 1353;
	public $FIPS_RANGE = 360;
	public $GEOIP_UNKNOWN_SPEED = 0;
	public $GEOIP_DIALUP_SPEED = 1;
	public $GEOIP_CABLEDSL_SPEED = 2;
	public $GEOIP_CORPORATE_SPEED = 3;
	public $GEOIP_DOMAIN_EDITION = 11;
	public $GEOIP_COUNTRY_EDITION_V6 = 12;
	public $GEOIP_LOCATIONA_EDITION = 13;
	public $GEOIP_ACCURACYRADIUS_EDITION = 14;
	public $GEOIP_CITYCOMBINED_EDITION = 15;
	public $GEOIP_CITY_EDITION_REV1_V6 = 30;
	public $GEOIP_CITY_EDITION_REV0_V6 = 31;
	public $GEOIP_NETSPEED_EDITION_REV1 = 32;
	public $GEOIP_NETSPEED_EDITION_REV1_V6 = 33;
	public $GEOIP_USERTYPE_EDITION = 28;
	public $GEOIP_USERTYPE_EDITION_V6 = 29;
	public $GEOIP_ASNUM_EDITION_V6 = 21;
	public $GEOIP_ISP_EDITION_V6 = 22;
	public $GEOIP_ORG_EDITION_V6 = 23;
	public $GEOIP_DOMAIN_EDITION_V6 = 24;

	public $CITYCOMBINED_FIXED_RECORD = 7;

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

		$this->geoip_open( $filename, $flags );
	}


	public function __destruct() {
		$this->geoip_close();
	}


	/**
	 *
	 *
	 * @param unknown $file
	 */
	public function geoip_load_shared_mem( $file ) {

		$fp = fopen( $file, "rb" );
		if ( !$fp ) {
			print "error opening $file: $php_errormsg\n";
			exit;
		}
		$s_array = fstat( $fp );
		$size = $s_array['size'];
		if ( $shmid = @shmop_open( $this->GEOIP_SHM_KEY, "w", 0, 0 ) ) {
			shmop_delete( $shmid );
			shmop_close( $shmid );
		}
		$shmid = shmop_open( $this->GEOIP_SHM_KEY, "c", 0644, $size );
		shmop_write( $shmid, fread( $fp, $size ), 0 );
		shmop_close( $shmid );
	}


	public function _setup_segments() {
		$this->databaseType = $this->GEOIP_COUNTRY_EDITION;
		$this->record_length = $this->STANDARD_RECORD_LENGTH;
		if ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
			$offset = @shmop_size( $this->shmid ) - 3;
			for ( $i = 0; $i < $this->STRUCTURE_INFO_MAX_SIZE; $i++ ) {
				$delim = @shmop_read( $this->shmid, $offset, 3 );
				$offset += 3;
				if ( $delim == ( chr( 255 ) . chr( 255 ) . chr( 255 ) ) ) {
					$this->databaseType = ord( @shmop_read( $this->shmid, $offset, 1 ) );
					$offset++;

					if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV0 ) {
						$this->databaseSegments = $this->GEOIP_STATE_BEGIN_REV0;
					} else if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV1 ) {
							$this->databaseSegments = $this->GEOIP_STATE_BEGIN_REV1;
						} else if ( ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV0 ) ||
							( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1 )
							|| ( $this->databaseType == $this->GEOIP_ORG_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ORG_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION )
							|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_ISP_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ISP_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_USERTYPE_EDITION )
							|| ( $this->databaseType == $this->GEOIP_USERTYPE_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_LOCATIONA_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ACCURACYRADIUS_EDITION )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV0_V6 )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1_V6 )
							|| ( $this->databaseType == $this->GEOIP_NETSPEED_EDITION_REV1 )
							|| ( $this->databaseType == $this->GEOIP_NETSPEED_EDITION_REV1_V6 )
							|| ( $this->databaseType == $this->GEOIP_ASNUM_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ASNUM_EDITION_V6 ) ) {
							$this->databaseSegments = 0;
							$buf = @shmop_read( $this->shmid, $offset, $this->SEGMENT_RECORD_LENGTH );
							for ( $j = 0; $j < $this->SEGMENT_RECORD_LENGTH; $j++ ) {
								$this->databaseSegments += ( ord( $buf[$j] ) << ( $j * 8 ) );
							}
							if ( ( $this->databaseType == $this->GEOIP_ORG_EDITION )
								|| ( $this->databaseType == $this->GEOIP_ORG_EDITION_V6 )
								|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION )
								|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION_V6 )
								|| ( $this->databaseType == $this->GEOIP_ISP_EDITION )
								|| ( $this->databaseType == $this->GEOIP_ISP_EDITION_V6 ) ) {
								$this->record_length = $this->ORG_RECORD_LENGTH;
							}
						}
					break;
				} else {
					$offset -= 4;
				}
			}
			if ( ( $this->databaseType == $this->GEOIP_COUNTRY_EDITION ) ||
				( $this->databaseType == $this->GEOIP_COUNTRY_EDITION_V6 ) ||
				( $this->databaseType == $this->GEOIP_PROXY_EDITION ) ||
				( $this->databaseType == $this->GEOIP_NETSPEED_EDITION ) ) {
				$this->databaseSegments = $this->GEOIP_COUNTRY_BEGIN;
			}
		} else {
			$filepos = ftell( $this->filehandle );
			fseek( $this->filehandle, -3, SEEK_END );
			for ( $i = 0; $i < $this->STRUCTURE_INFO_MAX_SIZE; $i++ ) {
				$delim = fread( $this->filehandle, 3 );
				if ( $delim == ( chr( 255 ) . chr( 255 ) . chr( 255 ) ) ) {
					$this->databaseType = ord( fread( $this->filehandle, 1 ) );
					if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV0 ) {
						$this->databaseSegments = $this->GEOIP_STATE_BEGIN_REV0;
					} else if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV1 ) {
							$this->databaseSegments = $this->GEOIP_STATE_BEGIN_REV1;
						} else if ( ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV0 )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1 )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV0_V6 )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1_V6 )
							|| ( $this->databaseType == $this->GEOIP_ORG_EDITION )
							|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ISP_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ORG_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_ISP_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_LOCATIONA_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ACCURACYRADIUS_EDITION )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV0_V6 )
							|| ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1_V6 )
							|| ( $this->databaseType == $this->GEOIP_NETSPEED_EDITION_REV1 )
							|| ( $this->databaseType == $this->GEOIP_NETSPEED_EDITION_REV1_V6 )
							|| ( $this->databaseType == $this->GEOIP_USERTYPE_EDITION )
							|| ( $this->databaseType == $this->GEOIP_USERTYPE_EDITION_V6 )
							|| ( $this->databaseType == $this->GEOIP_ASNUM_EDITION )
							|| ( $this->databaseType == $this->GEOIP_ASNUM_EDITION_V6 ) ) {
							$this->databaseSegments = 0;
							$buf = fread( $this->filehandle, $this->SEGMENT_RECORD_LENGTH );
							for ( $j = 0; $j < $this->SEGMENT_RECORD_LENGTH; $j++ ) {
								$this->databaseSegments += ( ord( $buf[$j] ) << ( $j * 8 ) );
							}
							if ( ( $this->databaseType == $this->GEOIP_ORG_EDITION )
								|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION )
								|| ( $this->databaseType == $this->GEOIP_ISP_EDITION )
								|| ( $this->databaseType == $this->GEOIP_ORG_EDITION_V6 )
								|| ( $this->databaseType == $this->GEOIP_DOMAIN_EDITION_V6 )
								|| ( $this->databaseType == $this->GEOIP_ISP_EDITION_V6 ) ) {
								$this->record_length = $this->ORG_RECORD_LENGTH;
							}
						}
					break;
				} else {
					fseek( $this->filehandle, -4, SEEK_CUR );
				}
			}
			if ( ( $this->databaseType == $this->GEOIP_COUNTRY_EDITION ) ||
				( $this->databaseType == $this->GEOIP_COUNTRY_EDITION_V6 ) ||
				( $this->databaseType == $this->GEOIP_PROXY_EDITION ) ||
				( $this->databaseType == $this->GEOIP_NETSPEED_EDITION ) ) {
				$this->databaseSegments = $this->GEOIP_COUNTRY_BEGIN;
			}
			fseek( $this->filehandle, $filepos, SEEK_SET );
		}
	}


	/**
	 *
	 *
	 * @param unknown $filename
	 * @param unknown $flags
	 */
	public function geoip_open( $filename, $flags ) {
		$this->flags = $flags;
		if ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
			$this->shmid = @shmop_open( $this->GEOIP_SHM_KEY, "a", 0, 0 );
		} else {
			$this->filehandle = fopen( $filename, "rb" ) or die( "Can not open $filename\n" );
			if ( $this->flags & $this->GEOIP_MEMORY_CACHE ) {
				$s_array = fstat( $this->filehandle );
				$this->memory_buffer = fread( $this->filehandle, $s_array['size'] );
			}
		}

		$this->_setup_segments();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function geoip_close() {
		if ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
			return true;
		}

		return fclose( $this->filehandle );
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_id_by_name_v6( $name ) {
		$rec = dns_get_record( $name, DNS_AAAA );
		if ( !$rec ) {
			return false;
		}
		$addr = $rec[0]["ipv6"];
		if ( !$addr || $addr == $name ) {
			return false;
		}
		return $this->geoip_country_id_by_addr_v6( $addr );
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_id_by_name( $name ) {
		$addr = gethostbyname( $name );
		if ( !$addr || $addr == $name ) {
			return false;
		}
		return $this->geoip_country_id_by_addr( $addr );
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_code_by_name_v6( $name ) {
		$country_id = $this->geoip_country_id_by_name_v6( $name );
		if ( $country_id !== false ) {
			return $this->GEOIP_COUNTRY_CODES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_code_by_name( $name ) {
		$country_id = $this->geoip_country_id_by_name( $name );
		if ( $country_id !== false ) {
			return $this->GEOIP_COUNTRY_CODES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_name_by_name_v6( $name ) {
		$country_id = $this->geoip_country_id_by_name_v6( $name );
		if ( $country_id !== false ) {
			return $this->GEOIP_COUNTRY_NAMES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @return unknown
	 */
	public function geoip_country_name_by_name( $name ) {
		$country_id = $this->geoip_country_id_by_name( $name );
		if ( $country_id !== false ) {
			return $this->GEOIP_COUNTRY_NAMES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_id_by_addr_v6( $addr ) {
		$ipnum = inet_pton( $addr );
		return $this->_geoip_seek_country_v6( $ipnum ) - $this->GEOIP_COUNTRY_BEGIN;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_id_by_addr( $addr ) {
		$ipnum = ip2long( $addr );
		return $this->_geoip_seek_country( $ipnum ) - $this->GEOIP_COUNTRY_BEGIN;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_code_by_addr_v6( $addr ) {
		$country_id = $this->geoip_country_id_by_addr_v6( $addr );
		if ( $country_id !== false ) {
			return $this->GEOIP_COUNTRY_CODES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_code_by_addr( $addr ) {
		if ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1 ) {
			$record = $this->geoip_record_by_addr( $addr );
			if ( $record !== false ) {
				return $record->country_code;
			}
		} else {
			$country_id = $this->geoip_country_id_by_addr( $addr );
			if ( $country_id !== false ) {
				return $this->GEOIP_COUNTRY_CODES[$country_id];
			}
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_name_by_addr_v6( $addr ) {
		$country_id = $this->geoip_country_id_by_addr_v6( $addr );
		if ( $country_id !== false && isset( $this->GEOIP_COUNTRY_NAMES[$country_id] ) ) {
			return $this->GEOIP_COUNTRY_NAMES[$country_id];
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_country_name_by_addr( $addr ) {
		if ( $this->databaseType == $this->GEOIP_CITY_EDITION_REV1 ) {
			$record = $this->geoip_record_by_addr( $addr );
			return $record->country_name;
		} else {
			$country_id = $this->geoip_country_id_by_addr( $addr );
			if ( $country_id !== false ) {
				return $this->GEOIP_COUNTRY_NAMES[$country_id];
			}
		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _geoip_seek_country_v6( $ipnum ) {

		// arrays from unpack start with offset 1
		// yet another php mystery. array_merge work around
		// this broken behaviour
		$v6vec = array_merge( unpack( "C16", $ipnum ) );

		$offset = 0;
		for ( $depth = 127; $depth >= 0; --$depth ) {
			if ( $this->flags & $this->GEOIP_MEMORY_CACHE ) {
				// workaround php's broken substr, strpos, etc handling with
				// mbstring.func_overload and mbstring.internal_encoding

				if ( function_exists( 'mb_internal_encoding' ) ) {
					$enc = mb_internal_encoding();
					mb_internal_encoding( 'ISO-8859-1' );
				}

				$buf = substr( $this->memory_buffer,
					2 * $this->record_length * $offset,
					2 * $this->record_length );

				if ( isset( $enc ) ) {
					mb_internal_encoding( $enc );
				}

			} elseif ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
				$buf = @shmop_read( $this->shmid,
					2 * $this->record_length * $offset,
					2 * $this->record_length );
			} else {
				fseek( $this->filehandle, 2 * $this->record_length * $offset, SEEK_SET ) == 0
					or die( "fseek failed" );
				$buf = fread( $this->filehandle, 2 * $this->record_length );
			}
			$x = array( 0, 0 );
			for ( $i = 0; $i < 2; ++$i ) {
				for ( $j = 0; $j < $this->record_length; ++$j ) {
					$x[$i] += ord( $buf[$this->record_length * $i + $j] ) << ( $j * 8 );
				}
			}

			$bnum = 127 - $depth;
			$idx = $bnum >> 3;
			$b_mask = 1 << ( $bnum & 7 ^ 7 );
			if ( ( $v6vec[$idx] & $b_mask ) > 0 ) {
				if ( $x[1] >= $this->databaseSegments ) {
					return $x[1];
				}
				$offset = $x[1];
			} else {
				if ( $x[0] >= $this->databaseSegments ) {
					return $x[0];
				}
				$offset = $x[0];
			}
		}
		trigger_error( "error traversing database - perhaps it is corrupt?" );
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _geoip_seek_country( $ipnum ) {
		$offset = 0;
		for ( $depth = 31; $depth >= 0; --$depth ) {
			if ( $this->flags & $this->GEOIP_MEMORY_CACHE ) {
				// workaround php's broken substr, strpos, etc handling with
				// mbstring.func_overload and mbstring.internal_encoding
				if ( function_exists( 'mb_internal_encoding' ) ) {
					$enc = mb_internal_encoding();
					mb_internal_encoding( 'ISO-8859-1' );
				}

				$buf = substr( $this->memory_buffer,
					2 * $this->record_length * $offset,
					2 * $this->record_length );

				if ( isset( $enc ) ) {
					mb_internal_encoding( $enc );
				}

			} elseif ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
				$buf = @shmop_read( $this->shmid,
					2 * $this->record_length * $offset,
					2 * $this->record_length );
			} else {
				fseek( $this->filehandle, 2 * $this->record_length * $offset, SEEK_SET ) == 0
					or die( "fseek failed" );
				$buf = fread( $this->filehandle, 2 * $this->record_length );
			}
			$x = array( 0, 0 );
			for ( $i = 0; $i < 2; ++$i ) {
				for ( $j = 0; $j < $this->record_length; ++$j ) {
					$x[$i] += ord( $buf[$this->record_length * $i + $j] ) << ( $j * 8 );
				}
			}
			if ( $ipnum & ( 1 << $depth ) ) {
				if ( $x[1] >= $this->databaseSegments ) {
					return $x[1];
				}
				$offset = $x[1];
			} else {
				if ( $x[0] >= $this->databaseSegments ) {
					return $x[0];
				}
				$offset = $x[0];
			}
		}
		trigger_error( "error traversing database - perhaps it is corrupt?" );
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $seek_org
	 * @return unknown
	 */
	public function _common_get_org( $seek_org ) {
		$record_pointer = $seek_org + ( 2 * $this->record_length - 1 ) * $this->databaseSegments;
		if ( $this->flags & $this->GEOIP_SHARED_MEMORY ) {
			$org_buf = @shmop_read( $this->shmid, $record_pointer, $this->MAX_ORG_RECORD_LENGTH );
		} else {
			fseek( $this->filehandle, $record_pointer, SEEK_SET );
			$org_buf = fread( $this->filehandle, $this->MAX_ORG_RECORD_LENGTH );
		}
		// workaround php's broken substr, strpos, etc handling with
		// mbstring.func_overload and mbstring.internal_encoding
		if ( function_exists( 'mb_internal_encoding' ) ) {
			$enc = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}
		$org_buf = substr( $org_buf, 0, strpos( $org_buf, "\0" ) );
		if ( isset( $enc ) ) {
			mb_internal_encoding( $enc );
		}

		return $org_buf;
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _get_org_v6( $ipnum ) {
		$seek_org = $this->_geoip_seek_country_v6( $ipnum );
		if ( $seek_org == $this->databaseSegments ) {
			return null;
		}
		return $this->_common_get_org( $seek_org );
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _get_org( $ipnum ) {
		$seek_org = $this->_geoip_seek_country( $ipnum );
		if ( $seek_org == $this->databaseSegments ) {
			return null;
		}
		return $this->_common_get_org( $seek_org );
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_name_by_addr_v6( $addr ) {
		if ( $addr == null ) {
			return 0;
		}
		$ipnum = inet_pton( $addr );
		return $this->_get_org_v6( $ipnum );
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_name_by_addr( $addr ) {
		if ( $addr == null ) {
			return 0;
		}
		$ipnum = ip2long( $addr );
		return $this->_get_org( $ipnum );
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_org_by_addr( $addr ) {
		return $this->geoip_name_by_addr( $addr );
	}


	/**
	 *
	 *
	 * @param unknown $ipnum
	 * @return unknown
	 */
	public function _get_region( $ipnum ) {
		if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV0 ) {
			$seek_region = $this->_geoip_seek_country( $ipnum ) - $this->GEOIP_STATE_BEGIN_REV0;
			if ( $seek_region >= 1000 ) {
				$country_code = "US";
				$region = chr( ( $seek_region - 1000 ) / 26 + 65 ) . chr( ( $seek_region - 1000 ) % 26 + 65 );
			} else {
				$country_code = $this->GEOIP_COUNTRY_CODES[$seek_region];
				$region = "";
			}
			return array( $country_code, $region );
		} else if ( $this->databaseType == $this->GEOIP_REGION_EDITION_REV1 ) {
				$seek_region = $this->_geoip_seek_country( $ipnum ) - $this->GEOIP_STATE_BEGIN_REV1;
				//print $seek_region;
				if ( $seek_region < $this->US_OFFSET ) {
					$country_code = "";
					$region = "";
				} else if ( $seek_region < $this->CANADA_OFFSET ) {
						$country_code = "US";
						$region = chr( ( $seek_region - $this->US_OFFSET ) / 26 + 65 ) . chr( ( $seek_region - $this->US_OFFSET ) % 26 + 65 );
					} else if ( $seek_region < $this->WORLD_OFFSET ) {
						$country_code = "CA";
						$region = chr( ( $seek_region - $this->CANADA_OFFSET ) / 26 + 65 ) . chr( ( $seek_region - $this->CANADA_OFFSET ) % 26 + 65 );
					} else {
					$country_code = $this->GEOIP_COUNTRY_CODES[( $seek_region - $this->WORLD_OFFSET ) / $this->FIPS_RANGE];
					$region = "";
				}
				return array( $country_code, $region );
			}
	}


	/**
	 *
	 *
	 * @param unknown $addr
	 * @return unknown
	 */
	public function geoip_region_by_addr( $addr ) {
		if ( $addr == null ) {
			return 0;
		}
		$ipnum = ip2long( $addr );
		return $this->_get_region( $ipnum );
	}


	/**
	 *
	 *
	 * @param unknown $l
	 * @param unknown $ip
	 * @return unknown
	 */
	public function getdnsattributes( $l, $ip ) {
		$r = new Net_DNS_Resolver();
		$r->nameservers = array( "ws1.maxmind.com" );
		$p = $r->search( $l . "." . $ip . ".s.maxmind.com", "TXT", "IN" );
		$str = is_object( $p->answer[0] ) ? $p->answer[0]->string() : '';
		$str = substr( $str, 1, -1 );
		return $str;
	}


}
