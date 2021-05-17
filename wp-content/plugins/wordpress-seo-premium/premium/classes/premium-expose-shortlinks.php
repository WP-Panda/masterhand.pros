<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium
 */

/**
 * Exposes shortlinks to wpseoAdminL10n.
 */
class WPSEO_Premium_Expose_Shortlinks implements WPSEO_WordPress_Integration {

	/**
	 * Registers all hooks to WordPress
	 */
	public function register_hooks() {
		add_filter( 'wpseo_admin_l10n', [ $this, 'expose_shortlinks' ] );
	}

	/**
	 * Filter that adds the keyword synonyms shortlink to the localization object.
	 *
	 * @param array $input Admin localization object.
	 *
	 * @return array Admin localization object.
	 */
	public function expose_shortlinks( $input ) {
		$input['shortlinks.keyword_synonyms_info'] = WPSEO_Shortlinker::get( 'https://yoa.st/kd1' );

		return $input;
	}
}
