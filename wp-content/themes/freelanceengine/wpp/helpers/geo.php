<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Преобразование кодов в адрес
 *
 * @param null $country
 * @param null $state
 * @param null $city
 *
 * @return string
 */

function wpp_convert_int_too_location( $country = null, $state = null, $city = null ) {

	$location = getLocation( 0, [ 'country' => $country, 'state' => $state, 'city' => $city ] );

	if ( ! empty( $location['country'] ) ) {
		$str_location = [];
		foreach ( $location as $key => $item ) {
			if ( ! empty( $item['name'] ) ) {
				$str_location[] = $item['name'];
			}
		}
		$str_location = ! empty( $str_location ) ? implode( ' - ', $str_location ) : 'Error';
	} else {
		$str_location = '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
	}

	return $str_location;
}
