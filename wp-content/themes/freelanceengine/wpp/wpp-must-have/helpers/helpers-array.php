<?php
/**
 * File Description
 *
 * @author  WP Panda
 *
 * @package auto.calk
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpp_empty_array_clear' ) ):
	/**
	 * Sanitize array by empty elements
	 * Удаление пустых эденементов из массива
	 *
	 * @param $array
	 *
	 * @return array|bool
	 */
	function wpp_empty_array_clear( $array ) {

		if ( ! is_array( $array ) ) {
			return false;
		}

		foreach ( $array as $k => $v ) {
			if ( is_array( $v ) ) {
				$array[ $k ] = wpp_empty_array_clear( $v );
				if ( count( $array[ $k ] ) == false ) {
					unset( $array[ $k ] );
				}
			} else {
				if ( $v === '' || $v === null || $v == false ) {
					unset( $array[ $k ] );
				}
			}
		}

		return $array;
	}

endif;


if ( ! function_exists( 'wpp_fr_make_comparer' ) ) :
	/**
	 * @see https://stackoverflow.com/questions/96759/how-do-i-sort-a-multidimensional-array-in-php#16788610
	 */
	function wpp_fr_make_comparer() {
		// Normalize criteria up front so that the comparer finds everything tidy
		$criteria = func_get_args();
		foreach ( $criteria as $index => $criterion ) {
			$criteria[ $index ] = is_array( $criterion ) ? array_pad( $criterion, 3, null ) : [
				$criterion,
				SORT_ASC,
				null
			];
		}

		return function ( $first, $second ) use ( &$criteria ) {
			foreach ( $criteria as $criterion ) {
				// How will we compare this round?
				list( $column, $sortOrder, $projection ) = $criterion;
				$sortOrder = $sortOrder === SORT_DESC ? - 1 : 1;

				// If a projection was defined project the values now
				if ( $projection ) {
					$lhs = call_user_func( $projection, $first[ $column ] );
					$rhs = call_user_func( $projection, $second[ $column ] );
				} else {
					$lhs = $first[ $column ];
					$rhs = $second[ $column ];
				}

				// Do the actual comparison; do not return if equal
				if ( $lhs < $rhs ) {
					return - 1 * $sortOrder;
				} else if ( $lhs > $rhs ) {
					return 1 * $sortOrder;
				}
			}

			return 0; // tiebreakers exhausted, so $first == $second
		};
	}

endif;

/**
 * Dct djpvj;yst 'kktvtyns vfccbdf
 *
 * @param      $arrays
 * @param int $N
 * @param bool $count
 * @param bool $weight
 *
 * @return array
 */
function wpp_fr_array_combinate( $arrays, $N = - 1, $count = false, $weight = false ) {

	#wpp_d_log($arrays);
	/*
		Делает примерно то, о чём написано, например, здесь:
		http://www.sql.ru/Forum/actualthread.aspx?tid=725312
		Только мне было лень вникать в чужой код, и я написал свой :)
	*/

	#$variant = get_post_meta( $post->ID, 'ex_page_' . $term->term_id, true );
	/*if ( empty( $variant ) ) {
		#continue;
	}*/

	$out = '';
	if ( $N == - 1 ) {
		// Функция запущена в первый раз и запущена "снаружи", а не из самой себя.

		$arrays = array_values( $arrays );
		#wpp_dump($arrays);
		$count  = count( $arrays );
		$weight = array_fill( - 1, $count + 1, 1 );
		$Q      = 1;

		// Подсчитываем:
		// $Q - количество возможных комбинаций,
		// $weight - массив "весов" разрядов.
		foreach ( $arrays as $i => $array ) {
			$size         = count( $array );
			$Q            = $Q * $size;
			$weight[ $i ] = $weight[ $i - 1 ] * $size;

		}

		$result = [];
		for ( $n = 0; $n < $Q; $n ++ ) {
			$result[] = wpp_fr_array_combinate( $arrays, $n, $count, $weight );
		}

		return $result;
	} else {
		#wpp_dump($result);
		// Дано конкретное число, надо его "преобразовать" в комбинацию.
		// Чтобы не переспрашивать функцию count() обо всём каждый раз, нам уже даны:
		// $count - общее количество массивов, т.е. count($arrays),
		// $weight - "вес" одной единицы "разряда", с учётом веса предыдущих разрядов.

		// Заготавливаем нулевой массив состояний
		$SostArr = array_fill( 0, $count, 0 );

		$oldN = $N;

		// Идём по радрядам начиная с наибольшего
		for ( $i = $count - 1; $i >= 0; $i -- ) {
			// Поступаем как с числами в позиционных системах счисления,
			// то есть максимально заполняем наибольшие значения
			// и по остаточному принципу - наименьшие.
			// Число в i-ом разряде выражается как количество весов (i-1)0ых разрядов...
			// Да-да, я очень криво объясняю, просто поверьте на слово.
			// Вообще, эти две строки можно проверить и самостоятельно... =)
			$SostArr[ $i ] = floor( $N / $weight[ $i - 1 ] );
			$N             = $N - $SostArr[ $i ] * $weight[ $i - 1 ];
		}

		// Наконец, переводим "состояния" в реальные значения
		$result = [];
		for ( $i = 0; $i < $count; $i ++ ) {
			$result[ $i ] = $arrays[ $i ][ $SostArr[ $i ] ];
		}

		return implode( '-', $result );
	}


}

