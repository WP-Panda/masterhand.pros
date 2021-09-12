<?php
/**
 * Дополнительные функции по проектам
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Дополнительные опции для проектов
 * @return array
 */
function wpp_additional_options() {

	$default = [
		'create_project_for_all',
		'priority_in_list_project',
		'highlight_project',
		'urgent_project',
		'hidden_project',
	];


	return apply_filters( 'wpp_additional_options', $default );

}

/**
 * Стили для дополнительных опций
 *
 * @param $project
 *
 * @return mixed
 */
function optionsProject( $project ) {

	$option_for_project['highlight_project']        = empty( $project->highlight_project ) ? '' : 'style="background-color:rgba(251, 243, 65, 0.31)"';
	$option_for_project['urgent_project']           = empty( $project->urgent_project ) ? '' : ' - ' . __( 'Urgent Project', ET_DOMAIN );
	$option_for_project['create_project_for_all']   = ! empty( $project->create_project_for_all ) ? '' : ' - ' . __( 'for PRO', ET_DOMAIN );
	$option_for_project['priority_in_list_project'] = empty( $project->priority_in_list_project ) ? '' : ' - ' . __( 'TOP', ET_DOMAIN );
	$option_for_project['hidden_project']           = empty( $project->hidden_project ) ? '' : ' - ' . __( 'HIDDEN', ET_DOMAIN );

	return $option_for_project;
}

/**
 * Обновление данных по оплате для опций
 *
 * @param $post_ID
 * @param $option
 * @param $val
 * @param string $type - тип обновления
 *                       paid - оплачено
 *                       pay - отправка на оплату
 *
 * @return bool
 */

function wpp_update_additional_option( $post_ID, $option, $val, $type = 'pay' ) {

	if ( empty( $option ) || empty( $post_ID ) ) {

		if ( ! function_exists( 'wpp_d_log' ) ) {
			wpp_d_log( 'Отсутствует переменная ' . empty( $option ) ? 'option' : 'post_id' );
		}

		return false;
	}

	#если есть значение
	if ( ! empty( $val ) && false !== $val ) {

		if ( false !== $val ) {
			update_post_meta( $post_ID, 'et_' . $option, $val );
		}

		#онтрольный статус оплаты
		switch ( $type ):
			case 'paid':
				$statuus = ' paid';
				break;
			case 'pay':
			default:
				$statuus = 'send';
				break;
		endswitch;

		update_post_meta( $post_ID, "_{$option}", $statuus );

	} else {
		# сли нет значения - удаляем
		delete_post_meta( $post_ID, 'et_' . $option );
		delete_post_meta( $post_ID, "_{$option}" );
	}

}


function wpp_change_pay_status_for_option( $data, $payment_return ) {


	$options = wpp_additional_options();

	foreach ( $options as $option ) {

		$meta_data = get_post_meta( $data['ad_id'], "et_{$option}", true );

		if ( ! empty( $meta_data ) ) {
			update_post_meta( $data['ad_id'], "_{$option}", 'paid' );
		}

	}

}

add_action( 'wpp_payment_option_success', 'wpp_change_pay_status_for_option', 10, 2 );


function unset_pay_options( $data ) {

	if ( is_page_template( 'page-options-project.php' ) ||  wp_doing_ajax() ) {

		return $data;
	}

	$options = wpp_additional_options();

	foreach ( $options as $option ) {

		if ( ! empty( $data->{$option} ) ) {
			$pay = get_post_meta( $data->id, "_{$option}", true );
			if ( empty( $pay ) || 'paid' !== $pay ) {
				unset( $data->{$option} );
			}
		}
	}

	return $data;
}

add_filter( 'ae_convert_project', 'unset_pay_options' );