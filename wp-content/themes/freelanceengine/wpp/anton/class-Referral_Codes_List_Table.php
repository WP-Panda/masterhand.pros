<?php

class Referral_Codes_List_Table extends WP_List_Table {

	function __construct() {

		parent::__construct( [
			'singular' => 'referral code',
			'plural'   => 'referral codes',
			'ajax'     => false,
		] );

		add_screen_option( 'per_page', [
			'label'   => 'Показывать на странице',
			'default' => 20,
			'option'  => 'referral_codes_table_per_page',
		] );

		$this->prepare_items();

		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );

		#не используем анонимнные функции
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ], 99 );

	}


	function assets() {
	    #my-wp-admin   - не айс имя, префикс my он, ну такое((((
		wp_enqueue_script( 'an-wp-admin', get_template_directory_uri() . '/js/wp-admin.js' );
    }

	// создает элементы таблицы
	function prepare_items() {

		global $wpdb;

		#у таблиц не всегда будет преффикс wp_  - н легко меняется, соответственно имена таблиц надо
        # записывать с префиксом в виде переменной
        $user_table = $wpdb->prefix . 'users';
        $referal_table = $wpdb->prefix . 'referral_code';

		// пагинация
		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;

		$user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		if ( $user_search_key !== '' ) {

			$result_all1 = $wpdb->get_var( "SELECT COUNT(u.user_login)
				FROM $referal_table as rc
				INNER JOIN $user_table as u ON rc.user_ID = u.ID
				LEFT JOIN $user_table as uref ON rc.user_id_referral = u.ID
				WHERE u.user_login = $user_search_key"
			);

			$result_all2 = $wpdb->get_var( "SELECT COUNT(u.user_login)
				FROM $referal_table as rc
				INNER JOIN $user_table as u ON rc.user_ID = u.ID
				LEFT JOIN $user_table as uref ON rc.user_id_referral = u.ID
				WHERE rc.referral_code = $user_search_key"
			);

			$result_all = $result_all1 + $result_all2;

		} else {

			$result_all = $wpdb->get_var( "SELECT COUNT(u.user_login)
				FROM wp_referral_code as rc
				INNER JOIN $user_table as u ON rc.user_ID = u.ID
				LEFT JOIN $user_table as uref ON rc.user_id_referral = u.ID
				ORDER BY u.user_login"
			);
		}

		#тут не понгятно для чего так
		$total_items = $result_all;

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
		] );

		$cur_page = (int) $this->get_pagenum(); // желательно после set_pagination_args()

		$sort = isset( $_GET['order'] ) ? $_GET['order'] : '';

		$order_by = 'ORDER BY u.user_login ASC';

		if ( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'referral_code' ) {
			$order_by = 'ORDER BY rc.referral_code ' . $sort;
		}

		if ( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'user_login' ) {
			$order_by = 'ORDER BY u.user_login ' . $sort;
		}

		if ( $user_search_key != '' ) {

			$result1 = $wpdb->get_results( "SELECT u.ID, u.user_login, rc.referral_code,
		  	IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  	FROM $referal_table as rc
		  	INNER JOIN $user_table as u ON rc.user_ID = u.ID
		  	LEFT JOIN $user_table as uref ON rc.user_id_referral = uref.ID
				WHERE u.user_login = '" . $user_search_key . "'
		 		" . $order_by . " LIMIT " . ( $cur_page - 1 ) * $per_page . ", $per_page"
				, ARRAY_A );

			$result2 = $wpdb->get_results( "SELECT u.ID, u.user_login, rc.referral_code,
		  	IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  	FROM $referal_table as rc
		  	INNER JOIN $user_table as u ON rc.user_ID = u.ID
		  	LEFT JOIN $user_table as uref ON rc.user_id_referral = uref.ID
				WHERE rc.referral_code = '" . $user_search_key . "'
		 		" . $order_by . " LIMIT " . ( $cur_page - 1 ) * $per_page . ", $per_page"
				, ARRAY_A );

			$result = $result1 + $result2;

		} else {
			$result = $wpdb->get_results( "SELECT u.ID, u.user_login, rc.referral_code,
		  IF (uref.user_login IS NOT NULL, uref.user_login, '-') as referrer
		  FROM $referal_table as rc
		  INNER JOIN $user_table as u ON rc.user_ID = u.ID
		  LEFT JOIN $user_table as uref ON rc.user_id_referral = uref.ID
		  " . $order_by . " LIMIT " . ( $cur_page - 1 ) * $per_page . ", $per_page"
				, ARRAY_A );

		}

		$referrer_counts = $wpdb->get_results( "SELECT COUNT(rc.user_id_referral) as ucount,
		  rc.user_id_referral as user_id_referral
		  FROM $referal_table as rc INNER JOIN $user_table as u ON rc.user_ID = u.ID WHERE rc.user_id_referral > 0
			GROUP BY rc.user_id_referral"
			, ARRAY_A );

		$this->_column_headers = $this->get_column_info();

		foreach ( $result as $result ) {
			$count = 0;

			foreach ( $referrer_counts as $referrer_count ) {
				if ( $referrer_count['user_id_referral'] == $result['ID'] ) {
					$count = $referrer_count['ucount'];
					break;
				}
			}
			$this->items[] = [
				'user_login'     => $result['user_login'],
				'referral_code'  => $result['referral_code'],
				'referrer'       => $result['referrer'],
				'referral_count' => $count,
			];
		}
	}

	static function _list_table_css() {
		?>
        <style>
            table.referralcodes .column-user_login {
                width: 40%;
            }

            table.referralcodes .column-referral_code {
                width: 20%;
            }

            table.referralcodes .column-referrer {
                width: 20%;
            }

            table.referralcodes .column-referral_count {
                width: 20%;
            }

            .referrals {
                display: none;
            }
        </style>
		<?php
	}

	function get_columns() {

		return [
			'user_login'     => 'Имя пользователя',
			'referral_code'  => 'Код реферала',
			'referrer'       => 'Реферер',
			'referral_count' => 'Кол-во рефералов',
		];

	}

	function get_sortable_columns() {

		return [
			'user_login'    => [ 'user_login', 'desc' ],
			'referral_code' => [ 'referral_code', 'desc' ],
		];

	}

	function extra_tablenav( $which ) {}

	function column_default( $item, $colname ) {

		if ( $colname === 'user_login' ) {
			// ссылки действия над элементом
			$actions         = [];
			$actions['edit'] = sprintf( '<a href="%s">%s</a>', '#', 'Показать рефералов' );

			global $wpdb;

			$user_id = $wpdb->get_var( "SELECT user_id FROM wp_referral_code
				WHERE referral_code = '" . $item['referral_code'] . "'" );

			$referrals = $wpdb->get_col( "SELECT u.user_login FROM wp_referral_code as rc
			 	INNER JOIN wp_users AS u ON rc.user_id = u.ID
			 	WHERE rc.user_id_referral = '" . $user_id . "'
			 	ORDER BY u.user_login" );

			$referrals_string = '<ol>';

			foreach ( $referrals as $referral ) {
				$referrals_string .= '<li>' . $referral . '</li>';
			}

			$referrals_string .= '</ol>';

			if ( $item['referral_count'] ) {
				return esc_html( $item[ $colname ] ) . $this->row_actions( $actions ) . '<div class="referrals"><p><strong>Рефералы:</strong></p>' .
				       $referrals_string . '<p><button type="button" class="button cancel alignleft">Закрыть</button></p></div>';
			} else {
				return esc_html( $item[ $colname ] );
			}


		} else {
			return isset( $item[ $colname ] ) ? $item[ $colname ] : print_r( $item, 1 );
		}
	}

	// вывод каждой ячейки таблицы...
	protected function get_bulk_actions() {
		return [];
	}

}
