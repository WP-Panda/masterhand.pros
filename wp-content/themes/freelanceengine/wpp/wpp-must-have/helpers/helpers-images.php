<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


use ArtisansWeb\Optimizer;

if ( ! function_exists( 'wpp_fr_image_placeholder' ) ) :

	/**
	 * Get the placeholder image URL for products etc.
	 *
	 * @param array $key Image array.
	 *
	 * @return string
	 */
	function wpp_fr_image_placeholder( $key = null, $type = null ) {

		$images = apply_filters( 'wpp_fr_image_placeholder', [] );

		if ( ! empty( $images[ $key ] ) && ! empty( $images[ $key ] ) ) {
			$src = esc_url( $images[ $key ] );
		} else {
			$src = WPP_FR()->plugin_url() . '/assets/images/placeholder.jpg';
		}

		return $src;

	}

endif;


function wpp_fr_get_image_ful_data( $post_id = null ) {

	$out = false;
	if ( ! class_exists( 'WPP_Get_IMG' ) ) {
		return $out;
	}

	$post_id = ! empty( $post_id ) ? $post_id : null;

	$thumb_id = get_post_thumbnail_id( $post_id );

	if ( empty( $thumb_id ) ) {
		return $out;
	}

	return WPP_Get_IMG::get_img( $thumb_id, 'full' );

}


/**
 * Получение пачки картинок вместе
 *
 * @param $image
 * @param $params
 *
 * @return array
 */
function wpp_fr_image_bundle( $image, $params ) {


	$ext          = pathinfo( $image, PATHINFO_EXTENSION );
	$img          = [];
	$img['ext']   = 'jpeg' === $ext || 'jpg' === $ext ? 'jpeg' : $ext;
	$img['thumb'] = bfi_thumb( $image, $params );

	if ( ! empty( $img['thumb'] ) ) :
		foreach ( $img['thumb'] as $size => $crop_img ) {
			$img['webp'][ $size ] = WPP_Get_IMG::convert_to_webp( $crop_img );
		}

	endif;

	return $img;
}


/**
 * Получение пачки картинок вместе c улучшенй обработкой, ретиной, классами, вотермарками и прочими пирогами + тонкая настройка конвертации
 *
 * @param $image
 * @param $params
 *
 * @return array
 */
function n_wpp_fr_image_bundle( $image, $params ) {

	$img = [];

	$ext       = pathinfo( $image, PATHINFO_EXTENSION );
	$ext_final = 'jpeg' === $ext || 'jpg' === $ext ? 'jpeg' : $ext;


	if ( ! empty( $params['sizes'] ) ) :
		$i = 0;
		foreach ( $params['sizes'] as $one_img_params ) {

			$sizes = $sizes_r = [ 'width' => '', 'height' => '' ];

			if ( ! empty( $one_img_params['width'] ) ) {
				$sizes['width'] = $one_img_params['width'];
				if ( ! empty( $params['retina'] ) ) {
					$sizes_r['width'] = (int) $one_img_params['width'] * 2;
				}
			}

			if ( ! empty( $one_img_params['height'] ) ) {
				$sizes['height'] = $one_img_params['height'];
				if ( ! empty( $params['retina'] ) ) {
					$sizes_r['height'] = (int) $one_img_params['height'] * 2;
				}
			}


			$middle_key_part = ! empty( $sizes['width'] ) && ! empty( $sizes['height'] ) ? 'x' : '';
			$item_key        = $sizes['width'] . $middle_key_part . $sizes['height'];

			$sizes['water_mark'] = ! empty( $params['water_mark'] ) ? true : false;
			#$sizes['crop'] = ! empty( $params['crop'] ) ? true : false;
			$sizes['crop'] = true;

			$img[ $item_key ] = [
				'ext'   => $ext_final,
				'thumb' => [ 'norm' => bfi_thumb( $image, $sizes ) ]
			];

			if ( ! empty( $params['retina'] ) ) {
				$sizes_r['water_mark']            = ! empty( $params['water_mark'] ) ? true : false;
				$sizes_r['crop']                  = true;
				$img[ $item_key ]['thumb']['ret'] = bfi_thumb( $image, $sizes_r );
			}

			$img[ $item_key ]['webp']['norm'] = WPP_Get_IMG::convert_to_webp( $img[ $item_key ]['thumb']['norm'] );

			if ( ! empty( $img[ $item_key ]['thumb']['ret'] ) ) {
				$img[ $item_key ]['webp']['ret'] = WPP_Get_IMG::convert_to_webp( $img[ $item_key ]['thumb']['ret'] );
			}

			if ( ! empty( $params['sizes'][ $i ]['media'] ) ) {
				$img[ $item_key ]['media'] = $params['sizes'][ $i ]['media'];
			}

			if ( ! empty( $params['class'] ) ) {
				$img[ $item_key ]['class'] = $params['class'];
			}

			$i ++;
		}

	endif;

	return $img;
}

