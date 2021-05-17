<?php

class MailsterHelpmenu {

	public function __construct() {

	}


	public function init() {

		$screen = get_current_screen();

		require_once MAILSTER_DIR . 'includes/helppages.php';

		if ( isset( $pages[ $screen->id ] ) ) {

			if ( isset( $pages[ $screen->id ]['tabs'] ) ) {
				foreach ( $pages[ $screen->id ]['tabs'] as $tab ) {

					if ( ! isset( $tab['content'] ) ) {
						ob_start();

						if ( method_exists( $this, 'page_' . $screen->id ) ) {
							call_user_func( array( $this, 'page_' . $screen->id ) );
						}

						$content = ob_get_contents();

						ob_end_clean();

					} else {
						$content = $tab['content'];
					}

					$screen->add_help_tab(
						array(
							'id'      => 'tab' . uniqid(),
							'title'   => $tab['title'],
							'content' => $content,
						)
					);
				}
			}

			if ( isset( $pages[ $screen->id ]['sidebar'] ) ) {
				$screen->set_help_sidebar( $pages[ $screen->id ]['sidebar'] );
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $func
	 * @param unknown $args
	 */
	public function __call( $func, $args ) {

	}


	private function page_newsletter_page_mailster_dashboard() {

		?>
		<?php

	}


}
