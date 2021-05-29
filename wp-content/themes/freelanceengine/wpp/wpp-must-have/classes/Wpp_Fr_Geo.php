<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 31.07.2019
 * Time: 20:48
 */

class Wpp_Fr_Geo {

	public static function init() {
		add_action( 'init', [ __CLASS__, 'require' ] );
	}

	public static function require() {
		#когда подключаем GEO
		require_once WPP_ABSPATH . 'libs/geo-api/sx-api/SxGeo.php';
	}

	/**
	 * Получение IP пользователя
	 * @return mixed|void
	 */
	public static function get_user_ip() {

		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ip = $_SERVER['HTTP_FORWARDED'];
		} else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = false;
		}

		return apply_filters( 'wpp_fr_user_ip', $ip );
	}


	/**
	 * Адреса при умолчании при дебаге
	 *
	 * @return mixed|void
	 */
	public static function debug_ip_array() {

		$array = [
			'USA' => [
				'NY' => '72.229.28.185',
				'PH' => '148.167.2.30',
				'TX' => '76.31.84.249'
			],
			'FR'  => [ 'PZ' => '40.89.141.98' ],
			'UA'  => [ 'KY' => '91.198.36.14' ],
			'RU'  => [
				'MSK' => '212.248.4.254',
				"SPB" => '194.105.201.1'
			]
		];

		return apply_filters( 'wpp_fr_geo_array', $array );
	}

	/**
	 * Выыод IP пользователя с учетом дебага и не полученгия
	 *
	 * @param null $debug
	 *
	 * @return mixed|void
	 */

	public static function user_ip() {
		$array = self::debug_ip_array();


		if ( WPP_FR_GEO_DEV === true ) {
			$ip = apply_filters( 'wpp_fr_geo_default_IP', $array['RU']['MSK'] );
		} else {
			$ip_get = self::get_user_ip();
			if ( ! empty( $ip_get ) ) {
				$ip = $ip_get;
			}
		}

		return $ip;
	}

	public static function get_user_geo_data() {
		require_once WPP_ABSPATH . 'libs/geo-api/sx-api/SxGeo.php';
		$ip = self::user_ip();

		$ip    = explode( ',', $ip );
		$SxGeo = new SxGeo( WPP_ABSPATH . 'libs/geo-api/sx-data/SxGeoCity.dat' );

		return $SxGeo->getCityFull( $ip[0] );

	}


}

$GLOBALS['wpp_geo'] = new Wpp_Fr_Geo();


/**
 * проверка на соответствие условию
 *
 * @param $type - тип
 * @param $val - id из справочника
 *
 * @return bool
 */
function wpp_fr_if_user_geo( $type, $val, $debug = '' ) {

	$types = [
		1 => 'city',
		2 => 'region',
		3 => 'country'
	];

	$data = Wpp_Fr_Geo::get_user_geo_data();
	/*if ( ! empty( $debug ) ) {
		wpp_console( $data );
	}*/


	if ( ! empty( $data ) && $data[ $types[ $type ] ]['id'] === $val ) {
		$out = true;
	} else {
		$out = false;
	}

	return $out;
}

function wpp_fr_is_russia() {
	return wpp_fr_if_user_geo( 3, 185 );
}

/**
 * Страны по языкам
 *
 * @param null $lang
 *
 * @return mixed|void
 */
