<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_rating_set_option( $user_ID, $rating_key, $val = null ) {
	$options = get_option( 'wpp_skills' );

	$user_rating = get_user_meta( $user_ID, '_wpp_user_rating', true );
	if ( empty( $user_rating ) ) {
		$user_rating = [];
	}

	#получение текущего значения
	$old_val = ! empty( $user_rating[ $rating_key ] ) ? (int) $user_rating[ $rating_key ] : 0;

	#рассчет нового значения
	if ( ! isset( $val ) ) {
		$val = $options[ $rating_key ];
	}
	$user_rating[ $rating_key ] = $old_val + (int) $val;

	#рассчет общего значения
	if ( ! empty( $user_rating['total'] ) ) {
		unset( $user_rating['total'] );
	}
	$user_rating['total'] = array_sum( $user_rating );

	wpp_d_log($user_rating);

	#запись нового значения
	update_user_meta( $user_ID, '_wpp_user_rating', $user_rating );

}

function wpp_get_user_rating( $user_ID ) {
	$user_rating = get_user_meta( $user_ID, '_wpp_user_rating', true );
	return ! empty( $user_rating['total'] ) ? $user_rating['total'] : 0;
}

/**
 * Yачисление рэйтинга ля зареганного
 *
 * @param $user_id
 */
function wpp_rating_set_referral_data( $user_id ) {
	$key = wpp_is_fl_by_id( $user_id ) ? 'freelancer_as_referral' : 'employer_as_referral';
	wpp_rating_set_option( $user_id, $key );
}

add_action( 'wpp_referral_active', 'wpp_rating_set_referral_data' );

/**
 * Yачисление рэйтинга ля пригласившего
 *
 * @param $user_id
 */
function wpp_rating_set_referrer_data( $user_id ) {
	$key = wpp_is_fl_by_id( $user_id ) ? 'freelancer_as_referrer' : 'employer_as_referrer';
	wpp_rating_set_option( $user_id, $key );
}

add_action( 'wpp_referrer_active', 'wpp_rating_set_referrer_data' );