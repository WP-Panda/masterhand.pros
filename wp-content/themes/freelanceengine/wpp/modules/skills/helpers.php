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
	if ( $user_ID === get_current_user_id() ) {
		return false;
	}

	#Получение рефералов
	$refferal = get_referral( $current_user );


	if ( ! empty( $refferal ) ) {
		$refferal = wp_list_pluck( $refferal, 'user_id' );
	} else {
		$refferal = [];
	}

	#Получение спонсоров
	$sponsor = get_sponsor_id( $current_user );
	if ( ! empty( $sponsor ) ) {
		$refferal[] = $sponsor;
	}

	#Удаление самого себя
	$key = array_search( $current_user, $refferal );
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

		if ( $project_query->have_posts() ) :
			while ( $project_query->have_posts() ) :
				$project_query->the_post();

				$employer_ = new WP_Query( [
					'post_type'   => 'bid',
					'post_parent' => $project_query->post->ID,
					'post_status' => [ 'accept', 'complete' ],
					'nopaging'    => true,
					'post_author' => $user_ID
				] );
				if ( $employer_->have_posts() ) :
					while ( $employer_->have_posts() ) :
						$employer_->the_post();
					endwhile;
				endif;


				if ( ! empty( $employer_->found_posts ) ) {
					$refferal[] = $user_ID;
				}

			endwhile;
		endif;

	} else {


		#Выполненные проекты для специалиста
		$bids_query = new WP_Query( [
			'post_status' => [ 'accept', 'complete' ],
			'post_type'   => 'bid',
			'author'      => $current_user,
			'nopaging'    => true
		] );

	//	wpp_dump( $bids_query->found_posts );
		if ( $bids_query->have_posts() ) :
			while ( $bids_query->have_posts() ) :
				$bids_query->the_post();
				$post_b     = get_post( $bids_query->post->post_parent );
				$refferal[] = $post_b->post_author;
			endwhile;
		endif;

	}
	$refferal = array_unique($refferal);
	$in_allow = array_search( $user_ID, $refferal );
	//wpp_dump($refferal);
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