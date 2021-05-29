<?php

/**
 * Создание дирректории, вставка taccets и индекса
 *
 * @param $path
 */
function wpp_fr_create_dir( $path ) {
	// check if tmp folder exists => if not, initialize
	if ( ! @is_dir( $path ) ) {
		@mkdir( $path );
		// create .htaccess file and empty index.php to protect in case an open webfolder is used!
		@file_put_contents( $path . '.htaccess', 'deny from all' );
		@touch( $path . 'index.php' );
	}
}

/**
 * Получение папки В аплоадс дир
 */
function wpp_fr_get_tmp_base() {

	// wp_upload_dir() is used to set the base temp folder, under which a
	// 'wpo_wcpdf' folder and several subfolders are created
	//
	// wp_upload_dir() will:
	// * default to WP_CONTENT_DIR/uploads
	// * UNLESS the ‘UPLOADS’ constant is defined in wp-config (http://codex.wordpress.org/Editing_wp-config.php#Moving_uploads_folder)
	//
	// May also be overridden by the wpo_wcpdf_tmp_path filter

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {

		$tmp_base = false;

	} else {

		$upload_base = trailingslashit( $upload_dir['basedir'] );
		$tmp_base    = $upload_base . 'wpp-fr/';

	}

	$tmp_base = apply_filters( 'wpp_fr_tmp_path', $tmp_base );
	if ( $tmp_base !== false ) {
		$tmp_base = trailingslashit( $tmp_base );
	}

	return $tmp_base;
}


/**
 * оздание необходимых папок
 *
 * @param $tmp_base - основная папка
 * @param $subfolders - вложенные папки
 */
function wpp_fr_init_tmp( $tmp_base, $subfolders = null ) {

	wpp_fr_create_dir( $tmp_base );

	if ( ! empty( $subfolders ) && is_array( $subfolders ) ) {
		foreach ( $subfolders as $subfolder ) {

			$path = $tmp_base . $subfolder . '/';

			wpp_fr_create_dir( $path );

			// copy font files
			/*	if ( $subfolder === 'fonts' ) {
					wpp_fr_pdf_copy_fonts( $path, false );
				}*/


		}
	}

}

/**
 * Подключение файлов через массив
 *
 * @param $files
 */

function wpp_fr_require_files( $files ) {

	if ( ! empty( $files ) && is_array( $files ) ) :

		foreach ( $files as $file ) {

			if ( file_exists( $file ) ) {
				require_once $file;
			}

		}

	endif;

}


function wpp_fr_file_class( $path, $pref = null ) {
	return $pref . basename( $path, '.php' );
}

function e_wpp_fr_file_class( $path, $pref = null ) {
	echo wpp_fr_file_class( $path, $pref );
}