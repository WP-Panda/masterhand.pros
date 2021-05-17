<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes\Redirect\Presenters
 */

/**
 * Class WPSEO_Redirect_Settings_Presenter
 */
class WPSEO_Redirect_Settings_Presenter extends WPSEO_Redirect_Tab_Presenter {

	/**
	 * Extending the view vars with pre settings key
	 *
	 * @param array $passed_vars Optional. View data manually passed. Default empty array.
	 *
	 * @return array Contextual variables to pass to the view.
	 */
	protected function get_view_vars( array $passed_vars = [] ) {
		return array_merge(
			$passed_vars,
			[
				'file_path'     => WPSEO_Redirect_File_Util::get_file_path(),
				'redirect_file' => $this->writable_redirect_file(),
			]
		);
	}

	/**
	 * Check if it is possible to write to the files
	 *
	 * @return false|string
	 */
	private function writable_redirect_file() {
		if ( WPSEO_Options::get( 'disable_php_redirect' ) !== 'on' ) {
			return false;
		}

		// Do file checks.
		$file_exists = file_exists( WPSEO_Redirect_File_Util::get_file_path() );

		if ( WPSEO_Utils::is_apache() ) {
			$separate_file = ( WPSEO_Options::get( 'separate_file' ) === 'on' );

			if ( $separate_file && $file_exists ) {
				return 'apache_include_file';
			}

			if ( ! $separate_file ) {
				// Everything is as expected.
				if ( is_writable( WPSEO_Redirect_Htaccess_Util::get_htaccess_file_path() ) ) {
					return false;
				}
			}

			return 'cannot_write_htaccess';
		}

		if ( WPSEO_Utils::is_nginx() ) {
			if ( $file_exists ) {
				return 'nginx_include_file';
			}

			return 'cannot_write_file';
		}
	}
}
