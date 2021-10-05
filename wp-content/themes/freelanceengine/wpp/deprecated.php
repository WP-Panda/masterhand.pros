<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param $referral_code
 *
 * @deprecated
 *
 *
 * @return bool
 */
function check_ref_code_by_company( $referral_code ) {

	global $wpp_en;

	if ( ! empty( $referral_code ) ) {
		$out = $wpp_en->db->get_var( "SELECT EXISTS(SELECT id FROM {$wpp_en->db->get_blog_prefix()}posts WHERE id = {$referral_code} AND post_type = '" . COMPANY . "')" );
	}

	return $out ?? false;
}