<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * разрешено ли оценивать?
 * Пока это функция заглушка
 *
 * @return bool
 */
function wpp_is_endorse_allow( $user_ID ) {

	$current_user = get_current_user_id();

	if ( empty( (int) $current_user ) ) {
		return false;
	}

	#Запрет самолайка
	/*if ( $user_ID === get_current_user_id() ) {
		return false;
	}*/

	#Получение рефералов
	$refferal = get_referral( $user_ID );

	if ( ! empty( $refferal ) ) {
		$refferal = wp_list_pluck( $refferal, 'user_id' );
	} else {
		$refferal = [];
	}

	#Получение спонсоров
	$sponsor = get_sponsor_id( $user_ID );
	if ( ! empty( $sponsor ) ) {
		$referral[] = $sponsor;
	}

	#Удаление самого себя
	$key = array_search( $user_ID, $refferal );
	if ( isset( $key ) ) {
		unset( $refferal[ $key ] );
	}

	if ( ! wpp_fre_is_freelancer() ) {

		#Выполненные проекты для заказчика
		$project_query = new WP_Query( [
			'post_status' => [ 'complete' ],
			'post_type'   => PROJECT,
			'author'      => $current_user,
			'nopaging'    => true
		] );


		foreach ( $project_query as $one_pr ) {

			$employer_ = new WP_Query( [
				'post_type'   => 'bid',
				'post_parent' => $one_pr->ID,
				'post_status' => [ 'accept' ],
				'nopaging'    => true
			] );


			foreach ( $employer_ as $one_em ) {
				if ( ! empty( $one_em->post_author ) ) {
					$referal[] = $one_em->post_author;
				}
			}

		}

	} else {

		#Выполненные проекты для специалиста
		$bids_query = new WP_Query( [
			'post_status' => [ 'accept' ],
			'post_type'   => 'bid',
			'author'      => $current_user,
			'nopaging'    => true
		] );

		if ( $bids_query->have_posts() ) :
			while ( $bids_query->have_posts() ) :
				$bids_query->the_post();


				$bids = new WP_Query( [
					'post_type'   => PROJECT,
					'post_parent' => $bids_query->post->ID,
					'post_status' => [ 'complete' ],
					'nopaging'    => true
				] );

				foreach ( $bids as $one_bid ) {
					if ( ! empty( $one_bid->post_author ) ) {
						$refferal[] = $one_bid->post_author;
					}
				}

			endwhile;
		endif;

	}

	array_unique( $refferal );
	//wpp_dump( $refferal );
	$in_allow = array_search( $current_user, $refferal );

	if ( empty( $in_allow ) ) {
		return false;
	}

	return true;
}

/**
 * Оцененео ли?
 *
 * @return bool
 */
function wpp_is_endorsed( $user_id, $skill_id ) {
	$check = WPP_Skills_Actions::getInstance()->is_endorse( $user_id, $skill_id );

	return empty( $check ) ? false : true;
}


function countEndorseSkillsUser( $user_id ) {
	return WPP_Skills_User::getInstance()->count_user_skills( $user_id );
}