/**
 * Dct djpvj;yst 'kktvtyns vfccbdf
 *
 * @param      $arrays
 * @param int $N
 * @param bool $count
 * @param bool $weight
 *
 * @return array
 */
function wpp_fr_array_combinate2( $arrays, $N = - 1, $count = false, $weight = false ) {
	/*
		Делает примерно то, о чём написано, например, здесь:
		http://www.sql.ru/Forum/actualthread.aspx?tid=725312
		Только мне было лень вникать в чужой код, и я написал свой :)
	*/

	$out = '';
	if ( $N == - 1 ) {
		// Функция запущена в первый раз и запущена "снаружи", а не из самой себя.

		$arrays = array_values( $arrays );
		$count  = count( $arrays );
		$weight = array_fill( - 1, $count + 1, 1 );
		$Q      = 1;

		// Подсчитываем:
		// $Q - количество возможных комбинаций,
		// $weight - массив "весов" разрядов.
		foreach ( $arrays as $i => $array ) {
			$size         = count( $array );
			$Q            = $Q * $size;
			$weight[ $i ] = $weight[ $i - 1 ] * $size;

		}

		$result = [];
		for ( $n = 0; $n < $Q; $n ++ ) {
			$result[] = wpp_fr_array_combinate( $arrays, $n, $count, $weight );
		}

		return $result;
	} else {
		#wpp_dump($result);
		// Дано конкретное число, надо его "преобразовать" в комбинацию.
		// Чтобы не переспрашивать функцию count() обо всём каждый раз, нам уже даны:
		// $count - общее количество массивов, т.е. count($arrays),
		// $weight - "вес" одной единицы "разряда", с учётом веса предыдущих разрядов.

		// Заготавливаем нулевой массив состояний
		$SostArr = array_fill( 0, $count, 0 );

		$oldN = $N;

		// Идём по радрядам начиная с наибольшего
		for ( $i = $count - 1; $i >= 0; $i -- ) {
			// Поступаем как с числами в позиционных системах счисления,
			// то есть максимально заполняем наибольшие значения
			// и по остаточному принципу - наименьшие.
			// Число в i-ом разряде выражается как количество весов (i-1)0ых разрядов...
			// Да-да, я очень криво объясняю, просто поверьте на слово.
			// Вообще, эти две строки можно проверить и самостоятельно... =)
			$SostArr[ $i ] = floor( $N / $weight[ $i - 1 ] );
			$N             = $N - $SostArr[ $i ] * $weight[ $i - 1 ];
		}

		// Наконец, переводим "состояния" в реальные значения
		$result = [];
		for ( $i = 0; $i < $count; $i ++ ) {
			$result[ $i ] = $arrays[ $i ][ $SostArr[ $i ] ];
		}

		return implode( '-', $result );
	}


}


/**
 * Сортируем многомерный массив по значению вложенного массива
 *
 * @param $array array многомерный массив который сортируем
 * @param $field string название поля вложенного массива по которому необходимо отсортировать
 *
 * @return array отсортированный многомерный массив
 */
function customMultiSort( $array, $field, $dir = SORT_ASC ) {
	$sortArr = array();
	foreach ( $array as $key => $val ) {
		$sortArr[ $key ] = $val[ $field ];
	}

	array_multisort( $sortArr, $dir, $array );

	return $array;
}

/**
 * Числовые значение ключа в строку
 *
 * @param $array
 *
 * @return array
 */
function wpp_fr_array_keys_to_string( $array ) {
	$keys       = array_keys( $array );
	$values     = array_values( $array );
	$stringKeys = array_map( 'strval', $keys );

	return array_combine( $stringKeys, $values );
}