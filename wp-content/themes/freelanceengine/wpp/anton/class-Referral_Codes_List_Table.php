<?php

class Referral_Codes_List_Table extends WP_List_Table {

	function __construct(){
		parent::__construct([
			'singular' => 'referral code',
			'plural'   => 'referral codes',
			'ajax'     => false,
		]);

		add_screen_option( 'per_page', [
			'label'   => 'Показывать на странице',
			'default' => 20,
			'option'  => 'referral_codes_table_per_page',
		] );

		$this->prepare_items();

		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );
	}

	// создает элементы таблицы
	function prepare_items(){
		global $wpdb;

		// пагинация
		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;

		$user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		if ($user_search_key != '') {
			$result_all1 = $wpdb->get_var("SELECT COUNT(u.user_login)
				FROM wp_referral_code as rc
				INNER JOIN wp_users as u ON rc.user_ID = u.ID
				LEFT JOIN wp_users as uref ON rc.user_id_referral = u.ID
				WHERE u.user_login = '".$user_search_key."'"
			);

			$result_all2 = $wpdb->get_var("SELECT COUNT(u.user_login)
				FROM wp_referral_code as rc
				INNER JOIN wp_users as u ON rc.user_ID = u.ID
				LEFT JOIN wp_users as uref ON rc.user_id_referral = u.ID
				WHERE rc.referral_code = '".$user_search_key."'"
			);

			$result_all = $result_all1 + $result_all2;

		} else {
			$result_all = $wpdb->get_var("SELECT COUNT(u.user_login)
				FROM wp_referral_code as rc
				INNER JOIN wp_users as u ON rc.user_ID = u.ID
				LEFT JOIN wp_users as uref ON rc.user_id_referral = u.ID
				ORDER BY u.user_login"
			);
		}

		$total_items = $result_all;

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
		 ] );

		$cur_page = (int) $this->get_pagenum(); // желательно после set_pagination_args()

		$sort = isset($_GET['order']) ? $_GET['order'] : '';

		$order_by = 'ORDER BY u.user_login ASC';

		if (isset($_GET['orderby']) && $_GET['orderby'] == 'referral_code') {
			$order_by = 'ORDER BY rc.referral_code '.$sort;
		}

		if (isset($_GET['orderby']) && $_GET['orderby'] == 'user_login') {
			$order_by = 'ORDER BY u.user_login '.$sort;
		}

		if ($user_search_key != '') {
			$result1 = $wpdb->get_results("SELECT u.ID, u.user_login, rc.referral_code,
		  	IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  	FROM wp_referral_code as rc
		  	INNER JOIN wp_users as u ON rc.user_ID = u.ID
		  	LEFT JOIN wp_users as uref ON rc.user_id_referral = uref.ID
				WHERE u.user_login = '".$user_search_key."'
		 		".$order_by." LIMIT ".($cur_page - 1) * $per_page.", $per_page"
				, ARRAY_A);

			$result2 = $wpdb->get_results("SELECT u.ID, u.user_login, rc.referral_code,
		  	IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  	FROM wp_referral_code as rc
		  	INNER JOIN wp_users as u ON rc.user_ID = u.ID
		  	LEFT JOIN wp_users as uref ON rc.user_id_referral = uref.ID
				WHERE rc.referral_code = '".$user_search_key."'
		 		".$order_by." LIMIT ".($cur_page - 1) * $per_page.", $per_page"
				, ARRAY_A);

			$result	= $result1 + $result2;
		} else {
			$result = $wpdb->get_results("SELECT u.ID, u.user_login, rc.referral_code,
		  IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  FROM wp_referral_code as rc
		  INNER JOIN wp_users as u ON rc.user_ID = u.ID
		  LEFT JOIN wp_users as uref ON rc.user_id_referral = uref.ID
		  ".$order_by." LIMIT ".($cur_page - 1) * $per_page.", $per_page"
		  , ARRAY_A);
		}

		$referrer_counts = $wpdb->get_results("SELECT COUNT(rc.user_id_referral) as ucount,
		  rc.user_id_referral as user_id_referral
		  FROM wp_referral_code as rc INNER JOIN wp_users as u ON rc.user_ID = u.ID WHERE rc.user_id_referral > 0
			GROUP BY rc.user_id_referral"
	    , ARRAY_A);

		$this->_column_headers = $this->get_column_info();

		foreach ($result as $result) {
			$count = 0;

			foreach ($referrer_counts as $referrer_count) {
				if ($referrer_count['user_id_referral'] == $result['ID']) {
					$count = $referrer_count['ucount'];
					break;
				}
			}
			$this->items[] = [
				'user_login' => $result['user_login'],
				'referral_code' => $result['referral_code'],
				'referrer' => $result['referrer'],
				'referral_count' => $count,
			];
		}
	}

	function get_columns(){
		return [
			'user_login'    => 'Имя пользователя',
			'referral_code' => 'Код реферала',
			'referrer' => 'Реферер',
			'referral_count' => 'Кол-во рефералов',
		];
	}

	function get_sortable_columns(){
		return [
			'user_login' => [ 'user_login', 'desc' ],
			'referral_code' => [ 'referral_code', 'desc' ],
		];
	}

	protected function get_bulk_actions() {
		return [];
	}

	function extra_tablenav( $which ){
	}

	static function _list_table_css(){
		?>
		<style>
			table.referralcodes .column-user_login{ width: 40%; }
			table.referralcodes .column-referral_code{ width: 20%; }
			table.referralcodes .column-referrer{ width: 20%; }
			table.referralcodes .column-referral_count{ width: 20%; }
		</style>
		<?php
	}

	// вывод каждой ячейки таблицы...
	function column_default( $item, $colname ){

		if( $colname === 'user_login' ){
			// ссылки действия над элементом
			$actions = [];
			$actions['edit'] = sprintf( '<a href="%s">%s</a>', '#', 'Показать рефералов' );

			return esc_html( $item[$colname] ) . $this->row_actions( $actions );
		}
		else {
			return isset($item[$colname]) ? $item[$colname] : print_r($item, 1);
		}
	}

}
