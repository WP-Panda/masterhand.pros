<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 28.08.2019
 * Time: 17:10
 */

/**
 * Проверка источника видео
 *
 * @param $url
 *
 * @return string
 */
function wpp_fr_get_ext_video_host( $url ) {

	if ( strpos( $url, 'youtube' ) > 0 ) {
		return 'youtube';
	} elseif ( strpos( $url, 'vimeo' ) > 0 ) {
		return 'vimeo';
	} else {
		return 'unknown';
	}

}

/**
 * проверка наличия видео и получение основных данных
 *
 * @param $url
 * @param string $return
 *
 * @return array|bool|mixed|object
 */
function wpp_fr_get_youtube_video_data( $url, $return = 'check' ) {

	$link    = sprintf( 'https://www.youtube.com/oembed?url=%s', $url );
	$headers = get_headers( $link );

	if ( ! strpos( $headers[0], '200' ) ) {
		return false;
	}

	if ( $return === 'check' ) {
		return true;
	} elseif ( $return === 'data' ) {
		$data = file_get_contents( $link . '&type=json' );

		if ( ! empty( $data ) ) {
			return json_decode( $data );
		}
	}
}

/**
 * Получение id видео с youtube
 *
 * @param $url
 *
 * @return string|bool
 */
function wpp_fr_get_youtube_video_id( $url ) {

	$video_id = explode( "?v=", $url ); // For videos like http://www.youtube.com/watch?v=...

	if ( empty( $video_id[1] ) ) {
		$video_id = explode( "/v/", $url );
	} // For videos like http://www.youtube.com/watch/v/..

	$video_id = explode( "&", $video_id[1] ); // Deleting any other params
	$video_id = $video_id[0];

	return ! empty( $video_id ) ? $video_id : false;
}


/**
 * Получение миниатюр видео с ютуба
 *
 * @param $id
 * @param string $size
 *
 * @return string
 */
function wpp_fr_get_youtube_video_thumb( $id, $size = 'sddefault' ) {
	$sizes = [ 'default', 'hqdefault', 'mqdefault', 'sddefault', 'maxresdefault' ];

	if ( ! in_array( $size, $sizes ) ) {
		$size = 'sddefault';
	}

	$url = sprintf( 'https://img.youtube.com/vi/%s/%s.jpg', $id, $size );

	return $url;
}