<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes\Redirect\Exporters
 */

/**
 * Exporter for Nginx, only declares the two formats
 */
class WPSEO_Redirect_Nginx_Exporter extends WPSEO_Redirect_File_Exporter {

	/**
	 * %1$s is the origin
	 * %2$s is the target
	 * %3$s is the redirect type
	 * %4$s is the optional x-redirect-by filter.
	 *
	 * @var string
	 */
	protected $url_format = 'location /%1$s { %4$s return %3$s %2$s; }';

	/**
	 * %1$s is the origin
	 * %2$s is the target
	 * %3$s is the redirect type
	 * %4$s is the optional x-redirect-by filter.
	 *
	 * @var string
	 */
	protected $regex_format = 'location ~ %1$s { %4$s return %3$s %2$s; }';

	/**
	 * Formats a redirect for use in the export.
	 *
	 * @param WPSEO_Redirect $redirect The redirect to format.
	 *
	 * @return string
	 */
	public function format( WPSEO_Redirect $redirect ) {
		return sprintf(
			$this->get_format( $redirect->get_format() ),
			$redirect->get_origin(),
			$redirect->get_target(),
			$redirect->get_type(),
			$this->add_x_redirect_header()
		);
	}

	/**
	 * Adds an X-Redirect-By header if allowed by the filter.
	 *
	 * @return string
	 */
	private function add_x_redirect_header() {
		/**
		 * Filter: 'wpseo_add_x_redirect' - can be used to remove the X-Redirect-By header
		 * Yoast SEO Premium creates (defaults to true, which is adding it)
		 *
		 * @deprecated 12.9.0. Use the {@see 'Yoast\WP\SEO\add_x_redirect'} filter instead.
		 *
		 * @api bool
		 */
		$add_x_redirect = apply_filters_deprecated(
			'wpseo_add_x_redirect',
			[ true ],
			'YoastSEO Premium 12.9.0',
			'Yoast\WP\SEO\add_x_redirect'
		);

		/**
		 * Filter: 'Yoast\WP\SEO\add_x_redirect' - can be used to remove the X-Redirect-By header
		 * Yoast SEO Premium creates (defaults to true, which is adding it)
		 *
		 * Note: This is a Premium plugin-only hook.
		 *
		 * @since 12.9.0
		 *
		 * @api bool
		 */
		if ( apply_filters( 'Yoast\WP\SEO\add_x_redirect', $add_x_redirect ) === true ) {
			return 'add_header X-Redirect-By "Yoast SEO Premium";';
		}

		return '';
	}
}