function wpp_fr_geo_array_country( $lang = null ) {

	$array = [
		'en_US' => [
			'Afghanistan',
			'Albania',
			'Algeria',
			'American Samoa',
			'Andorra',
			'Angola',
			'Anguilla',
			'Antarctica',
			'Antigua and Barbuda',
			'Argentina',
			'Armenia',
			'Aruba',
			'Australia',
			'Austria',
			'Azerbaijan',
			'Bahamas',
			'Bahrain',
			'Bangladesh',
			'Barbados',
			'Belarus',
			'Belgium',
			'Belize',
			'Benin',
			'Bermuda',
			'Bhutan',
			'Bolivia',
			'Bosnia and Herzegovina',
			'Botswana',
			'Brazil',
			'Brunei Darussalam',
			'Bulgaria',
			'Burkina Faso',
			'Burundi',
			'Cambodia',
			'Cameroon',
			'Canada',
			'Cape Verde',
			'Cayman Islands',
			'Central African Republic',
			'Chad',
			'Chile',
			'China',
			'Colombia',
			'Comoros',
			'Democratic Republic of the Congo(Kinshasa)',
			'Congo, Republic of (Brazzaville)',
			'Cook Islands',
			'Costa Rica',
			'Cote d\'Ivoire',
			'Croatia',
			'Cuba',
			'Cyprus',
			'Czech Republic',
			'Denmark',
			'Djibouti',
			'Dominica',
			'Dominican Republic',
			'East Timor Timor-Leste',
			'Ecuador',
			'Egypt',
			'El Salvador',
			'Equatorial Guinea',
			'Eritrea',
			'Estonia',
			'Ethiopia',
			'Falkland Islands',
			'Faroe Islands',
			'Fiji',
			'Finland',
			'France',
			'French Guiana',
			'French Polynesia',
			'Gabon',
			'Gambia',
			'Georgia',
			'Germany',
			'Ghana',
			'Gibraltar',
			'Greece',
			'Greenland',
			'Grenada',
			'Guadeloupe',
			'Guam',
			'Guatemala',
			'Guinea',
			'Guinea-Bissau',
			'Guyana',
			'Haiti',
			'Honduras',
			'Hong Kong',
			'Hungary',
			'Iceland',
			'India',
			'Indonesia',
			'Iran',
			'Iraq',
			'Ireland',
			'Israel',
			'Italy',
			'Jamaica',
			'Japan',
			'Jordan',
			'Kazakhstan',
			'Kenya',
			'Kiribati',
			'Korea, (North Korea)',
			'Korea, (South Korea)',
			'Kuwait',
			'Kyrgyzstan',
			'Lao, People\'s Democratic Republic',
			'Latvia',
			'Lebanon',
			'Lesotho',
			'Liberia',
			'Libya',
			'Liechtenstein',
			'Lithuania',
			'Luxembourg',
			'Macao',
			'Macedonia, Rep. of',
			'Madagascar',
			'Malawi',
			'Malaysia',
			'Maldives',
			'Mali',
			'Malta',
			'Marshall Islands',
			'Martinique',
			'Mauritania',
			'Mauritius',
			'Mexico',
			'Micronesia, Federal States of',
			'Moldova',
			'Monaco',
			'Mongolia',
			'Montenegro',
			'Montserrat',
			'Morocco',
			'Mozambique',
			'Myanmar, Burma',
			'Namibia',
			'Nauru',
			'Nepal',
			'Netherlands',
			'Netherlands Antilles',
			'New Caledonia',
			'New Zealand',
			'Nicaragua',
			'Niger',
			'Nigeria',
			'Niue',
			'Northern Mariana Islands',
			'Norway',
			'Oman',
			'Pakistan',
			'Palau',
			'Palestinian territory',
			'Panama',
			'Papua New Guinea',
			'Paraguay',
			'Peru',
			'Philippines',
			'Poland',
			'Portugal',
			'Puerto Rico',
			'Qatar',
			'Reunion Island',
			'Romania',
			'Russian Federation',
			'Rwanda',
			'Saint Kitts and Nevis',
			'Saint Lucia',
			'Saint Vincent and the Grenadines',
			'Samoa',
			'San Marino',
			'Sao Tome and Príncipe',
			'Saudi Arabia',
			'Senegal',
			'Serbia',
			'Seychelles',
			'Sierra Leone',
			'Singapore',
			'Slovakia',
			'Slovenia',
			'Solomon Islands',
			'Somalia',
			'South Africa',
			'Spain',
			'Sri Lanka',
			'Sudan',
			'Suriname',
			'Swaziland',
			'Sweden',
			'Switzerland',
			'Syria',
			'Taiwan',
			'Tajikistan',
			'Tanzania',
			'Thailand',
			'Tibet',
			'Timor-Leste (East Timor)',
			'Togo',
			'Tokelau',
			'Tonga',
			'Trinidad and Tobago',
			'Tunisia',
			'Turkey',
			'Turkmenistan',
			'Tuvalu',
			'Uganda',
			'Ukraine',
			'United Arab Emirates',
			'United Kingdom of Great Britain and Northern Ireland',
			'United States',
			'Uruguay',
			'Uzbekistan',
			'Vanuatu',
			'Vatican City State',
			'Venezuela',
			'Vietnam',
			'Virgin Islands (British)',
			'Virgin Islands (U.S.)',
			'Wallis and Futuna Islands',
			'Western Sahara',
			'Yemen',
			'Zambia',
			'Zimbabwe'
		],
		'ru_RU' => [
			'Афганистан',
			'Албания',
			'Алжир',
			'Американское Самоа',
			'Андорра',
			'Ангола',
			'Ангилья',
			'Антарктида',
			'Антигуа и Барбуда',
			'Аргентина',
			'Армения',
			'Аруба',
			'Австралия',
			'Австрия',
			'Азербайджан',
			'Багамы',
			'Бахрейн',
			'Бангладеш',
			'Барбадос',
			'Белоруссия',
			'Бельгия',
			'Белиз',
			'Бенин',
			'Бермуды',
			'Бутан',
			'Боливия',
			'Босния и Герцеговина',
			'Ботсвана',
			'Бразилия',
			'Бруней',
			'Болгария',
			'Буркина-Фасо',
			'Бурунди',
			'Камбоджа',
			'Камерун',
			'Канада',
			'Кабо-Верде',
			'Каймановы Острова',
			'Центрально-Африканская Республика',
			'Чад',
			'Чили',
			'Китай',
			'Колумбия',
			'Коморские острова',
			'Демократическая Республика Конго',
			'Республика Конго',
			'Острова Кука',
			'Коста-Рика',
			'Кот-д\'Ивуар',
			'Хорватия',
			'Куба',
			'Кипр',
			'Чехия',
			'Дания',
			'Джибути',
			'Доминика',
			'Доминиканская Республика',
			'Восточный Тимор',
			'Эквадор',
			'Египет',
			'Эль-Сальвадор',
			'Экваториальная Гвинея',
			'Эритрея',
			'Эстония',
			'Эфиопия',
			'Фолклендские острова',
			'Фарерские острова',
			'Фиджи',
			'Финляндия',
			'Франция',
			'Французская Гвиана',
			'Французская Полинезия',
			'Габон',
			'Гамбия',
			'Грузия',
			'Германия',
			'Гана',
			'Гибралтар',
			'Греция',
			'Гренландия',
			'Гренада',
			'Гваделупа',
			'Гуам',
			'Гватемала',
			'Гвинея',
			'Гвинея-Бисау',
			'Гайана',
			'Гаити',
			'Гондурас',
			'Гонконг',
			'Венгрия',
			'Исландия',
			'Индия',
			'Индонезия',
			'Иран',
			'Ирак',
			'Ирландия',
			'Израиль',
			'Италия',
			'Ямайка',
			'Япония',
			'Иордания',
			'Казахстан',
			'Кения',
			'Кирибати',
			'Корея, северная',
			'Корея, южная',
			'Кувейт',
			'Киргизия',
			'Лаос',
			'Латвия',
			'Ливан',
			'Лесото',
			'Либерия',
			'Ливия',
			'Лихтенштейн',
			'Литва',
			'Люксембург',
			'Макао',
			'Македония',
			'Мадагаскар',
			'Малави',
			'Малайзия',
			'Мальдивы',
			'Мали',
			'Мальта',
			'Маршалловы Острова',
			'Мартиника',
			'Мавритания',
			'Маврикий',
			'Мексика',
			'Микронезии, Федеративные Штаты',
			'Молдавия',
			'Монако',
			'Монголия',
			'Черногория',
			'Монтсерат',
			'Марокко',
			'Мозамбик',
			'Мьянма',
			'Намибия',
			'Науру',
			'Непал',
			'Нидерланды',
			'Нидерландские Антильские острова',
			'Новая Каледония',
			'Новая Зеландия',
			'Никарагуа',
			'Нигер',
			'Нигерия',
			'Ниуэ',
			'Северные Марианские острова',
			'Норвегия',
			'Оман',
			'Пакистан',
			'Палау',
			'Палестинской территории',
			'Панама',
			'Папуа — Новая Гвинея',
			'Парагвай',
			'Перу',
			'Филиппины',
			'Польша',
			'Португалия',
			'Пуэрто-Рико',
			'Катар',
			'Реюньон',
			'Румыния',
			'Россия',
			'Руанда',
			'Сент-Китс и Невис',
			'Сент-Люсия',
			'Сент-Винсент и Гренадины',
			'Самоа',
			'Сан-Марино',
			'Сан-Томе и Принсипи',
			'Саудовская Аравия',
			'Сенегал',
			'Сербия',
			'Сейшельские острова',
			'Сьерра-Леоне',
			'Сингапур',
			'Словакия',
			'Словения',
			'Соломоновы Острова',
			'Сомали',
			'Южно-Африканская Республика (ЮАР)',
			'Испания',
			'Шри-Ланка',
			'Судан',
			'Суринам',
			'Свазиленд',
			'Швеция',
			'Швейцария',
			'Сирия',
			'Тайвань',
			'Таджикистан',
			'Танзания',
			'Таиланд',
			'Тибет',
			'Восточный Тимор',
			'Того',
			'Токелау',
			'Тонга',
			'Тринидад и Тобаго',
			'Тунис',
			'Турция',
			'Туркменистан',
			'Тувалу',
			'Уганда',
			'Украина',
			'Объединённые Арабские Эмираты (ОАЭ)',
			'Соединённое Королевство Великобритании и Северной Ирландии',
			'Соединённые Штаты Америки',
			'Уругвай',
			'Узбекистан',
			'Вануату',
			'Ватикан',
			'Венесуэла',
			'Вьетнам',
			'Британские Виргинские острова',
			'Американские Виргинские острова',
			'Острова Уоллис и Футуна',
			'Западная Сахара',
			'Йемен',
			'Замбия',
			'Зимбабве',
		]
	];

	if ( ! empty( $lang ) && in_array( $array[ $lang ], $array ) ) {
		return $array[ $lang ];
	} else {
		return apply_filters( 'wpp_fr_geo_def_country_array', $array['en_US'] );
	}
}