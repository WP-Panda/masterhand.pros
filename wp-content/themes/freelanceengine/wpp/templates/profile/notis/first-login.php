<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	extract( $args );
	if ( empty( $profile_id ) ):

		if ( fre_share_role() || ae_user_role( $user_ID ) == FREELANCER ) {
			$text = __( 'Paypal account and Profile completion are required to bid on projects and make deals. Please go to Settings to complete your profile.', WPP_TEXT_DOMAIN );
		} elseif ( fre_share_role() || ae_user_role( $user_ID ) == EMPLOYER ) {
			$text = __( 'Paypal account and Profile completion are recommended to make SafePay deals and receive money(refunds). Please go to Settings to complete your profile.', WPP_TEXT_DOMAIN );
		} else {
			$text = false;
		}

	endif;

	if ( ! empty( $text ) ) {
		printf( '<div class="notice-first-login"><p><i class="fa fa-warning"></i>%s</p></div>', $text );
	}