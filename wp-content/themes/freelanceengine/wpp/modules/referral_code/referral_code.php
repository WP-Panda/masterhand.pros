<?php
global $wpp_en;

define( 'TABLE_REFERRAL', $wpp_en->prefix . 'referral_code' );
define( 'PAGE_STEP', 10 );

class Wpp_Referal extends Wpp_Module_Base {
	function __construct() {

		$args = [
			'page_title' => __( 'Refereeral coder', 'wpp' ),
			'menu_title' => __( 'Refeeeerral coder', 'wpp' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'referral_code',
			'function'   => 'show_referral_code',
			'icon_url'   => get_stylesheet_directory_uri() . '/wpp/modules/asset/image/gear.png',
			'position'   => 100
		];

		parent::__construct( $args );
	}

	/**
	 * Получение максимального значения нутреннего USER ID
	 *
	 * @return bool
	 */
	function max_referal_code() {
		global $wpp_en;

		$query = sprintf( "SELECT max(cast(meta_value as unsigned)) FROM %s WHERE meta_key='%s'", $wpp_en->prefix . 'usermeta', '_user_ids' );

		$_ID = $wpp_en->db->get_var( $query );

		return $_ID ?? false;

	}


	function gernerate_user_id() {
		$current = $this->max_referal_code();

		return empty( $current ) ? 1111 : (int) $current + 1;
	}

}

new Wpp_Referal();


/**
 * Действия при регистрации пользователя
 *
 * @param $user_id
 */
function generate_referral_code( $user_id ) {
	global $wpp_en;

	if ( ! empty( $user_id ) ) {

		$code_u = new Wpp_Referal();
		$code   = $code_u->gernerate_user_id();

		$code_keys = [ 'code', 'referral_code', 'referral-code' ];

		$referral_code = null;

		foreach ( $code_keys as $code_key ) :

			if ( ! empty( $_REQUEST[ $code_key ] ) ) {
				$referral_code = absint( $_REQUEST[ $code_key ] );
				break;
			}
		endforeach;


		$referral_user_id = 0;

		#если есть реферал код
		if ( ! empty( $referral_code ) ) {

			#Проверка компания или нет
			$is_company = get_company( $referral_code );

			if ( ! empty( $_REQUEST['type_prof'] ) && COMPANY === $_REQUEST['type_prof'] ) {

				#validen li Код


				if ( $is_company ) {

					add_user_meta( $user_id, 'is_company', 1 );
					do_action( 'activityRating_asReferral', $user_id );
					#update_user_meta( $user_id, '_activityRating_asReferral', $referral_code );
					// тут удаляется компания
					company_delete( $referral_code );
				}

			} else {

				#валиден ли код
				$referral_user_id = get_user_by_referral_code( $referral_code );

				if ( $referral_user_id ) {
					Fre_Mailing::get_instance()->notification_registration_referral_code( $referral_user_id, $user_id );
					do_action( 'fre_new_referral', $referral_user_id, $user_id );
					update_user_meta( $user_id, '_activityRating_asReferral', $referral_code );
					update_user_meta( $user_id, '_activityRating_asReferrer', $referral_user_id );
					#do_action( 'activityRating_asReferral', $user_id );
					#do_action( 'activityRating_asReferrer', $referral_user_id );
				}
			}
		}

		$wpp_en->db->insert( TABLE_REFERRAL, [
			'user_id'          => $user_id,
			'referral_code'    => $code,
			'user_id_referral' => $referral_user_id
		], [ '%d', '%d', '%d' ] );
	}
}

add_action( 'user_register', 'generate_referral_code' );


/**
 * Получение юзера по реферальному коду
 *
 * @param $referral_code
 *
 * @return bool
 */
function get_user_by_referral_code( $referral_code ) {
	global $wpp_en;
	if ( ! empty( $referral_code ) ) {
		$out = $wpp_en->db->get_var( "SELECT user_id FROM " . TABLE_REFERRAL . " WHERE referral_code = $referral_code" );
	}

	return $out ?? false;
}

/**
 * Получение реферального кода юзера
 *
 * @param $user_id
 *
 * @return bool|int
 */
function get_referral_code_by_user( $user_id ) {

	global $wpp_en;

	if ( ! empty( $user_id ) ) {

		$referral_code = $wpp_en->db->get_var( "SELECT referral_code FROM " . TABLE_REFERRAL . " WHERE user_id = $user_id" );

		if ( empty( $referral_code ) ) {
			$referral_code = set_referral_code_by_old_user( $user_id );
		}
	}

	return $referral_code ?? false;
}

/**
 * Получение рефералов
 *
 * @param $user_id
 *
 * @return bool
 */
function get_referral( $user_id ) {

	global $wpp_en;

	if ( ! empty( $user_id ) ) {
		$list = $wpp_en->db->get_results( "SELECT user_id, user_id_referral FROM " . TABLE_REFERRAL . " WHERE user_id = $user_id OR user_id_referral = $user_id", ARRAY_A );
	}

	return $list ?? false;
}

/**
 * Спонсоры бзера
 *
 * @param $user_id
 *
 * @return bool|null|string
 */
function get_sponsor( $user_id ) {
	global $wpdb;
	if ( ! empty( $user_id ) ) {

		$sponsor_name = $wpdb->get_var( "SELECT wp_users.user_login FROM " . TABLE_REFERRAL . " 
                                            LEFT JOIN wp_users ON wp_users.ID = " . TABLE_REFERRAL . ".user_id_referral 
                                            WHERE " . TABLE_REFERRAL . ".user_id = $user_id" );
	}

	return $sponsor_name ?? false;
}

/**
 * Gjkextybt id Спонсора
 *
 * @param $user_id
 *
 * @return bool
 */
function get_sponsor_id( $user_id ) {
	global $wpp_en;
	if ( ! empty( $user_id ) ) {

		$sponsor = $wpp_en->db->get_var( "SELECT user_id_referral  FROM " . TABLE_REFERRAL . " 
                                            LEFT JOIN wp_users ON wp_users.ID = " . TABLE_REFERRAL . ".user_id_referral 
                                            WHERE " . TABLE_REFERRAL . ".user_id = $user_id" );
	}

	return $sponsor ?? false;
}


function referral_code_styles() {
	wp_enqueue_style( 'referer', get_template_directory_uri() . '/wpp/modules/referral_code/css/referral_code_style.css', [], time(), 'all' );
}

function show_referral_code() {

	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

	switch ( $action ) {
		case 'getList':
			$page      = ! empty( $_POST['page'] ) ? $_POST['page'] : 1;
			$referrals = get_list_referrals( $page );

			$html = '';
			foreach ( $referrals as $item ) {
				ob_start();
				include 'tpl/list.php';
				$html .= ob_get_clean();
			}
			$data['referrals']  = $html;
			$data['pagination'] = getPagination( $page );

			outputJSON( $data, 1 );
			break;
		default:
			$show_page   = ! empty( $_POST['page'] ) ? ( $_POST['page'] - 1 ) * PAGE_STEP : 0;
			$referrals   = get_list_referrals( $show_page );
			$pagination  = getPagination();
			$plugin_data = get_plugin_data( __FILE__ );
			$PATH_INC    = '/wp-content/plugins/' . basename( __DIR__ );
			include 'show_referral_code.php';
			break;
	}
}

function outputJSON( $data = [], $status = false ) {
	$response = [];

	if ( is_string( $data ) ) {
		$response['msg'] = $data;
	} else {
		$response = ! empty( $data ) ? $data : [];
	}

	$response['status'] = isset( $response['status'] ) ? $response['status'] : ( $status ? 'success' : 'error' );
	$response['msg']    = ( $response['status'] == 'error' && empty( $response['msg'] ) ) ? 'Error!!!' : $response['msg'];

	ob_clean();
	header_remove();
	header( 'Content-type: text/json; charset=UTF-8' );
	echo json_encode( $response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}

function get_list_referrals( $page = 1, $user = null ) {
	global $wpp_en, $wpdb;
	$user_id = ! empty( $user ) ? $user : ( ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : null );
	$where   = ! empty( $user_id ) ? " WHERE rc.user_id_referral=" . (int) $user_id : "";

	$orderBy = " ORDER BY user_id ASC";
	if ( ! empty( $_POST['orderBy'] ) ) {
		$dataOrderBy              = [];
		$data_parseOrderBy        = explode( ',', trim( $_POST['orderBy'] ) );
		$dataOrderBy['field']     = trim( $data_parseOrderBy[0] );
		$dataOrderBy['direction'] = ( trim( $data_parseOrderBy[1] ) == 'ASC' ) ? 'ASC' : 'DESC';

		$orderBy = " ORDER BY " . $dataOrderBy['field'] . " " . $dataOrderBy['direction'];
	}

	if ( ! empty( $page ) && $page == 'all' ) {
		$limit = '';
	} else {
		$show_page = ! empty( $page ) ? ( $page - 1 ) * PAGE_STEP : $page;
		$limit     = " LIMIT " . $show_page . "," . PAGE_STEP;
	}

	$referrals = $wpp_en->db->get_results( "
        SELECT rc.user_id, rc.referral_code, 
            u.user_login, u.display_name user_name, 
            u2.user_login user_referral_login, u2.display_name user_referral_name,
            (SELECT COUNT(*) FROM " . TABLE_REFERRAL . "
                WHERE  u.ID=user_id_referral) count_referrals
        FROM " . TABLE_REFERRAL . " rc
        LEFT JOIN $wpdb->users u 
        ON u.ID=rc.user_id
        LEFT JOIN $wpdb->users u2 ON u2.ID=rc.user_id_referral " . $where . $orderBy . $limit, ARRAY_A );

	return $referrals;
}


function set_referral_code_by_old_user( $user_id ) {
	global $wpp_en;
	if ( ! empty( $user_id ) ) {
		$code   = (int) time() + $user_id;
		$insert = $wpp_en->db->insert( TABLE_REFERRAL, [
			'user_id'          => $user_id,
			'referral_code'    => $code,
			'user_id_referral' => 0
		], [ '%d', '%d', '%d' ] );
	}

	return ! empty( $insert ) ? $code : false;
}

function get_count_referrals( $user_id = null ) {
	global $wpp_en;
	$where = ! empty( $user_id ) ? " WHERE user_id_referral=" . $user_id : '';
	$sql   = "SELECT COUNT(*) FROM " . TABLE_REFERRAL . $where;

	return $wpp_en->db->get_var( $sql );
}

function getPagination( $currentPage = 1 ) {
	$total_referrals = get_count_referrals();
	$currentPage     = (int) $currentPage;
	$urlPattern      = 'javascript:mod.getData(\'(:num)\')';

	include_once 'inc/Paginator.php';
	$paginator = new Paginator( $total_referrals, PAGE_STEP, (int) $currentPage, $urlPattern );

	return $paginator->toHtml();
}