<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Including WordPress Filesystem API
 */
include_once ABSPATH . '/wp-admin/includes/file.php';
if ( function_exists( 'WP_Filesystem' ) ) {
	WP_Filesystem();
}

if ( class_exists( 'WP_Filesystem_Direct' ) ) {
	class Xl_File_Api extends WP_Filesystem_Direct {
		private $upload_dir;
		private static $ins = null;
		private $core_dir = 'xlplugins';
		private $component = '';

		public function __construct( $component ) {
			$upload            = wp_upload_dir();
			$this->upload_dir  = $upload['basedir'];
			$this->xl_core_dir = $this->upload_dir . '/' . $this->core_dir;
			$this->set_component( $component );

			$this->makedirs();
			self::$ins = 1;

			$folder_arr = array(
				'xl_support',
				'finale-woocommerce-sales-countdown-timer-discount',
				'woo-thank-you-page-nextmove-lite',
				'xl-woocommerce-sales-triggers',
				'finale-woocommerce-sales-countdown-timer-discount-plugin',
				'wcct-finale-lite',
			);
			foreach ( $folder_arr as $folder_name ) {
				$folder_path = $this->upload_dir . '/' . $folder_name;
				$this->delete_folder( $folder_path, true );
			}

		}


		public function set_component( $component ) {
			if ( '' != $component ) {
				$this->component = $component;
			}
		}

		public function get_component_dir() {
			return $this->xl_core_dir . '/' . $this->component;
		}

		public function touch( $file, $time = 0, $atime = 0 ) {
			$file = $this->file_path( $file );

			return parent::touch( $file, $time, $atime );
		}

		public function file_path( $file ) {
			$file_path = $this->xl_core_dir . '/' . $this->component . '/' . $file;

			return $file_path;
		}

		public function folder_path( $folder_name ) {
			$folder_path = $this->xl_core_dir . '/' . $folder_name . '/';

			return $folder_path;
		}

		public function is_readable( $file ) {
			$file = $this->file_path( $file );

			return parent::is_readable( $file );
		}

		public function is_writable( $file ) {
			$file = $this->file_path( $file );

			return parent::is_writable( $file );
		}

		public function put_contents( $file, $contents, $mode = false ) {
			$file = $this->file_path( $file );

			return parent::put_contents( $file, $contents, $mode );
		}

		public function delete_file( $file, $recursive = false, $type = false ) {
			$file = $this->file_path( $file );

			return parent::delete( $file, $recursive, $type );
		}

		public function delete_all( $folder_name, $recursive = false ) {
			$folder_path = $this->folder_path( $folder_name );

			return parent::rmdir( $folder_path, $recursive );
		}

		public function delete_folder( $folder_path, $recursive = false ) {

			return parent::rmdir( $folder_path, $recursive );
		}

		public function exists( $file ) {
			$file = $this->file_path( $file );

			return parent::exists( $file );
		}

		public function get_contents( $file ) {
			$file = $this->file_path( $file );

			return parent::get_contents( $file );
		}

		public function makedirs() {
			$component = $this->component;

			if ( parent::is_writable( $this->upload_dir ) ) {
				if ( false === $this->is_dir( $this->xl_core_dir ) ) {
					$this->mkdir( $this->xl_core_dir );
				}

				/** creating htaccess file */
				if ( false === $this->is_file( trailingslashit( $this->xl_core_dir ) . '/.htaccess' ) ) {
					$file_handle = @fopen( trailingslashit( $this->xl_core_dir ) . '/.htaccess', 'w' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
					if ( $file_handle ) {
						$string = "Options -Indexes\ndeny from all\n<FilesMatch '\.(txt)$'>\nOrder Allow,Deny\nAllow from all\n</FilesMatch>";
						fwrite( $file_handle, $string ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
						fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					}
				}

				$dir = $this->xl_core_dir . '/' . $component;
				if ( false === $this->is_dir( $dir ) ) {
					$this->mkdir( $dir );
				}
			}
		}
	}
}
