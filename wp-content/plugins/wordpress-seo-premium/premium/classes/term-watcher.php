<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes
 */

/**
 * Class WPSEO_Term_Watcher.
 */
class WPSEO_Term_Watcher extends WPSEO_Watcher implements WPSEO_WordPress_Integration {

	/**
	 * Type of watcher.
	 *
	 * This will be used for the filters.
	 *
	 * @var string
	 */
	protected $watch_type = 'term';

	/**
	 * Used when the slug is changed using quick edit.
	 *
	 * @var string
	 */
	protected $old_url = '';

	/**
	 * Constructing the object.
	 *
	 * @codeCoverageIgnore Method relies on dependencies.
	 *
	 * @return void
	 */
	public function register_hooks() {
		global $pagenow;

		// Only set the hooks for the page where they are needed.
		if ( ! $this->term_redirect_can_be_made( $pagenow ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'page_scripts' ] );

		// Get all taxonomies.
		$taxonomies = get_taxonomies();

		// Loop through all taxonomies.
		if ( count( $taxonomies ) > 0 ) {
			foreach ( $taxonomies as $taxonomy ) {
				// Add old URL field to term edit screen.
				add_action( $taxonomy . '_edit_form_fields', [ $this, 'old_url_field' ], 10, 2 );
			}
		}

		add_action( 'wp_ajax_inline-save-tax', [ $this, 'set_old_url_quick_edit' ], 1 );

		// Detect the term slug change.
		add_action( 'edited_term', [ $this, 'detect_slug_change' ], 10, 3 );

		// Detect a term delete.
		add_action( 'delete_term_taxonomy', [ $this, 'detect_term_delete' ] );
	}

	/**
	 * Registers the page scripts.
	 *
	 * @param string $current_page The page that is opened at the moment.
	 *
	 * @return void
	 */
	public function page_scripts( $current_page ) {
		if ( ! $this->term_redirect_can_be_made( $current_page ) ) {
			return;
		}

		parent::page_scripts( $current_page );

		if ( $current_page === 'edit-tags.php' ) {
			wp_enqueue_script( 'wp-seo-premium-quickedit-notification' );
		}
		if ( $current_page === 'term.php' ) {
			wp_enqueue_script( 'wp-seo-premium-redirect-notifications' );
		}
	}

	/**
	 * Add an extra field to term edit screen.
	 *
	 * @param string $tag      The current tag name.
	 * @param string $taxonomy The name of the current taxonomy.
	 */
	public function old_url_field( $tag, $taxonomy ) {
		$url = $this->get_target_url( $tag, $taxonomy );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Correctly escaped in parse_url_field() method.
		echo $this->parse_url_field( $url, 'term' );
	}

	/**
	 * Set old URL when the quick edit is used for taxonomies.
	 */
	public function set_old_url_quick_edit() {
		$permalink = $this->get_taxonomy_permalink();

		if ( ! is_wp_error( $permalink ) ) {
			$this->old_url = str_replace( home_url(), '', $permalink );
		}
	}

	/**
	 * Detect if the slug changed, hooked into 'post_updated'.
	 *
	 * @param int      $term_id  The term id.
	 * @param int      $tt_id    The term taxonomy id.
	 * @param stdClass $taxonomy Object with the values of the taxonomy.
	 *
	 * @return bool
	 */
	public function detect_slug_change( $term_id, $tt_id, $taxonomy ) {
		/**
		 * Filter: 'wpseo_premium_term_redirect_slug_change' - Check if a redirect should be created
		 * on term slug change.
		 *
		 * @deprecated 12.9.0. Use the {@see 'Yoast\WP\SEO\term_redirect_slug_change'} filter instead.
		 *
		 * @api bool unsigned
		 */
		$create_redirect = apply_filters_deprecated(
			'wpseo_premium_term_redirect_slug_change',
			[ false ],
			'YoastSEO Premium 12.9.0',
			'Yoast\WP\SEO\term_redirect_slug_change'
		);

		/**
		 * Filter: 'Yoast\WP\SEO\term_redirect_slug_change' - Check if a redirect should be created
		 * on term slug change.
		 *
		 * Note: This is a Premium plugin-only hook.
		 *
		 * @since 12.9.0
		 *
		 * @api bool unsigned
		 */
		if ( apply_filters( 'Yoast\WP\SEO\term_redirect_slug_change', $create_redirect ) === true ) {
			return true;
		}

		/**
		 * Certain plugins use multisite context switching when saving terms. This can lead to incorrect redirects being
		 * created.
		 *
		 * See https://github.com/Yoast/bugreports/issues/437.
		 */
		if ( is_multisite() && ms_is_switched() ) {
			return false;
		}

		$old_url = $this->get_old_url();

		if ( ! $old_url ) {
			return false;
		}

		// Get the new URL.
		$new_url = $this->get_target_url( $term_id, $taxonomy );

		// Maybe we can undo the created redirect.
		$this->notify_undo_slug_redirect( $old_url, $new_url );
	}

