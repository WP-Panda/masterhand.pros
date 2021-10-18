<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
/**
 * Получение скилдов
 */
function wpp_get_user_skills() {
	$skills = WPP_Skills_User::getInstance()->get_user_skill_list();
	if ( ! empty( $skills ) ) {
		$out = [];
		foreach ( $skills as $skill ) {
			$out[] = [
				'title'   => $skill["title"],
				'id'      => $skill["id"],
				'endorse' => $skill["count"],
				'checked' => "1"
			];
		}

		wp_send_json_success( [
			'skills' => $out
		] );

	}

	wp_send_json_success( [
	] );

}