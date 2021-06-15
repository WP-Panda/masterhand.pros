<?php
/**
 * Функции для киррилицы
 *
 * @package WppFramework\WppMastHave\Helpers
 * @version 1.0.0
 * @since   1.0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'wpp_plural_form' ) ) :

	/**
	 * Русские окончания после числительных
	 * @since   1.0.1
	 *
	 * @param $number int   число
	 * @param $after  array варианты написания для количества 1, 2 и 5
	 *
	 * @return mixed
	 */
	function wpp_plural_form( $number, $after ) {

		$cases = array( 2, 0, 1, 1, 1, 2 );

		return $after[ ( (int) $number % 100 > 4 && (int) $number % 100 < 20 ) ? 2 : $cases[ min( (int) $number % 10, 5 ) ] ];

	}

endif;


if ( ! function_exists( 'wpp_num_2_str' ) ) :
	/**
	 * Возвращает сумму прописью
	 * @since   1.0.1
	 * @version 1.0.3
	 *
	 * @author  runcore
	 * @uses    wpp_plural_form()
	 */
	function wpp_num_2_str( $number ) {
		$nul = 'ноль';
		$ten = [
			[
				__( '' ),
				__( 'один' ),
				__( 'два' ),
				__( 'три' ),
				__( 'четыре' ),
				__( 'пять' ),
				__( 'шесть' ),
				__( 'семь' ),
				__( 'восемь' ),
				__( 'девять' ),
			],
			[
				__( '' ),
				__( 'одна' ),
				__( 'две' ),
				__( 'три' ),
				__( 'четыре' ),
				__( 'пять' ),
				__( 'шесть' ),
				__( 'семь' ),
				__( 'восемь' ),
				__( 'девять' ),
			]
		];

		$a20     = [
			__( 'десять' ),
			__( 'одиннадцать' ),
			__( 'двенадцать' ),
			__( 'тринадцать' ),
			__( 'четырнадцать' ),
			__( 'пятнадцать' ),
			__( 'шестнадцать' ),
			__( 'семнадцать' ),
			__( 'восемнадцать' ),
			__( 'девятнадцать' )
		];
		$tens    = [
			2 => __( 'двадцать' ),
			__( 'тридцать' ),
			__( 'сорок' ),
			__( 'пятьдесят' ),
			__( 'шестьдесят' ),
			__( 'семьдесят' ),
			__( 'восемьдесят' ),
			__( 'девяносто' )
		];
		$hundred = [
			__( '' ),
			__( 'сто' ),
			__( 'двести' ),
			__( 'триста' ),
			__( 'четыреста' ),
			__( 'пятьсот' ),
			__( 'шестьсот' ),
			__( 'семьсот' ),
			__( 'восемьсот' ),
			__( 'девятьсот' )
		];
		$unit    = [
			[
				__( 'копейка' ),
				__( 'копейки' ),
				__( 'копеек' ),
				1
			],
			[
				__( 'рубль' ),
				__( 'рубля' ),
				__( 'рублей' ),
				0
			],
			[
				__( 'тысяча' ),
				__( 'тысячи' ),
				__( 'тысяч' ),
				1
			],
			[
				__( 'миллион' ),
				__( 'миллиона' ),
				__( 'миллионов' ),
				0
			],
			[
				__( 'миллиард' ),
				__( 'милиарда' ),
				__( 'миллиардов' ),
				0
			]
		];
		//
		list( $rub, $kop ) = explode( '.', sprintf( "%015.2f", floatval( $number ) ) );
		$out = array();
		if ( intval( $rub ) > 0 ) {
			foreach ( str_split( $rub, 3 ) as $uk => $v ) { // by 3 symbols
				if ( ! intval( $v ) ) {
					continue;
				}
				$uk     = sizeof( $unit ) - $uk - 1; // unit key
				$gender = $unit[ $uk ][3];
				list( $i1, $i2, $i3 ) = array_map( 'intval', str_split( $v, 1 ) );
				// mega-logic
				$out[] = $hundred[ $i1 ]; # 1xx-9xx
				if ( $i2 > 1 ) {
					$out[] = $tens[ $i2 ] . ' ' . $ten[ $gender ][ $i3 ];
				} # 20-99
				else {
					$out[] = $i2 > 0 ? $a20[ $i3 ] : $ten[ $gender ][ $i3 ];
				} # 10-19 | 1-9
				// units without rub & kop
				if ( $uk > 1 ) {
					$out[] = wpp_plural_form( $v, [ $unit[ $uk ][0], $unit[ $uk ][1], $unit[ $uk ][2] ] );
				}
			} //foreach
		} else {
			$out[] = $nul;
		}
		$out[] = wpp_plural_form( (int) $rub, [ $unit[1][0], $unit[1][1], $unit[1][2] ] ); // rub
		$out[] = $kop . ' ' . wpp_plural_form( $kop, [ $unit[0][0], $unit[0][1], $unit[0][2] ] ); // kop

		return trim( preg_replace( '/ {2,}/', ' ', join( ' ', $out ) ) );
	}
endif;