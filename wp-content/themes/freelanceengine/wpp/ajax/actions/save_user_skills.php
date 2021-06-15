<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	function wpp_save_user_skills() {

		if ( empty( $_POST[ 'skills' ] ) ) {
			wp_send_json_error( [ 'msg' => __( 'Skills is Empty!', WPP_TEXT_DOMAIN ) ] );
		}

		$skills = [];

		foreach ( $_POST[ 'skills' ] as $skill ) :

			if ( empty( absint( $skill ) && $skill !== '0' ) ) {

				$new_skill      = WPP_Skills_Actions::getInstance()->create_skill( $skill );
				$new_skill_data = WPP_Skills_Actions::getInstance()->get_skill( $skill );
				$skills[]       = $new_skill_data->id;

			} else {
				$skills[] = absint( $skill );
			}

		endforeach;

		WPP_Skills_User::getInstance()->set_user_skills_meta( $skills );

		wp_send_json_success( [ 'msg' => __( 'Skills Updated!', WPP_TEXT_DOMAIN ) ] );
	}