/**
 * Упрщенный вывод шаблона картинок ддля любого количества разрешений экрана
 *
 * @param $img_one
 * @param $params
 */
function e_wpp_fr_image_html( $img_one, $params, $return = false ) {

	$img_nn = n_wpp_fr_image_bundle( $img_one, $params );

	if ( ! isset( $params['lazy'] ) || $params['lazy'] !== false ) {
		$source_code  = '<source type="image/%s" data-srcset="%s"%s>';
		$picture_code = '<picture class="%s">%s<img itemprop="image" data-src="%s?v=%s" alt="" class="lazy %s"></picture>';
	} else {
		$source_code  = '<source type="image/%s" srcset="%s"%s>';
		$picture_code = '<picture class="%s">%s<img itemprop="image" src="%s?v=%s" alt="" class="%s"></picture>';
	}


	$retina_srcset = '%s?v=%s 1x, %s?v=%s 2x';
	$source_data   = $out = '';
	$formats       = [
		'webp',
		'thumb'
	];


	if ( ! empty( $img_nn ) ) :
		foreach ( $img_nn as $img_size => $one_img_new ) :

			foreach ( $formats as $format ) {
				if ( ! empty( $one_img_new[ $format ] ) ) :
					$srcset      = ! empty( $one_img_new[ $format ]['ret'] ) ? sprintf( $retina_srcset, $one_img_new[ $format ]['norm'], BRABUS_VER, $one_img_new[ $format ]['ret'], BRABUS_VER ) : $one_img_new[ $format ]['norm'] . '?v=' . BRABUS_VER;
					$source_data .= sprintf( $source_code,
						'webp' === $format ? $format : $one_img_new['ext'],
						$srcset,
						! empty( $one_img_new['media'] ) ? sprintf( ' media="%s"', $one_img_new['media'] ) : ''
					);
				endif;
			}
		endforeach;
	endif;

	if ( ! empty( $source_data ) ) {
		$class = ! empty( $params['picture_class'] ) ? ' ' . $params['picture_class'] : '';
		/**if ( wpp_fr_user_is_admin() ) :
		 * $admin_edit_tmp = <<<ADMIN
		 * <span class="wpp-fr-chamge-image wpp-tooltips" data-title="Регенерировать картинку" data-image='%s' >
		 * <img src="%s/wp-content/themes/wpp-brabus/assets/img/icons/rechange.svg" alt="">
		 * </span>
		 * ADMIN;
		 * $source_data    .= sprintf( $admin_edit_tmp, json_encode( $params ), get_home_url() );
		 * endif;
		 */
		$one_img_data = array_shift( $img_nn );
		$out          = sprintf( $picture_code, $class, $source_data, $one_img_data['thumb']['norm'], BRABUS_VER, ! empty( $one_img_data['class'] ) ? sprintf( ' %s', $one_img_data['class'] ) : '' );
	}


	if ( ! empty( $params['wrap'] ) ) {
		$out = sprintf( $params['wrap'], $out );
	}

	if ( empty( $return ) ) {
		echo $out;
	} else {
		return $out;
	}


}

function wpp_ft_optim( $file ) {
	$img = new Optimizer();
	$img->optimize( $file );
}