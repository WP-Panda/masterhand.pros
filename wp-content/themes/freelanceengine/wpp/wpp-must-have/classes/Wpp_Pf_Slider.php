<?php
/**
 * Слайдер но навеное не надо
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wpp_Pf_Slider {

	public static function init() {

	}

	/**
	 * slider Query
	 *
	 * @param $option_name
	 *
	 * @return WP_Query
	 */
	public static function slider_query( $option_name ) {
		$slider = wpp_bt_get_option( $option_name );

		$query = new WP_Query(
			array(
				'post_type' => 'wpp_slide_image',
				'nopaging'  => true,
				'tax_query' => array(
					array(
						'taxonomy' => 'wpp_slider',
						'field'    => 'id',
						'terms'    => array( (int) $slider )
					)
				)
			)
		);

		return $query;
	}

}