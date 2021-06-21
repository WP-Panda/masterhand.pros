<?php
	global $wpdb;

	define( 'TABLE_REFERRAL', $wpdb->get_blog_prefix() . 'referral_code' );
	define( 'PAGE_STEP', 10 );


	/*
	 * functions
	 */
	function generate_referral_code( $user_id ) {
		global $wpdb;
		if ( ! empty( $user_id ) ) {
			$code = (int) time() + $user_id;

			if ( ! empty( $_REQUEST[ 'code' ] ) ) {
				$referral_code = (int) $_REQUEST[ 'code' ];
			} else {
				$referral_code = ! empty( $_REQUEST[ 'referral_code' ] ) ? (int) $_REQUEST[ 'referral_code' ] : ( ! empty( $_REQUEST[ 'referral-code' ] ) ? (int) $_REQUEST[ 'referral-code' ] : null );
			}


			$referral_user_id = 0;

			if ( ! empty( $referral_code ) ) {
				$is_type_company = ( ! empty( $_REQUEST[ 'type_prof' ] ) && $_REQUEST[ 'type_prof' ] == COMPANY ) ? true : false;
				if ( $is_type_company ) {
					$is_company = check_ref_code_by_company( $referral_code );
					if ( $is_company ) {
						add_user_meta( $user_id, 'is_company', 1 );
						do_action( 'activityRating_asReferral', $user_id );
						// delete company
						wp_delete_post( $referral_code, false );
					}
				} else {
					$referral_user_id = get_user_by_referral_code( $referral_code );
					if ( $referral_user_id ) {
						Fre_Mailing::get_instance()->notification_registration_referral_code( $referral_user_id, $user_id );
						do_action( 'fre_new_referral', $referral_user_id, $user_id );
						do_action( 'activityRating_asReferral', $user_id );
						do_action( 'activityRating_asReferrer', $referral_user_id );
					}
				}
			}

			$wpdb->insert( TABLE_REFERRAL, [
				'user_id'          => $user_id,
				'referral_code'    => $code,
				'user_id_referral' => $referral_user_id
			], [ '%d', '%d', '%d' ] );
		}
	}

	add_action( 'user_register', 'generate_referral_code' );

	function check_ref_code_by_company( $referral_code ) {
		global $wpdb;
		if ( empty( $referral_code ) ) {
			return false;
		}

		return $wpdb->get_var( "SELECT EXISTS(SELECT id FROM {$wpdb->get_blog_prefix()}posts WHERE id = {$referral_code} AND post_type = '" . COMPANY . "')" );
	}

	function get_user_by_referral_code( $referral_code ) {
		global $wpdb;
		if ( empty( $referral_code ) ) {
			return false;
		}
		$referral_user_id = $wpdb->get_var( "SELECT user_id FROM " . TABLE_REFERRAL . " WHERE referral_code = $referral_code" );

		return ! empty( $referral_user_id ) ? $referral_user_id : false;
	}

	function get_referral_code_by_user( $user_id ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return false;
		}
		$referral_code = $wpdb->get_var( "SELECT referral_code FROM " . TABLE_REFERRAL . " WHERE user_id = $user_id" );

		return ! empty( $referral_code ) ? $referral_code : false;
	}

	function get_referral( $user_id ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return false;
		}
		$list = $wpdb->get_results( "SELECT user_id, user_id_referral FROM " . TABLE_REFERRAL . " WHERE user_id = $user_id OR user_id_referral = $user_id", ARRAY_A );

		return ! empty( $list ) ? $list : false;
	}

	function get_sponsor( $user_id ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return false;
		}
		$sponsor_name = $wpdb->get_var( "SELECT wp_users.user_login FROM " . TABLE_REFERRAL . " 
                                            LEFT JOIN wp_users ON wp_users.ID = " . TABLE_REFERRAL . ".user_id_referral 
                                            WHERE " . TABLE_REFERRAL . ".user_id = $user_id" );

		return ! empty( $sponsor_name ) ? $sponsor_name : false;
	}

	function get_sponsor_id( $user_id ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return false;
		}
		$sponsor = $wpdb->get_var( "SELECT user_id_referral  FROM " . TABLE_REFERRAL . " 
                                            LEFT JOIN wp_users ON wp_users.ID = " . TABLE_REFERRAL . ".user_id_referral 
                                            WHERE " . TABLE_REFERRAL . ".user_id = $user_id" );

		return ! empty( $sponsor ) ? $sponsor : false;
	}

	/*
	 * admin panel
	 */
	function referral_menu() {
		add_menu_page( 'Referral code', 'Referral code', 'manage_options', 'referral-code', 'show_referral_code' );
		add_action( 'admin_print_styles', 'referral_code_styles' );
	}

	add_action( 'admin_menu', 'referral_menu' );

	function referral_code_styles() {
		wp_enqueue_style( 'referer', get_template_directory_uri() . '/wpp/modules/referral_code/css/referral_code_style.css', [], time(), 'all' );
	}

	function show_referral_code() {
		global $wpdb;
		$action = isset( $_REQUEST[ 'action' ] ) ? $_REQUEST[ 'action' ] : '';

		switch ( $action ) {
			case 'getList':
				$page      = ! empty( $_POST[ 'page' ] ) ? $_POST[ 'page' ] : 1;
				$referrals = get_list_referrals( $page );

				$html = '';
				foreach ( $referrals as $item ) {
					ob_start();
					include 'tpl/list.php';
					$html .= ob_get_clean();
				}
				$data[ 'referrals' ]  = $html;
				$data[ 'pagination' ] = getPagination( $page );

				outputJSON( $data, 1 );
				break;
			default:
				$show_page   = ! empty( $_POST[ 'page' ] ) ? ( $_POST[ 'page' ] - 1 ) * PAGE_STEP : 0;
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
			$response[ 'msg' ] = $data;
		} else {
			$response = ! empty( $data ) ? $data : [];
		}

		$response[ 'status' ] = isset( $response[ 'status' ] ) ? $response[ 'status' ] : ( $status ? 'success' : 'error' );
		$response[ 'msg' ]    = ( $response[ 'status' ] == 'error' && empty( $response[ 'msg' ] ) ) ? 'Error!!!' : $response[ 'msg' ];

		ob_clean();
		header_remove();
		header( 'Content-type: text/json; charset=UTF-8' );
		echo json_encode( $response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		exit;
	}

	function get_list_referrals( $page = 1, $user = null ) {
		global $wpdb;
		$user_id = ! empty( $user ) ? $user : ( ! empty( $_POST[ 'user_id' ] ) ? $_POST[ 'user_id' ] : null );
		$where   = ! empty( $user_id ) ? " WHERE rc.user_id_referral=" . (int) $user_id : "";

		$orderBy = " ORDER BY user_id ASC";
		if ( ! empty( $_POST[ 'orderBy' ] ) ) {
			$dataOrderBy                = [];
			$data_parseOrderBy          = explode( ',', trim( $_POST[ 'orderBy' ] ) );
			$dataOrderBy[ 'field' ]     = trim( $data_parseOrderBy[ 0 ] );
			$dataOrderBy[ 'direction' ] = ( trim( $data_parseOrderBy[ 1 ] ) == 'ASC' ) ? 'ASC' : 'DESC';

			$orderBy = " ORDER BY " . $dataOrderBy[ 'field' ] . " " . $dataOrderBy[ 'direction' ];
		}

		if ( ! empty( $page ) && $page == 'all' ) {
			$limit = '';
		} else {
			$show_page = ! empty( $page ) ? ( $page - 1 ) * PAGE_STEP : $page;
			$limit     = " LIMIT " . $show_page . "," . PAGE_STEP;
		}

		$referrals = $wpdb->get_results( "
        SELECT rc.user_id, rc.referral_code, 
            u.user_login, u.display_name user_name, 
            u2.user_login user_referral_login, u2.display_name user_referral_name,
            (SELECT COUNT(*) FROM " . TABLE_REFERRAL . "
                WHERE  u.ID=user_id_referral) count_referrals
        FROM " . TABLE_REFERRAL . " rc
        LEFT JOIN $wpdb->users u ON u.ID=rc.user_id
        LEFT JOIN $wpdb->users u2 ON u2.ID=rc.user_id_referral " . $where . $orderBy . $limit, ARRAY_A );

		return $referrals;
	}


	function set_referral_code_by_old_user( $user_id ) {
		global $wpdb;
		if ( ! empty( $user_id ) ) {
			$code = (int) time() + $user_id;
			$wpdb->insert( TABLE_REFERRAL, [
				'user_id'          => $user_id,
				'referral_code'    => $code,
				'user_id_referral' => 0
			], [ '%d', '%d', '%d' ] );
		}
	}

	function get_count_referrals( $user_id = null ) {
		global $wpdb;
		$where = ! empty( $user_id ) ? " WHERE user_id_referral=" . $user_id : '';
		$sql   = "SELECT COUNT(*) FROM " . TABLE_REFERRAL . $where;

		return $wpdb->get_var( $sql );
	}

	function getPagination( $currentPage = 1 ) {
		$total_referrals = get_count_referrals();
		$currentPage     = (int) $currentPage;
		$urlPattern      = 'javascript:mod.getData(\'(:num)\')';

		include_once 'inc/Paginator.php';
		$paginator = new Paginator( $total_referrals, PAGE_STEP, (int) $currentPage, $urlPattern );

		return $paginator->toHtml();
	}
