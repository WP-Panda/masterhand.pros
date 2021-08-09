<?php
/**
 * @package    WPP_Core
 * @subpackage Gallery Images
 * @author     WP_Panda
 * @version    2.0.0
 *
 * Функции для упрощения получения изображений галереи поста
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpp_get_post_gallery_ids' ) ) :

	/**
	 * Получение ID зображений галереи
	 */
	function wpp_get_post_gallery_ids( $post_id = null ) {

		if ( empty( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		}

		$attachments_ids_sting = get_post_meta( $post_id, '_wpp_post_gallery', true );

		if ( empty( $attachments_ids_sting ) ) {
			return false;
		}


		$attachments = explode( ',', $attachments_ids_sting );

		return $attachments;

	}

endif;

if ( ! function_exists( 'wpp_get_post_gallery_urls' ) ) :

	/**
	 * Получение полных урлов изображений галереи
	 */
	function wpp_get_post_gallery_urls( $post_id = null ) {

		$attachments = wpp_get_post_gallery_ids( $post_id ?? false );


		if ( ! empty( $attachments ) ) {
			$images = [];
			foreach ( $attachments as $attachment ) {
				$images[] = wp_get_attachment_image_src( $attachment, 'full' )[0];
			}

			return $images;

		} else {
			return false;
		}
	}

endif;