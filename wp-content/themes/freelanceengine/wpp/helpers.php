<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Проверка расширения файла
	 *
	 * @param       $file
	 * @param array $exts
	 *
	 * @return bool
	 */
	function wpp_validate_file_ext( $file, $exts = [] ) {

		$ext = pathinfo( $file, PATHINFO_EXTENSION );

		return in_array( $ext, $exts );

	}