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

	#тобы не было меньше нуля
	$new_val = $old_val + (int) $val;
	$user_rating[ $rating_key ] = $new_val < 0 ? 0 : $new_val;

	#рассчет общего значения
	if ( ! empty( $user_rating['total'] ) ) {
		unset( $user_rating['total'] );
	}
	$user_rating['total'] = array_sum( $user_rating );


	#запись нового значения
	update_user_meta( $user_ID, '_wpp_user_rating', $user_rating );

}

function wpp_get_user_rating( $user_ID ) {
	$user_rating = get_user_meta( $user_ID, '_wpp_user_rating', true );

	$rate = ! empty( $user_rating['total'] ) ? $user_rating['total'] : 0;

	$pro_diff = wpp_pro_rate_delta( $rate, $user_ID );

	return $rate + $pro_diff;
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
 * @param $ref
 *
 */
function wpp_rating_set_referrer_data( $user_id, $ref ) {
	$key = wpp_is_fl_by_id( $ref ) ? 'freelancer_as_referrer' : 'employer_as_referrer';
	wpp_rating_set_option( $user_id, $key );
}

add_action( 'wpp_referrer_active', 'wpp_rating_set_referrer_data', 10, 2 );


/**
 * Начисление рэйтинга за посещение сайта
 */
function wpp_rating_set_site_visit() {

	global $user_ID;

	if ( ! empty( $user_ID ) ) {
		$time = time();
		$now  = \DateTime::createFromFormat( 'U', $time );
		$now->setTimeZone( new \DateTimeZone( 'UTC' ) );

		$date = $now->format( 'Y-m-d' );

		$date_flag = get_user_meta( $user_ID, '_wpp_rating_date_flag', true );

		if ( ! empty( $date_flag ) && $date === $date_flag ) {
			return false;
		}

		wpp_rating_set_option( $user_ID, 'site_visit' );
		update_user_meta( $user_ID, '_wpp_rating_date_flag', $date );


	}

}

add_action( 'init', 'wpp_rating_set_site_visit', 100 );

/**
 * Начисление рэйтинга за скиллы
 */
function wpp_skills_rating( $skills ) {

	global $user_ID;
	$skills_old = WPP_Skills_User::getInstance()->get_user_skill_list( $user_ID );

	$count = ! empty( $skills_old ) ? count( $skills_old ) : 0;

	if ( $count > 25 ) {
		return false;
	}

	$count_new = count( $skills ) - $count;

	$key = wpp_fre_is_freelancer() ? 'freelancer_for_skill' : 'employer_for_skill';

	wpp_rating_set_option( $user_ID, $key, (int) $count_new * 10 );

}

add_action( 'wpp_skill_rating', 'wpp_skills_rating', 10 );

/**
 * Начисление рэйтинга за подтвержденные скилы скиллы
 */
function wpp_skills_approved_rating( $user_ID ) {

	$key = wpp_is_fl_by_id( $user_ID ) ? 'freelancer_for_endorse_skill' : 'employer_for_endorse_skill';

	wpp_rating_set_option( $user_ID, $key );

}

add_action( 'wpp_after_likes', 'wpp_skills_approved_rating' );


/**
 * Начисление рэйтинга за удаление подтверженного скила
 */
function wpp_skills_un_approved_rating( $user_ID ) {

	$key     = wpp_is_fl_by_id( $user_ID ) ? 'freelancer_for_endorse_skill' : 'employer_for_endorse_skill';
	$options = get_option( 'wpp_skills' );
	wpp_rating_set_option( $user_ID, $key, "-{$options[$key]}" );
}

add_action( 'wpp_after_un_likes', 'wpp_skills_un_approved_rating' );

/**
 * Начисление за деньги
 */
function wpp_payment_rating( $order_data ) {
	$options = get_option( 'wpp_skills' );
	$coef    = (int) $options['coefficient_amount_payment'];

	wpp_rating_set_option( (int) $order_data['payer'], 'coefficient_amount_payment', (int) $coef * (int) $order_data['total'] );

}

add_action( 'wpp_payment_option_rating', 'wpp_payment_rating', 10 );


/**
 * Заполненность профиля
 *
 * @param $type
 * @param $user_id
 */
function save_one_rating_field_profile( $type, $user_id ) {
	$key = 'one_field_profile';
	$val = get_option( 'wpp_skills' )[ $key ];


	$flags = get_user_meta( $user_id, '_wpp_user_flag_array', true );

	if ( empty( $flags ) ) {
		$flags = [];
	}

	if ( $type === 'avatar' && empty( $flags['avatar'] ) ) {
		$flags['avatar'] = 1;
		update_user_meta( $user_id, '_wpp_user_flag_array', $flags );

	}

	if ( $type === 'paypal' && empty( $flags['paypal'] ) ) {
		$flags['paypal'] = 1;
		update_user_meta( $user_id, '_wpp_user_flag_array', $flags );
	}

	if ( $type === 'phone' && empty( $flags['phone'] ) ) {
		$flags['phone'] = 1;
		update_user_meta( $user_id, '_wpp_user_flag_array', $flags );
	}

	if ( $type === 'email' && empty( $flags['email'] ) ) {
		$flags['email'] = 1;
		update_user_meta( $user_id, '_wpp_user_flag_array', $flags );
	}

	wpp_rating_set_option( $user_id, $key, $val );


}

add_action( 'wpp_rating_one_field_profile', 'save_one_rating_field_profile', 10, 2 );


function wpp_rating_action_bro_bid( $bid_id, $user_ID ) {
	$key = 'employer_bid_accepted';
	wpp_rating_set_option( $user_ID, $key );

}

add_action( 'wpp_rating_action_bro_bid', 'wpp_rating_action_bro_bid', 10, 2 );

/**
 * При закрытии сделки
 *
 * @param $post
 */
function wpp_close_progect_with_rewiew( $post ) {
	global $user_ID;

	$options = get_option( 'wpp_skills' );

	//Для отзыва
	if ( ! empty( $post['comment'] ) ) :
		$key = wpp_fre_is_freelancer() ? 'freelancer_for_review' : 'employer_for_review';
		wpp_rating_set_option( $user_ID, $key );
	endif;

	if ( ! empty( $post['project_id'] ) && ! wpp_fre_is_freelancer() ) {
		$bid_Id     = get_post_meta( $post['project_id'], 'accepted', true );
		$safe       = get_post_meta( $bid_Id, 'fre_bid_order', true );
		$bid_author = get_post( $bid_Id )->post_author;
		//$pro        = get_user_pro_status( $bid_author );
		$my_rate    = wpp_get_user_rating( $user_ID );
		$frere_rate = wpp_get_user_rating( $bid_author );

		// при безопасной сделке
		if ( ! empty( $safe ) ) {
			wpp_rating_set_option( $user_ID, 'employer_project_success' );
			wpp_rating_set_option( $bid_author, 'freelancer_project_success' );
		}
		$e = (int) $frere_rate * (int) $options['employer_project_success'] / 100;
		$f = (int) $my_rate * (int) $options['freelancer_project_success'] / 100;

		//процент от рейтинга
		wpp_rating_set_option( $user_ID, 'employer_coefficient_from_rating_freelancer', $e );
		wpp_rating_set_option( $bid_author, 'freelancer_coefficient_from_rating_employer', $f );


	}
}

add_action( 'wpp_close_project', 'wpp_close_progect_with_rewiew', 10, 1 );


/**
 * Для про коэффицента
 */
function wpp_pro_rate_delta( $rate, $user_ID ) {

	$pro = get_user_pro_status( $user_ID );

	if ( ! empty( $rate ) && ! empty( $pro ) && ( (int) $pro === 2 || (int) $pro === 3 || (int) $pro === 5 ) ) {
		$options = get_option( 'wpp_skills' );

		if ( (int) $pro === 2 ) {//обычный про
			$pro_diff = (int) $options['coefficient_pro_status'] / 100;
		} elseif ( (int) $pro === 3 || (int) $pro === 5 ) { // премиум про
			$pro_diff = (int) $options['coefficient_premium_pro_status'] / 100;
		}

		$user_rating = (int) $rate * (int) $pro_diff;
	}


	return ! empty( $user_rating ) ? $user_rating : 0;
}