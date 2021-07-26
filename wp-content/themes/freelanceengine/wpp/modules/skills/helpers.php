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
function wpp_is_endorse_allow( $user_id ) {

	#Запрет самолайка
	if ( $user_id === get_current_user_id() ) {
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