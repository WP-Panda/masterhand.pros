<?php
/**
 * ТУТ НАХОДЯТСЯ ХРЕНОВЫЕ РЕШЕНИЯ
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Подстановка шаблона give-endorsment
 * @param $template
 *
 * @return string
 *
 * @todo ТУТ НАДО ПЕРЕДЕЛАТЬ
 */
function give_endorsements_template_include( $template ) {
	if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/give-endorsements' ) !== false ) {

		add_filter( 'wp_title', function() {
			return __( 'Give Endorsements' ) . ' | ';
		}, 1 );

		status_header( 200 );

		$new_template = locate_template( [ 'give-endorsements.php' ] );
		if ( ! empty( $new_template ) ) {
			return $new_template;
		}

	}

	return $template;
}


/**
 * filter pre get profile
 *
 * @param $query
 *
 * @return
 * @package FreelanceEngine
 *
 * @todo   - Какая то херня
 */
function pre_get_profile( $query ) {


	if ( ! wp_doing_ajax() && is_admin() ) {
		return $query;
	}

	if ( strpos( $_SERVER['REQUEST_URI'], '/give-endorsements' ) !== false  ) {
		return $query;
	}

	if ( isset( $query->query['post_type'] ) && PROFILE === $query->query['post_type'] ) {

		if ( isset( $_REQUEST['query']['hour_rate'] ) && ! empty( $_REQUEST['query']['hour_rate'] ) ) {
			$hour_rate                       = $_REQUEST['query']['hour_rate'];
			$hour_rate                       = explode( ",", $hour_rate );
			$query->query_vars['meta_query'] = [
				[
					'key'     => 'hour_rate',
					'value'   => [ (int) $hour_rate[0], (int) $hour_rate[1] ],
					'type'    => 'numeric',
					'compare' => 'BETWEEN'
				]
			];

		} else {
			// Query Hour_rate default
			$query->query_vars['meta_query'][] = [
				'key' => 'hour_rate'
			];
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			/*
		 * fre/emp/visitor only see profile is available for hire.
		 */
			$query->query_vars['meta_query'][] = [
				'key'     => 'user_available',
				'value'   => 'on',
				'compare' => '='
			];
		}
	}
	// Search default
	if ( $query->is_search() && is_search() && ! is_admin() ) {
		$query->set( 'post_type', [ 'post', 'page' ] );
	} // end if

	return $query;
}
//add_action( 'pre_get_posts', 'pre_get_profile' );