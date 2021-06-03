<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	if ( ! function_exists( 'wpp_get_post_images' ) ) :

		/**
		 * Получение всех изображений записи для слайдкра
		 */
		function wpp_get_post_images() {

			global $post;

			$images = [];

			if ( has_post_thumbnail() ) {
				$images[] = get_the_post_thumbnail_url( $post->ID );
			}

			$gallery = wpp_get_post_gallery_urls();

			if ( ! empty( $gallery ) ) {
				$images = array_merge( $images, $gallery );
			}

			return $images;

		}

	endif;