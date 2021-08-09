<?php
	/**
	 * темплэйт для не залогиненого юзера
	 */

	get_header();
	/**
	 * @hooked wpp_fr_not_logged_template_message - 10
	 */
	do_action( 'wpp_fr_not_logged_template_content' );
	get_footer();