	/**
	 * Offer to create a redirect from the term that is about to get deleted.
	 *
	 * @param int $term_id The term id that will be deleted.
	 */
	public function detect_term_delete( $term_id ) {
		// When term is a menu don't show the redirect creation notice.
		if ( is_nav_menu( $term_id ) ) {
			return;
		}

		global $wpdb;

		// Get the term and taxonomy from the term_taxonomy table.
		$term_row = $wpdb->get_row( $wpdb->prepare( 'SELECT `term_id`, `taxonomy` FROM `' . $wpdb->term_taxonomy . '` WHERE `term_taxonomy_id` = %d ', $term_id ) );

		// Check result.
		if ( $term_row !== null ) {

			// Get the URL.
			$url = $this->get_target_url( get_term( $term_row->term_id, $term_row->taxonomy ), $term_row->taxonomy );

			$this->set_delete_notification( $url );
		}
	}

	/**
	 * Parses the hidden field with the old URL to show in the form.
	 *
	 * @param string $url  The old URL.
	 * @param string $type The type of the URL.
	 *
	 * @return string The parsed hidden input field.
	 */
	protected function parse_url_field( $url, $type ) {

		// Output the hidden field.
		return '<input type="hidden" name="' . esc_attr( 'wpseo_old_' . $type . '_url' ) . '" value="' . esc_attr( $url ) . '"/>';
	}

	/**
	 * Get the URL to the term and returns it's path.
	 *
	 * @param string $tag      The current tag name.
	 * @param string $taxonomy The name of the current taxonomy.
	 *
	 * @return string
	 */
	protected function get_target_url( $tag, $taxonomy ) {
		// Use the correct URL path.
		$url = wp_parse_url( get_term_link( $tag, $taxonomy ) );
		if ( is_array( $url ) && isset( $url['path'] ) ) {
			return $url['path'];
		}

		return '';
	}

	/**
	 * Get permalink for taxonomy.
	 *
	 * @return string|WP_Error
	 */
	protected function get_taxonomy_permalink() {
		return get_term_link( get_term( filter_input( INPUT_POST, 'tax_ID' ), filter_input( INPUT_POST, 'taxonomy' ) ), filter_input( INPUT_POST, 'taxonomy' ) );
	}

	/**
	 * Get the old URL.
	 *
	 * @return bool|string
	 */
	protected function get_old_url() {
		$wpseo_old_term_url = filter_input( INPUT_POST, 'wpseo_old_term_url' );

		if ( empty( $wpseo_old_term_url ) ) {
			if ( ! empty( $this->old_url ) ) {
				return $this->old_url;
			}

			return false;
		}
		return $wpseo_old_term_url;
	}

	/**
	 * Returns the undo message for the term.
	 *
	 * @return string
	 */
	protected function get_undo_slug_notification() {
		/* translators: %1$s: Yoast SEO Premium, %2$s and %3$s expand to a link to the admin page. */
		return __(
			'%1$s created a %2$sredirect%3$s from the old term URL to the new term URL.',
			'wordpress-seo-premium'
		);
	}

	/**
	 * Returns the delete message for the term.
	 *
	 * @return string
	 */
	protected function get_delete_notification() {
		/* translators: %1$s: Yoast SEO Premium, %2$s: List with actions, %3$s: <a href='{post_with_explaination.}'>, %4$s: </a>, %5%s: The removed url. */
		return __(
			'%1$s detected that you deleted a term (%5$s). You can either: %2$s Don\'t know what to do? %3$sRead this post %4$s.',
			'wordpress-seo-premium'
		);
	}

	/**
	 * Is the current page valid to make a redirect from.
	 *
	 * @param string $current_page The currently opened page.
	 *
	 * @return bool True when a redirect can be made on this page.
	 */
	protected function term_redirect_can_be_made( $current_page ) {
		return $this->is_term_page( $current_page ) || $this->is_action_inline_save_tax() || $this->is_action_delete_tag();
	}

	/**
	 * Is the current page related to a term (edit/overview).
	 *
	 * @param string $current_page The current opened page.
	 *
	 * @return bool True when page is a term edit/overview page.
	 */
	protected function is_term_page( $current_page ) {
		return ( in_array( $current_page, [ 'edit-tags.php', 'term.php' ], true ) );
	}

	/**
	 * Is the page in an AJAX-request and is the action "inline save".
	 *
	 * @return bool True when in an AJAX-request and the action is inline-save.
	 */
	protected function is_action_inline_save_tax() {
		return ( wp_doing_ajax() && filter_input( INPUT_POST, 'action' ) === 'inline-save-tax' );
	}

	/**
	 * Is the page in an AJAX-request and is the action "delete-tag".
	 *
	 * @return bool True when in an AJAX-request and the action is delete-tag.
	 */
	protected function is_action_delete_tag() {
		return ( wp_doing_ajax() && filter_input( INPUT_POST, 'action' ) === 'delete-tag' );
	}
}
