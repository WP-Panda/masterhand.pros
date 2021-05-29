<?php
	/**
	 * Функции для работы с PDF
	 */

	if ( !function_exists( 'wpp_fr_copy_fonts' ) ) :

		/**
		 * Копирование шрифтов
		 */
		function wpp_fr_pdf_copy_fonts( $path, $merge_with_local = true ) {

			$path = trailingslashit( $path );

			$dompdf_font_dir = WPP_ABSPATH . "libs/for-pdf/dompdf/dompdf/lib/fonts/";

			#wpp_d_log( $dompdf_font_dir );
			// get local font dir from filtered options
			$dompdf_options = apply_filters( 'wpo_wcpdf_dompdf_options', [
				'defaultFont'             => 'dejavu sans',
				'tempDir'                 => wpp_fr_pdf_get_tmp_path( 'dompdf' ),
				'logOutputFile'           => wpp_fr_pdf_get_tmp_path( 'dompdf' ) . "/log.htm",
				'fontDir'                 => wpp_fr_pdf_get_tmp_path( 'fonts' ),
				'fontCache'               => wpp_fr_pdf_get_tmp_path( 'fonts' ),
				'isRemoteEnabled'         => true,
				'isFontSubsettingEnabled' => true,
				'isHtml5ParserEnabled'    => true,
			] );

			$fontDir = $dompdf_options[ 'fontDir' ];

			// merge font family cache with local/custom if present
			$font_cache_files = [
				'cache'      => 'dompdf_font_family_cache.php',
				'cache_dist' => 'dompdf_font_family_cache.dist.php',
			];

			foreach ( $font_cache_files as $font_cache_name => $font_cache_filename ) {

				$plugin_fonts = @require $dompdf_font_dir . $font_cache_filename;

				if ( $merge_with_local && is_readable( $path . $font_cache_filename ) ) {

					$local_fonts = @require $path . $font_cache_filename;

					if ( is_array( $local_fonts ) && is_array( $plugin_fonts ) ) {
						// merge local & plugin fonts, plugin fonts overwrite (update) local fonts
						// while custom local fonts are retained
						$local_fonts = array_merge( $local_fonts, $plugin_fonts );
						// create readable array with $fontDir in place of the actual folder for portability
						$fonts_export = var_export( $local_fonts, true );
						$fonts_export = str_replace( '\'' . $fontDir, '$fontDir . \'', $fonts_export );
						$cacheData = sprintf( "<?php return %s;%s?>", $fonts_export, PHP_EOL );
						// write file with merged cache data
						file_put_contents( $path . $font_cache_filename, $cacheData );

					} else { // empty local file

						copy( $dompdf_font_dir . $font_cache_filename, $path . $font_cache_filename );

					}

				} else {
					// we couldn't read the local font cache file so we're simply copying over plugin cache file
					copy( $dompdf_font_dir . $font_cache_filename, $path . $font_cache_filename );
				}

			}

			// first try the easy way with glob!
			if ( function_exists( 'glob' ) ) {
				$files = glob( $dompdf_font_dir . "*.*" );
				foreach ( $files as $file ) {
					$filename = basename( $file );
					if ( !is_dir( $file ) && is_readable( $file ) && !in_array( $filename, $font_cache_files ) ) {
						$dest = $path . $filename;
						copy( $file, $dest );
					}
				}
			} else {
				// fallback method using font cache file (glob is disabled on some servers with disable_functions)
				$extensions = [
					'.ttf',
					'.ufm',
					'.ufm.php',
					'.afm',
					'.afm.php'
				];

				$fontDir = untrailingslashit( $dompdf_font_dir );
				$plugin_fonts = @require $dompdf_font_dir . $font_cache_files[ 'cache' ];

				foreach ( $plugin_fonts as $font_family => $filenames ) {
					foreach ( $filenames as $filename ) {
						foreach ( $extensions as $extension ) {
							$file = $filename . $extension;
							if ( file_exists( $file ) ) {
								$dest = $path . basename( $file );
								copy( $file, $dest );
							}
						}
					}
				}

			}
		}

	endif;

	function wpp_fr_pdf_get_tmp_path( $type = '' ) {
		$tmp_base = wpp_fr_get_tmp_base();

		// don't continue if we don't have an upload dir
		if ( $tmp_base === false ) {
			return false;
		}

		$subfolders = [
			'attachments',
			'fonts',
			'dompdf'
		];


		wpp_fr_init_tmp( $tmp_base, $subfolders );

		if ( empty( $type ) ) {
			return $tmp_base;
		}

		switch ( $type ) {
			case 'dompdf':
				$tmp_path = $tmp_base . 'dompdf';
				break;
			case 'font_cache':
			case 'fonts':
				$tmp_path = $tmp_base . 'fonts';
				break;
			case 'attachments':
				$tmp_path = $tmp_base . 'attachments/';
				break;
			default:
				$tmp_path = $tmp_base . $type;
				break;
		}

		// double check for existence, in case tmp_base was installed, but subfolder not created
		wpp_fr_create_dir( $tmp_path );

		return $tmp_path;
	}