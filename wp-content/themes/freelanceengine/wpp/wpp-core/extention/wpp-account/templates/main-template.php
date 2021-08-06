<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Date: 06.05.2019
	 * Time: 21:32
	 */
	get_header();
	global $wp_query;

	$point = Wpp_Pf_Endpoints::get_current_endpoint();
	if ( !empty( $point ) && empty( $wp_query->query[ $point ] ) ) {
		echo do_shortcode( '[mb_user_profile_info id="default-fields"]' );
	}
	/**
	 * wpp_fr_acc_header
	 *
	 * @hooked wpp_pf_acc_endpoints_header -10
	 */
	do_action( 'wpp_fr_acc_header' );


	get_footer();