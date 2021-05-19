<?php
defined( 'ABSPATH' ) || exit;

/**
 * this add logs in txt file inside WP uploads folder
 *
 * @param string $string
 * @param type $filename
 *
 * @return boolean
 */
function xlwuev_force_log( $string, $filename = 'force.txt', $mode = 'a' ) {

	if ( empty( $string ) ) {
		return false;
	}

	if ( ( XlWUEV_Common::$is_force_debug === true ) || ( WP_DEBUG === true && ! is_admin() ) ) {

		$current_date_obj = new DateTime( 'now', new DateTimeZone( XlWUEV_Common::wc_timezone_string() ) );

		$upload_dir = wp_upload_dir();
		$base_path  = $upload_dir['basedir'] . '/xlwuev-errors/';

		if ( ! file_exists( $base_path ) ) {
			mkdir( $base_path, 0777, true );
		}

		$file_path = $base_path . '/' . $filename;
		$file      = fopen( $file_path, $mode );
		$curTime   = $current_date_obj->format( 'M d, Y H.i.s' ) . ': ';
		$string    = "\r\n" . $curTime . $string;
		fwrite( $file, $string );
		fclose( $file );

		return true;
	}

}
