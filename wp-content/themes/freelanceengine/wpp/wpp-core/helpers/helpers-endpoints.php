<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'if_is_wpp_fr_endpoint' ) ) :

	/**
	 * Проверка текущщей точеки
	 *
	 * @param array $point_name
	 *
	 * @return bool
	 */
	function if_is_wpp_fr_endpoint( $point_name = array() ) {
		$point = Wpp_Pf_Endpoints::get_current_endpoint();

		if ( empty( $point ) ) {
			return false;
		} else if ( ! empty( $point_name ) && in_array( $point, $point_name ) ) {
			return true;
		} else if ( ! empty( $point_name ) && ! in_array( $point, $point_name ) ) {
			return false;
		} else {
			return true;
		}

	}

endif;